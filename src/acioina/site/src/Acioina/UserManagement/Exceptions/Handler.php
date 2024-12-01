<?php namespace Acioina\UserManagement\Exceptions;
      
      use Throwable;
      use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
      use Illuminate\Http\Response;
      use Illuminate\Database\QueryException;
      use Illuminate\Auth\AuthenticationException;
      use Illuminate\Validation\ValidationException;
      use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
      use Distilleries\Expendable\Helpers\UserUtils;
      use Acioina\UserManagement\Http\Controllers\Base\FrontEndErrorController;
      use Exception;

      class Handler extends ExceptionHandler 
      {
          /**
           * A list of the exception types that should not be reported.
           *
           * @var array
           */
          protected $dontReport = [
              \Illuminate\Auth\AuthenticationException::class,
              \Illuminate\Auth\Access\AuthorizationException::class,
              \Symfony\Component\HttpKernel\Exception\HttpException::class,
              \Illuminate\Database\Eloquent\ModelNotFoundException::class,
              \Illuminate\Session\TokenMismatchException::class,
              \Illuminate\Validation\ValidationException::class,
          ];

          
          /**
           * Convert an authentication exception into a response.
           *
           * @param  \Illuminate\Http\Request  $request
           * @param  \Illuminate\Auth\AuthenticationException  $exception
           * @return \Symfony\Component\HttpFoundation\Response
           */
          protected function unauthenticated($request, AuthenticationException $exception)
          {
              if ($request->expectsJson()) {
                  return response()->json(['error' => 'Unauthenticated.'], 401);
              }

              if (!config('app.debug'))
              {
                  return $this->displayException($exception, 401);
              }

          }

          /**
           * Render the given HttpException.
           *
           * @param  \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface  $e
           * @return \Symfony\Component\HttpFoundation\Response
           */
          protected function renderHttpException(HttpExceptionInterface $e)
          {
              //"RecursiveDirectoryIterator::__construct(): Access is denied. (code: 5)"

              $errorMessage = $e->getMessage();

              $uncaughtPDOException = /*overload*/mb_strrpos( $errorMessage, "SQLSTATE[HY000] [2002]" ); 
              if ( $uncaughtPDOException !== false )
              {
                  return $this->convertExceptionToResponse($e);
              }

              if (!config('app.debug'))
              {
                  return $this->displayException($e, $e->getStatusCode());
              }

              return $this->convertExceptionToResponse($e);
          }


          /**
           * Render an exception into a response.
           *
           * @param  \Illuminate\Http\Request  $request
           * @param  \Exception  $e
           * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
           */
          public function render($request, Throwable $e)
          {
              if ($request->expectsJson()) {
                  return $this->getJsonResponse($e);
              }

              $errorMessage = $e->getMessage();

              $integrityConstraint = /*overload*/mb_strrpos( $errorMessage, 'Integrity constraint violation' ); 
              $unserializeErrorAtOffset = /*overload*/mb_strrpos( $errorMessage, 'unserialize(): Error at offset' );

              $noSlugColumn = /*overload*/mb_strrpos( $errorMessage, "Unknown column 'slug' in 'where clause'" ); 
              $missingArgument = /*overload*/mb_strrpos( $errorMessage, 'Missing argument 1' );
              $stringBladeCompiler = /*overload*/mb_strrpos( $errorMessage, 'Method Wpb\\StringBladeCompiler\\StringView::__toString() must not throw an exception' );
              $tooFewArguments = /*overload*/mb_strrpos( $errorMessage, 'Too few arguments to function Acioina\\UserManagement\\Http\\Controllers\\Base\\BaseComponent::getView(), 0 passed and exactly 1 expected');
              
              $noQueryResults = $this->getNotFoundHttpException($e);

              if(
                  $integrityConstraint !== false || 
                  $unserializeErrorAtOffset !== false
                  )
              {
                  // 505 means known exception but you have to debug this exception. This is likely a Laravel bug. 
                  // 500 means unknown exception
                  $code = 505;
              }else{
                  $code =  ( method_exists($e, 'getStatusCode' ) ? $e->getStatusCode() : 404);
              }

              if (
                      !$this->isHttpException($e) && ( 
                      $noQueryResults !== false ||
                      $noSlugColumn !== false ||
                      $integrityConstraint !== false ||
                      $missingArgument !== false ||
                      $stringBladeCompiler !== false ||
                      $unserializeErrorAtOffset !== false ||
                      $tooFewArguments !== false )
                  )
              {
                  if (!config('app.debug'))
                  {
                      return $this->displayException($e, $code);
                  }
              }

              return parent::render($request, $e);
          }

          /**
           * Report or log an exception.
           *
           * @param  \Exception  $e
           * @return mixed
           *
           * @throws \Exception
           */
          public function report(Throwable $e)
          {
              $code =  ( method_exists($e, 'getStatusCode' ) ? $e->getStatusCode() : 0);
              if ($code == 403)
              {
                  UserUtils::forgotIsLoggedIn();
                  UserUtils::forgotArea();
              }
              
              $noQueryResults = $this->getNotFoundHttpException($e);
              // 405->{Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException}
              if ( ! ($code == 405 || $code == 404 || $code == 403 || $noQueryResults === true))
              {
                  if (config('app.debug'))
                  {
                      return parent::report($e);
                  }
              }
          }

          private function displayException(Exception $exception, $code)
          {
             return app(FrontEndErrorController::class)->callAction("display", [$exception, $code]);
          }

          private function getNotFoundHttpException(Throwable $e)
          {
              $errorMessage = $e->getMessage();

              $noQueryResults = $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
              $noQueryResults = $noQueryResults ? : $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException;
              $noQueryResults = $noQueryResults ? : /*overload*/mb_strrpos( $errorMessage, 'No query results for model' );

              return $noQueryResults === true ? : $noQueryResults === 0;
          }

          /**
           * Get the json response for the exception.
           *
           * @param Exception $exception
           * @return \Illuminate\Http\JsonResponse
           */
          protected function getJsonResponse(Throwable $exception)
          {
              $debugEnabled = config('app.debug');

              $exception = $this->prepareException($exception);

              /*
               * Handle validation errors thrown using ValidationException.
               */
              if ($exception instanceof ValidationException) {
                  $validationErrors = $exception->validator->errors()->getMessages();

                  return response()->json(['errors' => $validationErrors], 422);
              }

              /*
               * Handle database errors thrown using QueryException.
               * Prevent sensitive information from leaking in the error message.
               */
              if ($exception instanceof QueryException) {
                  if ($debugEnabled) {
                      $message = $exception->getMessage();
                  } else {
                      $message = 'Internal Server Error';
                  }
              }

              $statusCode = ( method_exists($exception, 'getStatusCode' ) ? $exception->getStatusCode() : 500);

              if (! isset($message) && ! ($message = $exception->getMessage())) {
                  $message = sprintf('%d %s', $statusCode, Response::$statusTexts[$statusCode]);
              }

              $errors = [
                  'message' => $message,
                  'status_code' => $statusCode,
              ];

              if ($debugEnabled) {
                  $errors['exception'] = get_class($exception);
                  $errors['trace'] = explode("\n", $exception->getTraceAsString());
              }

              return response()->json(['errors' => $errors], $statusCode);
          }
          
      }

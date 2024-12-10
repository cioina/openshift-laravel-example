<?php namespace Acioina\UserManagement\Http\Controllers;

      use Symfony\Component\Finder\Finder;
      use Symfony\Component\Mime\MimeTypes;
      use Symfony\Component\Finder\SplFileInfo;
      use Symfony\Component\HttpFoundation\BinaryFileResponse;
      use Illuminate\Support\Arr;

      /**
       * Class ResourceController.
       */
      class ResourceController 
      {
         /**
         * @var MimeTypes
         */
          private $mimeTypes;

          /**
           * @var Finder
           */
          private $finder;

          /**
           * ResourceController constructor.
           *
           * @param Finder    $finder
           */
          public function __construct(Finder $finder, MimeTypes $mimeTypes)
          {
              $this->finder = $finder;
              $this->mimeTypes = $mimeTypes;
          }

          /**
           * Serve the requested resource.
           *
           * @param string    $package
           * @param string    $path
           * @param Dashboard $dashboard
           *
           * @return BinaryFileResponse
           */
          public function getIndex($path)
          {
              //Accessing The Current Route
              //$route = Route::current();
              //$name = Route::currentRouteName();
              //$action = Route::currentRouteAction();

              $dir = $GLOBALS['CIOINA_Config']->get('AssetsDir');

              abort_if($dir === null, 404);

              $resources = $this->finder
                  ->ignoreUnreadableDirs()
                  ->followLinks()
                  ->in($dir)
                  ->files()
                  ->path(dirname($path))
                  ->name(basename($path));

              $iterator = tap($resources->getIterator())->rewind();

              /* Changing the separator for Windows operating systems */
              $path = str_replace('/', DIRECTORY_SEPARATOR, $path);

             $resource = collect($iterator)
                  ->filter(static function (SplFileInfo $file) use ($path) {
                      return $file->getRelativePathname() === $path;
                  })
                  ->first();

              abort_if($resource === null, 404);

              $mimeType = $this->getMimeType($resource->getExtension());

              return response()->file($resource->getRealPath(), [
                  'Content-Type'  =>  $mimeType ?? 'text/plain',
                  'Cache-Control' => 'public, max-age=31536000',
              ]);
          }

          /**
           * @param string     $mimeType
           * @param mixed|null $default
           *
           * @return string|null
           */
          private function getExtension(string $mimeType, string $default = null): ?string
          {
              return Arr::first($this->mimeTypes->getExtensions($mimeType), null, $default);
          }

          /**
           * @param string      $ext
           * @param string|null $default
           *
           * @return string|null
           */
          private function getMimeType(string $ext, string $default = null): ?string
          {
              return Arr::first($this->mimeTypes->getMimeTypes($ext), null, $default);
          }
      }

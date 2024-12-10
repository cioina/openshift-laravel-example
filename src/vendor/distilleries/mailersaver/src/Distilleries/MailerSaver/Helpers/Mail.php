<?php namespace Distilleries\MailerSaver\Helpers;

      use Swift_Mailer;
      use Wpb\StringBladeCompiler\StringView;
      use Distilleries\MailerSaver\Contracts\MailModelContract;

      use Illuminate\Config\Repository;
      use Illuminate\Mail\Mailer;
      use Illuminate\Mail\MailManager;
      use Illuminate\View\Factory;
      use Illuminate\Events\Dispatcher;

      class Mail extends Mailer 
      {
          protected $model;
          protected $config;
          protected $override;
 
          public function __construct(MailModelContract $model, Repository $config, string $name, Factory $views, MailManager $mailManager, Dispatcher $events = null)
          {
              $this->model  = $model;
              $this->config = $config;

              parent::__construct($name, $views, $mailManager->getSwiftMailer(), $events);
          }

          /**
           * Render the given view.
           *
           * @param  string  $view
           * @param  array  $data
           * @return string
           */
          protected function renderView($view, $data)
          {
              //Get content from the last Email model
              $body = $this->model->getTemplate($view);

              //If empty get the content from "standard" email.password view
              $body = (empty($body)) ? $this->views->make($view, $data)->render() : $body;

              $data['subject'] = $this->model->getSubject();

              $stringView = new StringView;

              $body = $stringView->make(
                  array(
                      'template'   => $body,
                      'cache_key'  => uniqid(),
                      'updated_at' => 0
                  ),
                  $data
              );

              $data['body_mail'] = $body;

              //Insert all into "nice" HTML email template
              $config = $this->config->get('mailersaver.mail');

              return $this->views->make($config['template'], $data)->render();
          }

          /**
           * Send a new message using a view.
           *
           * @param  string|array|MailableContract  $view
           * @param  array  $data
           * @param  \Closure|string  $callback
           * @return void
           */
          public function send($view, array $data = [], $callback = null)
          {
              // First we need to parse the view, which could either be a string or an array
              // containing both an HTML and plain text versions of the view which should
              // be used when sending an e-mail. We will extract both of them out here.
              list($view, $plain, $raw) = $this->parseView($view);

              $model = $this->model->initByTemplate($view);
              $template = $model->get()->last();
              
              //TODO: acioina Implement this later. Mailgun does not work with mime type so HTML $view is used as plain text.
              //$plain    = (!empty($template)) ? $template->getPlain() : $plain;

              if (!empty($template))
              {
                  $this->model = $template;
              }

              $data['message'] = $message = $this->createMessage();

              // Once we have retrieved the view content for the e-mail we will set the body
              // of this message using the HTML type, which will provide a simple wrapper
              // to creating view based emails that are able to receive arrays of data.
              $this->addContent($message, $view, $plain, $raw, $data);
              $this->addSubject($message);
              $this->addBcc($message);
              $this->addCc($message);
              $this->overideTo($message);

              call_user_func($callback, $message);

              $swiftMessage = $message->getSwiftMessage();

              $this->sendSwiftMessage($swiftMessage);



              //if ($view instanceof MailableContract) {
              //    return $this->sendMailable($view);
              //}

              //// First we need to parse the view, which could either be a string or an array
              //// containing both an HTML and plain text versions of the view which should
              //// be used when sending an e-mail. We will extract both of them out here.
              //list($view, $plain, $raw) = $this->parseView($view);

              //$data['message'] = $message = $this->createMessage();

              //// Once we have retrieved the view content for the e-mail we will set the body
              //// of this message using the HTML type, which will provide a simple wrapper
              //// to creating view based emails that are able to receive arrays of data.
              //$this->addContent($message, $view, $plain, $raw, $data);

              //call_user_func($callback, $message);

              //// If a global "to" address has been set, we will set that address on the mail
              //// message. This is primarily useful during local development in which each
              //// message should be delivered into a single mail address for inspection.
              //if (isset($this->to['address'])) {
              //    $this->setGlobalTo($message);
              //}

              //// Next we will determine if the message should be sent. We give the developer
              //// one final chance to stop this message and then we will send it to all of
              //// its recipients. We will then fire the sent event for the sent message.
              //$swiftMessage = $message->getSwiftMessage();

              //if ($this->shouldSendMessage($swiftMessage)) {
              //    $this->sendSwiftMessage($swiftMessage);

              //    $this->dispatchSentEvent($message);
              //}
          }

          /**
           * @param \Illuminate\Mail\Message $message
           */
          public function addCc($message)
          {

              $cc = ($this->isOveride()) ? $this->override['cc'] : (!empty($this->model) ? $this->model->getCc() : '');

              if (!empty($cc))
              {
                  $message->cc($cc);
              }

          }

          /**
           * @param \Illuminate\Mail\Message $message
           */
          public function addBcc($message)
          {
              $bcc = ($this->isOveride()) ? $this->override['bcc'] : $this->model->getBcc();

              if (!empty($bcc))
              {
                  $message->bcc($bcc);
              }

          }

          /**
           * @param \Illuminate\Mail\Message $message
           */
          public function addSubject($message)
          {
              $subject = $this->model->getSubject();

              if (!empty($subject))
              {
                  $message->subject($subject);
              }

          }

          /**
           * @param \Illuminate\Mail\Message $message
           */
          public function overideTo($message)
          {
              $to = ($this->isOveride()) ? $this->override['to'] : '';
              if (!empty($to))
              {
                  $message->to($to, null, true);
              }
          }

          public function isOveride()
          {
              $config = $this->config->get('mailersaver.mail');
              $this->override = $config['override'];

              return $this->override['enabled'];
          }

      } 
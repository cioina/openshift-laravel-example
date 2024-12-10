<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Datatables\FacebookImage\FacebookImageDatatable;
      use Distilleries\Expendable\Forms\FacebookImage\FacebookImageForm;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\BaseComponent;
      use Distilleries\Expendable\Helpers\StaticLabel;
      use Distilleries\Expendable\Helpers\FormUtils;
      use Distilleries\Expendable\Models\FacebookImage;
      use Distilleries\Expendable\Models\Language;
      use Distilleries\Expendable\Models\SentEmail;
      use Distilleries\Expendable\Models\Client;
      use Distilleries\Expendable\Formatter\Message;

      use \Request;
      use \Session;

      use \CIOINA_Util;

      use \Facebook\Facebook;
      use \Facebook\Exceptions\FacebookSDKException;
      use \Facebook\Exceptions\FacebookResponseException;
      use \Facebook\Authentication\AccessToken;

      class FacebookImageController extends BaseComponent 
      {
          public function __construct(FacebookImageDatatable $datatable, FacebookImageForm $form, FacebookImage $model, LayoutManagerContract $layoutManager)
          {
              parent::__construct($model, $layoutManager);

              $this->datatable = $datatable;
              $this->form      = $form;
          }

          /**
           * @codeCoverageIgnore
           */
          public function getSynchronize()
          {
              SentEmail::create([
                'sent_to_email'       => $GLOBALS['CIOINA_Config']->get('MailgunRecipient'),
                'email_type'          => 7,
                'request_session'     => Session::getId(), 
                'request_ip_address'  => Request::ip(),
                 ]);

              $locale = Language::where('iso','like', app()->getLocale() . '%')->where('is_default','=',1)->first();
              if (empty($locale)) 
              {
                  abort(500);
              }

              $fb_settings = FormUtils::getFacebookSettings();
              if ($fb_settings === false)
              {
                  $CIOINA_fatalError('Incorrect Facebook settings.');
              }

              if(! $fb_settings->data->IsFacebookEnabled)
              {
                  return redirect()->back()->with(Message::WARNING, [trans('expendable::success.facebook_is_not_ok')]);
              }

              // gets $client->fb_id is null for some reason
              //$client = Client::findOrFail(1);

              $query = 'SELECT '
                . CIOINA_Util::backquote('fb_token') .','
                . CIOINA_Util::backquote('fb_id') 
                . ' FROM ' . CIOINA_Util::backquote($GLOBALS['CIOINA_Config']->get('MySqlDatabase')) 
                . '.' . CIOINA_Util::backquote('clients')
                . ' WHERE ' . CIOINA_Util::backquote('id') . '= 1';
              $fb_user = $GLOBALS['CIOINA_dbi']->fetchResult($query);
              
              if (! (isset($fb_user) && count($fb_user) === 1))
              {
                  CIOINA_fatalError('Cannot fiend client');
              }

              try {
                  $accessToken = new AccessToken($fb_user[0]['fb_token']);
                  if (!$accessToken instanceof AccessToken) {
                      $accessToken = null;
                  }
                  elseif ($accessToken->isExpired())
                  {
                      $accessToken = null;
                  }
              }
              catch(FacebookSDKException $e) {
                  $accessToken = null;
                  CIOINA_fatalError('Facebook Authentication AccessToken');
              }

              if ( !isset($accessToken ) ) {
                  CIOINA_fatalError('Facebook Authentication AccessToken');
              }
              
              try {
                  $fb = new Facebook([
                 'app_id'     => $GLOBALS['CIOINA_Config']->get('FacebookAppId'),
                 'app_secret' => $GLOBALS['CIOINA_Config']->get('FacebookAppSecret'),
                 'default_graph_version' => $GLOBALS['CIOINA_Config']->get('FacebookGraphVersion')]);
              }
              catch(Exception $e) {
                  $CIOINA_fatalError('Cannot create Facebook object.');
              }

              try 
              {
                  $fb->setDefaultAccessToken($accessToken);

                  $response = $fb->get('/' . $fb_user[0]['fb_id'] . '/albums');
                  $albums = $response->getGraphEdge();

                  $totalPhotos =  $newPhotos = $updatedPhotos = 0;
                  $totalAlbums = isset($albums) && is_array($albums) ? count($albums) : 0 ;

                  while(isset($albums)) 
                  {
                      foreach ($albums as $album) 
                      {
                          $response = $fb->get('/'.$album['id'].'/photos?fields=picture');
                          $photos = $response->getGraphEdge();
                          $totalPhotos += isset($photos) && is_array($photos) ? count($photos) : 0 ;

                          while(isset($photos)) 
                          {
                              foreach ($photos as $photo) 
                              {
                                  $response = $fb->get('/'.$photo['id'].'?fields=images');
                                  $images = $response->getGraphObject();
                                  foreach ($images as $image) 
                                  {
                                      $models = $this->model->getByAlbumAndPhoto( $album['id'], $photo['id'] );
                                      if ($models->isEmpty())
                                      {
                                          $model = new $this->model;
                                          $model->album_id = $album['id'];
                                          $model->photo_id = $photo['id'];
                                          $model->small_image_url = $photo['picture'];
                                          $model->original_image_url = $image[0]['source'];
                                          $model->status = StaticLabel::STATUS_OFFLINE;
                                          $model->save();

                                          $newPhotos++;
                                      }else{
                                          foreach ($models as $model) 
                                          {
                                              if(
                                                  ($model->small_image_url !== $photo['picture']) ||
                                                  ($model->original_image_url !== $image[0]['source'])
                                               )
                                                  $model->small_image_url = $photo['picture'];
                                              $model->original_image_url = $image[0]['source'];
                                              $model->save();

                                              $updatedPhotos++;
                                          }
                                      }
                                      break;
                                  }
                              }
                              $photos = $fb->next($photos);
                          }
                      }
                      $albums = $fb->next($albums);
                  }

                  SentEmail::create([
                      'sent_to_email'       => $GLOBALS['CIOINA_Config']->get('MailgunRecipient'),
                      'email_type'          => 7,
                      'request_session'     => "Total_Albums=$totalAlbums Total_Photos=$totalPhotos New_Photos=$newPhotos  Updated_Photos=$updatedPhotos",
                      'request_ip_address'  => Request::ip(),
                 ]);

              }
              catch(FacebookResponseException $e) {
                  CIOINA_fatalError('Graph returned an error: ' . $e->getMessage());
              }
              catch(FacebookSDKException $e) {
                  CIOINA_fatalError('Facebook SDK returned an error');
              }
              
              return redirect()->back()->with(Message::MESSAGE, [trans('expendable::success.synchronize')]);
          }

      }
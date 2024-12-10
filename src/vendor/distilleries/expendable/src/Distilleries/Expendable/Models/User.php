<?php namespace Distilleries\Expendable\Models;

      use Distilleries\Expendable\Helpers\UserUtils;
      use Distilleries\PermissionUtil\Contracts\PermissionUtilContract;
      use Distilleries\Expendable\Observers\PasswordObserver;
      use Distilleries\Expendable\Models\StatusTrait;

      use Illuminate\Notifications\Notifiable;
      use Illuminate\Auth\Authenticatable;
      use Illuminate\Auth\Passwords\CanResetPassword;
      use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
      use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class User extends Model implements BaseModelContract, AuthenticatableContract, CanResetPasswordContract, PermissionUtilContract 
      {
          use BaseModel, Authenticatable, CanResetPassword, Notifiable, StatusTrait;

          protected $tabPermission = [];
          protected $fillable = [
              'email',
              'password',
              'status',
              'role_id'
          ];

          /**
           * The attributes excluded from the model's JSON form.
           *
           * @var array
           */
          protected $hidden = ['password', 'remember_token'];

          public function role()
          {
              return $this->belongsTo('Distilleries\Expendable\Models\Role');
          }

          public static function boot()
          {
              parent::boot();
              self::observe(new PasswordObserver);
          }

          public function hasAccess($key)
          {
              if (!empty($this->role->overide_permission))
              {
                  return true;
              }

              return UserUtils::hasAccess($key);
          }

          public function getFirstRedirect($left)
          {
              foreach ($left as $item)
              {
                  if (!empty($item['action']) && $this->hasAccess($item['action']))
                  {
                      $action = \Distilleries\Expendable\Helpers\FormUtils::tryHttps(action($item['action']));
                      return preg_replace( '/\/index/i', '',  $action . (isset($item['slug'])?'/'.$item['slug']:''));

                  } else if (!empty($item['submenu']))
                  {
                      $redirect = $this->getFirstRedirect($item['submenu']);

                      if (!empty($redirect))
                      {
                          return $redirect;
                      }
                  }
              }

              return '';
          }

          /**
           * Send the given notification.
           *
           * @param  mixed  $instance
           * @return void
           */
          public function notify($instance)
          {
              \MailerSaver::send('emails.password', 
                  [
                    'token' => $instance->token, 
                  ], 
                  //Illuminate\Mail\Message $message
                  function ($message) 
                  {
                      $message->to($this->email, null, true);
                  }
              );

              if (defined('TESTSUITE'))
              {
                  $_SESSION['isLoggedIn'] = $instance->token;
              }
          }

          public function routeNotificationForMail()
          {
              return null;
          }

          /**
           * Get the entity's notifications.
           */
          public function notifications()
          {
              return null;
          }

          /**
           * Get the entity's read notifications.
           */
          public function readNotifications()
          {
          }

          /**
           * Get the entity's unread notifications.
           */
          public function unreadNotifications()
          {
          }

      }

<?php namespace Distilleries\Expendable\Models;

      use JWTAuth;
      use Illuminate\Notifications\Notifiable;
      use Illuminate\Foundation\Auth\User as Authenticatable;
      use Tymon\JWTAuth\Contracts\JWTSubject;
      use Illuminate\Support\Facades\Hash;
      use Distilleries\Expendable\Contracts\BaseModelContract;
      use Distilleries\Expendable\Helpers\FormUtils;

      class Client extends Authenticatable implements BaseModelContract, JWTSubject
      {
          use BaseModel, Notifiable;
          
          protected $fillable = [
            'fb_picture',
            'fb_token',

            'fb_id',
            'fb_verified',
            'fb_email',
            'fb_first_name',
            'fb_last_name',

            'email',
            'username',
            'password',

            'ac_code',
            'first_name',
            'last_name',
            'phone',
            'zip',
            'gender',
            'country_code',
            'state_code',
            'request_id',
            'request_ip_address',
            'is_suspended',
            'is_deleted',
           ];

          /**
           * The attributes excluded from the model's JSON form.
           *
           * @var array
           */
          protected $hidden = ['fb_token', 'password', 'remember_token'];

          /**
           * Set the password using bcrypt hash.
           *
           * @param $value
           */
          public function setPasswordAttribute($value)
          {
              //See Illuminate\Hashing\AbstractHasher uses password_get_info() and password_verify()
              $info = Hash::info($value);

              if(is_array($info) && count($info) !== 0 and $info['algo'] === null)
              {
                  $value = Hash::make($value); 
              }
              $this->attributes['password'] = $value;
          }

          /**
           * Generate a JWT token for the user.
           *
           * @return string
           */
          public function getTokenAttribute()
          {
              return JWTAuth::fromUser($this);
          }

          /**
           * Get the key name for route model binding.
           *
           * @return string
           */
          public function getRouteKeyName()
          {
              return 'username';
          }

          public function getJWTIdentifier()
          {
              return $this->getKey();
          }
          public function getJWTCustomClaims()
          {
              return [
                  'oli' => FormUtils::getSessionOnlineId(),
                  ];
          }

      }

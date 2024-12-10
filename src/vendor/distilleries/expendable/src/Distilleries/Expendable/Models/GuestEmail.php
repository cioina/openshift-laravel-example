<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class GuestEmail extends Model implements BaseModelContract 
      {
          use BaseModel;

          protected $fillable = [
            'is_facebook',
            'email',
            'email_subject',
            'email_body', 
            'first_name',
            'last_name',
            'phone',
            'zip',
            'gender',
            'country_code',
            'state_code',
            'age',
            'request_id',
            'request_ip_address',
            'has_facebook',
           ];
      }
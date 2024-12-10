<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class SentEmail extends Model implements BaseModelContract 
      {
          use BaseModel;

          protected $fillable = [
            'sent_to_email',
            'email_type',
            'request_session', 
            'request_ip_address',
           ];
      }
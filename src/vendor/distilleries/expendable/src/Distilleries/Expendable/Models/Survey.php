<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class Survey extends Model implements BaseModelContract 
      {
          use BaseModel;

          protected $fillable = [
            'person_name',
            'is_complex_name',
            'complex_name',
            'email',
            'phone',
            'zip',
            'gender',
            'country_code',
            'state_code',
            'first_drive_age',
            'recent_vacation',
            'ip_address',
            'session_id',
            'client_id',
           ];
      }

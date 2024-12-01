<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class EmailType extends Model implements BaseModelContract 
      {
          use BaseModel;

          protected $fillable = [
              'email_type_id',
              'email_type_name',
          ];
      }
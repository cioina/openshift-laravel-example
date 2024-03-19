<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class CodeBlock extends Model implements BaseModelContract 
      {
          use BaseModel;

          protected $fillable = [
              'label',
              'code_type',
              'code_block',
              'content',
              'status',
          ];
      }
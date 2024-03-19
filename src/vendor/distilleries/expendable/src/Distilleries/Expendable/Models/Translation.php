<?php namespace Distilleries\Expendable\Models;

      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class Translation extends Model implements BaseModelContract 
      {
          use BaseModel;

          protected $fillable = [
              'iso',
              'id_element',
              'model',
              'id_source'
          ];

          /**
           * Get the name of the "iso" column.
           *
           * @return string
           */
          public function getIsoColumn()
          {
              return 'iso';
          }

          /**
           * Get the name of the "id_element" column.
           *
           * @return string
           */
          public function getIdElementColumn()
          {
              return 'id_element';
          }

          /**
           * Get the name of the "id_element" column.
           *
           * @return string
           */
          public function getIdSourceColumn()
          {
              return 'id_source';
          }

          /**
           * Get the name of the "model" column.
           *
           * @return string
           */
          public function getModelColumn()
          {
              return 'model';
          }

          public function scopeByElement($query, Model $model)
          {
              return $query->where($this->getTable() . '.' . $this->getModelColumn(), '=', $model->getTable())
                           ->where($this->getTable() . '.' . $this->getIdSourceColumn(), '=', $model->getKey());
          }

      }

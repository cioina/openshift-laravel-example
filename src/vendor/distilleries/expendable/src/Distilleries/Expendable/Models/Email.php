<?php namespace Distilleries\Expendable\Models;

      use Distilleries\Expendable\Scopes\Translatable;
      use Distilleries\Expendable\Helpers\StaticLabel;
      use Distilleries\MailerSaver\Contracts\MailModelContract;
      use Illuminate\Database\Eloquent\Model; 
      use Distilleries\Expendable\Contracts\BaseModelContract;

      class Email extends Model implements BaseModelContract, MailModelContract {

          use BaseModel, Translatable;

          protected $fillable = [
              'label',
              'body_type',
              'action',
              'cc',
              'bcc',
              'content',
              'status',
          ];

          public function initByTemplate($view = null)
          {
              return $this->where('action', '=', $view)
                          ->where('status', '=', StaticLabel::STATUS_ONLINE);
          }

          public function getTemplate($view = null)
          {
              if (!empty($this->action))
              {
                  return $this->content;
              }

              return null;
          }

          public function getBcc()
          {
              return !empty($this->bcc) ? explode(',', $this->bcc) : [];
          }

          public function getSubject()
          {
              return $this->label;
          }

          public function getCc()
          {
              return !empty($this->cc) ? explode(',', $this->cc) : [];
          }

          public function getPlain()
          {
              return strtolower($this->body_type);
          }
      }
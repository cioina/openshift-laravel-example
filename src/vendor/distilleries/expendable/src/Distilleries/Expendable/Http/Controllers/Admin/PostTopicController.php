<?php namespace Distilleries\Expendable\Http\Controllers\Admin;

      use Distilleries\Expendable\Contracts\LayoutManagerContract;
      use Distilleries\Expendable\Forms\PostTopic\PostTopicForm;
      use Distilleries\Expendable\Http\Controllers\Admin\Base\ModelBaseController;
      use Distilleries\Expendable\States\FormStateTrait;
      use Illuminate\Http\Request;
      use Distilleries\Expendable\Models\PostTopic;
      use Distilleries\FormBuilder\Contracts\FormStateContract;
      use \FormBuilder;

      class PostTopicController extends ModelBaseController  implements FormStateContract
      {
          use FormStateTrait;

          public function __construct(PostTopicForm $form, PostTopic $model, LayoutManagerContract $layoutManager)
          {
              parent::__construct($model, $layoutManager);
              $this->form = $form;
          }

          public function getIndex()
          {
              return redirect()->to(action('\\' . get_class($this) . '@getEdit'), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
          }

          public function postEdit(Request $request)
          {
              $form = FormBuilder::create(get_class($this->form), [
                         'model' => $this->model,
              ]);

              if ($form->hasError())
              {
                  return $form->validateAndRedirectBack();
              }

              $postId = $request->get($this->model->getKeyName());
              if(! empty($postId) && isset($postId))
              {
                  PostTopic::where('post_id', '=', $postId)->delete();
              }

              $postTopics = $request->get('post_topics');
              if(! empty($postTopics) && isset($postTopics))
              {
                  foreach ($postTopics as $post_id=>$topics)
                  {
                      foreach ($topics as $topic_id)
                      {
                          $postTopicModel = new $this->model;
                          $postTopicModel->post_id = $post_id;
                          $postTopicModel->topic_id = $topic_id;
                          $postTopicModel->save();
                      }
                  }
              }

              return redirect()->to(action('\Distilleries\Expendable\Http\Controllers\Admin\PostController@getIndex'), 302, [], $GLOBALS['CIOINA_Config']->get('ForceSSL'));
          }
      }
<?php

namespace Acioina\UserManagement\Http\Controllers\Api;

use Distilleries\Expendable\Models\Post;
use Acioina\UserManagement\Http\Requests\Api\FilterArticles;
use Acioina\UserManagement\Transformers\ArticleTransformer;
use Acioina\UserManagement\Filters\ArticleFilter;
use Acioina\UserManagement\Paginate\Paginate;

class ArticleController extends ApiController
{
    /**
     * ArticleController constructor.
     *
     * @param ArticleTransformer $transformer
     */
    public function __construct(ArticleTransformer $transformer)
    {
        parent::__construct($transformer);
    }

    public function index(FilterArticles $request, ArticleFilter $filter)
    {
         $data = $request->only(
         'filter.tags',
         );

        $tags = isset($data['filter']['tags']) ? $data['filter']['tags'] :  null;

        if((is_array($tags) && (count($tags) === 0 || count($tags) > 100)))
        {
            return $this->respondFailedLogin('tags', 'array lenght must be greater than zero and less than 101');
        }

        $articles = new Paginate(Post::loadRelations()->filter($filter));

        return $this->respondWithPagination($articles);
    }

    protected function respondFailedLogin($key ='session', $message = 'unknown')
    {
        return $this->respond([
            'errors' => [
                $key => $message,
            ]
        ], 422);
    }

}

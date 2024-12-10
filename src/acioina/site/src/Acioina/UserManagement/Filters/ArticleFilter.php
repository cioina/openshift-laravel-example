<?php

namespace Acioina\UserManagement\Filters;

//use Illuminate\Http\Request;
use Distilleries\Expendable\Filters\Filter;

class ArticleFilter extends Filter
{

    //public function __construct(Request $request)
    //{
    //    parent::__construct($request, 'filter');
    //}

    /**
     * Filter by tag name.
     * Get all the articles tagged by the given tag ids.
     *
     * @param $name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function tags($idArray = null)
    {
        if(isset($idArray))
        {
            return $this->builder->whereHas('post_topics', function($q) use(&$idArray)
                 {
                     $q->whereIn('topic_id',$idArray);
                 });
        }

        return $this->builder;
    }
}

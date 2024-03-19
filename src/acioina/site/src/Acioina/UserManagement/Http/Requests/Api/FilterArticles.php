<?php

namespace Acioina\UserManagement\Http\Requests\Api;

class FilterArticles extends ApiRequest
{
    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        return $this->get('filter') ?: [];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'limit' => 'required|integer|min:10|max:50',
            'offset' => 'required|integer|min:0',
            'tags' => 'array',
            'tags.*' => 'integer|min:1',
        ];
    }

    public function messages()
    {
        return [
          'limit.required' => 'is required',
          'limit.integer' => 'must be integer',
          'limit.min' => 'must be graiter that 9',
          'limit.max' => 'must be lesss than 51',

          'offset.required' => 'is required',
          'offset.integer' => 'must be integer',
          'offset.min' => 'must be graiter that -1',

          'tags.array' => 'must be array',
          'tags.*.integer' => 'all elements must be integer',
          'tags.*.min' => 'all elements must be graiter then zero',
       ];
    }

}

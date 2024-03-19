<?php

namespace Acioina\UserManagement\Paginate;

use Illuminate\Database\Eloquent\Builder;

class Paginate
{
    /**
     * Total count of the items.
     *
     * @var int
     */
    protected $total;

    /**
     * Collection of items.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $data;

    /**
     * Paginate constructor.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int $limit
     * @param int $offset
     */
    public function __construct(Builder $builder, string $prefix = 'filter')
    {
        $data = request()->only([$prefix . '.limit', $prefix . '.offset',]);

        $this->total = $builder->count();

        $this->data = $builder->latest()->skip($data[$prefix]['offset'])->take($data[$prefix]['limit'])->get();
       //dd(vsprintf(str_replace(['?'], ['\'%s\''], $builder->toSql()), $builder->getBindings()));
    }

    /**
     * Get the total count of the items.
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Get the paginated collection of items.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getData()
    {
        return $this->data;
    }
}

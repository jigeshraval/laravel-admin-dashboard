<?php 

namespace App\Http\Controllers;

class AdminController extends Controller
{
    protected $paginate = 50;
        
    protected $filter = false;

    protected $filter_search = [];

    protected $skip_filtering = [
        'page'
    ];

    protected function initProcessFilter()
    {
        collect(
            array_keys(request()->input())
        )->each(function ($value, $filter) {

            if (!$value) {
                return;
            }

            $filter = str_replace('-', '.', $filter);

            if (!in_array($filter, $this->skip_filtering)) {

                $this->filter = true;
                $this->filter_search[] = [$filter, 'LIKE', '%' . $value . '%'];

            }
        });

    }
}
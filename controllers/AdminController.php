<?php 

namespace App\Http\Controllers;

class AdminController extends Controller
{
    protected $paginate = 50;
        
    protected $filter = false;

    protected $filter_search = [];

    protected function initProcessFilter()
    {
        $filters = request()->input();
        $skip_filters = ['page', 'generatePDF', 'report'];
        $filter_keys = array_keys($filters);

        if (count($filters)) {
            foreach ($filters as $filter => $value) {

                if (!$value) {
                    continue;
                }

                $filter = str_replace('-', '.', $filter);

                if (!in_array($filter, $skip_filters)) {

                    $this->filter = true;
                    $this->filter_search[] = [$filter, 'LIKE', '%' . $value . '%'];

                }
            }
        }
    }
}
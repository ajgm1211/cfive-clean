<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class AbstractFilter
{
	protected $filter_by = []; // Use it to search query
	protected $request; // Illuminate\Http\Request
	protected $filter_by_relations = []; // Use it to search query by relational fields
	protected $default_filter_by = [];  // Use it to filter initial default datatable
	protected $query; // Query to filter data
	protected $pagination = 20; // Use it to paginate datatable (Required)
    protected $parent_id = null;
    protected $with = [];

    public function __construct(Request $request, $query, $parent_id = null, $parent_model = null)
    {
    	$this->request = $request;
    	$this->query = $query;
        $this->parent_id = $parent_id;
        $this->parent_model = $parent_model;
    }

	public function filter() 
    {
        /*if($this->parent_id && $this->parent_model)
            $this->filterByParent();

        else if($this->request->query('filteredby', null) && $this->request->query('filteredval', null))
        	$this->defaulFilter();*/
            
        if( $this->request->query('q', null) && $this->request->query('q') != ''){
            $this->search();
            $this->searchByRelation();   
        }
        
        $this->sort();

        return $this->getResult(); 
    }

    protected function filterByParent() {
        $this->query->whereHas(
            $this->parent_model, function($q) {
                $q->where($this->default_filter_by[$this->parent_model], $this->parent_id);
            });
    }

    protected function defaulFilter() {

	    $values = $this->request->query('filteredval');
        
        $this->query->whereHas(
            $this->request->query('filteredby'), function($q) use ($values) {
                $q->whereIn($this->default_filter_by[$this->request->query('filteredby')], $values);
            });

    }

    protected function search() {

    	$qry = $this->request->query('q');

		$this->query->where(function($q) use ($qry) {

            $filter_by = $this->filter_by;

            foreach($filter_by as $column){

                    $q->orWhere($column, 'LIKE', '%'.$qry.'%');

            }

        });

    }

    protected function searchByRelation() {

    	$filter_by_relations = $this->filter_by_relations;

        $qry = $this->request->query('q');


        if($filter_by_relations){

            $this->query->where(function ($query) use ($filter_by_relations, $qry){ 

                foreach($filter_by_relations as $column){

                    $col = explode('__', $column);

                    $query->orWhereHas($col[0], function($q) use ($qry, $col){

                        $q->where($col[1], "LIKE", '%'.$qry.'%');

                    });
                }


            });
        }

    }

    protected function sort() {

        if($this->request->query('sort', null)){
            $sort = explode(':', $this->request->query('sort'));
            $this->query->orderBy($sort[0], $sort[1]); 
        } else {
            $this->query->orderBy('id', 'desc');
        }

    }

    protected function getResult() {

        if($this->with){
            $this->query->with($this->with);
        }

    	if($this->request->query('no_pagination', null)){
            return $this->query->get();
        } else {
        	return $this->query->paginate($this->paginate);
        }

    }

}

<?php

namespace App\Repositories;

use App\Company;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CompanyRepository implements CompanyRepositoryInterface
{
    protected $model;

    /**
     * CompanyRepository constructor.
     *
     * @param Company $company
     */
    public function __construct(Company $company)
    {
        $this->model = $company;
    }

    public function all()
    {
        $this->model->all();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        return $this->model->where('id', $id)
            ->update($data);
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function find($id)
    {
        if (null == $company = $this->model->find($id)) {
            throw new ModelNotFoundException('Company not found');
        }

        return $company;
    }
}

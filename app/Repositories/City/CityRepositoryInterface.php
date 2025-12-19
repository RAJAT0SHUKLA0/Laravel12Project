<?php

namespace App\Repositories\City;

interface CityRepositoryInterface
{
    public function getAll();

    public function find($id);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);
    
    public function getState();
}

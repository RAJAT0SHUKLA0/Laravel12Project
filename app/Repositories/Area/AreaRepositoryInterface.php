<?php

namespace App\Repositories\Area;

interface AreaRepositoryInterface
{
    public function getAll();

    public function create(array $data);
    
    public function find($id);
    
    public function getState();
    public function getCity($state_id);

    public function delete($id);
   
}

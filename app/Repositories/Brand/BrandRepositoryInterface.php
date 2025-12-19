<?php

namespace App\Repositories\Brand;

interface BrandRepositoryInterface
{
    public function getAll();

    public function find($id);

    public function create(array $data);

    public function update($id, array $data);
    
    public function statusupdate($id,$status);

    public function delete($id);
    
   
    
   



}

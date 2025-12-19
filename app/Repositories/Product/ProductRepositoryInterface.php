<?php

namespace App\Repositories\Product;

interface ProductRepositoryInterface
{
    public function query();

    public function find($id);

    public function create(array $data);

    public function update($id, array $data);
    
    public function statusupdate($id,$status);

    public function delete($id);
    
    public function getCategory();
    
    public function getVarient();
    
    public function getSubCategory();
    
    public function checkSubcategory($id);
    
    public function getAllbrand();
    public function getDetails($id);


}

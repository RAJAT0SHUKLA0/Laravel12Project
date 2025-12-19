<?php

namespace App\Repositories\Seller;

interface SellerRepositoryInterface
{
    public function getAll();

    public function find($id);

    public function create(array $data);

     public function update($id, array $data);
    
    public function statusupdate($id,$status);

    public function delete($id);
    
    public function getState();
    
    public function getCity($state_id);

    public function getAllCity();
    
    public function getArea($city_id);
    
    public function getAllArea();
    
    public function getAllSellerData($id);


    public function getTransactionReport($sellerId);

    



}

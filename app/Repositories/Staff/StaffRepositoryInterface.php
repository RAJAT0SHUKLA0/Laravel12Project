<?php

namespace App\Repositories\Staff;

interface StaffRepositoryInterface
{
    public function query();

    public function find($id);

    public function create(array $data);

    public function update($id, array $data);
    
    public function statusupdate($id,$status);

    public function delete($id);
    
    public function getState();
    
    public function getCity($state_id);

    public function getAllCity();


    public function getRole();
    
    public function changePassword($id,$password);

    public function isLocationEnable($id,$status);

}

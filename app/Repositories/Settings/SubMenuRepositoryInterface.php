<?php

namespace App\Repositories\Settings;

interface SubMenuRepositoryInterface
{
    public function getAll();

    public function find($id);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);
    
    public function getAllMenu();

    public function getAllType();

    public function getAllparent();

}

<?php

namespace App\Repositories\SellerType;

interface SellerTypeRepositoryInterface
{
    public function getAll();

    public function find($id);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);
}

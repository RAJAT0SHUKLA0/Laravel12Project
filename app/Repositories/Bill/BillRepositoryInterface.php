<?php

namespace App\Repositories\Bill;

interface BillRepositoryInterface
{
    public function getAll();

    public function find($id);

    public function create(array $data);

    public function update($id, array $data);

    public function status($id,$status);
    public function getSeller();

}

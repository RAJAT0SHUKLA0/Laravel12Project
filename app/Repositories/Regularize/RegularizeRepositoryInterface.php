<?php

namespace App\Repositories\Regularize;

use App\Models\User;
use App\Utils\Crypto;

interface RegularizeRepositoryInterface
{
    public function getAll();
    
    public function Regularizestatusupdate($id,$status);

   
}

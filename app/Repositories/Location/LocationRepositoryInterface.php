<?php

namespace App\Repositories\Location;

interface LocationRepositoryInterface
{
    public function getAll();
    
    public function statusupdate($id,$status);
    
     public function getUserAll();
   
}

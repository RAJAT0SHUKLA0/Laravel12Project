<?php

namespace App\Repositories\Leave;

interface LeaveRepositoryInterface
{
    public function getAll();
    
    public function leavestatusupdate($id,$status);

   
}

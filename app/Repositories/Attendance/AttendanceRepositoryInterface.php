<?php

namespace App\Repositories\Attendance;

interface AttendanceRepositoryInterface
{
    public function getAll();
    
    public function statusupdate($id,$status);
    
     public function getUserAll();
   
}

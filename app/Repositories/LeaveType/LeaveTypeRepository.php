<?php

namespace App\Repositories\LeaveType;

use App\Repositories\LeaveType\LeaveTypeRepositoryInterface;
use App\Models\LeaveType;
use App\Utils\Crypto;
class LeaveTypeRepository implements LeaveTypeRepositoryInterface
{
        public function getAll(){
            return LeaveType::where('is_delete',0)->get();
            }
        
          public function find($id)
            {
                $leavetype = Crypto::decryptId($id);
               return LeaveType::where('id',$leavetype)->first();  
            }
        
         public function create(array $data)
            {
               $leavetypeDataSet = LeaveType::create($data);
               if($leavetypeDataSet){
                 return true;
               }else{
                 return false;
               }
            }
             public function update($id, array $data)
                {
                   $leavetypeDataSetupdate = LeaveType::where('id',$id)->update($data); 
                   if($leavetypeDataSetupdate){
                     return true;
                   }else{
                     return false;
                   }
                    
                }
                
                 public function delete($id)
                    {
                      $leavetypeDataSet = LeaveType::where('id',$id)->update(['is_delete'=>1]); 
                       if($leavetypeDataSet){
                         return true;
                       }else{
                         return false;
                       }  
                    }

}
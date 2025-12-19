<?php
namespace App\Helper;

class Message {
    const SERVER_ERROR_MESSAGE  = 'Something went wrong! Please try again.';
    const  ROLE_VALIDATION ='Access denied: The mobile number %s is not associated with an authorized role.';
    const  UNAUTHORIZED_VALIDATION ='The provided credentials do not match our records.';
    const  AUTH_MESSAGE = 'Login Sucessfully As %s'; 
    const  LOGOUT_MESSAGE ='You have been logged out.';
    
    ################################################## API MESSAGE #######################################################
    
    ################################################## Attendance MESSAGE #######################################################
    const ATTENDANCE_START_TIME_MESSAGE = 'Day Start Successfully';
    const ATTENDANCE_END_TIME_MESSAGE   = 'Day End Successfully';
    
    ################################################## Leave MESSAGE #######################################################
    const LEAVE_SUCCESS ='leave add successfully';
    const LEAVE_UNSUCCESS ='leave not register';
    const LEAVE_SUCCESS_LIST ='leave List Found';
    const LEAVE_UNSUCCESS_LIST ='leave List Not Found';
    const LEAVE_STATUS_SUCCESS ="Leave Status Update  Successfully";
    const LEAVE_STATUS_UNSUCCESS = "Something Wrong For Update Leave Status ";
    const LEAVE_DELETE_SUCCESS ="%s Delete Successfully";
    const LEAVE_DELETE_UNSUCCESS ="Something Wrong For delete %s";


    ################################################## Regularize MESSAGE #######################################################
    const REGULARIZE_SUCCESS ='Regularize add successfully';
    const REGULARIZE_UNSUCCESS ='Regularize not register';
    const REGULARIZE_SUCCESS_LIST ='Regularize List Found';
    const REGULARIZE_UNSUCCESS_LIST ='Regularize List Not Found';
    const REGULARIZE_STATUS_SUCCESS ="Regularize Status Update  Successfully";
    const REGULARIZE_STATUS_UNSUCCESS = "Something Wrong For Update Leave Status ";
    
    ################################################## Login MESSAGE #######################################################
    const LOGIN_SUCCESS_MESSAGE ='Hi %s! You have been logged in as %s.';
    
    ################################################## Validation MESSAGE #######################################################
    const VALIDATION_MESSAGE   = 'Invalid parameters';
    
    ################################################## Location MESSAGE #######################################################
    const LOCATION_SUCCESS ="Location Add Successfully";
    const LOCATION_UNSUCCESS = "Something Wrong For Save The Location ";
    
    const LOCATION_CONFIG_SUCCESS ="Location configuration found";
    const LOCATION_CONFIG_UNSUCCESS = "Something Wrong From location configuration";
    
    ################################################## Staff MESSAGE #######################################################
    const STAFF_SUCCESS ="Staff %s Successfully";
    const STAFF_UNSUCCESS = "Something Wrong For %s The Staff ";
    const STAFF_LIST_SUCCESS ="Staff Data Found";
    const STAFF_LIST_UNSUCCESS = "Staff Data Not Found ";
    const STAFF_STATUS_SUCCESS ="Staff Status Update  Successfully";
    const STAFF_STATUS_UNSUCCESS = "Something Wrong For Update Status ";
    
     ################################################## teams MESSAGE #######################################################
    const TEAMS_SUCCESS ="team wise list found";
    const TEAMS_UNSUCCESS = "team wise list not found ";
    const TEAMS_WISE_ATTENDANCE_LIST_SUCCESS ="team wise %s list  found ";
    const TEAMS_WISE_ATTENDANCE_LIST_UNSUCCESS = "team wise %s list not found ";
    
      ################################################## Master MESSAGE #######################################################
    const MASTER_SUCCESS ="%s list found";
    const MASTER_UNSUCCESS = "%s list not found ";
        ################################################## Product MESSAGE #######################################################

    const PRODUCT_LIST =' %s list found';
    const PTODUCT_LIST_NOT_FOUND =' %s list not found';
    const PRODUCT_SUCCESS =' product %s successfully';
    const PTODUCT_UNSUCCESS =' Something Wrong For %s The product ';
    const  PTODUCT_STATUS_SUCCESS ="product Status Update  Successfully";
    const  PTODUCT_STATUS_UNSUCCESS = "Something Wrong For Update Status ";
    const  PRODUCT_PRICE_UPDATE_SUCCESS = "Product Price Update Successfully ";
    const  PRODUCT_PRICE_UPDATE_UNSUCCESS = "Something Wrong For Update Product Price ";


    ################################################## Cart MESSAGE #######################################################
 
    const CART_SUCCESS =' Cart %s successfully';
    const CART_UNSUCCESS =' Something Wrong For %s The Cart ';
    
    
     ################################################## Expense MESSAGE #######################################################
    const EXPENSE_SUCCESS ='Expense add successfully';
    const EXPENSE_UNSUCCESS ='Expense not register';
    const EXPENSE_SUCCESS_LIST ='Expense list found';
    const EXPENSE_UNSUCCESS_LIST ='Expense list not found';
    const EXPENSE_STATUS_SUCCESS ="Expense status update  successfully";
    const EXPENSE_STATUS_UNSUCCESS = "Something went wrong for update expense status ";
    const EXPENSE_DELETE_SUCCESS ="%s Delete successfully";
    const EXPENSE_DELETE_UNSUCCESS ="Something went wrong for delete %s";

 ################################################## Brand MESSAGE #######################################################
    const BRAND_SUCCESS_LIST ='Brand list found';
    const BRAND_UNSUCCESS_LIST ='Brand list not found';




}
?>
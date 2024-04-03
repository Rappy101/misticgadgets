<?php

require '../config/function.php';


$paraResultId = checkParamId('id');

if(is_numeric($paraResultId)) {


    $customersId = validate($paraResultId);

    $customer = getById('customers', $customersId);
    if ($customer['status'] == 200) {

        $customerDeleteRes = delete('customers', $customersId);
        if($customerDeleteRes){

            redirect('customers.php', 'Customer Deleted Succesfully');

        }else{
            redirect('customers.php', 'Something Went Wrong');
        }
    } else {
        redirect('customers.php', $customer['message']);
    }
} else {
    redirect('customers.php', 'Something Went Wrong');
}

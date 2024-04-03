<?php

require '../config/function.php';


$paraResultId = checkParamId('id');

if(is_numeric($paraResultId)) {


    $suppliersId = validate($paraResultId);

    $customer = getById('suppliers', $suppliersId);
    if ($customer['status'] == 200) {

        $customerDeleteRes = delete('suppliers', $suppliersId);
        if($customerDeleteRes){

            redirect('suppliers.php', 'Customer Deleted Succesfully');

        }else{
            redirect('suppliers.php', 'Something Went Wrong');
        }
    } else {
        redirect('suppliers.php', $customer['message']);
    }
} else {
    redirect('suppliers.php', 'Something Went Wrong');
}

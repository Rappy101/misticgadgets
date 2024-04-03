<?php

require '../config/function.php';


$paraResultId = checkParamId('id');

if(is_numeric($paraResultId)) {


    $categoryId = validate($paraResultId);

    $category = getById('categories', $categoryId);
    if ($category['status'] == 200) {

        $response = delete('categories', $categoryId);
        if($response){

            redirect('categories.php', 'Category Deleted Succesfully');

        }else{
            redirect('categories.php', 'Something Went Wrong');
        }
    } else {
        redirect('categories.php', $categoryId['message']);
    }
} else {
    redirect('categories.php', 'Something Went Wrong');
}

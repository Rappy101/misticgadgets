<?php 

require '../config/function.php';


$paramResult = checkParamId('index');
if(is_numeric($paramResult)){

    $indexValue = validate($paramResult);

    if(isset($_SESSION['productItems']) && isset($_SESSION['productItemsIds'])){

        unset($_SESSION['productItems'][$indexValue]);
        unset($_SESSION['productItemsIds'][$indexValue]);

        redirect('order-create.php', 'Item Removed');

    }else{ 
        redirect('order-create.php', 'There is no Item');
    }

}else{
    redirect('order-create.php', 'param not numeric');
}


?>
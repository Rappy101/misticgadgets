

<?php

include('../config/function.php');


if (isset($_POST['saveAdmin'])) {

    $name = validate($_POST['name']);
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);
    $phone = validate($_POST['phone']);
    $is_ban = isset($_POST['is_ban']) == true ? 1 : 0;

    if ($name != '' && $email != '' && $password != '') {


        $emailCheck = mysqli_query($conn, "SELECT * FROM admins WHERE email='$email'");
        if ($emailCheck) {
            if (mysqli_num_rows($emailCheck) > 0) {
                redirect('admins-create.php', 'Email Already used by another user.');
            }
        }
        $bycrypt_password = password_hash($password, PASSWORD_BCRYPT);

        $data = [

            'name' => $name,
            'email' => $email,
            'password' => $bycrypt_password,
            'phone' => $phone,
            'is_ban' => $is_ban
        ];
        $result = insert('admins', $data);

        if ($result) {
            redirect('admins.php', 'Admin Created Succesfully!');
        } else {
            redirect('admins-create.php', 'Something Went Wrong!');
        }
    } else {
        redirect('admins-create.php', 'Please fill required fields');
    }
}


if (isset($_POST['updateAdmin'])) {
    $adminId = validate($_POST['adminId']);

    $adminData =getById ('admins', $adminId);
    if($adminData['status'] != 200){
        redirect('admins-edit.php?id='.$adminId, 'Please fill required fields');
    }


    $name = validate($_POST['name']);
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);
    $phone = validate($_POST['phone']);
    $is_ban = isset($_POST['is_ban']) == true ? 1 : 0;


    $EmailCheckQuery ="SELECT   * FROM admins WHERE email='$email' AND id!='$adminId'";
    $checkResult = mysqli_query($conn, $EmailCheckQuery);
    if($checkResult){
        if(mysqli_num_rows($checkResult) > 0){
            redirect('admins-edit.php?id=' .$adminId,' Email Already used by another user');
        }
    }



    if($password != ''){
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    }else{
        $hashedPassword = $adminData['data']['password'];
    }

    if ($name != '' && $email != '' ) {

        $data = [

            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'phone' => $phone,
            'is_ban' => $is_ban
        ];
        $result = update('admins', $adminId, $data);

        if ($result) {
            redirect('admins.php?id='.$adminId, 'Admin Updated Succesfully!');
        } else {
            redirect('admins-edit.php?id='.$adminId, 'Something Went Wrong!');
        }

      
    } else {
        redirect('admins-create.php', 'Please fill required fields');
    }
}





if(isset($_POST['saveProduct'])){
    $category_id = validate($_POST['category_id']);
    $name = validate($_POST['name']);
    $description = validate($_POST['description']);
    $price = validate($_POST['price']);
    $quantity = validate($_POST['quantity']);
    $suppliers_id = validate($_POST['suppliers_id']);
    $status = isset($_POST['status']) == true ? 1 : 0;

    if($_FILES['image']['size'] > 0){
        $path   ="../assets/uploads/products";
        $image_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

        $filename = time().'.'.$image_ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $path."/".$filename);
        $finalImage = "/assets/uploads/products/".$filename;
    } else {
        $finalImage = '';
    }

    $data = [
        'category_id' => $category_id,
        'suppliers_id' => $suppliers_id,
        'name' => $name,
        'description' => $description,
        'price' => $price,
        'quantity' => $quantity,
        'image' => $finalImage,
        'status' => $status
    ];

    $result = insert('products', $data);

    if ($result) {
        redirect('products.php', 'Products Created Successfully!');
    } else {
        redirect('products-create.php', 'Something Went Wrong!');
    }
}


if(isset($_POST['updateProduct'])){
    $product_id = validate($_POST['product_id']);
    $productData = getById('products', $product_id);

    if(!$productData){
        redirect('product.php', 'No such product found');
    }

    $category_id = validate($_POST['category_id']);
    $name = validate($_POST['name']);
    $description = validate($_POST['description']);
    $suppliers_id = validate($_POST['suppliers_id']); 
    $status = isset($_POST['status']) == true ? 1 : 0;

    if($_FILES['image']['size'] > 0){
        $path   ="../assets/uploads/products";
        $image_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

        $filename = time().'.'.$image_ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $path."/".$filename);
        $finalImage = "/assets/uploads/products/".$filename;

        $deleteImage = "../".$productData['data']['image'];
        if(file_exists($deleteImage)){
            unlink($deleteImage);
        }
    } else {
        $finalImage = $productData['data']['image'];
    }

    $data = [
        'category_id' => $category_id,
        'suppliers_id' => $suppliers_id,
        'name' => $name,
        'description' => $description,
        'image' => $finalImage,
        'status' => $status
    ];

    $result = update('products', $product_id, $data);

    if ($result) {
        redirect('products.php?id='.$product_id, 'Products Updated Successfully!');
    } else {
        redirect('products-create.php?id='.$product_id, 'Something Went Wrong!');
    }
}


//////////////////////////////////////////////////////////////////////////////
// For adding quantity
if(isset($_POST['addQuantity'])){
    // Existing product details retrieval
    $product_id = validate($_POST['product_id']);
    $productData = getById('products', $product_id);

    if(!$productData){
        redirect('product.php', 'No such product found');
    }

    // Additional quantity to add
    $quantity_to_add = validate($_POST['quantity_to_add']);

    // Ensure the quantity to add is positive
    if($quantity_to_add < 1){
        redirect('edit-product.php?id='.$product_id, 'Quantity to add should be a positive number.');
        exit(); // Stop execution
    }

    // Calculate the new quantity
    $new_quantity = $productData['data']['quantity'] + $quantity_to_add;

    // Update the quantity in the database
    $data = [
        'quantity' => $new_quantity,
    ];

    // Update the product
    $result = update('products', $product_id, $data);

    // Get the logged-in user's name from the session
    $added_by = isset($_SESSION['loggedInUser']['name']) ? $_SESSION['loggedInUser']['name'] : 'Unknown User';

    // Insert into product history with the user's name
    $history_data = [
        'product_id' => $product_id,
        'quantity_added' => $quantity_to_add,
        'added_by' => $added_by,
    ];
    insert('product_history', $history_data);

    if ($result) {
        redirect('products.php?id='.$product_id, 'Quantity added successfully!');
    } else {
        redirect('edit-product.php?id='.$product_id, 'Something went wrong while adding quantity.');
    }
}




if(isset($_POST['saveCustomer'])){
    $name = validate($_POST['name']);
    $email = validate($_POST['email']);
    $phone = validate($_POST['phone']);
    $status = isset($_POST['status']) == true ?1:0;

    if($name !=''){


        $emailCheck = mysqli_query($conn, "SELECT * FROM customers WHERE email='$email'");
        if($emailCheck){
            if(mysqli_num_rows($emailCheck) > 0){
                redirect('customers.php', 'Email already exist');

            }
        }

        $data = [
            'name' =>$name,
            'email' =>$email,
            'phone' =>$phone,
            'status' =>$status

        ];
        $result =insert('customers', $data);
        if($result){
            redirect('customers.php', 'Customer Created Successfully');

        }else{
            redirect('customers.php', 'Something Went Wrong');
        }

    }else{
        redirect('customers.php', 'Please fill the Required fill');
    }

    
    }


    if(isset($_POST['updateCustomer'])){
    
    $customerId = validate($_POST['customerId']);
    $name = validate($_POST['name']);
    $email = validate($_POST['email']);
    $phone = validate($_POST['phone']);
    $status = isset($_POST['status']) == true ?1:0;

    if($name !=''){


        $emailCheck = mysqli_query($conn, "SELECT * FROM customers WHERE email='$email' AND id !='$customerId'");
        if($emailCheck){
            if(mysqli_num_rows($emailCheck) > 0){
                redirect('customers-edit.php?id='.$customerId, 'Email already exist');

            }
        }

        $data = [
            'name' =>$name,
            'email' =>$email,
            'phone' =>$phone,
            'status' =>$status

        ];
        $result =update('customers',$customerId, $data);
        if($result){
            redirect('customers.php?id='.$customerId, 'Customer Updated Successfully');

        }else{
            redirect('customers-edit.php?id='.$customerId, 'Something Went Wrong');
        }

    }else{
        redirect('customers-edit.php?id='.$customerId, 'Please fill the Required fill');
    }

}


//Suppliers
//supplier

if(isset($_POST['saveSuppliers'])){
    $name = validate($_POST['name']);
    $email = validate($_POST['email']);
    $phone = validate($_POST['phone']);
    $description = validate($_POST['description']);
    $status = isset($_POST['status']) == true ?1:0;

    if($name !=''){


        $emailCheck = mysqli_query($conn, "SELECT * FROM suppliers WHERE email='$email'");
        if($emailCheck){
            if(mysqli_num_rows($emailCheck) > 0){
                redirect('suppliers.php', 'Email already exist');

            }
        }

        $data = [
            'name' =>$name,
            'email' =>$email,
            'phone' =>$phone,
            'description' =>$description,
            'status' =>$status

        ];
        $result =insert('suppliers', $data);
        if($result){
            redirect('suppliers.php', 'Supplier Created Successfully');

        }else{
            redirect('suppliers.php', 'Something Went Wrong');
        }

    }else{
        redirect('suppliers.php', 'Please fill the Required fill');
    }

    
    }


    if(isset($_POST['updateSuppliers'])){
    
    $suppliersId = validate($_POST['suppliersId']);
    $name = validate($_POST['name']);
    $email = validate($_POST['email']);
    $phone = validate($_POST['phone']);
    $description = validate($_POST['description']);
    $status = isset($_POST['status']) == true ?1:0;

    if($name !=''){


        $emailCheck = mysqli_query($conn, "SELECT * FROM suppliers WHERE email='$email' AND id !='$suppliersId'");
        if($emailCheck){
            if(mysqli_num_rows($emailCheck) > 0){
                redirect('suppliers-edit.php?id='.$suppliersId, 'Email already exist');

            }
        }

        $data = [
            'name' =>$name,
            'email' =>$email,
            'phone' =>$phone,
            'description' =>$description,
            'status' =>$status

        ];
        $result =update('suppliers',$suppliersId, $data);
        if($result){
            redirect('suppliers.php?id='.$suppliersId, 'Supplier Updated Successfully');

        }else{
            redirect('suppliers-edit.php?id='.$suppliersId, 'Something Went Wrong');
        }

    }else{
        redirect('suppliers-edit.php?id='.$suppliersId, 'Please fill the Required fill');
    }

}

if(isset($_POST['saveCategory'])){
    $name = validate($_POST['name']);
    $description = validate($_POST['description']);
    $status = isset($_POST['status']) == true ?1:0;

    $data = [

        'name' => $name,
        'description' => $description,
        'status' => $status
    ];
    $result = insert('categories', $data);

    if ($result) {
        redirect('categories.php', ' Category Created Succesfully!');
    } else {
        redirect('categories-create.php', 'Something Went Wrong!');
    }


}


if(isset($_POST['updateCategory'])){
    $categoryId = validate($_POST['categoryId']);
    $name = validate($_POST['name']);
    $description = validate($_POST['description']);
    $status = isset($_POST['status']) == true ?1:0;

    $data = [

        'name' => $name,
        'description' => $description,
        'status' => $status
    ];
    $result = update('categories',$categoryId,$data);

    if ($result) {
        redirect('categories-edit.php?id='.$categoryId, ' Category Updated Succesfully!');
    } else {
        redirect('categories-edit.php?id='.$categoryId, 'Something Went Wrong!');
    }

}
    
//preorder

if(isset($_POST['savePreCustomer'])){
    $name = validate($_POST['name']);
    $email = validate($_POST['email']);
    $phone = validate($_POST['phone']);
    $status = isset($_POST['status']) == true ?1:0;

    if($name !=''){


        $emailCheck = mysqli_query($conn, "SELECT * FROM customers WHERE email='$email'");
        if($emailCheck){
            if(mysqli_num_rows($emailCheck) > 0){
                redirect('preorders-create.php', 'Email already exist');

            }
        }

        $data = [
            'name' =>$name,
            'email' =>$email,
            'phone' =>$phone,
            'status' =>$status

        ];
        $result =insert('customers', $data);
        if($result){
            redirect('preorders-create.php', 'Customer Created Successfully');

        }else{
            redirect('preorders-create.php', 'Something Went Wrong');
        }

    }else{
        redirect('preorders-create.php', 'Please fill the Required fill');
    }

    
    }


    if(isset($_POST['updatePreCustomer'])){
    
    $customerId = validate($_POST['customerId']);
    $name = validate($_POST['name']);
    $email = validate($_POST['email']);
    $phone = validate($_POST['phone']);
    $status = isset($_POST['status']) == true ?1:0;

    if($name !=''){


        $emailCheck = mysqli_query($conn, "SELECT * FROM customers WHERE email='$email' AND id !='$customerId'");
        if($emailCheck){
            if(mysqli_num_rows($emailCheck) > 0){
                redirect('customers-edit.php?id='.$customerId, 'Email already exist');

            }
        }

        $data = [
            'name' =>$name,
            'email' =>$email,
            'phone' =>$phone,
            'status' =>$status

        ];
        $result =update('customers',$customerId, $data);
        if($result){
            redirect('customers.php?id='.$customerId, 'Customer Updated Successfully');

        }else{
            redirect('customers-edit.php?id='.$customerId, 'Something Went Wrong');
        }

    }else{
        redirect('customers-edit.php?id='.$customerId, 'Please fill the Required fill');
    }

}
//pre product

if(isset($_POST['savePreProduct'])){
    $category_id = validate($_POST['category_id']);
    $name = validate($_POST['name']);
    $description = validate($_POST['description']);
    $price = validate($_POST['price']);
    $quantity = validate($_POST['quantity']);
    $status = isset($_POST['status']) ? 1 : 0; // Checking if status is set
    $suppliers_id = validate($_POST['suppliers_id']);
    $customers_id = validate($_POST['customers_id']);

    // File upload handling
    if($_FILES['image']['size'] > 0){
        // Upload file and set image path
        $path   ="../assets/uploads/products";
        $image_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = time().'.'.$image_ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $path."/".$filename);
        $finalImage = "/assets/uploads/products/".$filename;
    } else {
        $finalImage = ''; // If no file uploaded, set empty image path
    }

    $data = [
        'category_id' => $category_id,
        'name' => $name,
        'description' => $description,
        'price' => $price,
        'quantity' => $quantity,
        'image' => $finalImage,
        'status' => $status,
        'suppliers_id' => $suppliers_id,
        'customers_id' => $customers_id
    ];

    $result = insert('preorder_products', $data);

    if ($result) {
        redirect('preorders.php', 'PreOrders Created Successfully!');
    } else {
        redirect('preorders-create.php', 'Something Went Wrong!');
    }
}

if(isset($_POST['updatePreProduct'])){
    $product_id = validate($_POST['product_id']);
    $productData = getById('preorder_products', $product_id);

    if(!$productData){
        redirect('product.php', 'No such product found');
    }

    $category_id = validate($_POST['category_id']);
    $name = validate($_POST['name']);
    $description = validate($_POST['description']);
    $price = validate($_POST['price']);
    $quantity = validate($_POST['quantity']);
    $suppliers_id = validate($_POST['suppliers_id']);
    $customers_id = validate($_POST['customers_id']); // Assuming you have an input field for suppliers_id
    $status = isset($_POST['status']) == true ? 1 : 0;

    if($_FILES['image']['size'] > 0){
        $path   ="../assets/uploads/products";
        $image_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

        $filename = time().'.'.$image_ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $path."/".$filename);
        $finalImage = "/assets/uploads/products/".$filename;

        $deleteImage = "../".$productData['data']['image'];
        if(file_exists($deleteImage)){
            unlink($deleteImage);
        }
    } else {
        $finalImage = $productData['data']['image'];
    }

    $data = [
        'category_id' => $category_id,
        'suppliers_id' => $suppliers_id,
        'customers_id' => $customers_id,
        'name' => $name,
        'description' => $description,
        'price' => $price,
        'quantity' => $quantity,
        'image' => $finalImage,
        'status' => $status
    ];

    $result = update('preorder_products', $product_id, $data);

    if ($result) {
        redirect('preorders.php?id='.$product_id, 'PreOrders Updated Successfully!');
    } else {
        redirect('preorders-create.php?id='.$product_id, 'Something Went Wrong!');
    }
}

//save pre order product

if(isset($_POST['savePreOrder'])){
    $category_id = validate($_POST['category_id']);
    $name = validate($_POST['name']);
    $description = validate($_POST['description']);
    $price = validate($_POST['price']);
    $quantity = validate($_POST['quantity']);
    $status = isset($_POST['status']) ? 1 : 0; // Checking if status is set
    $suppliers_id = validate($_POST['suppliers_id']);
    $customers_id = validate($_POST['customers_id']);

    // File upload handling
    if($_FILES['image']['size'] > 0){
        // Upload file and set image path
        $path   ="../assets/uploads/products";
        $image_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = time().'.'.$image_ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $path."/".$filename);
        $finalImage = "/assets/uploads/products/".$filename;
    } else {
        $finalImage = ''; // If no file uploaded, set empty image path
    }

    $data = [
        'category_id' => $category_id,
        'name' => $name,
        'description' => $description,
        'price' => $price,
        'quantity' => $quantity,
        'image' => $finalImage,
        'status' => $status,
        'suppliers_id' => $suppliers_id,
        'customers_id' => $customers_id
    ];

    $result = insert('preorder_products', $data);

    if ($result) {
        redirect('preorders.php', 'PreOrders Created Successfully!');
    } else {
        redirect('preorders-create.php', 'Something Went Wrong!');
    }
}

    ?>

<?php

include('../config/function.php');


if (!isset($_SESSION['productItems'])) {

    $_SESSION['productItems'] = [];
}
if (!isset($_SESSION['productItemsIds'])) {

    $_SESSION['productItemsIds'] = [];
}

if (isset($_POST['addItem'])) {
    $productId = validate($_POST['product_id']);
    $quantity = validate($_POST['quantity']);

    if ($quantity <= 0) {
        redirect('order-create.php', 'Quantity must be a positive number');
    }

    $checkProduct = mysqli_query($conn, "SELECT * FROM products WHERE id='$productId' LIMIT 1 ");

    if ($checkProduct) {
        if (mysqli_num_rows($checkProduct) > 0) {
            $row = mysqli_fetch_assoc($checkProduct);

            if ($row['quantity'] < $quantity) {
                redirect('order-create.php', 'Only ' . $row['quantity'] . ' quantity available!');
            }

            $productData = [
                'product_id' => $row['id'],
                'name' => $row['name'],
                'image' => $row['image'],
                'price' => $row['price'],
                'quantity' => $quantity,
            ];

            if (!in_array($row['id'], $_SESSION['productItemsIds'])) {
                array_push($_SESSION['productItemsIds'], $row['id']);
                array_push($_SESSION['productItems'], $productData);
                redirect('order-create.php', 'Item Added ', $row['name']);
            } else {
                foreach ($_SESSION['productItems'] as $key => $prodSessionItem) {
                    if ($prodSessionItem['product_id'] == $row['id']) {
                        $newQuantity = $prodSessionItem['quantity'] + $quantity;

                        // Check if adding the new quantity exceeds the available stock
                        if ($newQuantity > $row['quantity']) {
                            redirect('order-create.php', 'Only ' . $row['quantity'] . ' quantity available!');
                        } else {
                            $productData = [
                                'product_id' => $row['id'],
                                'name' => $row['name'],
                                'image' => $row['image'],
                                'price' => $row['price'],
                                'quantity' => $newQuantity,
                            ];

                            $_SESSION['productItems'][$key] = $productData;
                            redirect('order-create.php', 'Item Added ', $row['name']);
                        }
                    }
                }
            }
        } else {
            redirect('order-create.php', 'No such product Found');
        }
    } else {
        redirect('order-create.php', 'Something Went Wrong');
    }
}



    //proceed


if (isset($_POST['proceedToPlaceBtn'])) {
    $name = validate($_POST['cname']);
    $payment_mode = validate($_POST['payment_mode']);

    //checking for custom
    $checkCustomer = mysqli_query($conn, "SELECT * FROM customers WHERE name='$name' LIMIT 1");
    if ($checkCustomer) {
        if (mysqli_num_rows($checkCustomer) > 0) {
            $_SESSION['invoice_no'] = "INV-" .rand(111111, 999999);
            $_SESSION['cname'] = $name;
            $_SESSION['payment_mode'] = $payment_mode;
            jsonResponse(200, 'success', 'Customer Found');
        } else {
            $_SESSION['cname'] = $name;
            jsonResponse(404, 'warning', 'Customer Not Found');
        }
    } else {
        jsonResponse(500, 'error', 'Something Went Wrong');
    }
}


if (isset($_POST['saveCustomerBtn'])) {
    $name = validate($_POST['name']);
    $phone = validate($_POST['phone']);
    $email = validate($_POST['email']);

    if ($name != '' && $phone != '') {

        $data = [
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
        ];
        $result = insert('customers', $data);
        if ($result) {

            jsonResponse(200, 'success', 'Customer Created Successfully');
        } else {
            jsonResponse(500, 'error', 'Something went wrong');
        }
    } else {
        jsonResponse(422, 'warning', 'Please fill the required fields');
    }
}


if (isset($_POST['saveOrder'])) {
    $name = validate($_SESSION['cname']);
    $invoice_no = validate($_SESSION['invoice_no']);
    $payment_mode = validate($_SESSION['payment_mode']);
    $order_placed_by_id = $_SESSION['loggedInUser']['user_id'];

    $checkCustomer = mysqli_query($conn, "SELECT * FROM customers WHERE name='$name' LIMIT 1");

    if (!$checkCustomer) {
        jsonResponse(500, 'error', 'Something Went Wrong');
    }
    if (mysqli_num_rows($checkCustomer) > 0) {
        $customerData = mysqli_fetch_assoc($checkCustomer);
        if (!isset($_SESSION['productItems'])) {
            jsonResponse(404, 'warning', 'No items to place order');
        }
        $sessionProducts = $_SESSION['productItems'];

        $totalAmount = 0;
        foreach ($sessionProducts as $amtItem) {
            $totalAmount += $amtItem['price'] * $amtItem['quantity'];
        }

        $data = [
            'customer_id' => $customerData['id'],
            'tracking_no' => rand(11111, 99999),
            'invoice_no' => $invoice_no,
            'total_amount' => $totalAmount,
            'order_date' => date('Y-m-d'),
            'order_status' => 'completed',
            'payment_mode' => $payment_mode,
            'order_placed_by_id' => $order_placed_by_id,




        ];
        $result = insert('orders', $data);

        $lastOrderId = mysqli_insert_id($conn);

        foreach ($sessionProducts as $prodItem) {
            $productId = $prodItem['product_id'];
            $price = $prodItem['price'];
            $quantity = $prodItem['quantity'];

            //insert order

            $dataOrderItem = [
                'order_id' => $lastOrderId,
                'product_Id' => $productId,
                'price' => $price,
                'quantity' => $quantity,

            ];
            $orderItemQuery = insert('order_items', $dataOrderItem);

            //checking quant inc dec

            $checkProductQuantityQuery = mysqli_query($conn, "SELECT * FROM products WHERE id='$productId'");
            $productQtyData = mysqli_fetch_assoc($checkProductQuantityQuery);
            $totalProductQuantity = $productQtyData['quantity'] - $quantity;

            $dataUpdate = [
                'quantity' => $totalProductQuantity
            ];

            $updateProductQuantity = update('products', $productId, $dataUpdate);
        }


        unset($_SESSION['productItemsIds']);
        unset($_SESSION['productItems']);
        unset($_SESSION['cname']);
        unset($_SESSION['payment_mode']);
        unset($_SESSION['invoice_no']);


        jsonResponse(200, 'success', 'Order placed Successfully');
    } 
    else
    {
        jsonResponse(404, 'warning', 'No Customer Found');
    }
}

if(isset($_GET['track'])) {
    $tracking_no = $_GET['track'];
    
    // Assuming you have a function to handle the return action
    handleReturn($conn, $tracking_no);
}

function handleReturn($conn, $tracking_no) {
    // Fetch order ID based on tracking number
    $query = "SELECT id FROM orders WHERE tracking_no = '$tracking_no'";
    $result = mysqli_query($conn, $query);
    
    if($result && mysqli_num_rows($result) > 0) {
        $order = mysqli_fetch_assoc($result);
        $order_id = $order['id'];
        
        // Insert return record into returns table
        $insert_return_query = "INSERT INTO returns (order_id, return_date) VALUES ('$order_id', NOW())";
        $insert_return_result = mysqli_query($conn, $insert_return_query);
        
        if($insert_return_result) {
            // Get the ID of the newly inserted return
            $return_id = mysqli_insert_id($conn);
            
            // Fetch items from the order
            $fetch_order_items_query = "SELECT * FROM order_items WHERE order_id = '$order_id'";
            $order_items_result = mysqli_query($conn, $fetch_order_items_query);
            
            // Insert return items into return_items table
            while($order_item = mysqli_fetch_assoc($order_items_result)) {
                $product_id = $order_item['product_id'];
                $price = $order_item['price'];
                $quantity = $order_item['quantity'];
                
                $insert_return_item_query = "INSERT INTO return_items (return_id, product_id, price, quantity) VALUES ('$return_id', '$product_id', '$price', '$quantity')";
                mysqli_query($conn, $insert_return_item_query);
            }
            
            echo '<script>alert("Items returned successfully!");</script>';
        } else {
            echo '<script>alert("Failed to return items. Please try again later.");</script>';
        }
    } else {
        echo '<script>alert("Order not found!");</script>';
    }
}



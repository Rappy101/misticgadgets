<?php
include('../config/function.php');

$sql = "SELECT name, quantity FROM products"; // Adjust your SQL query as needed
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $products = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    // Return the product data as JSON
    echo json_encode($products);
} else {
    echo "[]"; // Return an empty array if no products are found
}

// Close the database connection
mysqli_close($conn);
?>

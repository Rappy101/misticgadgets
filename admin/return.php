<?php
include('includes/header.php');

// Function to handle the return action
function handleReturn($conn, $trackingNo, $reason)
{
    // Fetch order data
    $orderQuery = "SELECT * FROM orders WHERE tracking_no = '$trackingNo'";
    $orderResult = mysqli_query($conn, $orderQuery);

    if (mysqli_num_rows($orderResult) > 0) {
        $orderData = mysqli_fetch_assoc($orderResult);

        // Extract order data
        $customerID = $orderData['customer_id'];
        $invoiceNo = $orderData['invoice_no'];
        $totalAmount = $orderData['total_amount'];
        $orderDate = $orderData['order_date'];
        $orderStatus = 'Returned';
        $paymentMode = $orderData['payment_mode'];
        $orderPlacedByID = $orderData['order_placed_by_id'];

        // Insert data into returns table
        $insertReturnQuery = "INSERT INTO returns (customer_id, tracking_no, invoice_no, total_amount, order_date, order_status, payment_mode, order_placed_by_id, reason) 
                             VALUES ('$customerID', '$trackingNo', '$invoiceNo', '$totalAmount', '$orderDate', '$orderStatus', '$paymentMode', '$orderPlacedByID', '$reason')";

        if (mysqli_query($conn, $insertReturnQuery)) {
            // Fetch order items
            $orderItemsQuery = "SELECT * FROM order_items WHERE order_id IN (SELECT id FROM orders WHERE tracking_no = '$trackingNo')";
            $orderItemsResult = mysqli_query($conn, $orderItemsQuery);

            // Insert order items into return_items table
            while ($orderItem = mysqli_fetch_assoc($orderItemsResult)) {
                $productId = $orderItem['product_id'];
                $quantity = $orderItem['quantity'];
                $price = $orderItem['price'];
                $orderId = $orderItem['order_id'];

                $insertReturnItemQuery = "INSERT INTO return_items (order_id, product_id, quantity, price) 
                                          VALUES ('$orderId', '$productId', '$quantity', '$price')";

                mysqli_query($conn, $insertReturnItemQuery);
            }

            // Delete order from orders table
            $deleteOrderQuery = "DELETE FROM orders WHERE tracking_no = '$trackingNo'";
            if (mysqli_query($conn, $deleteOrderQuery)) {
                echo '<div class="alert alert-success" role="alert">Item returned successfully!</div>';
            } else {
                echo '<div class="alert alert-danger" role="alert">Error deleting order: ' . mysqli_error($conn) . '</div>';
            }
        } else {
            echo '<div class="alert alert-danger" role="alert">Error returning item: ' . mysqli_error($conn) . '</div>';
        }
    } else {
        echo '<div class="alert alert-danger" role="alert">Order not found!</div>';
    }
}

// Check if the return button is clicked
if (isset($_GET['track']) && isset($_GET['reason'])) {
    $trackingNo = $_GET['track'];
    $reason = $_GET['reason'];
    handleReturn($conn, $trackingNo, $reason);
}

function printReturnsTable($conn, $query)
{
    $returns = mysqli_query($conn, $query);

    if ($returns) {
        if (mysqli_num_rows($returns) > 0) {
            // Initialize total price variable
            $totalPrice = 0;
?>
            <h1>Returns Report</h1> 
            <table id="returns-table" class="table table-striped table-bordered align-items-center justify-content-center">
                <thead>
                    <tr>
                        <th>Tracking No.</th>
                        <th>Return ID</th> <!-- Adjusted table headers -->
                        <th>Order Date</th>
                        <th>Refund Status</th>
                        <th>Price</th>
                        <th>Reason</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($returns as $returnItem) :
                        // Accumulate total price
                        $totalPrice += $returnItem['total_amount'];
                    ?>
                        <tr>
                            <td><?= $returnItem['tracking_no']; ?></td>
                            <td class="fw-bold"><?= $returnItem['id']; ?></td> <!-- Changed column values -->
                            <td><?= date('d-m-Y', strtotime($returnItem['order_date'])); ?></td>
                            <td><?= $returnItem['order_status']; ?></td>
                            <td>₱<?= number_format($returnItem['total_amount'],2) ?></td>
                            <td><?= $returnItem['reason']; ?></td>
                            <td class="action">
                                <a href="return-view.php?track=<?= $returnItem['tracking_no']; ?>" class="btn btn-info mb-0 px-2 btn-sm">View</a>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="4"></td>
                        <td>Total Refund: ₱<?= number_format($totalPrice); ?></td> <!-- Adjusted total price calculation -->
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
<?php
        } else {
            echo '<h5>No Record Available</h5>';
        }
    } else {
        echo '<h5>Something Went Wrong</h5>';
    }
}

// Adjusting the SQL query to fetch return-related data
$query = "SELECT * FROM `returns` ORDER BY id DESC";

?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow">
        <div class="card-header">
            <div class="row">
                <div class="col-md-4">
                    <h4 class="mb-0">Returns</h4> 
                </div>
                <div class="col-md-8">
                    
                </div>
            </div>
        </div>
        <div class="card-body">
            <?php
            printReturnsTable($conn, $query);
            ?>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

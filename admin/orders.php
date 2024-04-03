<?php
include('includes/header.php');

function printOrdersTable($conn, $query)
{
    $orders = mysqli_query($conn, $query);

    if ($orders) {
        if (mysqli_num_rows($orders) > 0) {
            // Initialize total price variable
            $totalPrice = 0;
?>
            <h1>Sales Report</h1> <!-- Added header -->
            <table id="orders-table" class="table table-striped table-bordered align-items-center justify-content-center">
                <thead>
                    <tr>
                        <th>Tracking No.</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Order Date</th>
                        <th>Payment Mode</th>
                        <th>Order Status</th>
                        <th>Price</th>
                        <th class="action">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $orderItem) :
                        // Accumulate total price
                        $totalPrice += $orderItem['total_amount'];
                    ?>
                        <tr>
                            <td class="fw-bold"><?= $orderItem['tracking_no']; ?></td>
                            <td><?= $orderItem['name']; ?></td>
                            <td><?= $orderItem['phone']; ?></td>
                            <td><?= date('d-m-Y', strtotime($orderItem['order_date'])); ?></td>
                            <td><?= $orderItem['payment_mode']; ?></td>
                            <td><?= $orderItem['order_status']; ?></td>
                            <td> ₱<?= number_format($orderItem['total_amount']); ?></td>
                            <td class="action">
                                <a href="orders-view.php?track=<?= $orderItem['tracking_no']; ?>" class="btn btn-info mb-0 px-2 btn-sm"><i class="fas fa-columns"></i> View</a>
                                <a href="orders-view-print.php?track=<?= $orderItem['tracking_no']; ?>" class="btn btn-primary mb-0 px-2 btn-sm"><i class="fas fa-print"></i> Print</a>
                                <script>
                                    function returnItem(trackingNo) {
                                        var reason = prompt("Please enter the reason for return:");
                                        if (reason !== null) {
                                            window.location.href = "return.php?track=" + trackingNo + "&reason=" + encodeURIComponent(reason);
                                        }
                                    }
                                </script>

                                <a href="#" onclick="returnItem('<?= $orderItem['tracking_no']; ?>')" class="btn btn-danger mb-0 px-2 btn-sm">
                                    <i class="fas fa-warning"></i> Return
                                </a>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="6"></td>
                        <td>Total Price: ₱<?= number_format($totalPrice); ?></td>
                        <td colspan="3"></td>
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

if (isset($_GET['search'], $_GET['start_date'], $_GET['end_date']) || isset($_GET['payment_status'])) {
    $trackingNumber = isset($_GET['search']) ? validate($_GET['search']) : '';
    $startDate = isset($_GET['start_date']) ? validate($_GET['start_date']) : '';
    $endDate = isset($_GET['end_date']) ? validate($_GET['end_date']) : '';
    $paymentStatus = isset($_GET['payment_status']) ? validate($_GET['payment_status']) : '';

    // construct filter
    $whereClause = "WHERE 1=1";
    if (!empty($trackingNumber)) {
        $whereClause .= " AND o.tracking_no LIKE '%$trackingNumber%'";
    }
    if (!empty($startDate) && !empty($endDate)) {
        $whereClause .= " AND o.order_date BETWEEN '$startDate' AND '$endDate'";
    }
    if (!empty($paymentStatus)) {
        $whereClause .= " AND o.payment_mode = '$paymentStatus'";
    }

    $query = "SELECT o.*, c.* FROM orders o JOIN customers c ON c.id = o.customer_id $whereClause ORDER BY o.id DESC";
} else {
    $query = "SELECT o.*, c.* FROM orders o JOIN customers c ON c.id = o.customer_id ORDER BY o.id DESC";
}

?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow">
        <div class="card-header">
            <div class="row">
                <div class="col-md-4">
                    <h4 class="mb-0">Orders</h4>
                </div>
                <div class="col-md-8">
                    <form action="" method="GET">
                        <div class="row g-1">
                            <div class="col-md-2">
                                <input type="text" name="search" class="form-control" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>" placeholder="Search by Tracking#" />
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="start_date" class="form-control" value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>" />
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="end_date" class="form-control" value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>" />
                            </div>
                            <div class="col-md-3">
                                <select name="payment_status" class="form-select">
                                    <option value="">Select Payment</option>
                                    <option value="Cash Payment" <?= isset($_GET['payment_status']) && $_GET['payment_status'] == 'Cash Payment' ? 'selected' : ''; ?>>Cash Payment</option>
                                    <option value="Online Payment" <?= isset($_GET['payment_status']) && $_GET['payment_status'] == 'Online Payment' ? 'selected' : ''; ?>>Online Payment</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
                                <a href="orders.php" class="btn btn-danger"><i class="fa-solid fa-rotate-right"></i> Reset</a>
                                <button type="button" class="btn btn-success" onclick="printTable()"> <i class="fa-solid fa-print"></i> Table</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <?php
            printOrdersTable($conn, $query);
            ?>
        </div>
    </div>
</div>

<script>
    function printTable() {
        // Hide the 'Action' column before printing
        var actionColumn = document.querySelectorAll('#orders-table .action');
        for (var i = 0; i < actionColumn.length; i++) {
            actionColumn[i].style.display = 'none';
        }

        // Preload the watermark background image
        var img = new Image();
        img.src = '../MG.jpg';
        img.onload = function() {
            // Add watermark background image after preloading
            var printContents = '<h1>Sales Report</h1>' + document.getElementById("orders-table").outerHTML; // Added header
            var watermark = '<div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background-image: url(\'../MG.jpg\'); background-size: cover; background-position: center; opacity: 0.3;"></div>';
            printContents += watermark;

            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;

            // Restore the 'Action' column after printing
            for (var i = 0; i < actionColumn.length; i++) {
                actionColumn[i].style.display = '';
            }
        };
    }
</script>

<?php include('includes/footer.php'); ?>
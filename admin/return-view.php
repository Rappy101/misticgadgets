<?php include('includes/header.php'); ?>
<div class="container-fluid px-4">
    <div class="card mt-4 shadow">
        <div class="card-header">
            <h4 class="mb-0">Return View
                <a href="return.php" class="btn btn-danger mx-2 btn-sm float-end">Back</a>
            </h4>
        </div>
        <div class="card-body">
            <?php alertMessage() ?>
            <?php
            if (isset($_GET['track'])) {
                if ($_GET['track'] == '') {
            ?>
                    <div class="text-center py 5">
                        <h5>No Tracking Number Found</h5>
                        <div>
                            <a href="returns.php" class="btn btn-primary mt-4 mw-25">Go back to Returns</a>
                        </div>
                    </div>
            <?php
                    return false;
                }
                $trackingNo = validate($_GET['track']);
                $query = "SELECT r.*, c.* FROM returns r, customers c WHERE c.id = r.customer_id AND tracking_no ='$trackingNo'";
                $returns = mysqli_query($conn, $query);
                if ($returns) {
                    if (mysqli_num_rows($returns) > 0) {
                        $returnData = mysqli_fetch_assoc($returns);
                        $returnId = $returnData['id'];
            ?>
                        <div class="card card-body shadow border-1 mb-4 ">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4>Return Details</h4>
                                    <label class="mb-1">
                                        Tracking No: <span class="fw-bold"><?= $returnData['tracking_no']; ?></span>
                                    </label>
                                    <br />
                                    <label class="mb-1">
                                        Return Date: <span class="fw-bold"><?= $returnData['order_date']; ?></span>
                                    </label>
                                    <br />
                                    <label class="mb-1">
                                        Return Status: <span class="fw-bold"><?= $returnData['order_status']; ?></span>
                                    </label>
                                    <br />
                                    <label class="mb-1">
                                        Payment Mode: <span class="fw-bold"><?= $returnData['payment_mode']; ?></span>
                                    </label>
                                    <br />
                                </div>
                                <div class="col-md-6">
                                    <h4>User Details</h4>
                                    <label class="mb-1">
                                        Full Name: <span class="fw-bold"><?= $returnData['name']; ?></span>
                                    </label>
                                    <br />
                                    <label class="mb-1">
                                        Email Address: <span class="fw-bold"><?= $returnData['email']; ?></span>
                                    </label>
                                    <br />
                                    <label class="mb-1">
                                        Phone Number: <span class="fw-bold"><?= $returnData['phone']; ?></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <?php
                        $returnItemQuery = "SELECT ri.quantity as returnItemQuantity, ri.price as returnItemPrice, r.*, ri.*, p.* FROM returns as r
                                            JOIN return_items as ri ON r.id = ri.id
                                            JOIN products as p ON ri.product_id = p.id
                                            WHERE r.tracking_no = '$trackingNo'";
                        $returnItemRes = mysqli_query($conn, $returnItemQuery);
                        if ($returnItemRes) {
                            $returnItems = mysqli_fetch_all($returnItemRes, MYSQLI_ASSOC);
                            if (!empty($returnItems)) {
                                ?>
                                <h4 class="my-3">Return Item Details</h4>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($returnItems as $returnItemRow) : ?>
                                            <tr>
                                                <td>
                                                    <img src="<?= $returnItemRow['image'] != '' ? '../' . $returnItemRow['image'] : '../assets/images/no-images.jpg'; ?>" style="width:50px;height:50px;" alt="Img" />
                                                    <?= $returnItemRow['name']; ?>
                                                </td>
                                                <td width="15%" class="fw-bold text-center">₱<?= number_format($returnItemRow['returnItemPrice'], 0) ?></td>
                                                <td width="15%" class="fw-bold text-center"><?= $returnItemRow['returnItemQuantity']; ?></td>
                                                <td width="15%" class="fw-bold text-center">₱<?= number_format($returnItemRow['returnItemPrice'] * $returnItemRow['returnItemQuantity'], 0) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr>
                                            <td class="text-end fw-bold">Total Price:</td>
                                            <td colspan="3" class="text-end fw-bold">₱<?= number_format($returnData['total_amount'], 0); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <?php
                            } else {
                                echo '<h5>No Return Item Found</h5>';
                            }
                        } else {
                            echo '<h5>Query Failed: ' . mysqli_error($conn) . '</h5>';
                        }
                        ?>
            <?php
                    } else {
                        echo '<h5>No Record Found</h5>';
                    }
                } else {
                    echo '<h5>Query Failed: ' . mysqli_error($conn) . '</h5>';
                }
            } else {
            ?>
                <div class="text-center py 5">
                    <h5>No Tracking Number Found</h5>
                    <div>
                        <a href="returns.php" class="btn btn-primary mt-4 mw-25">Go back to Returns</a>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>
<?php include('includes/footer.php'); ?>

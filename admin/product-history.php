<?php include('includes/header.php'); ?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow">
        <div class="card-header">
            <h4 class="mb-0">Product History</h4>
            <a href="products.php" class="btn btn-primary float-end">Back</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity Added</th>
                            <th>Date Added</th>
                            <th>Added By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Custom SQL query to retrieve product history in descending order
                        $sql = "SELECT * FROM product_history ORDER BY added_at DESC";
                        $history = customQuery($sql);

                        if ($history) {
                            foreach ($history as $record) {
                                $product = getById('products', $record['product_id']);
                                if ($product) {
                                    ?>
                                    <tr>
                                        <td><?= $product['data']['name']; ?></td>
                                        <td><?= $record['quantity_added']; ?></td>
                                        <td><?= $record['added_at']; ?></td>
                                        <td><?= $record['added_by']; ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="3">No history found.</td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

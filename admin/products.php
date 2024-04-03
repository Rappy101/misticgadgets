<?php include('includes/header.php'); ?>
<div class="container-fluid px-4">
    <div class="card mt-4 shadow">
        <div class="card-header">
            <h4 class="mb-0">Products</h4>
            <a href="products-create.php" class="btn btn-primary float-end">Add Product</a>
            <div class="col-md-8">
                <form action="" method="GET">
                    <div class="row g-1">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>" placeholder="Search by name" />
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i> Search</button>
                            <a href="products.php" class="btn btn-danger"><i class="fa-solid fa-rotate-right"></i> Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body">
            <?php alertMessage(); ?>

            <?php
        
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            $products = getAllProducts($search);

            if (!$products) {
                echo '<h4> Something Went Wrong! </h4>';
                return false;
            }

            if (mysqli_num_rows($products) > 0) {
            ?>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Supplier</th>
                                <th>Date Created:</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php foreach ($products as $item) : ?>
                                <tr>
                                    <td><img src="../<?= $item['image']; ?>" style="width:50px;height:50px;" alt="Img"></td>
                                    <td><?= $item['name'] ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td>₱<?= number_format($item['price'],2) ?></td>
                                    <td>
                                        <?php
                                        if ($item['quantity'] == 0) {
                                            echo '<span class="badge bg-danger">Not Available</span>';
                                        } else {
                                            echo '<span class="badge bg-primary">Available</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        // Fetch and display supplier name
                                        $suppliers_id = $item['suppliers_id'];

                                        // Query the database for supplier name
                                        $supplierQuery = "SELECT name FROM suppliers WHERE id = $suppliers_id";
                                        $supplierResult = mysqli_query($conn, $supplierQuery);

                                        if ($supplierResult && mysqli_num_rows($supplierResult) > 0) {
                                            $supplier = mysqli_fetch_assoc($supplierResult);
                                            echo $supplier['name'];
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                    <td><?= $item['created_at'] ?></td>
                                    <td>
                                        <a href="products-add.php?id=<?= $item['id']; ?>" class="btn btn-success btn-sm"><i class="fa-solid fa-plus"></i> Add</a>
                                        <a href="products-edit.php?id=<?= $item['id']; ?>" class="btn btn-info btn-sm"> <i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                        <a href="products-delete.php?id=<?= $item['id']; ?> " class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove the product?')"><i class="fa-solid fa-warning"></i> Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php
            } else {
                echo '<h4 class="mb-0">No Records of Products Found</h4>';
            }
            ?>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<?php
// Function to get all products
function getAllProducts($search = null)
{
    global $conn;

    $query = "SELECT * FROM products";

    // If search query is provided, filter by product name
    if ($search !== null) {
        $search = mysqli_real_escape_string($conn, $search);
        $query .= " WHERE name LIKE '%$search%'";
    }

    return mysqli_query($conn, $query);
}
?>

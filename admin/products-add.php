<?php include('includes/header.php'); ?>
<div class="container-fluid px-4">
    <div class="card mt-4 shadow">
        <div class="card-header">
            <h4 class="mb-0">Add quantity to the product</h4>
            <a href="products.php" class="btn btn-primary float-end">Back</a>
        </div>
        <div class="card-body">
            <?php
            alertMessage();
            ?>
            <form action="code.php" method="POST" enctype="multipart/form-data">


                <?php

                $paramValue = checkParamId('id');
                if (!is_numeric($paramValue)) {

                    echo '<h5> Id is  not an integer </h5>';
                    return false;
                }

                $product = getById('products', $paramValue);

                if ($product) {



                    if ($product['status'] == 200) {



                ?>
                        <input type="hidden" name="product_id" value="<?= $product['data']['id']; ?>">

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <!-- Quantity Field -->
                                <div class="col-md-4 mb-3">
                                    <label for="">Enter Quantity</label>
                                    <input type="number" name="quantity_to_add" min="1" required class="form-control" />
                                </div>
                                <!-- Save Button -->
                                <div class="col-md-6 mb-3 text-end">
                                    <br />
                                    <button type="submit" name="addQuantity" class="btn btn-primary">Add Quantity</button>
                                </div>

                            </div>

                    <?php

                    } else {
                        echo '<h5> ' . $product['message'] . ' <h5>';
                    }
                } else {

                    echo '<h5> Something Went wrong <h5>';
                    return false;
                }

                    ?>


            </form>

        </div>
    </div>
</div>


<?php include('includes/footer.php'); ?>
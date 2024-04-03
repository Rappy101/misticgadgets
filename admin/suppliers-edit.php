<?php include('includes/header.php'); ?>
<div class="container-fluid px-4">
<div class ="card mt-4 shadow">
            <div class="card-header">
                <h4 class="mb-0">Edit Customer</h4>
                <a href="suppliers.php" class="btn btn-danger float-end">Back</a>
            </div>
            <div class="card-body">
                <?php
                  alertMessage();
                ?>
                <form action="code.php" method="POST">



                    <?php

                    $paramValue = checkParamId('id');
                    if(!is_numeric($paramValue)){
                        echo '<h5>'.$paramValue.'</h5>';
                        return false;
                    }

                    $supplier = getById('suppliers', $paramValue);
                    if($supplier ['status'] == 200){



                        ?>

                        <input type="hidden" name="suppliersId" value="<?=$supplier ['data']['id']; ?>" />
                        <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="">Name *</label>
                            <input type="text" name="name" required value ="<?=$supplier ['data']['name']; ?>" class="form-control"/>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="">Email </label>
                            <input type="email" name="email" value ="<?=$supplier ['data']['email']; ?>" class="form-control"/>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="">Phone </label>
                            <input type="number" name="phone" value ="<?=$supplier ['data']['phone']; ?>" class="form-control"/>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="">Description *</label>
                            <input type="text" name="description" required value ="<?=$supplier ['data']['description']; ?>" class="form-control"/>
                        </div>
                        <div class="col-md-6">
                        <label>Status (UnChecked=Visible, Checked=Hidden)</label>
                        <br/>
                        <input type="checkbox" name="status" <?=$supplier ['data']['status'] == true? 'checked':''; ?> style="width:30px;height:30px";>
                        </div>

                        <div class="col-md-6 mb-3 text-end ">
                            <br/>
                            <button type ="submit" name="updateSuppliers" class="btn btn-primary">Update</button>
                            
                        </div>
                    </div>
                        <?php
                    }else{
                        echo '<h5>'.$supplier['message'].' </h5>';
                        return false;
                    }
                    
                    
                    ?>

                    



                 </form>

                </div>
</div>
</div>


<?php include('includes/footer.php'); ?>
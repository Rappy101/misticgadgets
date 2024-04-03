<?php include('includes/header.php'); ?>
<div class="container-fluid px-4">
            <div class ="card mt-4 shadow">
             <div class="card-header">
                <h4 class="mb-0">Suppliers</h4>
                <a href="suppliers-create.php" class="btn btn-primary float-end">Add Customer</a>
                </div>
                    <div class="card-body">
                    <?php alertMessage(); ?>

                    <?php 
                                $suppliers =getAll('suppliers');
                                if(!$suppliers){
                                    echo '<h4> Something Went Wrong! </h4>';
                                    return false;
                                }

                                if(mysqli_num_rows($suppliers) > 0)

                                {

                                
                            ?>

                        <div class="table-responsive">
                         <table class="table table-striped table=bordered">
                                <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>description</th>
                                <th>Status</th>    
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            

                            <?php foreach($suppliers as $item) : ?>
                          <tr>
                             <td><?= $item ['id'] ?></td>
                             <td><?= $item ['name'] ?></td>
                             <td><?= $item ['email'] ?></td>
                             <td><?= $item ['phone'] ?></td>
                             <td><?= $item ['description'] ?></td>

                             
                             <td> 
                                
                             <?php  
                                     if($item['status'] == 1){
                                        echo '<span class="badge bg-danger">Not Available</span>';
                                     }else{
                                        echo '<span class="badge bg-primary">Available</span>';
                                     }
                                     ?>

                                    </td>
                                    <td>
                             <a href="suppliers-edit.php?id=<?= $item ['id']; ?>" class ="btn btn-success btn-sm">Edit</a>
                             <a href="suppliers-delete.php?id=<?= $item ['id']; ?> " class ="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove the suppliers?')">Delete</a>
                            
                            </td>
                          </tr>
                                 <?php endforeach; ?>

                          
                    
                    </tbody>
                    </table>
                </div>
                <?php 
                               }
                               else{
                                ?>
                                <tr>

                                <h4 class="mb-0">
                                No Records of Suppliers Found
                               </h4>
                             </tr>
                             <?php
                               }
                          ?>
            </div>

     </div>
</div>

<?php include('includes/footer.php'); ?>
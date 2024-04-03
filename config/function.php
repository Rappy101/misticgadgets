<?php 
    session_start();

    require 'dbcon.php';
    //input field validation
    function validate($inputData){
        global $conn;
        $validatedData = mysqli_real_escape_string($conn, $inputData );
        return trim($validatedData);
    }

    //redir 1 to p2 with msg

    function redirect($url, $status){

        $_SESSION['status'] = $status;
        header('location: ' .$url);
        exit(0);

    }

    

    //display msg stats after any proc

    function alertMessage(){
        if (isset($_SESSION['status'])){
             echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <h6> '.$_SESSION['status'].' </h6>
                         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                 </div>';
            unset ($_SESSION['status']);
        }
    }

        //insert func to record


    function insert($tableName, $data){
        global $conn;

        
        $table = validate($tableName);

        $columns = array_keys($data);
        $values = array_values($data);

        $finalColumn = implode(',', $columns);
        $finalValues = "'".implode("','", $values)."'";

        $query = " INSERT INTO $table ($finalColumn) VALUES ($finalValues)";
        $result = mysqli_query($conn, $query);
        return $result;
    }

        //update data



        function update($tableName, $id, $data){
            global $conn;

        
        $table = validate($tableName);
        $id = validate($id);

        $updateDataString = "";
        foreach($data as $column => $value){
            $updateDataString .=$column.'='."'$value',";

        }

        $finalUpdateData = substr(trim($updateDataString),0,-1);
        $query = "UPDATE $table SET $finalUpdateData WHERE id='$id'";
        $result = mysqli_query($conn, $query);
        return $result;

        }

        function getAll($tableName, $status = NULL){

            global $conn;

            $table = validate($tableName);
            $status = validate($status);

            if($status == 'status')
            {
                $query = "SELECT * FROM $table WHERE status='0'";
            }

            else
            {
                $query = "SELECT * FROM $table";

            }
            return mysqli_query($conn, $query);

        }

        function getById($tableName, $id){

            global $conn;
            $table = validate($tableName);
            $id = validate($id);

            $query = " SELECT * FROM $table WHERE id='$id' LIMIT 1";
            $result = mysqli_query($conn, $query);

            if($result){
                if(mysqli_num_rows($result) == 1){

                    $row = mysqli_fetch_assoc($result);
                    $response =  [
                        'status' => 200,
                        'data' => $row,
                        'message' => 'Record Found'
                    ];
                    return $response;

                }else{
                    $response =  [
                        'status' => 200,
                        'message' => 'No Data Found'
                    ];
                    return $response;
                }
                 }else{

                $response =  [
                    'status' => 500,
                    'message' => 'Something went wrong'
                ];
                return $response;
                }

         }

         //updatequan
         

         function incrementProductQuantity($productId, $conn) {
            // Query to update the quantity of the product
            $query = "UPDATE products SET quantity = quantity + 1 WHERE id = $productId";
        
            // Execute the query
            $result = mysqli_query($conn, $query);
        
            // Check if the query was successful
            if($result) {
                return true; // Quantity incremented successfully
            } else {
                return false; // Failed to increment quantity
            }
        }

         //delete


         function delete($tableName, $id){

            global $conn;
            $table = validate($tableName);
            $id = validate($id);

            $query = "DELETE FROM $table WHERE id='$id' LIMIT 1";
            $result = mysqli_query($conn, $query);
            return  $result;
        
        }


        function checkParamId($type){
            if(isset($_GET[$type])){
                if($_GET[$type] != ''){
                    return $_GET[$type];
                }else{
                    '<h5>No id Found </h5>';
                }

            }else{
                return '<h5>No id Given </h5>';
            }
        }

        function logoutSession(){

            unset($_SESSION['loggedIn']);
            unset($_SESSION['loggedInUser']);
        }


        
        function jsonResponse($status, $status_type, $message){
            $response = [
                'status' => $status,
                'status_type' => $status_type,
                'message' => $message
    
            ];
            echo json_encode($response);
            return;
        }


        function getCount($tableName){
            global $conn;
            $table = validate($tableName);
            $query ="SELECT * FROM $table";
            $query_run = mysqli_query($conn, $query);

            if($query_run){

                $totalCount = mysqli_num_rows($query_run);
                return $totalCount;

            }else{
                return 'Something Went Wrong!';
            }



        }
        function getTotalAmount() {
            global $conn;
            
            $query = "SELECT SUM(total_amount) AS total FROM orders";
            $query_run = mysqli_query($conn, $query);
        
            if ($query_run) {
                $result = mysqli_fetch_assoc($query_run);
                $totalAmount = $result['total'];
                
                // Deduct 30%
                $finalAmount = $totalAmount;
        
                return $finalAmount;
            } else {
                return 'Something Went Wrong!';
            }
        }

        function getTotalded() {
            global $conn;
            
            $query = "SELECT SUM(total_amount) AS total FROM orders";
            $query_run = mysqli_query($conn, $query);
        
            if ($query_run) {
                $result = mysqli_fetch_assoc($query_run);
                $totalAmount = $result['total'];
                
                // Deduct 30%
                $finalAmount = $totalAmount - ($totalAmount *0.7);
        
                return $finalAmount;
            } else {
                return 'Something Went Wrong!';
            }
        }
        
        function getTotalinv() {
            global $conn;
            
            $query = "SELECT SUM(total_amount) AS total FROM orders";
            $query_run = mysqli_query($conn, $query);
        
            if ($query_run) {
                $result = mysqli_fetch_assoc($query_run);
                $totalAmount = $result['total'];
                
                // Deduct 30%
                $finalAmount = $totalAmount - ($totalAmount *0.3);
        
                return $finalAmount;
            } else {
                return 'Something Went Wrong!';
            }
        }
////////////////////////
        function customQuery($sql) {
            global $conn;
            $result = mysqli_query($conn, $sql);
            if ($result) {
                $data = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $data[] = $row;
                }
                return $data;
            } else {
                return false;
            }
        }

        ?>
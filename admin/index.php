<?php include('includes/header.php'); ?>
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mt-4">Dashboard</h1>
            <?php alertMessage(); ?>
        </div>
        <div class="row">
            <div class="col-md-12">
                <hr>
                <h5>Orders</h5>
            </div>
            <div class="col-md-3 bm-3">
                <div class="card card-body bg-info p-3">
                    <p class="text-sm mb-0 text-capitalize fw-bold">Today Orders</p>
                    <h5 class="fw-bold mb-0">
                        <?php
                        $todayDate = date('Y-m-d');
                        $todayOrders = mysqli_query($conn, "SELECT * FROM orders WHERE order_date='$todayDate'");
                        if ($todayOrders) {
                            if (mysqli_num_rows($todayOrders) > 0) {
                                $totalCountOrders = mysqli_num_rows($todayOrders);
                                echo $totalCountOrders;
                            } else {
                                echo "0";
                            }
                        } else {
                            echo 'Something Went Wrong';
                        }
                        ?>
                    </h5>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card card-body bg-info p-3">
                    <p class="text-sm mb-0 text-capitalize fw-bold">Total Orders</p>
                    <h5 class="fw-bold mb-0">
                        <?= getCount('orders'); ?>
                    </h5>
                </div>
            </div>
            <div class="col-md-12">
                <hr>
                <h5>Sales</h5>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card card-body bg-warning p-3">
                    <p class="text-sm mb-0 text-capitalize fw-bold">Today Sales</p>
                    <h5 class="fw-bold mb-0">
                        <?php
                        $todayDate = date('Y-m-d');
                        $todayOrders = mysqli_query($conn, "SELECT SUM(total_amount) AS totalSales FROM orders WHERE order_date='$todayDate'");
                        if ($todayOrders) {
                            $result = mysqli_fetch_assoc($todayOrders);
                            $totalSalesToday = $result['totalSales'];
                            // Display the total sales
                            echo '₱' . number_format($totalSalesToday, 2, '.', ','); // Format 
                        } else {
                            echo 'Something Went Wrong';
                        }
                        ?>
                    </h5>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card card-body bg-warning p-3">
                    <p class="text-sm mb-0 text-capitalize fw-bold">Total amount Sales</p>
                    <h5 class="fw-bold mb-0">
                        <?= '₱' . number_format(getTotalAmount(), 2, '.', ',') ?>
                    </h5>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card card-body bg-warning p-3">
                    <p class="text-sm mb-0 text-capitalize fw-bold">Total Investments</p>
                    <h5 class="fw-bold mb-0">
                        <?= '₱' . number_format(getTotalinv(), 2, '.', ',') ?>
                    </h5>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card card-body bg-warning p-3">
                    <p class="text-sm mb-0 text-capitalize fw-bold">Total Profit</p>
                    <h5 class="fw-bold mb-0">
                        <?= '₱' . number_format(getTotalded(), 2, '.', ',') ?>
                    </h5>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <figure class="highcharts-figure">
                <div id="dailySalesChart"></div>
            </figure>
        </div>
        <div class="col-md-6">
            <figure class="highcharts-figure">
                <div id="itemSalesChart"></div>
            </figure>
        </div>
    </div>
    <?php
    // Fetch data for Daily Sales chart
    $sql = "SELECT order_date, SUM(total_amount) AS daily_sales FROM orders GROUP BY order_date";
    $result = $conn->query($sql);
    $dates = array();
    $sales = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dates[] = $row["order_date"];
            $sales[] = (float)$row["daily_sales"];
        }
    }
    // Fetch data for Item Sales chart
    $sql = "SELECT p.name AS product_name, SUM(oi.quantity) AS total_quantity
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            GROUP BY p.name";
    $result = $conn->query($sql);
    $itemNames = array();
    $itemQuantities = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $itemNames[] = $row['product_name'];
            $itemQuantities[] = (int)$row['total_quantity'];
        }
        // Sort the item quantities in descending order
        array_multisort($itemQuantities, SORT_DESC, $itemNames);
    }
    ?>
    <script>
        // Daily Sales Chart
        Highcharts.chart('dailySalesChart', {
            title: {
                text: 'Daily Sales'
            },
            xAxis: {
                categories: <?php echo json_encode($dates); ?>,
                accessibility: {
                    rangeDescription: 'Range: 2019 to 2099'
                }
            },
            yAxis: {
                title: {
                    text: 'Total Amount'
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },
            series: [{
                name: 'Daily Sales',
                data: <?php echo json_encode($sales); ?>
            }]
        });

        // Item Sales Chart
        Highcharts.chart('itemSalesChart', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Fast-Moving and Slow-Moving Items'
            },
            xAxis: {
                categories: <?php echo json_encode($itemNames); ?>,
                labels: {
                    autoRotation: [-45, -90],
                    style: {
                        fontSize: '13px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Total Quantity Sold'
                }
            },
            legend: {
                enabled: false
            },
            tooltip: {
                pointFormat: 'Total Quantity Sold: <b>{point.y}</b>'
            },
            series: [{
                name: 'Items',
                color: '#4575b4',
                data: <?php echo json_encode($itemQuantities); ?>,
                dataLabels: {
                    enabled: true,
                    rotation: -90,
                    color: '#FFFFFF',
                    inside: true,
                    verticalAlign: 'top',
                    format: '{point.y}',
                    y: 10,
                    style: {
                        fontSize: '13px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                }
            }]
        });
    </script>
    <?php include('includes/footer.php'); ?>

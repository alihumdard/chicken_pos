<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rana Chicken Shop - POS</title>
    </head>
<body>
    <?php include 'header.php'; ?>
    <div class="content">
        <?php
        include 'config.php';

        // Calculations
        $today = date('Y-m-d');
        $trucks_today = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM truck_arrivals WHERE date='$today'"))['count'];
        $trucks_yesterday = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM truck_arrivals WHERE date=DATE_SUB('$today', INTERVAL 1 DAY)"))['count'];
        $trucks_change = $trucks_today - $trucks_yesterday;

        $sales_today = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM sales WHERE date='$today'"))['count'];

        $customers_total = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM customers"))['count'];

        $reports = 0; // Static for now

        $received_today = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(quantity) as sum FROM truck_arrivals WHERE date='$today'"))['sum'] ?? 0;
        $sold_today = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(quantity) as sum FROM sales WHERE date='$today'"))['sum'] ?? 0;
        $current_stock = $received_today - $sold_today; // Simplified, assume no previous stock for now
        $remaining = $current_stock;
        ?>
        <div class="section-title">Overview</div>
        <div class="sub-title">Real-time insights into your chicken shop operations</div>
        <div class="stats">
            <a href="truck_arrivals.php" class="stat-box blue">
                <div class="stat-text">
                    <div class="stat-value"><?php echo $trucks_today; ?></div>
                    <div class="stat-label">Trucks Arrived <br> +<?php echo $trucks_change; ?> from yesterday</div>
                </div>
                <img src="images/truck.png" alt="Truck Icon" class="stat-icon">
            </a>
            <a href="sales_management.php" class="stat-box orange">
                <div class="stat-text">
                    <div class="stat-value"><?php echo $sales_today; ?></div>
                    <div class="stat-label">Sales Deliveries</div>
                </div>
                <img src="images/sale.png" alt="Sale Icon" class="stat-icon">
            </a>
            <a href="customer_management.php" class="stat-box green">
                <div class="stat-text">
                    <div class="stat-value"><?php echo $customers_total; ?></div>
                    <div class="stat-label">Customers Transactions</div>
                </div>
                <img src="images/customer.png" alt="Customer Icon" class="stat-icon">
            </a>
            <a href="reports_analytics.php" class="stat-box yellow">
                <div class="stat-text">
                    <div class="stat-value"><?php echo $reports; ?></div>
                    <div class="stat-label">Reports</div>
                </div>
                <img src="images/report.png" alt="Reports Icon" class="stat-icon">
            </a>
        </div>
        <div class="placeholder">
            <div class="section-title">Current Inventory Status</div>
            <div class="stat-value"><?php echo $current_stock; ?> kg</div>
            <div class="stat-label">Available Stock</div>
            <div class="stats">
                <div class="stat-box light-blue">
                    <div class="stat-label">Received Today</div>
                    <div class="stat-value"><?php echo $received_today; ?> kg</div>
                </div>
                <div class="stat-box light-orange">
                    <div class="stat-label">Sold Today</div>
                    <div class="stat-value"><?php echo $sold_today; ?> kg</div>
                </div>
                <div class="stat-box light-green">
                    <div class="stat-label">Remaining</div>
                    <div class="stat-value"><?php echo $remaining; ?> kg</div>
                </div>
            </div>
        </div>
        <div class="stats">
            <div class="placeholder">
                <div class="section-title">Recent Truck Arrivals</div>
                <div><?php echo ($trucks_today > 0) ? 'Arrivals today' : 'No truck arrivals today'; ?></div>
            </div>
            <div class="placeholder">
                <div class="section-title">Permanent Customers</div>
                <div><?php echo (mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM customers WHERE type='permanent'"))['count'] > 0) ? 'Customers registered' : 'No permanent customers registered'; ?></div>
            </div>
        </div>
        <?php include 'footer.php'; ?>
    </div>
</body>
</html>
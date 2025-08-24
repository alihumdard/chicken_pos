<?php
include 'config.php';
include 'header.php';

$start_date = isset($_POST['start_date']) ? mysqli_real_escape_string($con, $_POST['start_date']) : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_POST['end_date']) ? mysqli_real_escape_string($con, $_POST['end_date']) : $today;

// Calculations for the selected period
$total_revenue = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(revenue) as sum FROM sales WHERE date BETWEEN '$start_date' AND '$end_date'"))['sum'] ?? 0;
$purchase_cost = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(quantity * price_per_kg) as sum FROM truck_arrivals WHERE date BETWEEN '$start_date' AND '$end_date'"))['sum'] ?? 0;
$gross_profit = $total_revenue - $purchase_cost;
$remaining_stock = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(ta.quantity) - SUM(s.quantity) as remaining FROM truck_arrivals ta LEFT JOIN sales s ON ta.date = s.date WHERE ta.date BETWEEN '$start_date' AND '$end_date'"))['remaining'] ?? 0;
$profit_margin = ($purchase_cost > 0) ? ($gross_profit / $purchase_cost * 100) : 0;

// Supply Chain
$truck_arrivals_count = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM truck_arrivals WHERE date BETWEEN '$start_date' AND '$end_date'"))['count'];
$chicken_received = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(quantity) as sum FROM truck_arrivals WHERE date BETWEEN '$start_date' AND '$end_date'"))['sum'] ?? 0;
$avg_cost_kg = ($chicken_received > 0) ? ($purchase_cost / $chicken_received) : 0;

// Sales Analysis
$truck_sales_qty = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(quantity) as sum FROM sales WHERE type='truck' AND date BETWEEN '$start_date' AND '$end_date'"))['sum'] ?? 0;
$shop_sales_qty = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(quantity) as sum FROM sales WHERE type='shop' AND date BETWEEN '$start_date' AND '$end_date'"))['sum'] ?? 0;
$truck_revenue = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(revenue) as sum FROM sales WHERE type='truck' AND date BETWEEN '$start_date' AND '$end_date'"))['sum'] ?? 0;
$shop_revenue = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(revenue) as sum FROM sales WHERE type='shop' AND date BETWEEN '$start_date' AND '$end_date'"))['sum'] ?? 0;
?>
    <div class="section-title">Reports & Analytics</div>
    <div class="sub-title">Comprehensive business insights and data export</div>
    <div class="buttons">
        <a href="index.php" class="back">Back to Home</a>
    </div>
    <form method="POST" style="background-color: white; padding: 10px; border-radius: 10px; margin-bottom: 20px;">
        <label>Report Period</label>
        <input type="date" name="start_date" value="<?php echo $start_date; ?>">
        <input type="date" name="end_date" value="<?php echo $end_date; ?>">
        <button type="submit" class="submit">Generate Report</button>
    </form>
    <div class="stats">
        <div class="stat-box blue">
            <div class="stat-value">$<?php echo number_format($total_revenue, 2); ?></div>
            <div class="stat-label">Total Revenue</div>
        </div>
        <div class="stat-box orange">
            <div class="stat-value">$<?php echo number_format($gross_profit, 2); ?></div>
            <div class="stat-label">Gross Profit</div>
        </div>
        <div class="stat-box green">
            <div class="stat-value"><?php echo number_format($remaining_stock, 2); ?> kg</div>
            <div class="stat-label">Remaining Stock</div>
        </div>
        <div class="stat-box yellow">
            <div class="stat-value"><?php echo number_format($profit_margin, 2); ?>%</div>
            <div class="stat-label">Profit Margin</div>
        </div>
    </div>
    <div class="stats">
        <div class="placeholder" style="flex: 1;">
            <div class="section-title">Supply Chain Analysis</div>
            <ul style="list-style: none; padding: 0;">
                <li>Truck Arrivals: <?php echo $truck_arrivals_count; ?></li>
                <li>Chicken Received: <?php echo number_format($chicken_received, 2); ?> kg</li>
                <li>Purchase Cost: $<?php echo number_format($purchase_cost, 2); ?></li>
                <li>Average Cost/kg: $<?php echo number_format($avg_cost_kg, 2); ?></li>
            </ul>
        </div>
        <div class="placeholder" style="flex: 1;">
            <div class="section-title">Sales Analysis</div>
            <ul style="list-style: none; padding: 0;">
                <li>Truck Sales: <?php echo number_format($truck_sales_qty, 2); ?> kg</li>
                <li>Shop Sales: <?php echo number_format($shop_sales_qty, 2); ?> kg</li>
                <li>Truck Revenue: $<?php echo number_format($truck_revenue, 2); ?></li>
                <li>Shop Revenue: $<?php echo number_format($shop_revenue, 2); ?></li>
            </ul>
        </div>
    </div>
    <?php if (isset($_POST['start_date'])): ?>
        <div class="placeholder">
            <div class="section-title">Detailed Report Table</div>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Truck Arrivals</th>
                        <th>Received (kg)</th>
                        <th>Cost ($)</th>
                        <th>Sales (kg)</th>
                        <th>Revenue ($)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = mysqli_query($con, "SELECT ta.date, COUNT(ta.id) as arrivals, SUM(ta.quantity) as received, SUM(ta.quantity * ta.price_per_kg) as cost, SUM(s.quantity) as sold, SUM(s.revenue) as revenue 
                                                  FROM truck_arrivals ta LEFT JOIN sales s ON ta.date = s.date 
                                                  WHERE ta.date BETWEEN '$start_date' AND '$end_date' GROUP BY ta.date");
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr><td>{$row['date']}</td><td>{$row['arrivals']}</td><td>" . number_format($row['received'], 2) . "</td><td>" . number_format($row['cost'], 2) . "</td><td>" . number_format($row['sold'], 2) . "</td><td>" . number_format($row['revenue'], 2) . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php include 'footer.php'; ?>
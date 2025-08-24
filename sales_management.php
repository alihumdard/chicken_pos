<?php
include 'config.php';
include 'header.php';

if (isset($_POST['record_sale'])) {
    $date = mysqli_real_escape_string($con, $_POST['date']);
    $type = mysqli_real_escape_string($con, $_POST['type']);
    $quantity = mysqli_real_escape_string($con, $_POST['quantity']);
    $revenue = mysqli_real_escape_string($con, $_POST['revenue']);
    $query = "INSERT INTO sales (`date`, `type`, quantity, revenue) VALUES ('$date', '$type', '$quantity', '$revenue')";
    mysqli_query($con, $query);
}

// Calculations
$truck_sales = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM sales WHERE type='truck' AND date='$today'"))['count'];
$shop_sales = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(quantity) as sum FROM sales WHERE type='shop' AND date='$today'"))['sum'] ?? 0;
$truck_revenue = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(revenue) as sum FROM sales WHERE type='truck' AND date='$today'"))['sum'] ?? 0;
$shop_revenue = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(revenue) as sum FROM sales WHERE type='shop' AND date='$today'"))['sum'] ?? 0;

// Fetch recent sales (last 5 for example)
$recent_sales = mysqli_query($con, "SELECT * FROM sales ORDER BY id DESC LIMIT 5");
?>
    <div class="section-title">Sales Management</div>
    <div class="sub-title">Track sales and direct shop sales</div>
    <div class="buttons">
        <a href="index.php" class="back">Back to Home</a>
        <button onclick="document.getElementById('addModal').style.display='flex'">Record Sale</button>
    </div>
    <div class="stats">
        <div class="stat-box blue">
            <div class="stat-value"><?php echo $truck_sales; ?></div>
            <div class="stat-label">Truck Sales</div>
        </div>
        <div class="stat-box green">
            <div class="stat-value"><?php echo number_format($shop_sales, 2); ?> kg</div>
            <div class="stat-label">Shop Sales</div>
        </div>
        <div class="stat-box orange">
            <div class="stat-value">$<?php echo number_format($truck_revenue, 2); ?></div>
            <div class="stat-label">Truck Revenue</div>
        </div>
        <div class="stat-box purple">
            <div class="stat-value">$<?php echo number_format($shop_revenue, 2); ?></div>
            <div class="stat-label">Shop Revenue</div>
        </div>
    </div>
    <div class="placeholder">
        <div class="section-title">Recent Sales</div>
        <?php if (mysqli_num_rows($recent_sales) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Quantity (kg)</th>
                        <th>Revenue ($)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($recent_sales)): ?>
                        <tr>
                            <td><?php echo $row['date']; ?></td>
                            <td><?php echo ucfirst($row['type']); ?></td>
                            <td><?php echo number_format($row['quantity'], 2); ?></td>
                            <td><?php echo number_format($row['revenue'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div>No sales recorded yet</div>
        <?php endif; ?>
    </div>

    <!-- Record Sale Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <h2>Record Sale</h2>
            <form method="POST">
                <label>Date</label>
                <input type="date" name="date" value="<?php echo $today; ?>" required>
                <label>Type</label>
                <select name="type" required>
                    <option value="truck">Truck</option>
                    <option value="shop">Shop</option>
                </select>
                <label>Quantity (kg)</label>
                <input type="number" step="0.01" name="quantity" value="0.00" required>
                <label>Revenue ($)</label>
                <input type="number" step="0.01" name="revenue" value="0.00" required>
                <div class="buttons">
                    <button type="button" class="cancel" onclick="document.getElementById('addModal').style.display='none'">Cancel</button>
                    <button type="submit" name="record_sale" class="submit">Record Sale</button>
                </div>
            </form>
        </div>
    </div>
<?php include 'footer.php'; ?>
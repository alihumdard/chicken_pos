<?php
include 'config.php';
include 'header.php';

if (isset($_POST['add_customer'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $type = mysqli_real_escape_string($con, $_POST['type']);
    $credit = mysqli_real_escape_string($con, $_POST['credit']);
    $query = "INSERT INTO customers (`name`, `type`, credit) VALUES ('$name', '$type', '$credit')";
    mysqli_query($con, $query);
}

// Calculations
$total_customers = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM customers"))['count'];
$permanent_customers = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM customers WHERE type='permanent'"))['count'];
$total_credit = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(credit) as sum FROM customers"))['sum'] ?? 0;

// Fetch recent customers (last 5 for example)
$recent_permanent = mysqli_query($con, "SELECT * FROM customers WHERE type='permanent' ORDER BY id DESC LIMIT 5");
$recent_regular = mysqli_query($con, "SELECT * FROM customers WHERE type='regular' ORDER BY id DESC LIMIT 5");
?>
    <div class="section-title">Customer Management</div>
    <div class="sub-title">Manage permanent and regular customers</div>
    <div class="buttons">
        <a href="index.php" class="back">Back to Home</a>
        <button onclick="document.getElementById('addModal').style.display='flex'">Add Customer</button>
    </div>
    <div class="stats">
        <div class="stat-box blue">
            <div class="stat-value"><?php echo $total_customers; ?></div>
            <div class="stat-label">Total Customers</div>
        </div>
        <div class="stat-box green">
            <div class="stat-value"><?php echo $permanent_customers; ?></div>
            <div class="stat-label">Permanent Customers</div>
        </div>
        <div class="stat-box orange">
            <div class="stat-value">$<?php echo number_format($total_credit, 2); ?></div>
            <div class="stat-label">Total Credit</div>
        </div>
    </div>
    <div class="placeholder">
        <div class="section-title">Permanent Customers</div>
        <?php if (mysqli_num_rows($recent_permanent) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Credit ($)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($recent_permanent)): ?>
                        <tr>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo number_format($row['credit'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div>No permanent customers registered</div>
        <?php endif; ?>
    </div>
    <div class="placeholder">
        <div class="section-title">Regular Customers</div>
        <?php if (mysqli_num_rows($recent_regular) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Credit ($)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($recent_regular)): ?>
                        <tr>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo number_format($row['credit'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div>No regular customers registered</div>
        <?php endif; ?>
    </div>

    <!-- Add Customer Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <h2>Add Customer</h2>
            <form method="POST">
                <label>Name</label>
                <input type="text" name="name" placeholder="Customer name" required>
                <label>Type</label>
                <select name="type" required>
                    <option value="permanent">Permanent</option>
                    <option value="regular">Regular</option>
                </select>
                <label>Credit ($)</label>
                <input type="number" step="0.01" name="credit" value="0.00" required>
                <div class="buttons">
                    <button type="button" class="cancel" onclick="document.getElementById('addModal').style.display='none'">Cancel</button>
                    <button type="submit" name="add_customer" class="submit">Add Customer</button>
                </div>
            </form>
        </div>
    </div>
<?php include 'footer.php'; ?>
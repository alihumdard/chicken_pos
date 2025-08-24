<?php
include 'config.php';
include 'header.php';

if (isset($_POST['add_arrival'])) {
    $date = mysqli_real_escape_string($con, $_POST['date']);
    $truck_number = mysqli_real_escape_string($con, $_POST['truck_number']);
    $quantity = mysqli_real_escape_string($con, $_POST['quantity']);
    $price_per_kg = mysqli_real_escape_string($con, $_POST['price_per_kg']);
    $supplier = mysqli_real_escape_string($con, $_POST['supplier']);
    $query = "INSERT INTO truck_arrivals (`date`, truck_number, quantity, price_per_kg, supplier) 
              VALUES ('$date', '$truck_number', '$quantity', '$price_per_kg', '$supplier')";
    mysqli_query($con, $query);
}

// Calculations
$trucks_today = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM truck_arrivals WHERE date='$today'"))['count'];
$total_quantity = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(quantity) as sum FROM truck_arrivals WHERE date='$today'"))['sum'] ?? 0;
$total_cost = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(quantity * price_per_kg) as sum FROM truck_arrivals WHERE date='$today'"))['sum'] ?? 0;

// Fetch recent arrivals (last 5 for example)
$recent_arrivals = mysqli_query($con, "SELECT * FROM truck_arrivals ORDER BY id DESC LIMIT 5");
?>
    <div class="section-title">Truck Arrivals</div>
    <div class="sub-title">Track incoming chicken deliveries</div>
    <div class="buttons">
        <a href="index.php" class="back">Back to Home</a>
        <button onclick="document.getElementById('addModal').style.display='flex'">Add Arrival</button>
    </div>
    <div class="stats">
        <div class="stat-box blue">
            <div class="stat-value"><?php echo $trucks_today; ?></div>
            <div class="stat-label">Trucks Today</div>
        </div>
        <div class="stat-box green">
            <div class="stat-value"><?php echo $total_quantity; ?> kg</div>
            <div class="stat-label">Total Quantity</div>
        </div>
        <div class="stat-box orange">
            <div class="stat-value">$<?php echo number_format($total_cost, 2); ?></div>
            <div class="stat-label">Total Cost</div>
        </div>
    </div>
    <div class="placeholder">
        <div class="section-title">Recent Arrivals</div>
        <?php if (mysqli_num_rows($recent_arrivals) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Truck Number</th>
                        <th>Quantity (kg)</th>
                        <th>Price per Kg ($)</th>
                        <th>Supplier</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($recent_arrivals)): ?>
                        <tr>
                            <td><?php echo $row['date']; ?></td>
                            <td><?php echo $row['truck_number']; ?></td>
                            <td><?php echo $row['quantity']; ?></td>
                            <td><?php echo number_format($row['price_per_kg'], 2); ?></td>
                            <td><?php echo $row['supplier']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div>No truck arrivals recorded yet</div>
        <?php endif; ?>
    </div>

    <!-- Add Arrival Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <h2>Add Truck Arrival</h2>
            <form method="POST">
                <label>Date</label>
                <input type="date" name="date" value="<?php echo $today; ?>" required>
                <label>Truck Number</label>
                <input type="text" name="truck_number" placeholder="e.g., TRK-001" required>
                <label>Quantity (kg)</label>
                <input type="number" step="0.01" name="quantity" value="0.00" required>
                <label>Price per Kg ($)</label>
                <input type="number" step="0.01" name="price_per_kg" value="0.00" required>
                <label>Supplier</label>
                <input type="text" name="supplier" placeholder="Supplier name" required>
                <div class="buttons">
                    <button type="button" class="cancel" onclick="document.getElementById('addModal').style.display='none'">Cancel</button>
                    <button type="submit" name="add_arrival" class="submit">Add Arrival</button>
                </div>
            </form>
        </div>
    </div>
<?php include 'footer.php'; ?>
<?php

require_once 'config.php';

// Redirect to login page if not logged in or not a resident
if (!isset($_SESSION['resident_id']) || $_SESSION['resident_role'] !== 'user') {
    header('Location: logout.php');
    exit();
}

// Handle booking service
if (isset($_POST['book_service'])) {
    $id = trim($_POST['id']);

    // Check if the facility is available
    $stmt = $pdo->prepare('SELECT * FROM service WHERE id = ? AND booked_status = "available"');
    $stmt->execute([$id]);
    $facility = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$facility) {
        $errors[] = 'Selected service is not available for booking.';
    }

   // Perform booking if no errors
   if (empty($errors)) {
    // Add your logic here to insert booking details into the payment table
    $amount = $facility['amount'];
    $month = date('F'); // You may customize this logic based on your requirements

    $stmt = $pdo->prepare('INSERT INTO payment (resident_id, service_id, amount, month) VALUES (?, ?, ?, ?)');
    $stmt->execute([$_SESSION['resident_id'], $id, $amount, $month]);

    // Update the booked_status of the facility to "booked"
    $stmt = $pdo->prepare('UPDATE service SET booked_status = "booked" WHERE id = ?');
    $stmt->execute([$id]);

    $_SESSION['success'] = 'Service booked successfully';

    header('Location: book_service.php'); // Redirect to the same page after booking
    exit();
}
}

include('header.php');

?>

<div class="container-fluid px-4">
<h1 class="mt-4">Book Service</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="service.php">Service Management</a></li>
        <li class="breadcrumb-item active">Book Service</li>
    </ol>

    <div class="col-md-4">
        <?php
        if (isset($errors)) {
            foreach ($errors as $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
        }
        ?>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Book Service</h5>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="id">Select Servie</label>
                        <select id="id" name="id" class="form-control">
                            <?php
                            // Fetch available facilities
                            $stmt = $pdo->query('SELECT * FROM service WHERE booked_status = "available"');
                            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($services as $service) {
                                echo "<option value=\"" . $service['id'] . "\">" . $service['name'] . " - $" . $service['amount'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" name="book_service" class="btn btn-primary">Book Service</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php

include('footer.php');

?>

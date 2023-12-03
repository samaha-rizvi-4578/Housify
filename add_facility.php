<?php

require_once 'config.php';

if (!isset($_SESSION['resident_id']) || $_SESSION['resident_role'] !== 'admin') 
{
  	header('Location: logout.php');
  	exit();
}

if(isset($_POST['add_facility']))
{
    $name = trim($_POST['name']);
    $amount = trim($_POST['amount']);

    // Validate form fields
    if (empty($name)) {
        $errors[] = 'Facility Name is required';
    }
    if (empty($amount)) 
    {
      $errors[] = 'Please enter Facility Charges';
    } 

    // Insert visitor data if there are no validation errors
    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO facility (name, amount, booked_status) VALUES (?, ?, ?)');
        $stmt->execute([$name, $amount, 'available']);

        $_SESSION['success'] = 'New Facility Data has been Added';

        header('Location: facility.php');
        exit();
    }
}



include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Add Facility</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="facility.php">Facility Management</a></li>
        <li class="breadcrumb-item active">Add Facility</li>
    </ol>
	<div class="col-md-4">
		<?php

		if(isset($errors))
        {
            foreach ($errors as $error) 
            {
                echo "<div class='alert alert-danger'>$error</div>";
            }
        }

		?>
		<div class="card">
			<div class="card-header">
				<h5 class="card-title">Add Facility</h5>
			</div>
			<div class="card-body">
				<form method="post">
				  	<div class="mb-3">
				    	<label for="name">Facility Name</label>
				    	<input type="text" class="form-control" id="name" name="name" placeholder="Enter facility name">
				  	</div>
				  	<div class="mb-3">
				    	<label for="amount">Amount</label>
				    	<input type="number" id="amount" name="amount" class="form-control" step="0.01" placeholder="Enter facility charges">
				  	</div>
				  	<button type="submit" name="add_facility" class="btn btn-primary">Add Facility</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>
<?php

require_once 'config.php';

if (!isset($_SESSION['resident_id']) || $_SESSION['resident_role'] !== 'admin') 
{
  	header('Location: logout.php');
  	exit();
}

if(isset($_POST['edit_facility']))
{
    $name = trim($_POST['name']);
    $amount = trim($_POST['amount']);
    $id = $_POST['id'];
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
        $stmt = $pdo->prepare('UPDATE facility SET name = ? , amount = ? where id = ?');
        $stmt->execute([$name, $amount, $id]);

        $_SESSION['success'] = 'Facility Data has been Edited';

        header('Location: facility.php');
        exit();
    }
}

if(isset($_GET['id']))
{
	// Prepare a SELECT statement to retrieve the facility details
  	$stmt = $pdo->prepare("SELECT * FROM facility WHERE id = ? AND booked_status = 'available' ");
  	$stmt->execute([$_GET['id']]);

  	// Fetch the visitor's details from the database
  	$visitor = $stmt->fetch(PDO::FETCH_ASSOC);
}

include('header.php');

?>

<div class="container-fluid px-4">
<h1 class="mt-4">Edit Facility</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="facility.php">Facilities Management</a></li>
        <li class="breadcrumb-item active">Edit Facility</li>
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
				<h5 class="card-title">Edit Facility</h5>
			</div>
			<div class="card-body">
				<form method="post">
				  	<div class="mb-3">
				    	<label for="name">Facility Name</label>
				    	<input type="text" class="form-control" id="name" name="name" placeholder="Enter facility name" value="<?php echo (isset($facility['name'])) ? $facility['name'] : ''; ?>">
				  	</div>
				  	<div class="mb-3">
				    	<label for="amount">Amount</label>
				    	<input type="number" id="amount" name="amount" class="form-control" step="0.01" placeholder="Enter facility charges" value="<?php echo (isset($facility['amount'])) ? $facility['amount'] : ''; ?>">
				  	</div>
				  	<button type="submit" name="edit_facility" class="btn btn-primary">Edit Facility</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>
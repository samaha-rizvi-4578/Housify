<?php

require_once 'config.php';
if(isset($_POST['add_maintenance']))
{
	// Validate the form data
  	$resident_id = $_POST['resident_id'];
  	$month = $_POST['month'];
  	$amount = $_POST['amount'];

  	if (empty($resident_id)) 
  	{
	    $errors[] = 'Please Select Resident ID';
  	}
      if (empty($amount)) 
  	{
    	$errors[] = 'Please enter Bill Amount';
  	} 
      if (empty($month)) 
  	{
 	   $errors[] = 'Please enter Bill Month';
  	}

      $paid_amount = 0;
  	// If the form data is valid, update the user's password
  	if (empty($errors)) 
  	{  
      // Insert bill data into the database
	    $stmt = $pdo->prepare("INSERT INTO maintenance (resident_id, amount, month, paid_amount) VALUES (?, ?, ?, ?)");

	    $stmt->execute([$resident_id, $amount, $month, $paid_amount ]);

	    // get last inserted ID
		$id = $pdo->lastInsertId();

$resident_id = $pdo->query("SELECT id FROM resident WHERE id = '".$resident_id."'")->fetchColumn();

	    // insert notification data into notifications table
		$message = "New Maintenance bill added. Amount: ".$amount.", Month: ".$month."";
		
		$notification_link = 'maintenance_payment.php?id='.$id.'&action=notification';
		$stmt = $pdo->prepare("INSERT INTO notifications (resident_id, notification_type, event_id, message, link) VALUES (?, ?, ?, ?, ?)");
		$stmt->execute([$resident_id, 'Maintenance Bill', $id, $message, $notification_link]);

  		$_SESSION['success'] = 'New Maintenance Bill Data Added';

  		header('location:maintenance.php');
  		exit();
  	}
}

if (!isset($_SESSION['resident_id']) || $_SESSION['resident_role'] !== 'admin') 
{
  	header('Location: logout.php');
  	exit();
}

$sql = "SELECT id, name , ssn FROM resident ORDER BY id DESC";

$stmt = $pdo->prepare($sql);

$stmt->execute();

$residents = $stmt->fetchAll(PDO::FETCH_ASSOC);

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Add Maintenance Bill Data</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="maintenace.php">Maintenance Bills Management</a></li>
        <li class="breadcrumb-item active">Add Maintenance Bill Data</li>
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
				<h5 class="card-title">Add Maintenance Bill Data</h5>
			</div>
			<div class="card-body">
				<form method="post">
                <div class="mb-3">
				    	<label for="resident_id">Resident ID</label>
				    	<select name="resident_id" class="form-control">
				    		<option value="">Select Resident</option>
				    		<?php foreach($residents as $resident): ?>
				    		<option value="<?php echo $resident['id']; ?>"><?php echo $resident['name'] . ' - ' . $resident['ssn']; ?></option>
				    		<?php endforeach; ?>
				    	</select>
				  	</div>
				  	<div class="mb-3">
				    	<label for="amount">Amount</label>
				    	<input type="number" id="amount" name="amount" class="form-control" step="0.01">
				  	</div>
				  	<div class="mb-3">
				    	<label for="month">Month</label>
				    	<input type="month" id="month" name="month" class="form-control">
				  	</div>
				  	<button type="submit" name="add_maintenance" class="btn btn-primary">Add Maintenance Bill</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>
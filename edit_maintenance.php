<?php

require_once 'config.php';

if(isset($_POST['edit_maintenance']))
{
	// Validate the form data
    $resident_id = $_POST['resident_id'];
    $month = $_POST['month'];
    $amount = $_POST['amount'];
    $paid_amount = $_POST['paid_amount'];

    if (empty($resident_id)) 
    {
      $errors[] = 'Please Select Resident ID';
    }
    if (empty($amount)) 
    {
      $errors[] = 'Please enter Maintenance Bill Amount';
    } 
    if (empty($month)) 
    {
      $errors[] = 'Please enter Maintenance Bill Month';
    }
    if (empty($paid_amount)) 
    {
  	$errors[] = 'Please enter Paid Maintenance Bill Amount';
    } 
  	// If the form data is valid, update the user's password
  	if (empty($errors)) 
  	{
        $id = $_GET['id'];
  		// Insert user data into the database
	    $stmt = $pdo->prepare("UPDATE maintenance SET resident_id = ?, amount = ?, month = ?, paid_amount = ?  WHERE id = ?");

        $stmt->execute([$resident_id, $amount, $month, $paid_amount, $id]);

  		$_SESSION['success'] = 'Maintenance Bill Data has been edited';

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

if(isset($_GET['id']))
{
	$stmt = $pdo->prepare("SELECT * FROM maintenance WHERE id = ?");

	$stmt->execute([$_GET['id']]);

	$bill = $stmt->fetch(PDO::FETCH_ASSOC);
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Maintenance Bill Data</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="maintenance.php">Maintenance Bills Management</a></li>
        <li class="breadcrumb-item active">Edit Maintenance Bill Data</li>
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
				<h5 class="card-title">Edit Maintenance Bill Data</h5>
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
				    	<input type="number" id="amount" name="amount" class="form-control" step="0.01" value="<?php echo (isset($maintenance['amount'])) ? $maintenance['amount'] : ''; ?>">
				  	</div>
				  	<div class="mb-3">
				    	<label for="month">Month</label>
				    	<input type="month" id="month" name="month" class="form-control" value="<?php echo (isset($maintenance['month'])) ? $maintenance['month'] : ''; ?>">
				  	</div>
				  	
				  	<input type="hidden" name="id" value="<?php echo (isset($maintenance['id'])) ? $maintenance['id'] : ''; ?>" />
				  	<button type="submit" name="edit_maintenance" class="btn btn-primary">Edit Maintenance Bill</button>
				  	<script>
				  	$('#resident_id').val('<?php echo (isset($maintenance['resident_id'])) ? $resident['resident_id'] : ''; ?>');
				  	</script>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>
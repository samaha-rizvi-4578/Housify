<?php

require_once 'config.php';

if (!isset($_SESSION['resident_id']) || ($_SESSION['resident_role'] !== 'admin' && $_SESSION['resident_role'] !== 'user')) 
{
  	header('Location: logout.php');
  	exit();
}

if(isset($_GET['id']))
{
	$sql = '
	SELECT visitor.id, house.house_number, house.block_number, visitor.name, visitor.ssn, visitor.reason, visitor.in_datetime, visitor.out_datetime, visitor.is_in_out FROM visitor 
		JOIN house ON house.id = visitor.house_id 
		WHERE visitor.id = ? 
	';
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$_GET['id']]);
	$visitor = $stmt->fetch(PDO::FETCH_ASSOC);
}

if(isset($_POST['out_visitor']))
{
	echo '<pre>';
	print_r($_POST);
	echo '</pre>';
	$out_datetime = $_POST['out_datetime'];
	$id = $_POST['id'];

  	if (empty($out_datetime)) 
  	{
	    $errors[] = 'Please Select Out Date and Time';
  	}

  	// If the form data is valid, update the user's password
  	if (empty($errors)) 
  	{
  		$sql = "UPDATE visitor SET  out_datetime = ?, is_in_out = ? WHERE id = ?";

		$pdo->prepare($sql)->execute([$out_datetime, 'out', $id]);

  		$_SESSION['success'] = 'Visitor Outer Remark has been added';

  		header('location:visitor.php');
  		exit();
  	}
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">View Visitor</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="visitor.php">Visitor Management</a></li>
        <li class="breadcrumb-item active">View Visitor</li>
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
				<h5 class="card-title">View Visitor Details</h5>
			</div>
			<div class="card-body">
				<div class="row mb-3">
					<div class="col-md-4"><b>House Number</b></div>
					<div class="col-md-8"><?php echo $visitor['block_number'] . ' - ' . $visitor['house_number']; ?></div>
				</div>
				<div class="row mb-3">
					<div class="col-md-4"><b>Visitor Name</b></div>
					<div class="col-md-8"><?php echo $visitor['name']; ?></div>
				</div>
				<div class="row mb-3">
					<div class="col-md-4"><b>Visitor SSN</b></div>
					<div class="col-md-8"><?php echo $visitor['ssn']; ?></div>
				</div>
				<div class="row mb-3">
					<div class="col-md-4"><b>Reason to Meet</b></div>
					<div class="col-md-8"><?php echo $visitor['reason']; ?></div>
				</div>
				<div class="row mb-3">
					<div class="col-md-4"><b>In Date & Time</b></div>
					<div class="col-md-8"><?php echo $visitor['in_datetime']; ?></div>
				</div>

				<?php

				if(is_null($visitor['out_datetime']))
				{
				?>
				<div class="row mb-3">
					<div class="col-md-4"><b>Status</b></div>
					<div class="col-md-8"><span class="badge bg-danger">In</span></div>
				</div>
				<form method="post">
					<div class="mb-3">
				    	<label for="out_datetime">Out Date/Time</label>
				    	<input type="datetime-local" id="out_datetime" name="out_datetime" class="form-control">
				  	</div>
				  	<input type="hidden" name="id" value="<?php echo $visitor['id']; ?>" />
				  	<button type="submit" name="out_visitor" class="btn btn-primary">Submit</button>
				</form>
				<?php
				}
				else
				{
				?>
				<div class="row mb-3">
					<div class="col-md-4"><b>Out Date & Time</b></div>
					<div class="col-md-8"><?php echo $visitor['out_datetime']; ?></div>
				</div>
				<div class="row mb-3">
					<div class="col-md-4"><b>Status</b></div>
					<div class="col-md-8"><span class="badge bg-success">Out</span></div>
				</div>
				<?php
				}
				?>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>
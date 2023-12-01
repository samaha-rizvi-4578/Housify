<?php

require_once 'config.php';

if(isset($_POST['add_house']))
{
	// Validate the form data
  	$house_number = $_POST['house_number'];
  	$street_name = $_POST['street_name'];
  	$block_number = $_POST['block_number'];
  	$created_at = date('Y-m-d H:i:s');

  	if (empty($house_number)) 
  	{
	    $errors[] = 'house Number is required';
  	}
  	if (empty($street_name)) 
  	{
	    $errors[] = 'Street Name is required';
  	}
  	if (empty($block_number)) 
  	{
	    $errors[] = 'Block Number is required';
  	}

  	// If the form data is valid, update the user's password
  	if (empty($errors)) 
  	{
  		$sql = "INSERT INTO house (house_number, street_name, block_number, created_at) VALUES (?, ?, ?, ?)";

  		$pdo->prepare($sql)->execute([$house_number, $street_name, $block_number,  $created_at]);

  		$_SESSION['success'] = 'New house Data Added';

  		header('location:house.php');
  		exit();
  	}
}

if (!isset($_SESSION['resident_id']) || $_SESSION['resident_role'] !== 'admin') 
{
  	header('Location: logout.php');
  	exit();
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Add House</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="flats.php">House Management</a></li>
        <li class="breadcrumb-item active">Add House Management</li>
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
				<h5 class="card-title">Add House</h5>
			</div>
			<div class="card-body">
				<form id="add-house-form" method="POST">
				  	<div class="mb-3">
				    	<label for="house-number" class="form-label">house Number</label>
				    	<input type="text" class="form-control" id="house-number" name="house_number">
				  	</div>
				  	<div class="mb-3">
				    	<label for="street-name" class="form-label">Street Name</label>
				    	<input type="text" class="form-control" id="street-name" name="street_name">
				  	</div>
				  	<div class="mb-3">
				    	<label for="block-number" class="form-label">Block Number</label>
				    	<input type="text" class="form-control" id="block-number" name="block_number">
				  	</div>
				  	<button type="submit" name="add_house" class="btn btn-primary">Add House</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>
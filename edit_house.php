<?php

require_once 'config.php';

if (!isset($_SESSION['resident_id']) || $_SESSION['resident_role'] !== 'admin') 
{
  	header('Location: logout.php');
  	exit();
}

if(isset($_POST['edit_house']))
{
	// Validate the form data
  	$house_number = $_POST['house_number'];
  	$street_name = $_POST['street_name'];
  	$block_number = $_POST['block_number'];
  	$id = $_POST['id'];

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
  		$sql = "UPDATE house SET house_number = ?, street_name = ?, block_number = ? WHERE id = ?";

  		$pdo->prepare($sql)->execute([$house_number, $street_name, $block_number, $id]);

  		$_SESSION['success'] = 'House Data Edit';

  		header('location:house.php');
  		exit();
  	}
}

if(isset($_GET['id']))
{
	// Prepare a SELECT statement to retrieve the flats's details
  	$stmt = $pdo->prepare("SELECT * FROM house WHERE id = ?");
  	$stmt->execute([$_GET['id']]);

  	// Fetch the user's details from the database
  	$flat = $stmt->fetch(PDO::FETCH_ASSOC);
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Edit House</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="house.php">House Management</a></li>
        <li class="breadcrumb-item active">Edit House Management</li>
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
				<h5 class="card-title">Edit House Data</h5>
			</div>
			<div class="card-body">
				<form id="add-house-form" method="POST">
				  	<div class="mb-3">
				    	<label for="house-number" class="form-label">House Number</label>
				    	<input type="text" class="form-control" id="house-number" name="house_number" value="<?php echo (isset($house['house_number'])) ? $house['house_number'] : ''; ?>">
				  	</div>
				  	<div class="mb-3">
				    	<label for="street-name" class="form-label">Street Name</label>
				    	<input type="text" class="form-control" id="street-name" name="street_name" value="<?php echo (isset($house['street_name'])) ? $house['street_name'] : ''; ?>">
				  	</div>
				  	<div class="mb-3">
				    	<label for="block-number" class="form-label">Block Number</label>
				    	<input type="text" class="form-control" id="block-number" name="block_number" value="<?php echo (isset($house['block_number'])) ? $house['block_number'] : ''; ?>">
				  	</div>
				  	<input type="hidden" name="id" value="<?php echo (isset($house['id'])) ? $house['id'] : ''; ?>" />
				  	<button type="submit" name="edit_house" class="btn btn-primary">Edit House</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>
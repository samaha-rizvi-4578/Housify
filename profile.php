<?php

require_once 'config.php';

if (!isset($_SESSION['resident_id']) || $_SESSION['resident_role'] !== 'admin') 
{
  	header('Location: logout.php');
  	exit();
}
else
{
	// Check if the user ID is set in the query string
	if (isset($_SESSION['resident_id'])) 
	{
  		// Retrieve the user ID from the query string
  		$resident_id = $_SESSION['resident_id'];

  		// Prepare a SELECT statement to retrieve the user's details
  		$stmt = $pdo->prepare("SELECT * FROM resident WHERE id = ?");
  		$stmt->execute([$resident_id]);

  		// Fetch the user's details from the database
  		$resident = $stmt->fetch(PDO::FETCH_ASSOC);
	}
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Profile</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">View Profile</li>
    </ol>
	<div class="col-md-4">
		<?php

		if(isset($_SESSION['success']))
		{
			echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';

			unset($_SESSION['success']);
		}

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
				<h5 class="card-title">View Profile</h5>
			</div>
			<div class="card-body">
				<div class="mb-3">
					<label for="name" class="form-label">Name</label>
					<p><?php echo $resident['name']; ?></p>
				</div>
				<div class="mb-3">
					<label for="ssn" class="form-label">SSN</label>
					<p><?php echo $resident['ssn']; ?></p>
				</div>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>

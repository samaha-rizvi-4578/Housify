<?php

require_once 'config.php';

if (!isset($_SESSION['resident_id']) || ($_SESSION['resident_role'] !== 'admin' && $_SESSION['resident_role'] !== 'user')) 
{
  	header('Location: logout.php');
  	exit();
}

if(isset($_POST['edit_complaint']))
{
	// Validate the form data

	$comment = $_POST['comment'];
	$id = $_POST['id'];

  	if (empty($comment)) 
  	{
	    $errors[] = 'Complaints Description is required';
  	}

  	// If the form data is valid, update the user's password
  	if (empty($errors)) 
  	{   
        $id = $_GET['id'];
  		$sql = "UPDATE complaints SET comment = ? WHERE id = ?";

  		$pdo->prepare($sql)->execute([$comment, $id]);

  		$_SESSION['success'] = 'Your Complaints has been edited';

  		header('location:complaints.php');
  		exit();
  	}
}

if(isset($_GET['id']))
{
	$stmt = $pdo->prepare('SELECT * FROM complaints WHERE id = ?');
	$stmt->execute([$_GET['id']]);
	$complaint = $stmt->fetch(PDO::FETCH_ASSOC);
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Complaints</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="complaints.php">Complaints Management</a></li>
        <li class="breadcrumb-item active">Edit Complaints</li>
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
				<h5 class="card-title">Edit Complaint</h5>
			</div>
			<div class="card-body">
				<form id="add-flat-form" method="POST">
				  	<div class="mb-3">
				    	<label for="comment" class="form-label">Complaint Description</label>
				    	<textarea name="comment" id="comment" class="form-control" rows="5" placeholder="Enter Commment about complaint"><?php echo (isset($complaints['comment'])) ? $complaints['comment'] : ''; ?></textarea>
				  	</div>
				  	<input type="hidden" name="id" value="<?php echo (isset($complaints['id'])) ? $complaints['id'] : ''; ?>" />
				  	<button type="submit" name="edit_complaint" class="btn btn-primary">Edit Complaint</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>
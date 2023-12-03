<?php

require_once 'config.php';

if(isset($_POST['add_complaint']))
{
	// Validate the form data

	// $stmt = $pdo->prepare('SELECT house_id FROM resident WHERE resident_id = ?');
	// $stmt->execute([$_SESSION['resident_id']]);
	
	$resident_id = $_SESSION['resident_id'];
	$comment = $_POST['comment'];
  	$created_at = date('Y-m-d H:i:s');

  	if (empty($comment)) 
  	{
	    $errors[] = 'Complaints Description is required';
  	}

  	// If the form data is valid, update the user's password
  	if (empty($errors)) 
  	{
  		$sql = "INSERT INTO complaints (resident_id,  comment, status, created_at) VALUES (?, ?, ?, ?)";

  		$pdo->prepare($sql)->execute([$resident_id, $comment, 'pending', $created_at]);

  		// get last inserted ID
		$complaint_id = $pdo->lastInsertId();

	    $admin_id = $pdo->query("SELECT id FROM resident WHERE role = 'admin'")->fetchColumn();

	    // $flat_number = $pdo->query("SELECT CONCAT(block_number, ' - ', flat_number) AS flat_number FROM flats WHERE id = '".$flat_id['flat_id']."'")->fetchColumn();

	    // insert notification data into notifications table
		$message = "New Complaint added by resident ".$resident_id.".";
		
		$notification_link = 'view_complaint.php?id='.$complaint_id.'&action=notification';
		$stmt = $pdo->prepare("INSERT INTO notifications (resident_id, notification_type, event_id, message, link) VALUES (?, ?, ?, ?, ?)");
		$stmt->execute([$admin_id, 'Complaint', $complaint_id, $message, $notification_link]);

  		$_SESSION['success'] = 'Your Complaints has been submitted';

  		header('location:complaints.php');
  		exit();
  	}
}

if (!isset($_SESSION['resident_id']) || ($_SESSION['resident_role'] !== 'admin' && $_SESSION['resident_role'] !== 'user')) 
{
  	header('Location: logout.php');
  	exit();
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Add Complaints</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="complaints.php">Complaints Management</a></li>
        <li class="breadcrumb-item active">Add Complaints</li>
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
				<h5 class="card-title">Add Complaint</h5>
			</div>
			<div class="card-body">
				<form id="add-flat-form" method="POST">
				  	<div class="mb-3">
				    	<label for="comment" class="form-label">Complaint Description</label>
				    	<textarea name="comment" id="comment" class="form-control" rows="5" placeholder="Enter Comments about complaint"></textarea>
				  	</div>
				  	<button type="submit" name="add_complaint" class="btn btn-primary">Add Complaint</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>
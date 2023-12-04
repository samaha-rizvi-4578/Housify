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
	SELECT complaints.id, complaints.resident_id, resident.name, complaints.comment, complaints.status, complaints.created_at FROM complaints
		JOIN resident ON resident.id = complaints.resident_id  
		WHERE complaints.id = ?
	';
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$_GET['id']]);
	$complaint = $stmt->fetch(PDO::FETCH_ASSOC);

	if(isset($_GET['action']) && $_GET['action'] == 'notification')
	{
		if($_SESSION['resident_role'] == 'admin')
		{
			$notification_type = 'Complaint';
		}
		else
		{
			$notification_type = 'Complaint Status';
		}
		$stmt = $pdo->prepare("UPDATE notifications SET read_status = 'read' WHERE resident_id = '".$_SESSION['resident_id']."' AND notification_type = '".$notification_type."' AND event_id = '".$_GET['id']."'");

		$stmt->execute();
	}
}

if(isset($_POST['process_complaint']))
{
	$status = $_POST['status'];
	$id = $_POST['id'];

  	if (empty($status)) 
  	{
	    $errors[] = 'Please Select Complaint Status';
  	}

  	// If the form data is valid, update the user's password
  	if (empty($errors)) 
  	{

		$sql = "UPDATE complaints SET  status = ? WHERE id = ?";

		$pdo->prepare($sql)->execute([$status, $id]);

	    // insert notification data into notifications table
		$message = "Your Complaint for ".$_POST['hidden_description']." has been processed by Admin.";
		
		$notification_link = 'view_complaint.php?id='.$id.'&action=notification';
		$stmt = $pdo->prepare("INSERT INTO notifications (resident_id, notification_type, event_id, message, link) VALUES (?, ?, ?, ?, ?)");
		$stmt->execute([$_POST["hidden_resident_id"], 'Complaint Status', $id, $message, $notification_link]);

  		$_SESSION['success'] = 'Complaint has been processed';

  		header('location:complaints.php');
  		exit();
  	}
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">View Complaints</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="complaints.php">Complaints Management</a></li>
        <li class="breadcrumb-item active">View Complaints</li>
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
				<h5 class="card-title">View Complaint</h5>
			</div>
			<div class="card-body">
				<div class="row mb-3">
					<div class="col-md-4"><b>Resident Name</b></div>
					<div class="col-md-8"><?php echo (isset($complaint['name'])) ? $complaint['name'] : 'NA'; ?></div>
				</div>
				<div class="row mb-3">
					<div class="col-md-4"><b>Complaints Details</b></div>
					<div class="col-md-8"><?php echo (isset($complaint['comment'])) ? $complaint['comment'] : 'NA'; ?></div>
				</div>
				<?php

				$status = '';

				$tstatus = '';

				if(isset($complaint['status']))
				{
					if($complaint['status'] == 'pending')
					{
						$tstatus = 'pending';

						$status = '<span class="badge bg-primary">Pending</span>';
					}
					if($complaint['status'] == 'in_progress')
					{
						$tstatus = 'in_progress';

						$status = '<span class="badge bg-warning">In Progress</span>';
					}
					if($complaint['status'] == 'resolved')
					{
						$tstatus = 'resolved';

						$status = '<span class="badge bg-success">Resolved</span>';
					}
				}
				?>
				<div class="row mb-3">
					<div class="col-md-4"><b>Status</b></div>
					<div class="col-md-8"><?php echo $status; ?></div>
				</div>

				<?php


				if($_SESSION['resident_role'] == 'admin')
				{
					if($tstatus != 'resolved')
					{
				?>

				<form method="post">
					<div class="mb-3">
						<label><b>Complaint Status</b></label>
						<select name="status" class="form-control">
							<option value="">Select Status</option>
							<option value="in_progress">In Progress</option>
							<option value="resolved">Resolved</option>
						</select>
					</div>
					<div class="mb-3">
						<input type="hidden" name="id" value="<?php echo isset($complaint['id']) ? $complaint['id'] : ''; ?>" />
						<input type="hidden" name="hidden_comment" value="<?php echo isset($complaint['comment']) ? $complaint['comment'] : ''; ?>" />
						<input type="hidden" name="hidden_resident_id" value="<?php echo isset($complaint['resident_id']) ? $complaint['resident_id'] : ''; ?>" />
						<input type="submit" name="process_complaint" class="btn btn-primary" value="Submit" />
					</div>
				</form>

				<?php
					}
				}

				?>

			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>
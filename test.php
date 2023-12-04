<?php

require_once 'config.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'user'))
{
  	header('Location: logout.php');
  	exit();
}

if(isset($_POST['payment_bill']))
{
	// Validate the form data
  	$paid_amount = $_POST['paid_amount'];
  	$paid_date = $_POST['paid_date'];
  	$payment_method = $_POST['payment_method'];
  	$id = $_POST['id'];

  	if (empty($paid_date)) 
  	{
	    $errors[] = 'Please Select Bill Payment Date';
  	}
  	if (empty($paid_amount)) 
  	{
    	$errors[] = 'Please enter Bill Amount';
  	} 
  	else if (!is_numeric($paid_amount)) 
  	{
    	$errors[] = 'Amount must be a number';
  	}
  	else if($paid_amount != $_POST['hidden_amount'])
  	{
  		$errors[] = 'Amount not match, please enter ' . $_POST['hidden_amount'] . '';
  	}
  	if (empty($payment_method)) 
  	{
 	   $errors[] = 'Please Select Payment Method';
  	}

  	// If the form data is valid, update the user's password
  	if (empty($errors)) 
  	{
  		// Insert user data into the database
	    $stmt = $pdo->prepare("UPDATE bills SET paid_date = ?, payment_method = ?, paid_amount = ? WHERE id = ?");

	    $stmt->execute([$paid_date, $payment_method, $paid_amount, $id]);

	    $admin_id = $pdo->query("SELECT id FROM users WHERE role = 'admin'")->fetchColumn();

	    // insert notification data into notifications table
		$message = "Bill Payment Done by Flat Number - ".$_POST['flat_number']." for ".$_POST['hidden_bill_title'].".";
		
		$notification_link = 'bill_payment.php?id='.$id.'&action=notification';
		$stmt = $pdo->prepare("INSERT INTO notifications (user_id, notiification_type, event_id, message, link) VALUES (?, ?, ?, ?, ?)");
		$stmt->execute([$admin_id, 'Bill Payment', $id, $message, $notification_link]);

  		$_SESSION['success'] = 'Bill Payment has been done';

  		header('location:bills.php');
  		exit();
  	}
}

if(isset($_GET['id']))
{
	$stmt = $pdo->prepare("SELECT flats.flat_number, flats.block_number, bills.bill_title, bills.id, bills.amount, bills.month, bills.paid_date, bills.paid_amount, bills.payment_method FROM bills INNER JOIN flats ON flats.id = bills.flat_id WHERE bills.id = ?");

	$stmt->execute([$_GET['id']]);

	$bill = $stmt->fetch(PDO::FETCH_ASSOC);

	if(isset($_GET['action']) && $_GET['action'] == 'notification')
	{
		if($_SESSION['user_role'] == 'admin')
		{
			$notification_type = 'Bill Payment';
		}
		else
		{
			$notification_type = 'Bill';
		}
		$stmt = $pdo->prepare("UPDATE notifications SET read_status = 'read' WHERE user_id = '".$_SESSION['user_id']."' AND notiification_type = '".$notification_type."' AND event_id = '".$_GET['id']."'");

		$stmt->execute();
	}
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Bill Payment</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="bills.php">Bills Management</a></li>
        <li class="breadcrumb-item active">Bill Payment</li>
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
				<h5 class="card-title">Bill Payment</h5>
			</div>
			<div class="card-body">
				<form method="post">
				  	<div class="mb-3">
				  		<div class="row">
				    		<div class="col-md-5"><b>Flat Number</b></div>
				    		<div class="col-md-7"><?php echo (isset($bill['flat_number'])) ? $bill['block_number'] . ' - ' . $bill['flat_number'] : ''; ?></div>
				    	</div>
				    	<div class="row">
				    		<div class="col-md-5"><b>Bill Details</b></div>
				    		<div class="col-md-7"><?php echo (isset($bill['bill_title'])) ? $bill['bill_title'] : ''; ?></div>
				    	</div>
				    	<div class="row">
				    		<div class="col-md-5"><b>Bill Month</b></div>
				    		<div class="col-md-7"><?php echo (isset($bill['month'])) ? $bill['month'] : ''; ?></div>
				    	</div>
				    	<div class="row">
				    		<div class="col-md-5"><b>Bill Amounth</b></div>
				    		<div class="col-md-7"><?php echo (isset($bill['amount'])) ? $bill['amount'] : ''; ?></div>
				    	</div>
				  	
				  	<?php
				  	if(isset($bill['paid_date']) && !is_null($bill['paid_date']))
				  	{
				  	?>
				  		<div class="row">
				    		<div class="col-md-5"><b>Payment Date</b></div>
				    		<div class="col-md-7"><?php echo (isset($bill['paid_date'])) ? $bill['paid_date'] : ''; ?></div>
				    	</div>
				    	<div class="row">
				    		<div class="col-md-5"><b>Paid Bill Amount</b></div>
				    		<div class="col-md-7"><?php echo (isset($bill['paid_amount'])) ? $bill['paid_amount'] : ''; ?></div>
				    	</div>
				    	<div class="row">
				    		<div class="col-md-5"><b>Payment Method</b></div>
				    		<div class="col-md-7"><?php echo (isset($bill['payment_method'])) ? $bill['payment_method'] : ''; ?></div>
				    	</div>
				  	<?php 
				  	}
				  	else
				  	{
				  		if($_SESSION['user_role'] == 'user')
				  		{
				  	?>
				  	</div>
				  	<div class="mb-3">
				  		<label>Payment Date</label>
				  		<input type="date" class="form-control" name="paid_date" id="paid_date">
				  	</div>
				  	<div class="mb-3">
				    	<label for="amount">Paid Bill Amount</label>
				    	<input type="number" id="paid_amount" name="paid_amount" class="form-control" step="0.01" value="">
				  	</div>
				  	<div class="mb-3">
				    	<label for="month">Payment Method</label>
				    	<select name="payment_method" class="form-control">
				    		<option value="">Select Payment Method</option>
				    		<option value="Cash">Cash</option>
				    	</select>
				  	</div>
				  	<input type="hidden" name="id" value="<?php echo (isset($bill['id'])) ? $bill['id'] : ''; ?>" />
				  	<input type="hidden" name="hidden_amount" value="<?php echo (isset($bill['amount'])) ? $bill['amount'] : ''; ?>" />
				  	<input type="hidden" name="hidden_bill_title" value="<?php echo isset($bill['bill_title']) ? $bill['bill_title'] : ''; ?>" />
				  	<input type="hidden" name="flat_number" value="<?php echo (isset($bill['flat_number'])) ? $bill['block_number'] . ' - ' . $bill['flat_number'] : ''; ?>" />
				  	<button type="submit" name="payment_bill" class="btn btn-primary">Payment</button>
				  	<?php
				  		}
				  		else
				  		{
				  	?>
				  		<div class="row">
				    		<div class="col-md-5"><b>Payment Status</b></div>
				    		<div class="col-md-7"><span class="badge bg-danger">Not Paid</span></div>
				    	</div>
				    </div>
				  	<?php
				  		}
				  	}
				  	?>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>
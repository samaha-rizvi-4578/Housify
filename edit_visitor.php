<?php

require_once 'config.php';

if (!isset($_SESSION['resident_id']) || $_SESSION['resident_role'] !== 'admin') 
{
  	header('Location: logout.php');
  	exit();
}

if(isset($_POST['edit_visitor']))
{
	$house_id = trim($_POST['house_id']);
    $name = trim($_POST['name']);
    $ssn = trim($_POST['ssn']);
    $reason = trim($_POST['reason']);
    $in_datetime = trim($_POST['in_datetime']);
    $id = $_POST['id'];

    // Validate form fields
    if (empty($house_id)) {
        $errors[] = 'House ID is required';
    }

    if (empty($name)) {
        $errors[] = 'Name is required';
    }

    if (empty($ssn)) {
        $errors[] = 'SSN is required';
    } elseif (strlen($ssn) != 9) {
        $errors[] = 'SSN must be exactly 9 digits';
    }

    if (empty($reason)) {
        $errors[] = 'Reason is required';
    }

    if (empty($in_datetime)) {
        $errors[] = 'In date and time is required';
    }

    // Insert visitor data if there are no validation errors
    if (empty($errors)) {
        $stmt = $pdo->prepare('UPDATE visitor SET house_id = ?, name = ?, ssn = ?, reason = ?, in_datetime = ? WHERE id = ?');
        $stmt->execute([$house_id, $name, $ssn, $reason, $in_datetime, $id]);

        $_SESSION['success'] = 'Visitor Data has been Edited';

        header('Location: visitor.php');
        exit();
    }
}

if(isset($_GET['id']))
{
	// Prepare a SELECT statement to retrieve the visitor's details
  	$stmt = $pdo->prepare("SELECT * FROM visitor WHERE id = ? AND out_datetime IS NULL");
  	$stmt->execute([$_GET['id']]);

  	// Fetch the visitor's details from the database
  	$visitor = $stmt->fetch(PDO::FETCH_ASSOC);
}


include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Visitor</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="visitor.php">Visitors Management</a></li>
        <li class="breadcrumb-item active">Edit Visitor</li>
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
				<h5 class="card-title">Edit Visitor</h5>
			</div>
			<div class="card-body">
				<form method="post">
                <div class="mb-3">
                        <label for="house_id">House Number</label>
                        <select id="house_id" name="house_id" class="form-control">
                            <option value="">Select House</option>
                            <?php
                            // Query to get flat numbers from flats table
                            $stmt = $pdo->prepare('SELECT id, house_number, block_number FROM house ORDER BY house_number ASC');
                            $stmt->execute(); // Remove [$_SESSION['resident_id']]
                            $house_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($house_result as $house) {
                                echo "<option value=\"" . $house['id'] . "\">" . $house['block_number'] . ' - ' . $house['house_number'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
				  	<div class="mb-3">
				    	<label for="name">Name</label>
				    	<input type="text" class="form-control" id="name" name="name" placeholder="Enter name" value="<?php echo (isset($visitor['name'])) ? $visitor['name'] : ''; ?>">
				  	</div>
				  	<div class="mb-3">
				    	<label for="ssn">SSN</label>
				    	<input type="text" class="form-control" id="ssn" name="ssn" placeholder="Enter ssn"  value="<?php echo (isset($visitor['ssn'])) ? $visitor['ssn'] : ''; ?>">
				  	</div>

				  	<div class="mb-3">
				    	<label for="reason">Reason for Visit</label>
				    	<textarea id="reason" name="reason" class="form-control" placeholder="Enter reason to visit"  value="<?php echo (isset($visitor['reason'])) ? $visitor['reason'] : ''; ?>"></textarea>
				  	</div>
				  	<div class="mb-3">
				    	<label for="in_datetime">In Date/Time</label>
				    	<input type="datetime-local" id="in_datetime" name="in_datetime" class="form-control" placeholder="Enter time"  value="<?php echo (isset($visitor['in_datetime'])) ? $visitor['in_datetime'] : ''; ?>">
				  	<input type="hidden" name="id" value="<?php echo (isset($visitor['id'])) ? $visitor['id'] : ''; ?>" />
				  	<button type="submit" name="edit_visitor" class="btn btn-primary">Edit Visitor</button>
				  	<script>
				  		$('#house_id').val('<?php echo (isset($visitor['house_id'])) ? $visitor['house_id'] : ''; ?>')
				  	</script>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>
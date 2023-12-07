<?php

require_once 'config.php';

if (!isset($_SESSION['resident_id']) || $_SESSION['resident_role'] !== 'admin') 
{
  	header('Location: logout.php');
  	exit();
}

if(isset($_POST['add_visitor']))
{
	$house_id = trim($_POST['house_id']);
    $name = trim($_POST['name']);
    $ssn = trim($_POST['ssn']);
    $reason = trim($_POST['reason']);
    $in_datetime = trim($_POST['in_datetime']);

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
    } else {
        // Check if SSN is already registered
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM resident WHERE ssn = ?");
        $stmt->execute([$ssn]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $errors[] = 'SSN is already registered';
        }
    }

    if (empty($reason)) {
        $errors[] = 'Reason is required';
    }

    if (empty($in_datetime)) {
        $errors[] = 'In date and time is required';
    }

    // Insert visitor data if there are no validation errors
    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO visitor (house_id, name, ssn, reason, in_datetime, is_in_out) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$house_id, $name, $ssn, $reason, $in_datetime, 'in']);

        $_SESSION['success'] = 'New Visitor Data has been Added';

        header('Location: visitor.php');
        exit();
    }
}



include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Add Visitor</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="visitor.php">Visitor Management</a></li>
        <li class="breadcrumb-item active">Add Visitor</li>
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
				<h5 class="card-title">Add Visitor</h5>
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
				    	<input type="text" class="form-control" id="name" name="name" placeholder="Enter name">
				  	</div>
				  	<div class="mb-3">
				    	<label for="ssn">SSN</label>
				    	<input type="text" class="form-control" id="ssn" name="ssn" placeholder="Enter ssn">
				  	</div>

				  	<div class="mb-3">
				    	<label for="reason">Reason for Visit</label>
				    	<textarea id="reason" name="reason" class="form-control" placeholder="Enter reason to visit"></textarea>
				  	</div>
				  	<div class="mb-3">
                      <label for="in_datetime">In Date/Time</label>
                        <input type="datetime-local" id="in_datetime" name="in_datetime" class="form-control" placeholder="Enter time" value="<?= date('Y-m-d\TH:i'); ?>">
				  	</div>
				  	<button type="submit" name="add_visitor" class="btn btn-primary">Add Visitor</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>

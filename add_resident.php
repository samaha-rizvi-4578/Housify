<?php

require_once 'config.php';

if(isset($_POST['add_resident']))
{
	// Validate the form data
  	$name = $_POST['name'];
  	$ssn = $_POST['ssn'];
  	$house_id = $_POST['house_id'];
  	$password = $_POST['password'];
  	$role = $_POST['role'];
    $allowed_roles = ['admin', 'user'];
  	if (empty($name)) 
  	{
	    $errors[] = 'Please enter your name';
  	}
  	if (empty($ssn)) 
  	{
    	$errors[] = 'Please enter your social security number (SSN)';
  	} 
  	if (empty($house_id)) 
  	{
    	$errors[] = 'Please enter your house id';
  	} 
  	if (empty($password)) 
  	{
 	   $errors['password'] = 'Please enter your password';
  	}
  	else
  	{
  		$password = password_hash($password, PASSWORD_DEFAULT);
  	}
      if (empty($role)) 
  	{
    	$errors[] = 'Please enter your role';
  	} //role ka radio button hai 
    else if (!in_array($role, $allowed_roles)) {
          $errors[] = 'Invalid role';
      }

  	// If the form data is valid, update the user's password
  	if (empty($errors)) 
  	{
  		// Insert user data into the database
	    $stmt = $pdo->prepare("INSERT INTO resident (name, ssn, house_id, password, role) VALUES (?, ?, ?, ?, ?)");

	    $stmt->execute([$name, $ssn, $house_id, $password, $role]);

  		$_SESSION['success'] = 'New resident Data Added';

  		header('location:resident.php');
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
    <h1 class="mt-4">Add Resident</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="resident.php">Resident Management</a></li>
        <li class="breadcrumb-item active">Add Resident</li>
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
				<h5 class="card-title">Add Resident</h5>
			</div>
			<div class="card-body">
				<form method="post">
				  	<div class="mb-3">
				    	<label for="name">Name</label>
				    	<input type="text" class="form-control" id="name" name="name" placeholder="Enter name">
				  	</div>
				  	<div class="mb-3">
				    	<label for="ssn">SSN</label>
				    	<input type="text" class="form-control" id="ssn" name="ssn" placeholder="Enter ssn">
				  	</div>
					  <div class="mb-3">
                        <label for="house_id">House ID</label>
                        <select id="house_id" name="house_id" class="form-control">
                            <option value="">Select House</option>
                            <?php
                            // Query to get flat numbers from flats table
                            $stmt = $pdo->prepare('SELECT id, house_number, block_number FROM house where house.id not in (select house_id from resident) ORDER BY house_number ASC');
                            $stmt->execute(); // Remove [$_SESSION['resident_id']]
                            $house_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($house_result as $house) {
                                echo "<option value=\"" . $house['id'] . "\">" . $house['block_number'] . ' - ' . $house['house_number'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
				  	<div class="mb-3">
				    	<label for="password">Password</label>
				    	<input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
				  	</div>
                      <div class="mb-3">
                        <label for="role">Role</label><br>
                        <input type="radio" id="admin" name="role" value="admin" checked>
                        <label for="admin">Admin</label><br>
                        <input type="radio" id="user" name="role" value="user">
                        <label for="user">User</label><br>
                    </div>
				  	<button type="submit" name="add_resident" class="btn btn-primary">Submit</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>
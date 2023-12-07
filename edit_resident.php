<?php

require_once 'config.php';

if (!isset($_SESSION['resident_id']) || $_SESSION['resident_role'] !== 'admin') {
    header('Location: logout.php');
    exit();
}

if (isset($_POST['edit_resident'])) {
    // Validate the form data
    $name = $_POST['name'];
   // $ssn = $_POST['ssn'];
    $house_id = $_POST['house_id'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $allowed_roles = ['admin', 'user'];
    // echo $ssn;
    // echo $name;
    // echo $house_id;
    // echo $password;
    // echo $role;
    if (empty($name)) {
        $errors[] = 'Please enter your name';
    }
    // if (empty($ssn)) {
    //     $errors[] = 'SSN is required';
    // } elseif (strlen($ssn) != 9) {
    //     $errors[] = 'SSN must be exactly 9 digits';
    // }
    if (empty($house_id)) {
        $errors[] = 'Please enter your house id';
    }
    if (empty($password)) {
        echo $password;
        $errors[] = 'Please enter your password';
    } else {
        echo $password;
        $password = password_hash($password, PASSWORD_DEFAULT);
    }
    if (empty($role)) {
        echo $role;
        $errors[] = 'Please enter your role';
    } //role ka radio button hai 
    else if (!in_array($role, $allowed_roles)) {
        $errors[] = 'Invalid role';
    }
    // If the form data is valid, update the user's password
    if (empty($errors)) {
        $id = $_GET['id'];
            $sql = "UPDATE resident SET name = ?,  house_id = ?,password = ?, role = ? WHERE id = ?";

            echo $sql;

            $pdo->prepare($sql)->execute([$name, $house_id, $password, $role, $id]);


        $_SESSION['success'] = 'Resident Data has been edited';

       header('location:resident.php');
       exit();
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Prepare a SELECT statement to retrieve the flats's details
    $stmt = $pdo->prepare("SELECT * FROM resident WHERE id = ?");
    $stmt->execute([$_GET['id']]);

    // Fetch the user's details from the database
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Resident</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="resident.php">Resident Management</a></li>
        <li class="breadcrumb-item active">Edit Resident Data</li>
    </ol>
    <div class="col-md-4">
        <?php

        if (isset($errors)) {
            foreach ($errors as $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
        }

        ?>
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Edit Resident Data</h5>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter name" value="<?php echo isset($resident['name']) ? $resident['name'] : ''; ?>">
                    </div>
                    <!-- <div class="mb-3">
                        <label for="ssn">SSN</label>
                        <input type="text" class="form-control" id="ssn" name="ssn" placeholder="Enter SSN" value="<?php echo isset($resident['ssn']) ? $resident['ssn'] : ''; ?>">
                    </div> -->
                    <div class="mb-3">
                        <label for="house-id">House ID</label>
                        <input type="number" class="form-control" id="house-id" name="house_id" placeholder="Enter House ID" value="<?php echo isset($resident['house_id']) ? $resident['house_id'] : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" value="<?php echo isset($resident['password']) ? $resident['password'] : ''; ?>">
                    </div>
                
                    <div class="mb-3">
                        <label for="role">Role</label><br>
                        <input type="radio" id="admin" name="role" value="admin" <?php echo (isset($resident['role']) && $resident['role'] === 'admin') ? 'checked' : ''; ?>>
                        <label for="admin">Admin</label><br>
                        <input type="radio" id="user" name="role" value="user" <?php echo (isset($resident['role']) && $resident['role'] === 'user') ? 'checked' : ''; ?>>
                        <label for="user">User</label><br>
                    </div>
                 
                    <button type="submit" name="edit_resident" class="btn btn-primary">Edit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php

include('footer.php');

?>
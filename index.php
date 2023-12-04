<?php

// Check if the system setup is complete

require_once 'config.php';

if (isset($_SESSION['resident_id'])) 
{
    header('Location: dashboard.php');
    exit();
}

$errorMessage = '';

if(isset($_POST['btn_login']))
{
    // Get the email and password entered by the user
    // $ssn = $_POST['ssn'];
    
    // $password = $_POST['password'];

    $ssn = $_POST['ssn'];
    $password = $_POST['password'];
    if (empty($ssn)) 
    {
        $errors[] = 'Please enter a ssn.';
    }

    // Validate email address format
    // if (!filter_var($ssn, FILTER_VALIDATE_EMAIL)) 
    // {
    //     $errors[] = 'Please enter a valid social security number (SSN).';
    // }

    // Validate password field is not empty
    if (empty($password)) 
    {
        $errors[] = 'Please enter a password.';
    }

    // If there are no validation errors, attempt to log in
    if(empty($errors)) 
    {

        // Query the database to see if a user with that username exists
        $stmt = $pdo->prepare("SELECT * FROM resident WHERE ssn = ?");
        $stmt->execute([$ssn]);
        $resident = $stmt->fetch();

        // If the user exists, retrieve their password hash from the database
        if ($resident) 
        {
            echo "$ssn";
            echo "$password";
           // $passwordHash = $resident['password'];
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
          //  $passwordHash = password_hash($passwordHash, PASSWORD_DEFAULT);
            echo "$passwordHash";

            // Use the password_verify function to check if the entered password matches the password hash
            if (password_verify($password, $passwordHash)) 
            // if($password == $passwordHash)
            {
                echo "agya";
                // Password is correct, log the user in
                $_SESSION['resident_id'] = $resident['id'];
                $_SESSION['resident_role'] = $resident['role'];
                $_SESSION['resident_name'] = $resident['name'];
                 if($resident['role'] == 'user')
                 {
                     header('Location: maintenance.php');
                 }
                 else
                 {
                    header('Location: dashboard.php');
                }
                exit;
            } 
            else
            {
                // Password is incorrect, show an error message
                $errors[] = "Invalid password";
            }
        } 
        else 
        {
            // User not found, show an error message
            $errors[] = "email not found in database";
        }
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Housify-Housing Society Management System</title>
        <!-- Load Bootstrap 5 CSS -->
    
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/lux/bootstrap.min.css">
        <link rel='stylesheet' href="style.css">
    </head>
    <body id="login">
        <div class="container" className='d-flex justify-content-center align-items-center'>
            <div class="mt-5">
                <h1 class="text-center">Housify</h1>
                <div class="row justify-content-center">
                    <div class="col-md-6 col-lg-4 mt-5">
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
                                <h3 class="card-title text-center">Login</h3>
                            </div>
                            <div class="card-body">                            
                            <!-- Login form -->
                            <form id="login-form" method="post">
                                <div class="mb-3">
                                    <label for="ssn" class="form-label">SSN</label>
                                    <input type="text" class="form-control" id="ssn" name="ssn">
                                    <div class="invalid-feedback">Please enter a valid ssn</div>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                    <div class="invalid-feedback">Please enter a password.</div>
                                </div>
                                <button type="submit" name="btn_login" class="btn btn-primary">Login</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Load Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        
    </body>
</html>

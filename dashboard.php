<?php

require_once 'config.php';

//Check if user is logged in and has admin role
if (!isset($_SESSION['resident_id']) || $_SESSION['resident_role'] !== 'admin') 
{
  	header('Location: logout.php');
  	exit();
}

// Get total total houses
$sql = "SELECT COUNT(*) AS total_houses FROM house";
$stmt = $pdo->query($sql);
$total_houses = $stmt->fetch(PDO::FETCH_ASSOC)['total_houses'];

$house_id = '';

// Get total bills
$sql = "SELECT COUNT(*) AS maintenanceFee FROM maintenance";
if($_SESSION['resident_role'] == 'resident')
{
    // $stmt = $pdo->prepare('SELECT id FROM resident WHERE resident_id = ?');
    // $stmt->execute([$_SESSION['user_id']]);
    // $flat_id = $stmt->fetch(PDO::FETCH_ASSOC)['flat_id'];
    // $sql .= " WHERE flat_id = '".$flat_id."'";
}
$stmt = $pdo->query($sql);
$maintenanceFee = $stmt->fetch(PDO::FETCH_ASSOC)['maintenanceFee'];

// Get total allotments
$sql = "SELECT COUNT(*) AS total_allotments FROM house WHERE id IN (SELECT DISTINCT(house_id) FROM resident)";
$stmt = $pdo->query($sql);
$total_allotments = $stmt->fetch(PDO::FETCH_ASSOC)['total_allotments'];

// Get total visitors
$sql = "SELECT COUNT(*) AS total_visitors FROM visitor";
if($_SESSION['resident_role'] == 'user')
{
	$sql .= " WHERE house_id = '".$house_id."'";
}
$stmt = $pdo->query($sql);
$total_visitors = $stmt->fetch(PDO::FETCH_ASSOC)['total_visitors'];

// Get total unresolved complaints
$sql = "SELECT COUNT(*) AS total_unresolved_complaints FROM complaints WHERE status = 'unresolved'";
if($_SESSION['resident_role'] == 'user')
{
	$sql .= " AND resident_id = '".$_SESSION['resident_id']."'";
}
$stmt = $pdo->query($sql);
$total_unresolved_complaints = $stmt->fetch(PDO::FETCH_ASSOC)['total_unresolved_complaints'];

// Get total in progress complaints
$sql = "SELECT COUNT(*) AS total_in_progress_complaints FROM complaints WHERE status = 'in_progress'";
if($_SESSION['resident_role'] == 'user')
{
	$sql .= " AND resident_id = '".$_SESSION['resident_id']."'";
}
$stmt = $pdo->query($sql);
$total_in_progress_complaints = $stmt->fetch(PDO::FETCH_ASSOC)['total_in_progress_complaints'];

// Get total resolved complaints
$sql = "SELECT COUNT(*) AS total_resolved_complaints FROM complaints WHERE status = 'resolved'";
if($_SESSION['resident_role'] == 'user')
{
	$sql .= " AND resident_id = '".$_SESSION['resident_id']."'";
}
$stmt = $pdo->query($sql);
$total_resolved_complaints = $stmt->fetch(PDO::FETCH_ASSOC)['total_resolved_complaints'];

// Get total complaints
$sql = "SELECT COUNT(*) AS total_complaints FROM complaints";
if($_SESSION['resident_role'] == 'user')
{
	$sql .= " AND resident_id = '".$_SESSION['resident_id']."'";
}
$stmt = $pdo->query($sql);
$total_complaints = $stmt->fetch(PDO::FETCH_ASSOC)['total_complaints'];

// Get total booked facilities
$sql = "SELECT COUNT(*) AS total_booked_facilities FROM facility WHERE booked_status = 'booked'";
if($_SESSION['resident_role'] == 'user')
{
	$sql .= " AND id in (Select facility_id from payment where resident_id = '".$_SESSION['resident_id']."')";
}
$stmt = $pdo->query($sql);
$total_booked_facilities = $stmt->fetch(PDO::FETCH_ASSOC)['total_booked_facilities'];

// Get total booked services
$sql = "SELECT COUNT(*) AS total_booked_services FROM service WHERE booked_status = 'booked'";
if($_SESSION['resident_role'] == 'user')
{
	$sql .= " AND id in (Select service_id from payment where resident_id = '".$_SESSION['resident_id']."')";
}
$stmt = $pdo->query($sql);
$total_booked_services = $stmt->fetch(PDO::FETCH_ASSOC)['total_booked_services'];

// Get total booked facilities
$sql = "SELECT COUNT(*) AS total_payments FROM payment";
if($_SESSION['resident_role'] == 'user')
{
	$sql .= " AND resident_id = '".$_SESSION['resident_id']."'";
}
$stmt = $pdo->query($sql);
$total_payments = $stmt->fetch(PDO::FETCH_ASSOC)['total_payments'];
include('header.php');

?>



                <div class="container-fluid px-4">
                    <h1 class="mt-4">Dashboard</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                    <div class="row">
                    	<?php 
                    	if($_SESSION['resident_role'] == 'admin')
                    	{
                    	?>
                        <div class="col-xl-3 col-md-6">
                            <div class="card">
								<div class="card-header">
									<h5>Total houses</h5>
								</div>
								<div class="card-body">
									<p class="card-text"><?php echo $total_houses; ?></p>
								</div>
							</div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                        	<div class="card">
								<div class="card-header">
									<h5>Total Maintenance Bills</h5>
								</div>
								<div class="card-body">
									<p class="card-text"><?php echo $maintenanceFee; ?></p>
								</div>
							</div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                        	<div class="card">
								<div class="card-header">
									<h5>Total Allotment</h5>
								</div>
								<div class="card-body">
									<p class="card-text"><?php echo $total_allotments; ?></p>
								</div>
							</div>
                        </div>
                        <div class="col-xl-3 col-md-6 mt-3">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Total Visitors</h5>
								</div>
								<div class="card-body">
                                    <p class="card-text"><?php echo $total_visitors; ?></p>
								</div>
							</div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Total In-process Complaints</h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text"><?php echo $total_in_progress_complaints; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mt-3">
                        	<div class="card">
								<div class="card-header">
									<h5>Total Unresolved Complaints</h5>
								</div>
								<div class="card-body">
									<p class="card-text"><?php echo $total_unresolved_complaints; ?></p>
								</div>
							</div>
                        </div>
                        <div class="col-xl-3 col-md-6 mt-3">
                        	<div class="card">
								<div class="card-header">
									<h5>Total Resolved Complaints</h5>
								</div>
								<div class="card-body">
									<p class="card-text"><?php echo $total_resolved_complaints; ?></p>
								</div>
							</div>
                        </div>
                        <div class="col-xl-3 col-md-6 mt-3">
                        	<div class="card">
								<div class="card-header">
									<h5>Total Booked Facilities</h5>
								</div>
								<div class="card-body">
									<p class="card-text"><?php echo $total_booked_facilities; ?></p>
								</div>
							</div>
                        </div>
                        <div class="col-xl-3 col-md-6 mt-3">
                        	<div class="card">
								<div class="card-header">
									<h5>Total Booked Services</h5>
								</div>
								<div class="card-body">
									<p class="card-text"><?php echo $total_booked_services; ?></p>
								</div>
							</div>
                        </div>
                        <div class="col-xl-3 col-md-6 mt-3">
                        	<div class="card">
								<div class="card-header">
									<h5>Total Payments</h5>
								</div>
								<div class="card-body">
									<p class="card-text"><?php echo $total_payments; ?></p>
								</div>
							</div>
                        </div>
                        <?php
                    	}
                    	else
                    	{
                    	?>
                    	<div class="col-xl-4 col-md-6">
                        	<div class="card">
								<div class="card-header">
									<h5>Total In-process Complaints</h5>
								</div>
								<div class="card-body">
									<p class="card-text"><?php echo $total_in_progress_complaints; ?></p>
								</div>
							</div>
                        </div>                        
                        <div class="col-xl-4 col-md-6 mt-3">
                        	<div class="card">
								<div class="card-header">
									<h5>Total Unresolved Complaints</h5>
								</div>
								<div class="card-body">
									<p class="card-text"><?php echo $total_unresolved_complaints; ?></p>
								</div>
							</div>
                        </div>
                        <div class="col-xl-4 col-md-6 mt-3">
                        	<div class="card">
								<div class="card-header">
									<h5>Total Resolved Complaints</h5>
								</div>
								<div class="card-body">
									<p class="card-text"><?php echo $total_resolved_complaints; ?></p>
								</div>
							</div>
                        </div>
                        <div class="col-xl-4 col-md-6 mt-3">
                        	<div class="card">
								<div class="card-header">
									<h5>Total Complaints</h5>
								</div>
								<div class="card-body">
									<p class="card-text"><?php echo $total_complaints; ?></p>
								</div>
							</div>
                        </div>
                        <div class="col-xl-4 col-md-6">
                        	<div class="card">
								<div class="card-header">
									<h5>Total Bills</h5>
								</div>
								<div class="card-body">
									<p class="card-text"><?php echo $maintenanceFee; ?></p>
								</div>
							</div>
                        </div>
                        
                        <div class="col-xl-4 col-md-6 mt-3">
                        	<div class="card">
								<div class="card-header">
									<h5>Total Visitors</h5>
								</div>
								<div class="card-body">
									<p class="card-text"><?php echo $total_visitors; ?></p>
								</div>
							</div>
                        </div>
                        
                        
                    	<?php
                    	}
                        ?>
                    </div>
                </div>
<?php
	include('footer.php');
?>
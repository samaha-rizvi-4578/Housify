<?php

require_once 'config.php';

if (!isset($_SESSION['resident_id']) && ($_SESSION['resident_role'] !== 'admin')) 
{
  	header('Location: logout.php');
  	exit();
}

if(isset($_GET['action'], $_GET['id']) && $_GET['action'] == 'delete' && $_SESSION['resident_role'] == 'admin')
{
	$stmt = $pdo->prepare("DELETE FROM maintenance WHERE id = ?");
  	$stmt->execute([$_GET['id']]);
  	$_SESSION['success'] = 'Maintenance Bills Data has been removed';
  	header('location:maintenance.php');
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Maintenance Bills Management</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Maintenance Bill Management</li>
    </ol>
    <?php

    if(isset($_SESSION['success']))
	{
		echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';

		unset($_SESSION['success']);
	}

    ?>
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col col-6">
					<h5 class="card-title">Maintenance Bills Management</h5>
				</div>
				<div class="col col-6">
					<?php
					if($_SESSION['resident_role'] == 'admin')
					{
					?>
					<div class="float-end"><a href="add_maintenance.php" class="btn btn-success btn-sm">Add</a></div>
					<?php
					}
					?>
				</div>
			</div>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered table-striped table-bordered" id="maintenance-table">
					<thead>
						<tr>
							<th>ID</th>
							<th>House Number</th>
							<th>Bill Amount</th>
							<th>Month</th>
							<th>Paid Amount</th>
							<th>Updated At</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.2/js/dataTables.bootstrap5.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.2/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">


<?php

include('footer.php');

?>

<script>

$(document).ready(function() {
    $('#maintenance-table').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
        	url: 'action.php',
        	method:"POST",
        	data: {action : 'fetch_maintenance'}
        }
    });

    $(document).on('click', '.delete_btn', function(){
    	if(confirm("Are you sure you want to remove this Maintenance Bill data?"))
    	{
    		window.location.href = 'maintenance.php?action=delete&id=' + $(this).data('id') + '';
    	}
    });
});

</script>
<?php

require_once 'config.php';

if (!isset($_SESSION['resident_id']) || $_SESSION['resident_role'] !== 'admin') 
{
  	header('Location: logout.php');
  	exit();
}

if (isset($_GET['action'], $_GET['id']) && $_GET['action'] == 'delete') {
    try {
        $pdo->beginTransaction();

        // Update booked_status in facility and service tables
        $stmtUpdateFacility = $pdo->prepare("UPDATE facility SET booked_status = 'available' WHERE id IN (SELECT facility_id FROM payment WHERE id = ?)");
        $stmtUpdateFacility->execute([$_GET['id']]);

        $stmtUpdateService = $pdo->prepare("UPDATE service SET booked_status = 'available' WHERE id IN (SELECT service_id FROM payment WHERE id = ?)");
        $stmtUpdateService->execute([$_GET['id']]);

        // Delete resident
        $stmtDeleteResident = $pdo->prepare("DELETE FROM resident WHERE id = ?");
        $stmtDeleteResident->execute([$_GET['id']]);

        $pdo->commit();

        $_SESSION['success'] = 'Resident Data has been removed';
        header('location:resident.php');
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Resident Management</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Resident Management</li>
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
					<h5 class="card-title">Resident Management</h5>
				</div>
				<div class="col col-6">
					<div class="float-end"><a href="add_resident.php" class="btn btn-success btn-sm">Add</a></div>
				</div>
			</div>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered table-striped table-bordered" id="resident-table">
					<thead>
						<tr>
							<td>ID</td>
							<th>Name</th>
							<th>SSN</th>
							<th>House ID</th>
							<th>Role</th>
							<th>Created At</th>
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
    $('#resident-table').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
        	url: 'action.php',
        	method:"POST",
        	data: {action : 'fetch_resident'}
        },
        "columns": [
            { "data": "id" },
            { "data": "name" },
            { "data": "ssn" },
            { "data": "house_id" },
            { "data" : "role"},
            { "data": "created_at"},
            {
        		"data": null,
        		"render": function(data, type, row) {
          			return '<a href="edit_resident.php?id='+row.id+'" class="btn btn-sm btn-primary">Edit</a>&nbsp;<button type="button" class="btn btn-sm btn-danger delete_btn" data-id="'+row.id+'">Delete</button>';
        		}
        	}
        ]
    });

    $(document).on('click', '.delete_btn', function(){
    	if(confirm("Are you sure you want to remove this Resident's data?"))
    	{
    		window.location.href = 'resident.php?action=delete&id=' + $(this).data('id') + '';
    	}
    });
});

</script>
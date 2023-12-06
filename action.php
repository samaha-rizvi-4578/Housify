<?php
header('Content-Type: application/json');
require_once 'config.php';

if (isset($_POST['action'])) {
    if ($_POST['action'] == 'fetch_house') {
        // Define the columns that should be returned in the response
        $columns = array(
            'id',
            'house_number',
            'street_name',
            'block_number',
            'created_at'
        );

        // Define the table name and the primary key column
        $table = 'house';
        $primaryKey = 'id';

        // Define the base query
        $query = "SELECT " . implode(", ", $columns) . " FROM $table";

        // Get the total number of records
        $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();

        // Define the filter query
        $filterQuery = '';
        if (!empty($_POST['search']['value'])) {
            $search = $_POST['search']['value'];

            $filterQuery = " WHERE (house_number LIKE '%$search%' OR street_name LIKE '%$search%' OR block_number LIKE '%$search%')";
        }

        // Add the filter query to the base query
        $query .= $filterQuery;

        // Get the number of filtered records
        $countFiltered = $pdo->query($query)->rowCount();

        // Add sorting to the query
        $orderColumn = $columns[$_POST['order'][0]['column']];
        $orderDirection = $_POST['order'][0]['dir'];
        $query .= " ORDER BY $orderColumn $orderDirection";

        // Add pagination to the query
        $start = $_POST['start'];
        $length = $_POST['length'];
        $query .= " LIMIT $start, $length";

        // Execute the query and fetch the results
        $stmt = $pdo->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Build the response
        $response = array(
            "draw" => intval($_REQUEST['draw']),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($countFiltered),
            "data" => $results
        );

        // Convert the response to JSON and output it
        echo json_encode($response);
    }

    if ($_POST['action'] == 'fetch_resident') {
        // Define the columns that should be returned in the response
        $columns = array(
            'id',
            'name',
            'ssn',
            'house_id',
            'role',
            'created_at'
        );

        // Define the table name and the primary key column
        $table = 'resident';
        $primaryKey = 'id';

        // Define the base query
        $query = "SELECT " . implode(", ", $columns) . " FROM $table";

        // Get the total number of records
        $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();

        // Define the filter query
        $filterQuery = '';
        if (!empty($_POST['search']['value'])) {
            $search = $_POST['search']['value'];

            $filterQuery = " WHERE (name LIKE '%$search%' OR ssn LIKE '%$search%' OR house_id LIKE '%$search%' OR role LIKE '%$search%')";
        }

        // Add the filter query to the base query
        $query .= $filterQuery;

        // Get the number of filtered records
        $countFiltered = $pdo->query($query)->rowCount();

        // Add sorting to the query
        $orderColumn = $columns[$_POST['order'][0]['column']];
        $orderDirection = $_POST['order'][0]['dir'];
        $query .= " ORDER BY $orderColumn $orderDirection";

        // Add pagination to the query
        $start = $_POST['start'];
        $length = $_POST['length'];
        $query .= " LIMIT $start, $length";

        // Execute the query and fetch the results
        $stmt = $pdo->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Build the response
        $response = array(
            "draw" => intval($_REQUEST['draw']),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($countFiltered),
            "data" => $results
        );

        // Convert the response to JSON and output it
        echo json_encode($response);
    }
    if ($_POST['action'] == 'fetch_maintenance') {
        // Define the columns that should be returned in the response
        $columns = array(
            'maintenance.id',
            'maintenance.resident_id',
            'maintenance.amount',
            'maintenance.month',
			'maintenance.paid_date',
            'maintenance.paid_amount',
            'maintenance.created_at'
        );
    
        // Define the table name and the primary key column
        $table = 'maintenance';
        $primaryKey = 'id';
    
        // Define the base query
        $query = "
        SELECT maintenance.id,maintenance.resident_id, maintenance.amount, maintenance.month, maintenance.paid_date, maintenance.paid_amount, maintenance.created_at
        FROM $table
        ";
    
        // Get the total number of records
        if ($_SESSION['resident_role'] == 'user') {
            $count = $pdo->query("SELECT COUNT(*) FROM $table WHERE resident_id = '" . $_SESSION['resident_id'] . "'")->fetchColumn();
        } else {
            $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        }
    
        // Define the filter query
        $filterQuery = '';
        if (!empty($_POST['search']['value'])) {
            $search = $_POST['search']['value'];
    
            $filterQuery = " WHERE ( maintenance.resident_id LIKE '%$search%' OR maintenance.amount LIKE '%$search%' OR maintenance.month LIKE '%$search%'OR maintenance.paid_date LIKE '%$search%' OR maintenance.paid_amount LIKE '%$search%' )";
        }
    
        if ($_SESSION['resident_role'] == 'user') {
            if ($filterQuery != '') {
                $filterQuery = " AND maintenance.resident_id = '" . $_SESSION['resident_id'] . "'";
            } else {
                $filterQuery = " WHERE maintenance.resident_id = '" . $_SESSION['resident_id'] . "'";
            }
        }
    
        // Add the filter query to the base query
        $query .= $filterQuery;
    
        // Get the number of filtered records
        $countFiltered = $pdo->query($query)->rowCount();
    
        // Add sorting to the query
        $orderColumn = $columns[$_POST['order'][0]['column']];
        $orderDirection = $_POST['order'][0]['dir'];
        $query .= " ORDER BY $orderColumn $orderDirection";
    
        // Add pagination to the query
        $start = $_POST['start'];
        $length = $_POST['length'];
        $query .= " LIMIT $start, $length";
    
        // Execute the query and fetch the results
        $stmt = $pdo->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $data = array();
    
        foreach ($results as $row) {
            $sub_array = array();
            $sub_array[] = $row['id'];
            $sub_array[] = $row['resident_id'];
            $sub_array[] = $row['amount'];
            $sub_array[] = $row['month'];
            $sub_array[] = ($row['paid_date'] !== NULL) ? $row['paid_date'] : '<span class="badge bg-danger">Not Paid</span>';
            $sub_array[] = ($row['paid_amount'] > 0) ? $row['paid_amount'] : '<span class="badge bg-danger">Not Paid</span>';
            $sub_array[] = $row['created_at'];
    
            $payment_button = '<a href="maintenance_payment.php?id=' . $row['id'] . '" class="btn btn-warning btn-sm">View</a>&nbsp;';
    
            if ($_SESSION['resident_role'] == 'admin') {
                $payment_button .= '<a href="edit_maintenance.php?id=' . $row['id'] . '" class="btn btn-sm btn-primary">Edit</a>&nbsp;<button type="button" class="btn btn-sm btn-danger delete_btn" data-id="' . $row['id'] . '">Delete</button>';
            }
    
            $sub_array[] = $payment_button;
            $data[] = $sub_array;
        }
    
        // Build the response
        $response = array(
            "draw" => intval($_REQUEST['draw']),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($countFiltered),
            "data" => $data
        );
    
        // Convert the response to JSON and output it
        echo json_encode($response);
    }
    if($_POST['action'] == 'fetch_complaints')
	{
		$columns = array(
		    'complaints.id',
		    'complaints.resident_id',
		    'resident.name',
		    'complaints.comment',
		    'complaints.status',
		    'complaints.created_at'
		);
		
		// Define the table name and the primary key column
		$table = 'complaints';
		$primaryKey = 'id';

		// Define the base query
		$query = "
		SELECT complaints.id, complaints.resident_id, resident.name, complaints.comment, complaints.status, complaints.created_at FROM $table
		JOIN resident ON resident.id = complaints.resident_id 
		";

		// Get the total number of records
		if($_SESSION['resident_role'] == 'user')
		{
			$count = $pdo->query("SELECT COUNT(*) FROM $table WHERE resident_id = '".$_SESSION["resident_id"]."'")->fetchColumn();
		}
		else
		{
			$count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
		}

		// Define the filter query
		$filterQuery = '';
		if (!empty($_POST['search']['value'])) 
		{
		    $search = $_POST['search']['value'];

		    $filterQuery = " WHERE (complaints.id LIKE '%$search%' OR resident.name LIKE '%$search%' OR complaints.resident_id LIKE '%$search%' OR complaints.comment LIKE '%$search%' OR complaints.status LIKE '%$search%')";
		}
		
		if($_SESSION['resident_role'] == 'user')
		{
			if($filterQuery != '')
			{				
				$filterQuery = " AND complaints.resident_id = '".$_SESSION['resident_id']."'";
			}
			else
			{
				$filterQuery = " WHERE complaints.resident_id = '".$_SESSION['resident_id']."'";
			}
		}


		// Add the filter query to the base query
		$query .= $filterQuery;

		// Get the number of filtered records
		$countFiltered = $pdo->query($query)->rowCount();

		// Add sorting to the query
		$orderColumn = $columns[$_POST['order'][0]['column']];
		$orderDirection = $_POST['order'][0]['dir'];
		$query .= " ORDER BY $orderColumn $orderDirection";

		// Add pagination to the query
		$start = $_POST['start'];
		$length = $_POST['length'];
		$query .= " LIMIT $start, $length";

		// Execute the query and fetch the results
		$stmt = $pdo->query($query);
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$data = array();

		foreach($results as $row)
		{
			$sub_array = array();
			$sub_array[] = $row['id'];
			$sub_array[] = $row['resident_id'];
			$sub_array[] = $row['name'];
			$sub_array[] = $row['comment'];

			if($row['status'] == 'pending')
			{
				$sub_array[] = '<span class="badge bg-primary">Pending</span>';
			}

			if($row['status'] == 'in_progress')
			{
				$sub_array[] = '<span class="badge bg-warning">In Progress</span>';
			}

			if($row['status'] == 'resolved')
			{
				$sub_array[] = '<span class="badge bg-success">Resolve</span>';
			}

			$sub_array[] = $row['created_at'];

			$view_btn = '<a href="view_complaint.php?id='.$row["id"].'" class="btn btn-warning btn-sm">View</a>&nbsp;';
			$edit_btn = '';
			$delete_btn = '';

			$sub_array[] = $view_btn . $edit_btn . $delete_btn;

			$data[] = $sub_array;
		}

		// Build the response
		$response = array(
		    "draw" => intval($_REQUEST['draw']),
		    "recordsTotal" => intval($count),
		    "recordsFiltered" => intval($countFiltered),
		    "data" => $data
		);

		// Convert the response to JSON and output it
		echo json_encode($response);
	}
    if ($_POST['action'] == 'fetch_visitor') {
        $columns = array(
            'visitor.id',
            'house.house_number',
            'visitor.name',
            'visitor.ssn',
            'visitor.in_datetime',
            'visitor.out_datetime',
            'visitor.is_in_out',
            'visitor.created_at'
        );

        // Define the table name and the primary key column
        $table = 'visitor';
        $primaryKey = 'visitor.id';

        // Define the base query
        $query = "
		SELECT visitor.id, visitor.house_id, house.house_number, house.block_number, visitor.name, visitor.ssn, visitor.in_datetime, visitor.out_datetime, visitor.is_in_out, visitor.created_at FROM $table 
		JOIN house ON house.id = visitor.house_id 
		";

        // Get the total number of records
        if ($_SESSION['resident_role'] == 'user') {
            $stmt = $pdo->prepare('SELECT house_id FROM resident WHERE id = ?');
            $stmt->execute([$_SESSION['resident_id']]);
            $stmt->errorInfo();
            $house_id = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $pdo->query("SELECT COUNT(*) FROM $table WHERE visitor.house_id = '" . $house_id['house_id'] . "'")->fetchColumn();
        } else {
            $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        }

        // Define the filter query
        $filterQuery = '';

        if (!empty($_POST['search']['value'])) {
            $search = $_POST['search']['value'];

            $filterQuery = " WHERE (visitor.id LIKE '%$search%' OR house.house_number LIKE '%$search%' OR visitor.name LIKE '%$search%' OR visitor.ssn LIKE '%$search%' OR visitor.in_datetime LIKE '%$search%' OR visitor.out_datetime LIKE '%$search%' OR visitor.is_in_out LIKE '%$search%')";
        }

        if ($_SESSION['resident_role'] == 'user') {
            if ($filterQuery != '') {
                $filterQuery = " AND visitor.house_id = '" . $house_id['house_id'] . "'";
            } else {
                $filterQuery = " WHERE visitor.house_id = '" . $house_id['house_id'] . "'";
            }
        }


        // Add the filter query to the base query
        $query .= $filterQuery;

        // Get the number of filtered records
        $countFiltered = $pdo->query($query)->rowCount();

        // Add sorting to the query
        $orderColumn = $columns[$_POST['order'][0]['column']];
        $orderDirection = $_POST['order'][0]['dir'];
        $query .= " ORDER BY $orderColumn $orderDirection";

        // Add pagination to the query
        $start = $_POST['start'];
        $length = $_POST['length'];
        $query .= " LIMIT $start, $length";

        // Execute the query and fetch the results
        $stmt = $pdo->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = array();

        foreach ($results as $row) {
            $sub_array = array();
            $sub_array[] = $row['id'];
            $sub_array[] = $row['block_number'] . ' - ' . $row['house_number'];

            $sub_array[] = $row['name'];
            $sub_array[] = $row['ssn'];
            $sub_array[] = $row['in_datetime'];
            $sub_array[] = ($row['out_datetime'] != '') ? $row['out_datetime'] : 'NA';

            if ($row['is_in_out'] == 'in') {
                $sub_array[] = '<span class="badge bg-danger">In</span>';
            } else {
                $sub_array[] = '<span class="badge bg-success">Out</span>';
            }
            $sub_array[] = $row['created_at'];
            $view_btn = '<a href="view_visitor.php?id=' . $row["id"] . '" class="btn btn-warning btn-sm">View</a>&nbsp;';
            $edit_btn = '';
            $delete_btn = '';

            if ($_SESSION['resident_role'] == 'admin') {
                if (is_null($row['out_datetime'])) {
                    $edit_btn = '<a href="edit_visitor.php?id=' . $row["id"] . '" class="btn btn-sm btn-primary">Edit</a>&nbsp;';
                    $delete_btn = '<button type="button" class="btn btn-sm btn-danger delete_btn" data-id="' . $row['id'] . '">Delete</button>&nbsp;';
                }
            }

            $sub_array[] = $view_btn . $edit_btn . $delete_btn;

            $data[] = $sub_array;
        }

        // Build the response
        $response = array(
            "draw" => intval($_REQUEST['draw']),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($countFiltered),
            "data" => $data
        );

        // Convert the response to JSON and output it
        echo json_encode($response);
    }
    if($_POST['action'] == 'fetch_available')
	{
		// Define the columns that should be returned in the response
		$columns = array(
		    'house.id',
		    'house.house_number',
		    'house.street_name',
		    'house.block_number',
		    'house.created_at'
		);

		// Define the table name and the primary key column
		$table = 'house';
		$primaryKey = 'id';

		// Define the base query
		$query = "
		SELECT " . implode(", ", $columns) . " FROM $table
        Where id not in (SELECT house_id FROM resident)
		";

		// Get the total number of records
        $count = $pdo->query("SELECT COUNT(*) FROM $table Where id NOT IN (SELECT house_id FROM resident)")->fetchColumn();

        // Define the filter query
        $filterQuery = '';
        if (!empty($_POST['search']['value'])) {
            $search = $_POST['search']['value'];

            $filterQuery = " WHERE (house_number LIKE '%$search%' OR street_name LIKE '%$search%' OR block_number LIKE '%$search%')";
        }

        // Add the filter query to the base query
        $query .= $filterQuery;

        // Get the number of filtered records
        $countFiltered = $pdo->query($query)->rowCount();

        // Add sorting to the query
        $orderColumn = $columns[$_POST['order'][0]['column']];
        $orderDirection = $_POST['order'][0]['dir'];
        $query .= " ORDER BY $orderColumn $orderDirection";

        // Add pagination to the query
        $start = $_POST['start'];
        $length = $_POST['length'];
        $query .= " LIMIT $start, $length";

        // Execute the query and fetch the results
        $stmt = $pdo->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Build the response
        $response = array(
            "draw" => intval($_REQUEST['draw']),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($countFiltered),
            "data" => $results
        );

        // Convert the response to JSON and output it
        echo json_encode($response);
	}
    if ($_POST['action'] == 'fetch_facility') {
        $columns = array(
            'id',
            'name',
            'amount',
            'booked_status',
            'created_at'
        );

        // Define the table name and the primary key column
        $table = 'facility';
        $primaryKey = 'id';

        // Define the base query
        $query = "
		SELECT id, name, amount, booked_status, created_at FROM $table
		";

        // Get the total number of records
    
            $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();

        // Define the filter query
        $filterQuery = '';

        if (!empty($_POST['search']['value'])) {
            $search = $_POST['search']['value'];

            $filterQuery = " WHERE (id LIKE '%$search%' OR name LIKE '%$search%' OR amount LIKE '%$search%' OR booked_status LIKE '%$search%')";
        }


        // Add the filter query to the base query
        $query .= $filterQuery;

        // Get the number of filtered records
        $countFiltered = $pdo->query($query)->rowCount();

        // Add sorting to the query
        $orderColumn = $columns[$_POST['order'][0]['column']];
        $orderDirection = $_POST['order'][0]['dir'];
        $query .= " ORDER BY $orderColumn $orderDirection";

        // Add pagination to the query
        $start = $_POST['start'];
        $length = $_POST['length'];
        $query .= " LIMIT $start, $length";

        // Execute the query and fetch the results
        $stmt = $pdo->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = array();

        foreach ($results as $row) {
            $sub_array = array();
            $sub_array[] = $row['id'];
            $sub_array[] = $row['name'];
            $sub_array[] = $row['amount'];
            if ($row['booked_status'] == 'booked') {
                $sub_array[] = '<span class="badge bg-danger">Booked</span>';
            } else {
                $sub_array[] = '<span class="badge bg-success">Available</span>';
            }
            $sub_array[] = $row['created_at'];
            // $view_btn = '<a href="view_facility.php?id=' . $row["id"] . '" class="btn btn-warning btn-sm">View</a>&nbsp;';
            $edit_btn = '';
            $delete_btn = '';

            if ($_SESSION['resident_role'] == 'admin') {
                
                    $edit_btn = '<a href="edit_facility.php?id=' . $row["id"] . '" class="btn btn-sm btn-primary">Edit</a>&nbsp;';
                    $delete_btn = '<button type="button" class="btn btn-sm btn-danger delete_btn" data-id="' . $row['id'] . '">Delete</button>&nbsp;';
            }

            $sub_array[] =  $edit_btn . $delete_btn;

            $data[] = $sub_array;
        }

        // Build the response
        $response = array(
            "draw" => intval($_REQUEST['draw']),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($countFiltered),
            "data" => $data
        );

        // Convert the response to JSON and output it
        echo json_encode($response);
    }
    if ($_POST['action'] == 'fetch_service') {
        $columns = array(
            'id',
            'name',
            'amount',
            'booked_status',
            'created_at'
        );

        // Define the table name and the primary key column
        $table = 'service';
        $primaryKey = 'id';

        // Define the base query
        $query = "
		SELECT id, name, amount, booked_status, created_at FROM $table
		";

        // Get the total number of records
    
            $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();

        // Define the filter query
        $filterQuery = '';

        if (!empty($_POST['search']['value'])) {
            $search = $_POST['search']['value'];

            $filterQuery = " WHERE (id LIKE '%$search%' OR name LIKE '%$search%' OR amount LIKE '%$search%' OR booked_status LIKE '%$search%')";
        }


        // Add the filter query to the base query
        $query .= $filterQuery;

        // Get the number of filtered records
        $countFiltered = $pdo->query($query)->rowCount();

        // Add sorting to the query
        $orderColumn = $columns[$_POST['order'][0]['column']];
        $orderDirection = $_POST['order'][0]['dir'];
        $query .= " ORDER BY $orderColumn $orderDirection";

        // Add pagination to the query
        $start = $_POST['start'];
        $length = $_POST['length'];
        $query .= " LIMIT $start, $length";

        // Execute the query and fetch the results
        $stmt = $pdo->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = array();

        foreach ($results as $row) {
            $sub_array = array();
            $sub_array[] = $row['id'];
            $sub_array[] = $row['name'];
            $sub_array[] = $row['amount'];
            if ($row['booked_status'] == 'booked') {
                $sub_array[] = '<span class="badge bg-danger">Booked</span>';
            } else {
                $sub_array[] = '<span class="badge bg-success">Available</span>';
            }
            $sub_array[] = $row['created_at'];
            // $view_btn = '<a href="view_service.php?id=' . $row["id"] . '" class="btn btn-warning btn-sm">View</a>&nbsp;';
            $edit_btn = '';
            $delete_btn = '';

            if ($_SESSION['resident_role'] == 'admin') {
                
                    $edit_btn = '<a href="edit_service.php?id=' . $row["id"] . '" class="btn btn-sm btn-primary">Edit</a>&nbsp;';
                    $delete_btn = '<button type="button" class="btn btn-sm btn-danger delete_btn" data-id="' . $row['id'] . '">Delete</button>&nbsp;';
            }

            $sub_array[] = $edit_btn . $delete_btn;

            $data[] = $sub_array;
        }

        // Build the response
        $response = array(
            "draw" => intval($_REQUEST['draw']),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($countFiltered),
            "data" => $data
        );

        // Convert the response to JSON and output it
        echo json_encode($response);
    }
    if ($_POST['action'] == 'fetch_payment') {
        // Define the columns that should be returned in the response
        $columns = array(
            'payment.id',
            'payment.resident_id',
            'payment.facility_id',
            'payment.service_id',
            'payment.amount',
            'payment.month',
            'payment.paid_date',
            'payment.paid_amount',
            'payment.created_at'
        );
    
        // Define the table name and the primary key column
        $table = 'payment';
        $primaryKey = 'id';
    
        // Define the base query
        $query = "
        SELECT payment.id, payment.resident_id, payment.facility_id, payment.service_id, payment.amount, payment.month, payment.paid_date, payment.paid_amount, payment.created_at
        FROM $table
        ";
    
        // Get the total number of records
        if ($_SESSION['resident_role'] == 'user') {
            $count = $pdo->query("SELECT COUNT(*) FROM $table WHERE resident_id = '" . $_SESSION['resident_id'] . "'")->fetchColumn();
        } else {
            $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        }
    
        // Define the filter query
        $filterQuery = '';
        if (!empty($_POST['search']['value'])) {
            $search = $_POST['search']['value'];
    
            $filterQuery = " WHERE ( payment.resident_id LIKE '%$search%' OR payment.facility_id LIKE '%$search%' OR payment.service_id LIKE '%$search%' OR payment.amount LIKE '%$search%' OR payment.month LIKE '%$search%' OR payment.paid_date LIKE '%$search%' OR payment.paid_amount LIKE '%$search%' )";
        }
    
        if ($_SESSION['resident_role'] == 'user') {
            if ($filterQuery != '') {
                $filterQuery = " AND payment.resident_id = '" . $_SESSION['resident_id'] . "'";
            } else {
                $filterQuery = " WHERE payment.resident_id = '" . $_SESSION['resident_id'] . "'";
            }
        }
    
        // Add the filter query to the base query
        $query .= $filterQuery;
    
        // Get the number of filtered records
        $countFiltered = $pdo->query($query)->rowCount();
    
        // Add sorting to the query
        $orderColumn = $columns[$_POST['order'][0]['column']];
        $orderDirection = $_POST['order'][0]['dir'];
        $query .= " ORDER BY $orderColumn $orderDirection";
    
        // Add pagination to the query
        $start = $_POST['start'];
        $length = $_POST['length'];
        $query .= " LIMIT $start, $length";
    
        // Execute the query and fetch the results
        $stmt = $pdo->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $data = array();
    
        foreach ($results as $row) {
            $sub_array = array();
            $sub_array[] = $row['id'];
            $sub_array[] = $row['resident_id'];
            $sub_array[] = $row['facility_id'];
            $sub_array[] = $row['service_id'];
            $sub_array[] = $row['amount'];
            $sub_array[] = $row['month'];
            $sub_array[] = ($row['paid_date'] !== NULL) ? $row['paid_date'] : '<span class="badge bg-danger">Not Paid</span>';
            $sub_array[] = ($row['paid_amount'] > 0) ? $row['paid_amount'] : '<span class="badge bg-danger">Not Paid</span>';
            $sub_array[] = $row['created_at'];
    
            $payment_button = '<a href="payment_payment.php?id=' . $row['id'] . '" class="btn btn-warning btn-sm">View</a>&nbsp;';
    
            if ($_SESSION['resident_role'] == 'admin') {
                $payment_button .= '<button type="button" class="btn btn-sm btn-danger delete_btn" data-id="' . $row['id'] . '">Delete</button>';
            }
    
            $sub_array[] = $payment_button;
            $data[] = $sub_array;
        }
    
        // Build the response
        $response = array(
            "draw" => intval($_REQUEST['draw']),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($countFiltered),
            "data" => $data
        );
    
        // Convert the response to JSON and output it
        echo json_encode($response);
    }
    
}

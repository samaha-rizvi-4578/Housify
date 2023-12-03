<?php

require_once 'config.php';

if(isset($_POST['action']))
{
	if($_POST['action'] == 'fetch_house')
	{
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
		if (!empty($_POST['search']['value'])) 
		{
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

	if($_POST['action'] == 'fetch_resident')
	{
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
		if (!empty($_POST['search']['value'])) 
		{
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
    if($_POST['action'] == 'fetch_maintenance')
	{
		// Define the columns that should be returned in the response
		$columns = array(
		    'id',
		    'house_id',
		    'amount',
            'month',
            'paid_date',
            'paid_amount',
		    'created_at'
		);

		// Define the table name and the primary key column
		$table = 'maintenance';
		$primaryKey = 'id';

		// Define the base query
		$query = "SELECT " . implode(", ", $columns) . " FROM $table";

		// Get the total number of records
		$count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();

		// Define the filter query
		$filterQuery = '';
		if (!empty($_POST['search']['value'])) 
		{
		    $search = $_POST['search']['value'];

		    $filterQuery = " WHERE (house_id LIKE '%$search%' OR amount LIKE '%$search%' OR month LIKE '%$search%' OR paid_date LIKE '%$search%' OR paid_amount LIKE '%$search%')";
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
	// if($_POST['action'] == 'fetch_maintenance')
	// {
	// 	// Define the columns that should be returned in the response
	// 	$columns = array(
	// 	    'id',
	// 	    'house_id',
	// 	    'amount',
    //         'month',
    //         'paid_date',
    //         'paid_amount',
	// 	    'created_at'
	// 	);

	// 	// Define the table name and the primary key column
	// 	$table = 'maintenance';
	// 	$primaryKey = 'id';

	// 	// Define the base query
	// 	// Define the base query
	// 	$query = "SELECT " . implode(", ", $columns) . " FROM $table";


	// 	//Get the total number of records
	// 	if($_SESSION['resident_role'] == 'owner' || $_SESSION['resident_role'] == 'user' )
	// 	{
	// 		$stmt = $pdo->prepare('SELECT house_id FROM resident WHERE resident_id = ?');
	// 		$stmt->execute([$_SESSION['resident_id']]);
	// 		$flat_id = $stmt->fetch(PDO::FETCH_ASSOC);
	// 		$count = $pdo->query("SELECT COUNT(*) FROM $table WHERE resident_id = '".$house_id['house_id']."'")->fetchColumn();
	// 	}
	// 	else
	// 	{
            
	// 		$count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
	// 	}

	// 	// Define the filter query
	// 	$filterQuery = '';
	// 	if (!empty($_POST['search']['value'])) 
	// 	{
	// 	    $search = $_POST['search']['value'];

	// 	    $filterQuery = " WHERE (house_id LIKE '%$search%' OR amount LIKE '%$search%' OR month LIKE '%$search%' OR paid_date LIKE '%$search%' OR paid_amount LIKE '%$search%')";
	// 	}


		
		
	// 	if($_SESSION['resident_role'] == 'owner' || $_SESSION['resident_role'] == 'user')
	// 	{
	// 		$stmt = $pdo->prepare('SELECT house_id FROM resident WHERE resident_id = ?');
	// 		$stmt->execute([$_SESSION['resident_id']]);
	// 		$house_id = $stmt->fetch(PDO::FETCH_ASSOC);
	// 		if($filterQuery != '')
	// 		{				
	// 			$filterQuery = " AND maintenance.house_id = '".$house_id["house_id"]."'";
	// 		}
	// 		else
	// 		{
	// 			$filterQuery = " WHERE maintenance.house_id = '".$house_id["house_id"]."'";
	// 		}
	// 	}


	// 	// Add the filter query to the base query
	// 	$query .= $filterQuery;

	// 	// Get the number of filtered records
	// 	$countFiltered = $pdo->query($query)->rowCount();

	// 	// Add sorting to the query
	// 	$orderColumn = $columns[$_POST['order'][0]['column']];
	// 	$orderDirection = $_POST['order'][0]['dir'];
	// 	$query .= " ORDER BY $orderColumn $orderDirection";

	// 	// Add pagination to the query
	// 	$start = $_POST['start'];
	// 	$length = $_POST['length'];
	// 	$query .= " LIMIT $start, $length";

	// 	// Execute the query and fetch the results
	// 	$stmt = $pdo->query($query);
	// 	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// 	$data = array();

	// 	foreach($results as $row)
	// 	{
	// 		$sub_array = array();
	// 		$sub_array[] = $row['id'];
	// 		$sub_array[] = $row['house_id'];
	// 		$sub_array[] = $row['amount'];
	// 		$sub_array[] = $row['month'];
	// 		$sub_array[] = ($row['paid_date'] > 0) ? $row['paid_date'] : '<span class="badge bg-danger">Not Paid</span>';
	// 		$sub_array[] = ($row['paid_amount'] > 0) ? $row['paid_amount'] : '<span class="badge bg-danger">Not Paid</span>';
	// 		$sub_array[] = $row['created_at'];
	// 		$payment_button = '';
	// 		if($_SESSION['resident_role'] !== 'admin' )
	// 		{
	// 			$sub_array[] = '<a href="maintenance_payment.php?id='.$row['id'].'" class="btn btn-warning btn-sm">View</a>&nbsp;';
	// 		}
	// 		else
	// 		{
	// 			$sub_array[] ='<a href="maintenance_payment.php?id='.$row['id'].'" class="btn btn-warning btn-sm">View</a>&nbsp;<a href="edit_maintenance.php?id='.$row['id'].'" class="btn btn-sm btn-primary">Edit</a>&nbsp;<button type="button" class="btn btn-sm btn-danger delete_btn" data-id="'.$row['id'].'">Delete</button>';
	// 		}
	// 		$data[] = $sub_array;
	// 	}

	// 	// Build the response
	// 	$response = array(
	// 	    "draw" => intval($_REQUEST['draw']),
	// 	    "recordsTotal" => intval($count),
	// 	    "recordsFiltered" => intval($countFiltered),
	// 	    "data" => $data
	// 	);

	// 	// Convert the response to JSON and output it
	// 	echo json_encode($response);
	// }

	if($_POST['action'] == 'fetch_complaints')
	{
		$columns = array(
		    'id',
		    'resident_id',
		    'comment',
		    'status',
		    'created_at'
		);
		
		// Define the table name and the primary key column
		$table = 'complaints';
		$primaryKey = 'id';

		// Define the base query
		// $query = "
		// SELECT complaints.id, resident.name, complaints.comment, complaints.status, complaints.created_at FROM $table
		// JOIN resident ON resident.id = complaints.resident_id
		// ";
        $query = "SELECT " . implode(", ", $columns) . " FROM $table";

		// Get the total number of records
		//$count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
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

		    $filterQuery = " WHERE (id LIKE '%$search%' OR resident_id LIKE '%$search%' OR comment LIKE '%$search%' OR status LIKE '%$search%')";
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


		// // Add the filter query to the base query
		// $query .= $filterQuery;

		// // Get the number of filtered records
		// $countFiltered = $pdo->query($query)->rowCount();

		// // Add sorting to the query
		// $orderColumn = $columns[$_POST['order'][0]['column']];
		// $orderDirection = $_POST['order'][0]['dir'];
		// $query .= " ORDER BY $orderColumn $orderDirection";

		// // Add pagination to the query
		// $start = $_POST['start'];
		// $length = $_POST['length'];
		// $query .= " LIMIT $start, $length";

		// // Execute the query and fetch the results
		// $stmt = $pdo->query($query);
		// $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

		// $data = array();

		// foreach($results as $row)
		// {
		// 	$sub_array = array();
		// 	$sub_array[] = $row['id'];
		// 	$sub_array[] = $row['resident_id'];
		// 	$sub_array[] = $row['comment'];

		// 	if($row['status'] == 'pending')
		// 	{
		// 		$sub_array[] = '<span class="badge bg-primary">Pending</span>';
		// 	}

		// 	if($row['status'] == 'in_progress')
		// 	{
		// 		$sub_array[] = '<span class="badge bg-warning">In Progress</span>';
		// 	}

		// 	if($row['status'] == 'resolved')
		// 	{
		// 		$sub_array[] = '<span class="badge bg-success">Resolve</span>';
		// 	}

		// 	$sub_array[] = $row['created_at'];

		// 	$view_btn = '<a href="view_complaints.php?id='.$row["id"].'" class="btn btn-warning btn-sm">View</a>&nbsp;';
		// 	$edit_btn = '';
		// 	$delete_btn = '';

		// 	// if($row['master_comment'] == '')
		// 	// {
		// 	// 	$edit_btn = '<a href="edit_complaints.php?id='.$row["id"].'" class="btn btn-sm btn-primary">Edit</a>&nbsp;';
		// 	// 	$delete_btn = '<button type="button" class="btn btn-sm btn-danger delete_btn" data-id="'.$row['id'].'">Delete</button>&nbsp;';
		// 	// }

		// 	$sub_array[] = $view_btn . $edit_btn . $delete_btn;

		// 	$data[] = $sub_array;
		// }

		// // Build the response
		// $response = array(
		//     "draw" => intval($_REQUEST['draw']),
		//     "recordsTotal" => intval($count),
		//     "recordsFiltered" => intval($countFiltered),
		//     "data" => $data
		// );

		// // Convert the response to JSON and output it
		// echo json_encode($response);
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

	if($_POST['action'] == 'fetch_visitors')
	{
		$columns = array(
		    'visitors.id',
		    'flats.flat_number',
		    'visitors.name',
		    'visitors.phone',
		    'visitors.person_to_meet',
		    'visitors.in_datetime',
		    'visitors.out_datetime',
		    'visitors.is_in_out'
		);

		// Define the table name and the primary key column
		$table = 'visitors';
		$primaryKey = 'id';

		// Define the base query
		$query = "
		SELECT visitors.id, flats.flat_number, flats.block_number, visitors.name, visitors.phone, visitors.person_to_meet, visitors.in_datetime, visitors.out_datetime, visitors.is_in_out FROM $table 
		JOIN flats ON flats.id = visitors.flat_id 
		";

		// Get the total number of records
		if($_SESSION['user_role'] == 'user')
		{
			$stmt = $pdo->prepare('SELECT flat_id FROM allotments WHERE user_id = ?');
			$stmt->execute([$_SESSION['user_id']]);
			$flat_id = $stmt->fetch(PDO::FETCH_ASSOC);
			$count = $pdo->query("SELECT COUNT(*) FROM $table WHERE flat_id = '".$flat_id['flat_id']."'")->fetchColumn();
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

		    $filterQuery = " WHERE (visitors.id LIKE '%$search%' OR flats.flat_number LIKE '%$search%' OR visitors.name LIKE '%$search%' OR visitors.phone LIKE '%$search%' OR visitors.person_to_meet LIKE '%$search%' OR visitors.in_datetime LIKE '%$search%' OR visitors.out_datetime LIKE '%$search%' OR visitors.is_in_out LIKE '%$search%')";
		}
		
		if($_SESSION['user_role'] == 'user')
		{
			if($filterQuery != '')
			{				
				$filterQuery = " AND visitors.flat_id = '".$flat_id['flat_id']."'";
			}
			else
			{
				$filterQuery = " WHERE visitors.flat_id = '".$flat_id['flat_id']."'";
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
			$sub_array[] = $row['block_number'] . ' - ' . $row['flat_number'];
			$sub_array[] = $row['name'];
			$sub_array[] = $row['phone'];
			$sub_array[] = $row['person_to_meet'];
			$sub_array[] = $row['in_datetime'];
			$sub_array[] = ($row['out_datetime'] != '') ? $row['out_datetime'] : 'NA';

			if($row['is_in_out'] == 'in')
			{
				$sub_array[] = '<span class="badge bg-danger">In</span>';
			}
			else
			{
				$sub_array[] = '<span class="badge bg-success">Out</span>';
			}

			$view_btn = '<a href="view_visitor.php?id='.$row["id"].'" class="btn btn-warning btn-sm">View</a>&nbsp;';
			$edit_btn = '';
			$delete_btn = '';

			if($_SESSION['user_role'] == 'admin')
			{
				if(is_null($row['out_datetime']))
				{
					$edit_btn = '<a href="edit_visitor.php?id='.$row["id"].'" class="btn btn-sm btn-primary">Edit</a>&nbsp;';
					$delete_btn = '<button type="button" class="btn btn-sm btn-danger delete_btn" data-id="'.$row['id'].'">Delete</button>&nbsp;';
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
}

?>
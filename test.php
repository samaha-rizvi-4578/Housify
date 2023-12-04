<?php 
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

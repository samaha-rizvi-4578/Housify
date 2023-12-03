<?php if($_POST['action'] == 'fetch_maintenance')
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
?>
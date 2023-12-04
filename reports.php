<?php

require_once 'config.php';

if (!isset($_SESSION['resident_id']) || $_SESSION['resident_role'] !== 'admin') 
{
  	header('Location: logout.php');
  	exit();
}

$start_date = '';

$end_date = '';

$report_type = '';

if(isset($_POST['generate_report']))
{
	$start_date = $_POST['start_date'];

	$end_date = $_POST['end_date'];

	$report_type = $_POST['report_type'];

	if(empty($start_date))
	{
		$errors[] = 'Please Select Start Date';
	}

	if(empty($end_date))
	{
		$errors[] = 'Please Select End Date';
	}

	if(empty($report_type))
	{
		$errors[] = 'Please Select Report Type';
	}
}

if(isset($_GET['type'], $_GET['start_date'], $_GET['end_date'], $_GET['page']))
{
	if(!empty($_GET['start_date']))
	{
		$start_date = $_GET['start_date'];
	}
	if(!empty($_GET['end_date']))
	{
		$end_date = $_GET['end_date'];
	}

	if(!empty($_GET['type']))
	{
		$report_type = $_GET['type'];
	}

	$current_page = $_GET['page'];
}
else
{
	$current_page = 1;
}

if($start_date != '' && $end_date != '' && $report_type != '')
{
	$total_pages = 1;

	// Number of records to show per page
	$records_per_page = 10;

	if($report_type == 'Maintenance_Bill')
	{
		$output_data = '
		<div class="card mt-3">
			<div class="card-header">
				<div class="row">
					<div class="col-md-9">
						<h5 class="card-title">Maintenance Bill Data for '.$start_date.' to '.$end_date.' Date</h5>
					</div>
					<div class="col-md-3">
						<a href="reports.php?action=export&type=Maintenance_Bill&start_date='.$start_date.'&end_date='.$end_date.'" class="btn btn-success btn-sm float-end">Export</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="table-resposive">
					<table class="table table-bordered">
						<tr>
                        <th>ID</th>
                        <th>Resident ID</th>
                        <th>Amount</th>
                        <th>Month</th>
                        <th>Paid Date</th>
                        <th>Paid Amount</th>
                        <th>Status</th>
						</tr>
		';

		// Get the total number of records from the database
		$total_records = $pdo->query('SELECT COUNT(*) FROM maintenance WHERE created_at BETWEEN "'.$start_date.'" AND "'.$end_date.'"')->fetchColumn();

		// Calculate the number of pages needed to display all the records
		$total_pages = ceil($total_records / $records_per_page);

		// Calculate the offset for the SQL LIMIT clause
		$offset = ($current_page - 1) * $records_per_page;

		// Retrieve the records from the database
		$stmt = $pdo->prepare('SELECT maintenance.id,maintenance.resident_id, maintenance.amount, maintenance.month, maintenance.paid_date, maintenance.paid_amount FROM maintenance WHERE maintenance.created_at BETWEEN "'.$start_date.'" AND "'.$end_date.'" ORDER BY maintenance.id DESC LIMIT '.$offset.', '.$records_per_page.'');
		$stmt->execute();
		$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

		// Output the records as HTML table rows
		if(count($records) > 0)
		{
			foreach($records as $record)
			{
				$paid_date = ($record['paid_date'] !='') ? $record['paid_date'] : 'NA';
				$paid_amount = ($record['paid_amount'] != '') ? $record['paid_amount'] : 'NA';
				$status = ($paid_date == 'NA') ? '<span class="badge bg-danger">Not Paid</span>' : '<span class="badge bg-success">Paid</span>';
				$output_data .= '
						<tr>
							<td>'.$record["id"].'</td>
							<td>'.$record["resident_id"].'</td>
							<td>'.$record["amount"].'</td>
							<td>'.$record["month"].'</td>
							<td>'.$paid_date.'</td>
							<td>'.$paid_amount.'</td>
							<td>'.$status.'</td>
						</tr>
				';
			}
		}
		else
		{
			$output_data .= '
						<tr>
							<td colspan="8" class="text-center">No Bill Data Found</td>
						</tr>
			';
		}

		$output_data .= '
					</table>
					<nav aria-label="Page navigation">
					    <ul class="pagination justify-content-center">
							<li class="page-item' . ($current_page == 1 ? ' disabled' : '') . '"><a class="page-link" href=reports.php?type=Maintenance_Bill&start_date='.$start_date.'&end_date='.$end_date.'&page=' . ($current_page - 1) . '">Previous</a></li>
		';

		for ($i = 1; $i <= $total_pages; $i++) 
		{
			$output_data .= '
							<li class="page-item' . ($i == $current_page ? ' active' : '') . '"><a class="page-link" href="reports.php?type=Maintenance_Bill&start_date='.$start_date.'&end_date='.$end_date.'&page=' . $i . '">' . $i . '</a></li>';
		}

		$output_data .= '
							<li class="page-item' . ($current_page == $total_pages ? ' disabled' : '') . '"><a class="page-link" href="reports.php?type=Maintenance_Bill&start_date='.$start_date.'&end_date='.$end_date.'&page=' . ($current_page + 1) . '">Next</a></li>
						</ul>
					</nav>
				</div>
			</div>
		</div>
		';

	}
	if($report_type == 'Complaint')
	{
		$output_data = '
		<div class="card mt-3">
			<div class="card-header">
				<div class="row">
					<div class="col-md-9">
						<h5 class="card-title">Complaint Data for '.$start_date.' to '.$end_date.' Date</h5>
					</div>
					<div class="col-md-3">
						<a href="reports.php?action=export&type=Complaint&start_date='.$start_date.'&end_date='.$end_date.'" class="btn btn-success btn-sm float-end">Export</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="table-resposive">
					<table class="table table-bordered">
						<tr>
							<th>Resident ID</th>
							<th>Resident Name</th>
							<th>Complaint</th>
							<th>Status</th>
							<th>Updated At</th>
						</tr>
		';

		// Get the total number of records from the database
		$total_records = $pdo->query('SELECT COUNT(*) FROM complaints WHERE created_at BETWEEN "'.$start_date.'" AND "'.$end_date.'"')->fetchColumn();

		// Calculate the number of pages needed to display all the records
		$total_pages = ceil($total_records / $records_per_page);

		// Calculate the offset for the SQL LIMIT clause
		$offset = ($current_page - 1) * $records_per_page;

		// Retrieve the records from the database
		$stmt = $pdo->prepare('SELECT complaints.id, complaints.resident_id, resident.name, complaints.comment, complaints.status, complaints.created_at FROM complaints JOIN resident ON resident.id = complaints.resident_id  WHERE complaints.created_at BETWEEN "'.$start_date.'" AND "'.$end_date.'" ORDER BY complaints.id DESC LIMIT '.$offset.', '.$records_per_page.'');
		$stmt->execute();
		$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

		// Output the records as HTML table rows
		if(count($records) > 0)
		{
			foreach($records as $record)
			{
				$status = '';
				if($record['status'] == 'pending')
				{
					$status = '<span class="badge bg-primary">Pending</span>';
				}

				if($record['status'] == 'in_progress')
				{
					$status = '<span class="badge bg-warning">In Progress</span>';
				}

				if($record['status'] == 'resolved')
				{
					$status = '<span class="badge bg-success">Resolve</span>';
				}
				$output_data .= '
						<tr>
							<td>'.$record["resident_id"].'</td>
							<td>'.$record["name"].'</td>
							<td>'.$record["comment"].'</td>
							<td>'.$status.'</td>
							<td>'.$record["created_at"].'</td>
						</tr>
				';
			}
		}
		else
		{
			$output_data .= '
						<tr>
							<td colspan="5" class="text-center">No Complaint Data Found</td>
						</tr>
			';
		}

		$output_data .= '
					</table>
					<nav aria-label="Page navigation">
					    <ul class="pagination justify-content-center">
							<li class="page-item' . ($current_page == 1 ? ' disabled' : '') . '"><a class="page-link" href=reports.php?type=Complaint&start_date='.$start_date.'&end_date='.$end_date.'&page=' . ($current_page - 1) . '">Previous</a></li>
		';

		for ($i = 1; $i <= $total_pages; $i++) 
		{
			$output_data .= '
							<li class="page-item' . ($i == $current_page ? ' active' : '') . '"><a class="page-link" href="reports.php?type=Complaint&start_date='.$start_date.'&end_date='.$end_date.'&page=' . $i . '">' . $i . '</a></li>';
		}

		$output_data .= '
							<li class="page-item' . ($current_page == $total_pages ? ' disabled' : '') . '"><a class="page-link" href="reports.php?type=Complaint&start_date='.$start_date.'&end_date='.$end_date.'&page=' . ($current_page + 1) . '">Next</a></li>
						</ul>
					</nav>
				</div>
			</div>
		</div>
		';

	}
	if($report_type == 'Visitor')
	{
		$output_data = '
		<div class="card mt-3">
			<div class="card-header">
				<div class="row">
					<div class="col-md-9">
						<h5 class="card-title">Visitor Data for '.$start_date.' to '.$end_date.' Date</h5>
					</div>
					<div class="col-md-3">
						<a href="reports.php?action=export&type=Visitor&start_date='.$start_date.'&end_date='.$end_date.'" class="btn btn-success btn-sm float-end">Export</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="table-resposive">
					<table class="table table-bordered">
						<tr>
                            <th>House Number</th>
							<th>Visitor Name</th>
							<th>Visitor SSN</th>
							<th>Reason to Meet</th>
							<th>In Time</th>
							<th>Out Time</th>
							<th>Status</th>
						</tr>
		';

		// Get the total number of records from the database
		$total_records = $pdo->query('SELECT COUNT(*) FROM visitor WHERE created_at BETWEEN "'.$start_date.'" AND "'.$end_date.'"')->fetchColumn();

		// Calculate the number of pages needed to display all the records
		$total_pages = ceil($total_records / $records_per_page);

		// Calculate the offset for the SQL LIMIT clause
		$offset = ($current_page - 1) * $records_per_page;

		// Retrieve the records from the database
		$stmt = $pdo->prepare('SELECT visitor.id, house.house_number, house.block_number, visitor.name, visitor.ssn, visitor.reason, visitor.in_datetime, visitor.out_datetime, visitor.is_in_out FROM visitor JOIN house ON house.id = visitor.house_id  WHERE visitor.created_at BETWEEN "'.$start_date.'" AND "'.$end_date.'" ORDER BY visitor.id DESC LIMIT '.$offset.', '.$records_per_page.'');
		$stmt->execute();
		$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

		// Output the records as HTML table rows
		if(count($records) > 0)
		{
			foreach($records as $record)
			{
				$status = '';
				if($record['is_in_out'] == 'in')
				{
					$status = '<span class="badge bg-danger">In</span>';
				}
				else
				{
					$status = '<span class="badge bg-success">Out</span>';
				}
				$output_data .= '
						<tr>
							<td>'.$record['block_number'] . ' - ' . $record['flat_number'].'</td>
							<td>'.$record["name"].'</td>
							<td>'.$record["ssn"].'</td>
							<td>'.$record["reason"].'</td>
							<td>'.$record["in_datetime"].'</td>
							<td>'.$record["out_datetime"].'</td>
							<td>'.$status.'</td>
						</tr>
				';
			}
		}
		else
		{
			$output_data .= '
						<tr>
							<td colspan="10" class="text-center">No Visitor Data Found</td>
						</tr>
			';
		}

		$output_data .= '
					</table>
					<nav aria-label="Page navigation">
					    <ul class="pagination justify-content-center">
							<li class="page-item' . ($current_page == 1 ? ' disabled' : '') . '"><a class="page-link" href=reports.php?type=Visitor&start_date='.$start_date.'&end_date='.$end_date.'&page=' . ($current_page - 1) . '">Previous</a></li>
		';

		for ($i = 1; $i <= $total_pages; $i++) 
		{
			$output_data .= '
							<li class="page-item' . ($i == $current_page ? ' active' : '') . '"><a class="page-link" href="reports.php?type=Visitor&start_date='.$start_date.'&end_date='.$end_date.'&page=' . $i . '">' . $i . '</a></li>';
		}

		$output_data .= '
							<li class="page-item' . ($current_page == $total_pages ? ' disabled' : '') . '"><a class="page-link" href="reports.php?type=Visitor&start_date='.$start_date.'&end_date='.$end_date.'&page=' . ($current_page + 1) . '">Next</a></li>
						</ul>
					</nav>
				</div>
			</div>
		</div>
		';
	}
}

if(isset($_GET['action'], $_GET['type'], $_GET['start_date'], $_GET['end_date']))
{
	$action = '';

	if(!empty($_GET['action']))
	{
		$action = $_GET['action'];
	}

	if(!empty($_GET['start_date']))
	{
		$start_date = $_GET['start_date'];
	}

	if(!empty($_GET['end_date']))
	{
		$end_date = $_GET['end_date'];
	}

	if(!empty($_GET['type']))
	{
		$report_type = $_GET['type'];
	}

	if($action == 'export' && $report_type != '' && $start_date != '' && $end_date != '')
	{
		if($report_type == 'Maintenance_Bill')
		{
			$stmt = $pdo->prepare('SELECT maintenance.id,maintenance.resident_id, maintenance.amount, maintenance.month, maintenance.paid_date, maintenance.paid_amoun FROM maintenance WHERE maintenance.created_at BETWEEN "'.$start_date.'" AND "'.$end_date.'" ORDER BY maintenance.id DESC LIMIT '.$offset.', '.$records_per_page.'" ORDER BY bills.id DESC');
			$stmt->execute();
			$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

			// Output headers
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename="bill_payment_report_for_'.$start_date.'_to_'.$end_date.'.csv"');

			// Output CSV data
			$output = fopen('php://output', 'w');
			fputcsv($output, array('Bill Title', 'Flat Number', 'Amount', 'Month', 'Paid Date', 'Payment Method', 'Paid Amount', 'Status'));

			foreach($records as $record)
			{
				$sub_array = array();

				$sub_array[] = $record['bill_title'];
				$sub_array[] = $record['block_number'] . ' - ' . $record['flat_number'];
				$sub_array[] = $record['amount'];
				$sub_array[] = $record['month'];
				$sub_array[] = $record['paid_date'];
				$sub_array[] = $record['payment_method'];
				$sub_array[] = $record['paid_amount'];
				$sub_array[] = ($record['paid_date'] == '') ? 'Not Paid' : 'Paid';
				fputcsv($output, $sub_array);
			}

			fclose($output);
			exit;
		}

		if($report_type == 'Complaint')
		{
			$stmt = $pdo->prepare('SELECT complaints.id, users.name, flats.flat_number, flats.block_number, complaints.description, complaints.status, complaints.created_at, complaints.master_comment FROM complaints JOIN users ON users.id = complaints.user_id JOIN flats ON flats.id = complaints.flat_id WHERE complaints.created_at BETWEEN "'.$start_date.'" AND "'.$end_date.'" ORDER BY complaints.id');
			$stmt->execute();
			$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

			// Output headers
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename="complaint_report_for_'.$start_date.'_to_'.$end_date.'.csv"');

			// Output CSV data
			$output = fopen('php://output', 'w');
			fputcsv($output, array('User Name', 'Flat Number', 'Complaint', 'Status', 'Updated At'));

			foreach($records as $record)
			{
				$sub_array = array();

				$sub_array[] = $record["name"];
				$sub_array[] = $record['block_number'] . ' - ' . $record['flat_number'];
				$sub_array[] = $record["description"];
				$sub_array[] = $record["status"];
				$sub_array[] = $record["created_at"];
				fputcsv($output, $sub_array);
			}

			fclose($output);
			exit;
		}

		if($report_type == 'Visitor')
		{
			$stmt = $pdo->prepare('SELECT visitors.id, flats.flat_number, flats.block_number, visitors.name, visitors.phone, visitors.address, visitors.person_to_meet, visitors.reason, visitors.in_datetime, visitors.out_remark, visitors.out_datetime, visitors.is_in_out FROM visitors JOIN flats ON flats.id = visitors.flat_id  WHERE visitors.created_at BETWEEN "'.$start_date.'" AND "'.$end_date.'" ORDER BY visitors.id');
			$stmt->execute();
			$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

			// Output headers
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename="visitor_report_for_'.$start_date.'_to_'.$end_date.'.csv"');

			// Output CSV data
			$output = fopen('php://output', 'w');
			fputcsv($output, array('Flat Number', 'Visitor Name', 'Visitor Phone', 'Address', 'Person to Meet', 'Reason to Meet', 'In Time', 'Out Remark', 'Out Time', 'Status'));

			foreach($records as $record)
			{
				$sub_array = array();

				$sub_array[] = $record['block_number'] . ' - ' . $record['flat_number'];
				$sub_array[] = $record['name'];
				$sub_array[] = $record['phone'];
				$sub_array[] = $record['address'];
				$sub_array[] = $record['person_to_meet'];
				$sub_array[] = $record['reason'];
				$sub_array[] = $record['in_datetime'];
				$sub_array[] = $record['out_remark'];
				$sub_array[] = $record['out_datetime'];
				$sub_array[] = $record["is_in_out"];
				fputcsv($output, $sub_array);
			}

			fclose($output);
			exit;
		}
	}
}


include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Maintenance Bill Report</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Reports</li>
    </ol>
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
			<h5 class="card-title">Report</h5>
		</div>
		<div class="card-body">
			<form method="post">
				<div class="row">
					<div class="col-md-3">
						<label for="report_for">Report For</label>
						<select name="report_type" id="report_type" class="form-control">
							<option value="">Select</option>
							<option value="Maintenance_Bill">Maintenance Bill</option>
							<option value="Complaint">Complaint</option>
							<option value="Visitor">Visitor</option>
						</select>
					</div>
					<div class="col-md-3">
				    	<label for="start_date">Start Date</label>
				    	<input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo $start_date; ?>">
				    </div>
				    <div class="col-md-3">
				    	<label for="end_date">End Date</label>
				    	<input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo $end_date; ?>">
				    </div>
				    <div class="col-md-3">
				    	<button type="submit" name="generate_report" class="btn btn-primary mt-4">Generate Report</button>
				    </div>
				</div>
			</form>
			<script>
				$('#report_type').val('<?php echo $report_type; ?>');
			</script>
		</div>
	</div>

<?php

if(isset($output_data))
{
	echo $output_data;
}

?>
</div>

<?php

include('footer.php');

?>
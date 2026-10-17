<?php
// Database configuration
include '../../../includes/db-config.php';
session_start();

// Read Datatable values
$draw        = $_POST['draw'];
$row         = $_POST['start'];
$rowperpage  = $_POST['length'];

if (isset($_POST['order'])) {
  $columnIndex     = $_POST['order'][0]['column'];
  $columnName      = $_POST['columns'][$columnIndex]['data'];
  $columnSortOrder = $_POST['order'][0]['dir'];
}

$searchValue = mysqli_real_escape_string($conn, $_POST['search']['value']);

// Order by
if (isset($columnSortOrder)) {
  $orderby = "ORDER BY $columnName $columnSortOrder";
} else {
  $orderby = "ORDER BY Fee_Heads.ID ASC";
}

// Search query
$searchQuery = "";
if ($searchValue != '') {
  $searchQuery = " AND Fee_Heads.Name LIKE '%$searchValue%'";
}

// Total records (without filter)
$all_count = $conn->query("
  SELECT COUNT(ID) AS allcount 
  FROM Fee_Heads
");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

// Total records (with filter)
$filter_count = $conn->query("
  SELECT COUNT(ID) AS filtered 
  FROM Fee_Heads 
  WHERE 1 $searchQuery
");
$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

// Fetch records
$result_record = "
  SELECT ID, Name 
  FROM Fee_Heads 
  WHERE 1 $searchQuery 
  $orderby 
  LIMIT $row, $rowperpage
";

$empRecords = mysqli_query($conn, $result_record);
$data = [];

while ($row = mysqli_fetch_assoc($empRecords)) {
  $data[] = [
    "Name" => $row["Name"],
    "ID"   => $row["ID"]
  ];
}

// Response
$response = [
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
];

echo json_encode($response);

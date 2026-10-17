<?php
if (isset($_POST['id'])) {
  require '../../includes/db-config.php';

  $id = $_POST['id'];
  $check = $conn->query("SELECT ID FROM Student_Ledgers WHERE ID = $id");
  if ($check->num_rows > 0) {
    $delete_details = $conn->query("DELETE FROM Student_Ledgers WHERE ID = $id");
    if ($delete_details) {
      echo json_encode(['status' => 200, 'message' => 'Data deleted successfully!']);
    } else {
      echo json_encode(['status' => 400, 'message' => 'Server is not responding. Please try again later']);
    }
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong']);
  }
}

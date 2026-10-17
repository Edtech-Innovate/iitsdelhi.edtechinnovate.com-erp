<?php
if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])) {
  require '../../../includes/db-config.php';

  $id = mysqli_real_escape_string($conn, $_GET['id']);

  // $admission_sessions = $conn->query("SELECT ID FROM Admission_Sessions WHERE Scheme_ID = $id");
  // if ($admission_sessions->num_rows > 0) {
  //   echo json_encode(['status' => 302, 'message' => 'This schemes already exist in admission sessions!']);
  //   exit();
  // }

  $check = $conn->query("SELECT ID, Account_Holder_Name FROM Bank_Accounts WHERE ID = $id");
  if ($check->num_rows > 0) {
    $holder_name = $check->fetch_assoc()['Account_Holder_Name'];
    $delete = $conn->query("DELETE FROM Bank_Accounts WHERE ID = $id");
    if ($delete) {
      echo json_encode(['status' => 200, 'message' => $holder_name.' deleted successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => 302, 'message' => 'Scheme not exists!']);
  }
}

<?php
if (isset($_POST['id'])) {
  require '../../../includes/db-config.php';
  session_start();

  $account_holder_name = trim($_POST['account_holder_name']);
  $account_no = trim($_POST['account_no']);
  $bank_name = trim($_POST['bank_name']);
  $branch_name = trim($_POST['branch_name']);
  $ifsc_code = trim($_POST['ifsc_code']);
  $account_type = trim($_POST['account_type']);
  $university_id = intval($_POST['university_id']);
  $id = intval($_POST['id']);

  if (empty($id) || empty($account_holder_name) || empty($account_no) || empty($bank_name) || empty($branch_name) || empty($ifsc_code) || empty($account_type)) {
    echo json_encode(['status' => 403, 'message' => 'All mandatory fields are required!']);
    exit();
  }

  $check = $conn->prepare("SELECT ID FROM Bank_Accounts WHERE Account_Holder_Name = ? AND Account_No = ? AND Bank_Name = ? AND Branch_Name = ? AND IFSC_Code = ? AND Account_Type = ? AND University_ID = ? AND ID <> ?");
  $check->bind_param("ssssssii", $account_holder_name, $account_no, $bank_name, $branch_name, $ifsc_code, $account_type, $university_id, $id);
  $check->execute();
  $result = $check->get_result();

  if ($result->num_rows > 0) {
    echo json_encode(['status' => 400, 'message' => $account_holder_name . ' already exists!']);
    exit();
  }

  $update_data = $conn->prepare("UPDATE Bank_Accounts SET Account_Holder_Name = ?, Account_No = ?, Bank_Name = ?, Branch_Name = ?, IFSC_Code = ?, Account_Type = ?, University_ID = ? WHERE ID = ?");
  $update_data->bind_param("ssssssii", $account_holder_name, $account_no, $bank_name, $branch_name, $ifsc_code, $account_type, $university_id, $id);

  if ($update_data->execute()) {
    echo json_encode(['status' => 200, 'message' => 'Bank account updated successfully!']);
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
  }
  $check->close();
  $update_data->close();
  $conn->close();
} else {
  echo json_encode(['status' => 405, 'message' => 'Invalid request!']);
}
?>
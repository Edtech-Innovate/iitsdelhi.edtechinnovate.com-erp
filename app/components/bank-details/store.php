<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['account_holder_name'])) {
    require '../../../includes/db-config.php';
    session_start();

    $account_holder_name = trim($_POST['account_holder_name']);
    $account_no = trim($_POST['account_no']);
    $bank_name = trim($_POST['bank_name']);
    $branch_name = trim($_POST['branch_name']);
    $ifsc_code = trim($_POST['ifsc_code']);
    $account_type = trim($_POST['account_type']);
    $university_id = intval($_POST['university_id']);
    
    if (empty($account_holder_name) || empty($account_no) || empty($bank_name) || empty($branch_name) || empty($ifsc_code) || empty($account_type)) {
      echo json_encode(['status' => 403, 'message' => 'All mandatory fields are required!']);
      exit();
    }
    $check_acc = $conn->prepare("SELECT ID FROM Bank_Accounts WHERE Account_No = ? AND University_ID = ?");
    $check_acc->bind_param("si", $account_no, $university_id);
    $check_acc->execute();
    $check_result = $check_acc->get_result();
    if ($check_result->num_rows > 0) {
      echo json_encode(['status' => 403, 'message' => 'Account number already exists!']);
      exit();
    }
    $insert_acc = $conn->prepare("INSERT INTO Bank_Accounts(Account_Holder_Name, Account_No,Bank_Name,Branch_Name,IFSC_Code,Account_Type, University_ID) VALUES(?,?,?,?,?,?,?)");
    $insert_acc->bind_param("ssssssi", $account_holder_name, $account_no, $bank_name, $branch_name, $ifsc_code,  $account_type, $university_id);

    if ($insert_acc->execute()) {
      echo json_encode(['status' => 200, 'message' => 'Bank account added successfully!']);
    } else {
        echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
    }
    $check_acc->close();
    $insert_acc->close();
    $conn->close();
  } else {
    echo json_encode(['status' => 400, 'message' => 'Invalid request!']);
}


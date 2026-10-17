<?php
if (isset($_POST['student_id']) && isset($_POST['university_id'])) {
    ini_set('display_errors', 1);
    require '../../includes/db-config.php';


    $feeHeadArr = (isset($_POST['misce_fee']) && is_array($_POST['misce_fee'])) ? $_POST['misce_fee'] : [];
    $uniFeeArr = (isset($_POST['uni_fee']) && is_array($_POST['uni_fee'])) ? $_POST['uni_fee'] : [];

    $student_id = intval($_POST['student_id']);
    $university_id = intval($_POST['university_id']);
    $getStudent_data_sql = $conn->query("SELECT Duration,Created_At FROM Students Where ID = $student_id");
    $studnent_arr = $getStudent_data_sql->fetch_assoc();
    $date = date('Y-m-d', strtotime($studnent_arr['Created_At']));

    $acc_holder_id = isset($_POST['holder_name']) ? intval($_POST['holder_name']) : "";
    $utr_no = $_POST['utr_no'] ?? "";
    $date_of_transation = $_POST['date_of_transation'] ?? "";
    $mode_of_payment = $_POST['mode_of_payment'] ?? "";
    $receipt_file = "";
    $result = true;


    // allot Miscellaneous  Fee
    if (!empty($feeHeadArr)) {
        $fee_head_ids = implode(',', array_keys($feeHeadArr));
        $check_student = $conn->query("SELECT * FROM Student_Ledgers where Student_ID = $student_id AND University_ID = $university_id AND Type=1 AND Source = 'Miscellaneous Type Fee' AND fee_head IN($fee_head_ids)");
        $sources = 'Miscellaneous Type Fee';
        if ($check_student->num_rows == 0) { // generate ledger
            foreach ($feeHeadArr as $fee_head => $fee) {
                $uni_fee = $uniFeeArr[$fee_head];
                $result = $conn->query("INSERT INTO Student_Ledgers (`Date`,`Student_ID`,Duration,`Fee`,`University_ID`,`Type`,`Status`,`Source`,`mode_of_payment`,`date_of_transation`,`utr_no`,Receipt_File,  fee_head, University_Fee) VALUES ('" . $date . "','$student_id','" . $studnent_arr['Duration'] . "','" . trim($fee) . "','$university_id',1,1,'" . $sources . "','$mode_of_payment','$date_of_transation','$utr_no','$receipt_file','" . $fee_head . "','" . trim($uni_fee) . "')");
            }
        } else {
            // update
        }
    }

    if (!empty($_FILES['file']['name'])) {
        $target_dir = "../../uploads/student_fees/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_ext = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
        $new_filename = 'receipt_' . time() . '.' . $file_ext;
        $target_file = $target_dir . $new_filename;
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $receipt_file = "/uploads/student_fees/" . $new_filename;
        }
    }

    if (isset($_POST['credit_amount'])) {
        $credit_amount = $_POST['credit_amount'];
        $feeHeadIdsArr = (isset($_POST['fee_head']) && is_array($_POST['fee_head'])) ? $_POST['fee_head'] : [];
        $fee_head_ids = (!empty( $feeHeadIdsArr )) ? implode(',',  $feeHeadIdsArr): "";
        
        $result = $conn->query("INSERT INTO Student_Ledgers (`Date`,`Student_ID`,Duration,`Fee`,`University_ID`,`Type`,`Status`,`mode_of_payment`,`date_of_transation`,`utr_no`,Receipt_File, Account_Holder_ID,`Source`) VALUES ('" . $date . "','$student_id','" . $studnent_arr['Duration'] . "','" . $credit_amount . "','$university_id',2,1,'$mode_of_payment','$date_of_transation','$utr_no','$receipt_file',$acc_holder_id,'Miscellaneous Type Fee')");
        
    }
    if ($result) {
        echo json_encode(['status' => 200, 'message' => "Miscellaneous Fee allotted successfully!"]);
    } else {
        echo json_encode(['status' => 400, 'message' => 'Something went to wrong!!']);
    }
} else {
    echo json_encode(['status' => 400, 'message' => 'Something went to wrong!!']);
}

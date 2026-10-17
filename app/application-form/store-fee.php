<?php
if (isset($_POST['student_id']) && isset($_POST['university_id']) && isset($_POST['fee'])) {
    ini_set('display_errors', 1);
    require '../../includes/db-config.php';
 
    $student_id = intval($_POST['student_id']);
    $university_id = intval($_POST['university_id']);
    $feeArr = $_POST['fee'];
    $utr_no = isset($_POST['utr_no']) ? $_POST['utr_no'] :"";
    $date_of_transation = isset($_POST['date_of_transation'])? $_POST['date_of_transation']:"";
    $mode_of_payment = isset($_POST['mode_of_payment'])? $_POST['mode_of_payment'] :"";
    $getStudent_data_sql = $conn->query("SELECT Duration,Created_At FROM Students Where ID = $student_id");
    $studnent_arr = $getStudent_data_sql->fetch_assoc();
    $date = date('Y-m-d', strtotime($studnent_arr['Created_At']));

    $check_debit = $conn->query("SELECT * FROM Student_Ledgers WHERE Student_ID =$student_id  AND  University_ID= '$university_id' AND Type=1");

    if ($check_debit->num_rows > 0) {
        $check_debit_arr = $check_debit->fetch_assoc();
         $result = $conn->query("UPDATE Student_Ledgers Set Fee = '".$feeArr[1]."' WHERE Student_ID = '$student_id' AND University_ID= '$university_id' AND Type=1");
        if(isset($feeArr[2])){
           $result  = $conn->query("INSERT INTO Student_Ledgers (`Date`,`Student_ID`,Duration,`Fee`,`University_ID`,`Type`,`Status`,`mode_of_payment`,`date_of_transation`,`utr_no`) VALUES ('" . $date . "','$student_id','" . $studnent_arr['Duration'] . "','".$feeArr[2]."','$university_id',2,1,'$mode_of_payment','$date_of_transation','$utr_no')");
        }
    }else{
  
       $result  = $conn->query("INSERT INTO Student_Ledgers (`Date`,`Student_ID`,Duration,`Fee`,`University_ID`,`Type`,`Status`,`mode_of_payment`,`date_of_transation`,`utr_no`) VALUES ('" . $date . "','$student_id','" . $studnent_arr['Duration'] . "','".$feeArr[1]."','$university_id','1',1,'$mode_of_payment','$date_of_transation','$utr_no')");
    }

    if ($result) {
        $check_credited = $conn->query("SELECT Fee,Type FROM Student_Ledgers WHERE Student_ID = '$student_id' AND University_ID= '" . $university_id . "' AND Type=2");
        $i = 1;
        $pending_fee = [];
        $total_fee = 0;
        while ($row = $check_credited->fetch_assoc()) {
          $pending_fee[] = $row['Fee'];
        }
        
        $fee_sql = $conn->query("SELECT Fee,Type FROM Student_Ledgers WHERE Student_ID = '$student_id' AND University_ID= '" . $university_id . "' AND Type=1");
        $fee_arr = $fee_sql->fetch_assoc();
        
        $total_fee = $fee_arr['Fee'] - array_sum($pending_fee);
        if($total_fee<=0 || $total_fee==0 )
        {
           $updateProceedByCenter = $conn->query("UPDATE Students set Process_By_Center='".date('Y-m-d H:i:s')."' Where ID = $student_id");
        }
        echo json_encode(['status' => 200, 'message' => "Course Fee alloted succefully!!"]);
    } else {
        echo json_encode(['status' => 400, 'message' => 'Something went to wrong!!']);
    }
}

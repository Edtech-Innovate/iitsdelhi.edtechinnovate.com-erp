<?php



if($_POST['studentid']!='' )
{
    require '../../includes/db-config.php';
    $amount = $_POST['unifee'];
    $uniid = $_POST['uniid'];
    $student_id = $_POST['studentid'];
    
    $check = $conn->query("select * from Student_Ledgers where Type=3 and Student_ID=$student_id and University_ID=$uniid and Source='University Fee'");
    // print_r($check->num_rows);die;
    if($check->num_rows>0){
        $feeid = $check->fetch_assoc();
        $update = $conn->query("update Student_Ledgers set Fee=$amount where ID=".$feeid['ID']);
        echo json_encode(['status' => 200, 'message' => "University Fee Updated succefully!!"]);
        exit;
    }
    
    $query = "insert into Student_Ledgers (Date,Student_ID,Duration,University_ID,Type,Source,Fee,Status) value(now(),$student_id,1,$uniid,3,'University Fee',$amount,1)";
    $run = $conn->query($query);
    if($run)
    {
        echo json_encode(['status' => 200, 'message' => "University Fee Created succefully!!"]);
    }
    else
    {
        echo json_encode(['status' => 400, 'message' => "Something went wrong"]);
    }
}

?>
<?php



if($_POST['id']!='' )
{
    require '../../includes/db-config.php';
    $amount = $_POST['amount'];
    $utr_no = $_POST['utr_no'];
    $mode_of_payment = $_POST['mode_of_payment'];
    $id = $_POST['id'];
    $query = "UPDATE Student_Ledgers set Fee=$amount, utr_no='$utr_no',mode_of_payment='$mode_of_payment' where ID=$id";
    $run = $conn->query($query);
    if($run)
    {
        echo json_encode(['status' => 200, 'message' => "Payment updated succefully!!"]);
    }
    else
    {
        echo json_encode(['status' => 400, 'message' => "Something went wrong"]);
    }
}

?>
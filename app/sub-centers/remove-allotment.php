<?php
if ( isset($_POST['id'])) {
    require '../../includes/db-config.php';

    session_start();

    $id = intval($_POST['id']);
    $remove = $conn->query("DELETE FROM Sub_Center_Course_Types WHERE `User_ID` = $id ");
    $remove = $conn->query("DELETE FROM Sub_Center_Sub_Courses WHERE `User_ID` = $id ");

    if ($remove) {
        echo json_encode(['status' => 200, 'message' => 'Allotment removed successfully!']);
    } else {
        echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
    }
}

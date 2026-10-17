<?php
require '../../includes/db-config.php';
if (isset($_POST['university'])) {
    $id_uni = $_POST['university'];
    session_start();
    $sql = ($id_uni == "All") ? '' : "AND University_ID=" . $id_uni;
    $option = "<option value=''>Select Admission Session</option>";
    $option .= "<option value='All'>All</option>";
    $query = "SELECT Admission_Sessions.Name, Admission_Sessions.ID, Universities.Name AS uni_name 
              FROM Admission_Sessions 
              LEFT JOIN Universities ON Admission_Sessions.University_ID = Universities.ID 
              WHERE Admission_Sessions.Status = 1 $sql";
    $university_id = $conn->query($query);
    while ($row = $university_id->fetch_assoc()) {
        $u_name = ($id_uni == "All") ? ' (' . $row['uni_name'] . ')' : '';
        $option .= '<option value="' . $row['Name'] . '">' . $row['Name'] . $u_name . '</option>';
    }
    echo $option;
} else {
    echo "University ID not set.";
}
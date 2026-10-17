<?php
## Database configuration
include '../../includes/db-config.php';

ini_set('display_errors', 1);
session_start();
$universityQuery = "";
if($_GET['uniId'] && $_GET['uniId']!="" && $_GET['uniId'] !='All')
{
    $universityQuery = "AND `Students`.`University_ID` = ". $_GET['uniId'];
}
if (isset($_SESSION['current_session'])) {
    if ($_SESSION['current_session'] == 'All') {
        $session_query = '';
    } else {
        $session_query = "AND Admission_Sessions.Name like '%" . $_SESSION['current_session'] . "%'";
    }
} else {
    $get_current_session = $conn->query("SELECT Name FROM Admission_Sessions WHERE Current_Status = 1");
    if ($get_current_session->num_rows > 0) {
        $gsc = mysqli_fetch_assoc($get_current_session);
        $session_query = ''; //kp
        // $session_query = "AND Admission_Sessions.Name like '%" . $gsc['Name'] . "%'";
    } else {
        $session_query = '';
    }
}

$role_query = str_replace('{{ table }}', 'Students', $_SESSION['RoleQuery']);
$role_query = str_replace('{{ column }}', 'Added_For', $role_query);
$step_query = "";

## Search 
$searchQuery = " ";

$filterByDepartment = "";
if (isset($_SESSION['filterByDepartment'])) {
    $filterByDepartment = $_SESSION['filterByDepartment'];
}

$filterBySubCourse = "";
if (isset($_SESSION['filterBySubCourses'])) {
    $filterBySubCourse = $_SESSION['filterBySubCourses'];
}

$filterByStatus = "";
if (isset($_SESSION['filterByStatus'])) {
    $filterByStatus = $_SESSION['filterByStatus'];
}

$filterQueryUser = "";
if (isset($_SESSION['filterByUser'])) {
    $filterQueryUser = $_SESSION['filterByUser'];
}

$filterByDate = "";
if (isset($_SESSION['filterByDate'])) {
    $filterByDate = $_SESSION['filterByDate'];
}

$filterByFeeAssigned = "";
if (isset($_SESSION['filterByFeeAssigned'])) {
    $filterByFeeAssigned = $_SESSION['filterByFeeAssigned'];
}

$searchQuery .= $filterByDepartment . $filterQueryUser . $filterByDate . $filterBySubCourse . $filterByStatus . $filterByFeeAssigned;
//University Head

$whereQuery = "";
if ($_SESSION['Role'] == 'University Head') {
    $universities = implode(',', $_SESSION['Alloted_Universities']);
    $whereQuery = " AND Students.University_ID IN ($universities)";
}

// echo $searchQuery; die;

## Total number of records without filtering

## Total number of record with filtering


## Fetch records
$result_record = "SELECT Students.ID as Student_Unique_ID, Student_Pendencies.ID as Pendency,Students.University_ID, Student_Pendencies.Status as Pendency_Status, UPPER(DATE_FORMAT(Students.DOB, '%d%b%Y')) as DOB, Students.Status, Students.Created_At as Creation_Date, Students.ID, Students.Added_For, CONCAT(TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name)), ' (', IF(Students.Unique_ID='' OR Students.Unique_ID IS NULL, RIGHT(CONCAT('000000', Students.ID), 6), Students.Unique_ID), ')') as Unique_ID, CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name) as First_Name,Students.Course_ID, Students.Sub_Course_ID, Students.Father_Name, Students.Enrollment_No, Students.OA_Number, Students.Duration,
Students.Course_Category, Students.Step, Students.Process_By_Center, Students.Payment_Received, Students.Document_Verified, Students.Processed_To_University, Admission_Sessions.`Name` as Adm_Session, Admission_Types.`Name` as Adm_Type, CONCAT(Courses.Short_Name, ' (', Sub_Courses.Name, ')') as Short_Name, Student_Documents.`Location`, Students.ID_Card, Students.Admit_Card, Students.Exam, Studnent_Sub_Course_Fee.Fee AS sub_course_fee, Universities.Name as university_name,Courses.Name as courseName FROM Students
 LEFT JOIN Universities on Students.University_ID = Universities.ID
  LEFT JOIN Student_Pendencies ON Students.ID = Student_Pendencies.Student_ID AND Student_Pendencies.Status != 1 
  LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID
   LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID 
   LEFT JOIN Courses ON Students.Course_ID = Courses.ID 
   LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID 
   LEFT JOIN Student_Documents ON Students.ID = Student_Documents.Student_ID AND Student_Documents.`Type` = 'Photo' 
   LEFT JOIN Studnent_Sub_Course_Fee ON Students.ID = Studnent_Sub_Course_Fee.Student_ID WHERE Students.University_ID IS NOT NULL $universityQuery $whereQuery $searchQuery $role_query $step_query $session_query";
$empRecords = mysqli_query($conn, $result_record);
$data = array();
// print_r($result_record) ;die('SD');






















while ($row = mysqli_fetch_assoc($empRecords)) {
    // Added_For
    if ($_SESSION['Role'] == 'Center') {
        $user = $conn->query("SELECT ID, Code, Name FROM Users WHERE ID = " . $row['Added_For'] . "");
        if ($user->num_rows == 0) {
            $user = $conn->query("SELECT Users.ID, Code, Name FROM Users LEFT JOIN Center_SubCenter ON Users.ID = Center_SubCenter.Center WHERE `Sub_Center` = " . $row['Added_For']);
        }
    } else {
        $user = $conn->query("SELECT ID, Code, Name FROM Users WHERE ID = " . $row['Added_For'] . " AND Role = 'Center'");
        if ($user->num_rows == 0) {
            $user = $conn->query("SELECT Users.ID, Code, Name FROM Users LEFT JOIN Center_SubCenter ON Users.ID = Center_SubCenter.Center WHERE `Sub_Center` = " . $row['Added_For']);
        }
    }

    $user = mysqli_fetch_array($user);
    // Sub_Center Name 
    $sub_centers = [];
    $sub_centers['Name'] = "";
    if (!empty($user)) {
        $sub_centers = $conn->query("SELECT Users.ID, Code, Name FROM Users LEFT JOIN Center_SubCenter ON Users.ID = Center_SubCenter.Sub_Center WHERE `Sub_Center` = " . $row['Added_For']);
        $sub_centers = mysqli_fetch_array($sub_centers);
    } else {
        $sub_centers = '';
    }

    // RM
    $rm['Name'] = "";
    if (!empty($user)) {
        // RM
        $rm = $conn->query("SELECT CONCAT(Users.Name, ' (', Users.Code, ')') as Name FROM Alloted_Center_To_Counsellor LEFT JOIN Users ON Alloted_Center_To_Counsellor.Counsellor_ID = Users.ID  WHERE Alloted_Center_To_Counsellor.Code = " . $user['ID'] . " ");
        if ($rm->num_rows > 0) {
            $rm = mysqli_fetch_array($rm);
        } else {
            $rm = $user;
        }
    }

    $get_ledger_stu_data = $conn->query("SELECT Fee,Type FROM Student_Ledgers WHERE Student_ID = '" . $row['ID'] . "'");
    $debited_fee = 0;
    $credited_fee = [];
    $fee_arr = [];
    while ($fee_arr = $get_ledger_stu_data->fetch_assoc()) {
        if ($fee_arr['Type'] == 1) {
            $debited_fee = $fee_arr['Fee'];
        }
        if ($fee_arr['Type'] == 2) {
            $credited_fee[] = $fee_arr['Fee'];
        }
    }
    if ($debited_fee == 0) {
        $course_fee = "Fee Not Initiated";
    } else {
        $course_fee = array_sum($credited_fee) . '/' . $debited_fee;
    }
        //Start Total Miscellaneous Fee
    // unpaid fee
    $total_mis_fee_sql = $conn->query("SELECT SUM(Fee) as total_mis_fee FROM `Student_Ledgers` WHERE Source='Miscellaneous Type Fee' AND Type =1 AND Student_ID =" . $row['ID']);
    $total_mis_fee_sql = $total_mis_fee_sql->fetch_assoc();
    $total_mis_fee = $total_mis_fee_sql['total_mis_fee'] ?? 0;

    // paid fee
    $paid_mis_fee_sql = $conn->query("SELECT SUM(Fee) as paid_mis_fee FROM `Student_Ledgers` WHERE Source='Miscellaneous Type Fee' AND Type =2 AND Student_ID =" . $row['ID']);
    $paid_mis_fee_sql = $paid_mis_fee_sql->fetch_assoc();
    $paid_mis_fee = $paid_mis_fee_sql['paid_mis_fee'] ?? 0;

    // pending fee
    $pending_mis_fee = (int) $total_mis_fee - (int) $paid_mis_fee;
    //End Total Miscellaneous Fee

    // start Miscellaneous university Fee
     $mis_uni_fee_arr = getMiscellaneousUniFee($conn,$row['ID']);
    // end Miscellaneous university Fee
    $university_fee = $conn->query("select Fee from Student_Ledgers where type=3 and Student_ID =" . $row['ID']);
    $total_university_fee = 0;
    $unifeearr = $university_fee->fetch_assoc();
    if(isset($unifeearr['Fee']) && $unifeearr['Fee']!=""){
        $total_university_fee = $unifeearr['Fee'];
    }
    $total_university_paid_fee = "";
    $total_university_paid_fee1 = 0;
    $university_paid_fee = $conn->query("select sum(amount) as Paid_uni_fee from university_payment where student_id=". $row['ID']);
    $total_university_paid_fee = $university_paid_fee->fetch_assoc();
    if(isset($total_university_paid_fee['Paid_uni_fee'])){
        $total_university_paid_fee1 = $total_university_paid_fee['Paid_uni_fee'];
    }
    
    //  print_r();
    $data[] = array(
        "Student_Unique_Id" => $row['Student_Unique_ID'],
        "University_ID" => $row['University_ID'],
        "Photo" => empty($row['Location']) ? '/assets/img/default-user.png' : $row['Location'],
        "First_Name" => $row['First_Name'],
        "Father_Name" => $row['Father_Name'],
        "Unique_ID" => $row['Unique_ID'],
        "Enrollment_No" => !empty($row['Enrollment_No']) ? $row['Enrollment_No'] : '',
        "OA_Number" => !empty($row['OA_Number']) ? $row['OA_Number'] : '',
        "Duration" => $row['Duration'],
        "Step" => $row['Step'],
        "Process_By_Center" => !empty($row['Process_By_Center']) ? date("d-m-Y", strtotime($row['Process_By_Center'])) : "1",
        "Payment_Received" => !empty($row['Payment_Received']) ? date("d-m-Y", strtotime($row['Payment_Received'])) : "1",
        "Document_Verified" => !empty($row['Document_Verified']) ? date("d-m-Y", strtotime($row['Document_Verified'])) : "1",
        "Processed_To_University" => !empty($row['Processed_To_University']) ? date("d-m-Y", strtotime($row['Processed_To_University'])) : '1',
        "Adm_Session" => $row['Adm_Session'],
        "Adm_Type" => $row['Adm_Type'],
        "Short_Name" => $row['Short_Name'],
        "Center_Code" => isset($user['Code']) ? $user['Code'] : '',
        "Center_Name" => isset($user['Name']) ? $user['Name'] : '',
        "Sub_Center_Name" => (!empty($sub_centers['Name']) && $_SESSION['Role'] != 'Center') ? $sub_centers['Name'] : '',
        "RM" => $rm['Name'],
        "Status" => $row['Status'],
        "DOB" => $row['DOB'],
        "ID_Card" => $row['ID_Card'],
        "courseName" => $row['courseName'],
        "Admit_Card" => $row['Admit_Card'],
        "Exam" => $row['Exam'],
        "Pendency" => empty($row['Pendency']) ? 0 : (int)$row['Pendency'],
        "Pendency_Status" => empty($row['Pendency_Status']) ? 0 : (int)$row['Pendency_Status'],
        "ID" => base64_encode($row['ID'] . 'W1Ebt1IhGN3ZOLplom9I'),
        "Course_ID" => $row['Course_ID'],
        "Sub_Course_ID" => $row['Sub_Course_ID'],
        "Added_For" => $row['Added_For'],
        "Fee" => $course_fee,
        "university_name" => $row['university_name'],
        "Creation_Date" => $row['Creation_Date'],
        "total_mis_fee" => $total_mis_fee,
        'paid_mis_fee' => $paid_mis_fee,
        'pending_mis_fee' => $pending_mis_fee,
        'total_misc_uni_fee'=>$mis_uni_fee_arr['total_misc_uni_fee'],
        'total_misc_uni_paid_fee'=>$mis_uni_fee_arr['total_misc_uni_paid_fee'],
        'total_pending_misc_uni_fee'=>$mis_uni_fee_arr['total_pending_misc_uni_fee'],
        'total_university_fee'=>$total_university_fee,
        'total_paid_university_fee'=>$total_university_paid_fee1,
        'pending_uni_fee' => (int)$total_university_fee - (int)$total_university_paid_fee1,
    );
    // print_r($data);


}
// die;

// echo "<pre>";print_r($data);die;
## Response

if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'csv') {
    if (count($data) > 0) {
        $delimiter = ",";
        $filename = "application_data_" . date('Y-m-d') . ".csv";
        // Set headers for file download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        // Open file pointer to output stream
        $f = fopen('php://output', 'w');
        // Set CSV column headers
        $fields = array('Unique Id','First_Name', 'Status', 'Process_By_Center', 'Document_Verified', 'Payment_Received', 'Processed_To_University', 'Enrollment_No', 'Adm_Session', 'Adm_Type', 'Pendency', 'Father_Name', 'courseName','Paid Fee', 'Total Fee', 'Pending Fee', 'Duration', 'DOB', 'Center_Code', 'Center_Name', 'university_name','Creation_Date','Total Miscellaneous University Fee',  'Miscellaneous University Paid Fee', 'Pending Miscellaneous University Fee','Total Universiyt Fee','Paid University Fee','Pending University Fee');
        fputcsv($f, $fields, $delimiter);
        // Write rows to CSV
        foreach ($data as $row) {
            $debt = ($row['Fee'] != "Fee Not Initiated") ? explode('/', $row['Fee'])[1] : 0;
            $credit = ($row['Fee'] != "Fee Not Initiated") ? explode('/', $row['Fee'])[0] : 0;
            $balance =  $debt - $credit;
            $lineData = array(    
                $row['Student_Unique_Id'],
                $row['First_Name'],
                $row['Status'],
                $row['Process_By_Center'],
                $row['Document_Verified'],
                $row['Payment_Received'],
                $row['Processed_To_University'],
                $row['Enrollment_No'],
                $row['Adm_Session'],
                $row['Adm_Type'],
                $row['Pendency'],
                $row['Father_Name'],
                $row['courseName'],
                $credit,
                $debt,
                $balance,
                $row['Duration'],
                $row['DOB'],
                $row['Center_Code'],
                $row['Center_Name'],
                $row['university_name'],
                $row['Creation_Date'],
                $row['total_misc_uni_fee'],
                $row['total_misc_uni_paid_fee'],
                $row['total_pending_misc_uni_fee'],
                $row['total_university_fee'],
                $row['total_paid_university_fee'],
                $row['pending_uni_fee'],
               
            );
            fputcsv($f, $lineData, $delimiter);
        }
        fclose($f);
        exit();
    } else {
        echo "No data available to download.";
    }
}


function getMiscellaneousUniFee($conn, $student_id)
{
    // Total Miscellaneous University Fee
    $total_mis_uni_fee_sql = $conn->query("SELECT SUM(University_Fee) as total_misc_uni_fee FROM `Student_Ledgers` WHERE Source='Miscellaneous Type Fee' AND Type =1 AND Student_ID =" . $student_id);
    $total_mis_uni_fee_sql = $total_mis_uni_fee_sql->fetch_assoc();
    $total_misc_uni_fee = $total_mis_uni_fee_sql['total_misc_uni_fee'] ?? 0;

    // Paid Miscellaneous University Fee
    $total_misc_uni_paid_fee_sql = $conn->query("SELECT SUM(amount) as fee FROM university_payment where Student_ID = $student_id AND payment_type = 1");
    $total_misc_uni_paid_fee_sql = $total_misc_uni_paid_fee_sql->fetch_assoc();
    $total_misc_uni_paid_fee = $total_misc_uni_paid_fee_sql['fee'] ?? 0;

    // total pending Miscellaneous University Fee
    $total_pending_misc_uni_fee = $total_misc_uni_fee - $total_misc_uni_paid_fee;

      return [
        'total_misc_uni_fee' => (float)$total_misc_uni_fee,
        'total_misc_uni_paid_fee' => (float)$total_misc_uni_paid_fee,
        'total_pending_misc_uni_fee' => (float)$total_pending_misc_uni_fee,
    ];
}

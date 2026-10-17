<?php
if (isset($_POST['id']) && isset($_POST['by'])) {
  session_start();
  require '../../includes/db-config.php';
  $by = $_POST['by'];
  $id = intval($_POST['id']);
  $sub_center_name = "";
  if ($by == 'sub_courses') {
    if ($_POST['id'] == 'All') {
      $_SESSION['filterBySubCourses'] = '';
    } else {
      $_SESSION['filterBySubCourses'] = " AND Students.Sub_Course_ID = $id";
    }
  } elseif ($by == 'universities') {
    if ($_POST['id'] == 'All') {
      $_SESSION['filterByUniversities'] = "";
      $_SESSION['current_session'] = '';
    } else {
      $_SESSION['filterByUniversities'] = " AND Students.University_ID = $id";
      $_SESSION['current_session'] = '';
    }
  } elseif ($by == 'users') {
    if ($_POST['id'] == 'All') {
      $_SESSION['filterByUser'] = '';
    } else {
      $user = $conn->query("SELECT Role FROM Users WHERE ID = $id");
      $user = $user->fetch_assoc();
      $role = $user['Role'];
      $role_query = " AND Students.Added_For = $id";
      if ($role === 'Center') {
        $center_uni_ids = [];
        $get_university_ids = $conn->query("SELECT University_ID FROM Alloted_Center_To_Counsellor WHERE Code = $id");
        while ($CenterArr = $get_university_ids->fetch_assoc()) {
          $center_uni_ids[] = $CenterArr['University_ID'];
        }
        $center_university_ids = '';
        $center_university_ids = implode(',', $center_uni_ids);
        $role_query = " AND Students.Added_For IN ($id) AND Students.University_ID IN($center_university_ids)";
      } elseif ($role == 'Sub-Center') {
        $role_query = " AND Students.Added_For IN ($id) AND Students.University_ID = " . $_SESSION['University_ID'];
      }
      $_SESSION['filterByUser'] = $role_query;
    }
  } elseif ($by == 'date') {
    $startDate = date("Y-m-d 00:00:00", strtotime($_POST['startDate']));
    $endDate = date("Y-m-d 23:59:59", strtotime($_POST['endDate']));
    $_SESSION['filterByDate'] = " AND Students.Created_At BETWEEN '$startDate' AND '$endDate'";
  } elseif ($by == 'search_assign_fee') {
    if ($_POST['id'] == 'All') {
      $_SESSION['filterByFeeAssigned'] = '';
    } else {
      $get_ledger_stu_data = $conn->query("SELECT Student_ID, Fee 
            FROM Student_Ledgers 
            WHERE University_ID = '" . $_SESSION['University_ID'] . "' AND Type = 1 
            GROUP BY Student_ID 
            ORDER BY Student_ID ASC
        ");
      $stu_ledger_id_arr = [];
      while ($stu_ledger_id = $get_ledger_stu_data->fetch_assoc()) {
        $stu_ledger_id_arr[] = $stu_ledger_id['Student_ID'];
      }
      $fee_alloted_ids = implode(',', $stu_ledger_id_arr);
      if (isset($id) && $id == 1) {
        $_SESSION['filterByFeeAssigned'] = " AND Students.ID IN (" . $fee_alloted_ids . ")";
      } else {
        $_SESSION['filterByFeeAssigned'] = " AND Students.ID NOT IN (" . $fee_alloted_ids . ")";
      }
    }
  }










  echo json_encode(['status' => true, 'subCenterName' => $sub_center_name]);
}
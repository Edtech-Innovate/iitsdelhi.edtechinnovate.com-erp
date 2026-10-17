<?php
ini_set('display_errors', 1);
if (isset($_POST['id'])) {
  require '../../includes/db-config.php';
  session_start();
  $id = intval($_POST['id']);
  foreach ($_POST['adm_session'] as $sub_course => $sessionArr) {
    $session_ids[$sub_course] = implode(',', $sessionArr);
  }
  $uni = array_unique($_POST['university_id']);
  $university_id = implode(',', $uni);
  $sub_course = is_array($_POST['sub_course']) ? $_POST['sub_course'] : [];
  $sub_courseArr = array_filter($sub_course);
  $course_types = is_array($_POST['course_id']) ? $_POST['course_id'] : [];
  $course_types = array_values(array_unique($course_types));

  if (empty($university_id) || empty($id) || empty($sub_courseArr)) {
    echo json_encode(['status' => 403, 'message' => 'Missing required fields!']);
    exit();
  }

  $course_to_university = array();
  $conn->query("DELETE FROM Sub_Center_Course_Types WHERE `User_ID` = $id AND University_ID IN ($university_id)");
  for ($i = 0; $i < count($course_types); $i++) {
    list($course_type, $university_ids) = explode('|', $course_types[$i]);
    $course_to_university[$course_type] = $university_ids;
    $conn->query("INSERT INTO Sub_Center_Course_Types (`User_ID`, `Course_Type_ID`, `University_ID`) VALUES ($id, $course_type, $university_ids)");
  }

  $conn->query("DELETE FROM Sub_Center_Sub_Courses WHERE `User_ID` = $id AND University_ID IN ($university_id)");
  foreach ($sub_courseArr as $sub_course_id => $courses_ids) {
    $adm_session_id = $session_ids[$sub_course_id];
    $university_id = $course_to_university[$courses_ids];
    $allot = $conn->query("INSERT INTO Sub_Center_Sub_Courses (`Duration`, `User_ID`, `Course_ID`, `Sub_Course_ID`,`Admission_Sessions_ID`, `University_ID`) VALUES (1, $id, $courses_ids, $sub_course_id,'$adm_session_id', $university_id)");
  }

  if ($allot) {
    echo json_encode(['status' => 200, 'message' => 'University alloted successfully!']);
  } else {
    echo json_encode(['status' => 403, 'message' => 'Unable to allot university!']);
  }
}
?>
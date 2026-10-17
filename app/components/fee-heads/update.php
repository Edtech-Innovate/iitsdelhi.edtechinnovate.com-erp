<?php
if (isset($_POST['name']) && isset($_POST['id'])) {

  require '../../../includes/db-config.php';
  session_start();

  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $id   = intval($_POST['id']);

  if (empty($name) || empty($id)) {
    echo json_encode([
      'status' => 403,
      'message' => 'All fields are mandatory!'
    ]);
    exit();
  }

  // Check duplicate fee head (exclude current ID)
  $check = $conn->query("
    SELECT ID 
    FROM Fee_Heads 
    WHERE Name LIKE '$name' 
    AND ID <> $id
  ");

  if ($check->num_rows > 0) {
    echo json_encode([
      'status' => 400,
      'message' => $name . ' already exists!'
    ]);
    exit();
  }

  // Update fee head
  $update = $conn->query("
    UPDATE Fee_Heads 
    SET Name = '$name' 
    WHERE ID = $id
  ");

  if ($update) {
    echo json_encode([
      'status' => 200,
      'message' => $name . ' updated successfully!'
    ]);
  } else {
    echo json_encode([
      'status' => 400,
      'message' => 'Something went wrong!'
    ]);
  }
}
?>

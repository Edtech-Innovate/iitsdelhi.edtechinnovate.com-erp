<?php
if (isset($_POST['name'])) {

  require '../../../includes/db-config.php';
  session_start();

  $name = mysqli_real_escape_string($conn, $_POST['name']);

  if (empty($name)) {
    echo json_encode([
      'status' => 403,
      'message' => 'Fee Head name is required!'
    ]);
    exit();
  }

  // Check duplicate fee head
  $check = $conn->query("
    SELECT ID 
    FROM Fee_Heads 
    WHERE Name LIKE '$name'
  ");

  if ($check->num_rows > 0) {
    echo json_encode([
      'status' => 400,
      'message' => $name . ' already exists!'
    ]);
    exit();
  }

  // Insert fee head
  $add = $conn->query("
    INSERT INTO Fee_Heads (Name)
    VALUES ('$name')
  ");

  if ($add) {
    echo json_encode([
      'status' => 200,
      'message' => $name . ' added successfully!'
    ]);
  } else {
    echo json_encode([
      'status' => 400,
      'message' => 'Something went wrong!'
    ]);
  }
}
?>

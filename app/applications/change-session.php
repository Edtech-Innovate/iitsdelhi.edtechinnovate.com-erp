<?php
session_start();
if (isset($_POST['session_id'])) {
  if ($_POST['session_id'] == 'All') {
    $_SESSION['current_session'] = '';
  } else {
    $_SESSION['current_session'] = $_POST['session_id'];
    print_r($_SESSION['current_session']);
  }
}
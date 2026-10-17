<?php
// error_reporting(1);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

if (isset($_GET['student_id'])) {
  require '../../includes/db-config.php';
  session_start();
  $id = mysqli_real_escape_string($conn, $_GET['student_id']);
    $id = base64_decode($id);
    $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));
   $student = $conn->query("SELECT * FROM Students WHERE Students.ID = $id");
    $student = mysqli_fetch_assoc($student);
  if ($student['University_ID'] == 55 || $student['University_ID'] == 55) {
    $id = mysqli_real_escape_string($conn, $_GET['student_id']);
    $id = base64_decode($id);
    $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));
    $student = $conn->query("SELECT Students.*, Courses.Name as Course, Sub_Courses.Name as Sub_Course, Admission_Sessions.Name as `Session`, Admission_Types.Name as Type FROM Students LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID WHERE Students.ID = $id");
    $student = mysqli_fetch_assoc($student);
    $address = json_decode($student['Address'], true);

    require_once('../../extras/vendor/setasign/fpdf/fpdf.php');
    require_once('../../extras/vendor/setasign/fpdi/src/autoload.php');
    $check = '../../assets/img/form/checked.png';

    if ($student['Course'] == 'CERTIFICATION') {
      $pdf = new Fpdi();
      $pdf->SetTitle('Application Form');
      $pageCount = $pdf->setSourceFile('Glocal-certificate.pdf');
      $pdf->SetFont('Arial', 'B', 11);
      $checke_image = '../../assets/img/form/checked.png';
      // Extensions
      $file_extensions = array('.png', '.jpg', '.jpeg');

      //this folder will have there images.
      $path = "photos/";

      // Photo
      $student_photo = "";
      $photo = "";
      $photo = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Photo'");
      if ($photo->num_rows > 0) {
        $photo = mysqli_fetch_assoc($photo);
        $photo = "../.." . $photo['Location'];
        $student_photo = base64_encode(file_get_contents($photo));
        $i = 0;
        $end = 3;
        while ($i < $end) {
          $data1 = base64_decode($student_photo);

          $filename1 = $id . "_Photo" . $file_extensions[$i];
          file_put_contents($filename1, $data1); //we save our new images to the path above
          $i++;
        }
      } else {
        $photo = "";
      }

      // Page 1
      $pageId = $pdf->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
      $pdf->addPage();
      $pdf->useImportedPage($pageId, 0, 0, 210);

      $pdf->SetFont('Arial', '', 11);

      // Student Name
      if ($student['Gender'] == "Female") {
        $pdf->Image($checke_image, 23, 76, 3, 3);
      } else if ($student['Gender'] == "Female") {
        if ($student['Marital_Status'] == "Unmarried") {
          $pdf->Image($checke_image, 47, 76, 3, 3);
        } else {
          $pdf->Image($checke_image, 73, 76, 3, 3);
        }
      }

      $pdf->SetXY(116, 77.5);
      $pdf->Write(1, $student['First_Name'] . '' . $student['Middle_Name'] . $student['Lastt_Name']);

      // Enrollment No.
      $enrollment_no = str_split($student['Enrollment_No']);
      $x = 34.8;
      foreach ($enrollment_no as $enroll) {
        $pdf->SetXY($x, 60.8);
        $pdf->Write(1, $enroll);
        $x += 3.97;
      }

      // Photo
      if (filetype($photo) === 'file' && file_exists($photo)) {
        try {
          $filename = $id . "_Photo" . $file_extensions[0];
          $image = $filename;
          $pdf->Image($image, 163, 23, 30.5, 35.9);
          $photo = $image;
        } catch (Exception $e) {
          try {
            $filename = $id . "_Photo" . $file_extensions[1];
            $image = $filename;
            $pdf->Image($image, 163, 23, 30.5, 35.9);
            $photo = $image;
          } catch (Exception $e) {
            try {
              $filename = $id . "_Photo" . $file_extensions[2];
              $image = $filename;
              $pdf->Image($image, 163, 23, 30.5, 35.9);
              $photo = $image;
            } catch (Exception $e) {
              echo 'Message: ' . $e->getMessage();
            }
          }
        }
      }

      // Father Name
      $student_name = str_split($student['Father_Name']);

      $x = 76;
      foreach ($student_name as $name) {
        $pdf->SetXY($x, 91);
        $pdf->Write(1, $name);
        $x += 3.9;
      }

      // DOB
      $dob = str_split($student['DOB']);
      // Day
      $pdf->SetXY(40, 97.5);
      $pdf->Write(1, $dob[8]);
      $pdf->SetXY(42, 97.5);
      $pdf->Write(1, $dob[9] . '/');
      // Month
      $pdf->SetXY(45.5, 97.5);
      $pdf->Write(1, $dob[5]);
      $pdf->SetXY(48, 97.5);
      $pdf->Write(1, $dob[6] . '/');
      // Year
      $pdf->SetXY(51.5, 97.5);
      $pdf->Write(1, $dob[0]);
      $pdf->SetXY(53.5, 97.5);
      $pdf->Write(1, $dob[1]);
      $pdf->SetXY(55.5, 97.5);
      $pdf->Write(1, $dob[2]);
      $pdf->SetXY(57.5, 97.5);
      $pdf->Write(1, $dob[3]);

      // Mobile
      $pdf->SetFont('Arial', '', 10);
      $pdf->SetXY(111, 97.5);
      $pdf->Write(1, $student['Contact']);

      // Gender
      $gender = $student['Gender'] == "Male" ? "Male" : "Female";
      $pdf->SetXY(29, 104);
      $pdf->Write(1, $gender);

      // Email
      $pdf->SetXY(117.5, 104);
      $pdf->Write(1, $student['Email']);

      // Category
      if ($student['Category'] == "SC") {
        $pdf->Image($checke_image, 47, 109, 3, 3);
      } else if ($student['Category'] == "ST") {
        $pdf->Image($checke_image, 70.5, 109, 3, 3);
      } else if ($student['Category'] == "OBC") {
        $pdf->Image($checke_image, 95.5, 109, 3, 3);
      } else if ($student['Category'] == "Ganeral") {
        $pdf->Image($checke_image, 128, 109, 3, 3);
      }


      // Country
      $pdf->SetXY(154, 118);
      $pdf->Write(1, $student['Nationality']);

      // Permanent Address
      $pdf->SetXY(31, 118);
      $pdf->Write(1, $address['present_address']);

      // City
      // $pdf->SetFont('Arial', '', 10);
      $pdf->SetXY(45, 118);
      $pdf->Write(1, substr($address['present_city'], 0, 15) . ', ' . $address['present_district']);

      $pdf->SetXY(30, 124);
      $pdf->Write(1, $address['present_district']);

      // City
      // $pdf->SetFont('Arial', '', 10);
      $pdf->SetXY(115.5, 124);
      $pdf->Write(1, substr($address['present_state'], 0, 18));

      // Pincode
      $permanent_pincode = str_split($address['present_pincode']);
      $x = 165;
      for ($i = 0; $i < count($permanent_pincode); $i++) {
        $pdf->SetXY($x, 124);
        $pdf->Write(1, $permanent_pincode[$i]);
        $x += 3.92;
      }

      // Adhar
      // $pdf->SetXY(154, 111);
      // $pdf->Write(1, $student['Aadhar_Number']);
      // preg_replace('/[^a-zA-Z0-9_ -]/s',' ',$str);
      $permanent_pincode = str_split($student['Aadhar_Number']);
      $x = 45;
      for ($i = 0; $i < count($permanent_pincode); $i++) {
        if ($permanent_pincode[$i] != '-') {
          $pdf->SetXY($x, 136);
          $pdf->Write(1, $permanent_pincode[$i]);
          $x += 5;
        }
      }

      // Academics
      // $academis = array(
      //   'High School', 'Intermediate', 'Under Graduation',
      //   'Post Graduation', 'Other'
      // );
      // $y = '210';
      // foreach ($academis as $academic) {
      //   $x = '53';

      //   // Details
      //   $type = $academic == 'Under Graduation' ? 'UG' : ($academic ==
      //     'Post Graduation' ? 'PG' : $academic);
      //   $data = $conn->query("SELECT * FROM Student_Academics WHERE
      //   Student_ID = $id AND Type = '$type'");
      //   if ($data->num_rows > 0) {

      //     $data = mysqli_fetch_assoc($data);

      //     // $pdf->SetXY($x, $y);
      //     // $pdf->Write(1, $academic);
      //     $x += 3;
      //     $pdf->SetXY($x, $y);
      //     $pdf->Write(1, !empty($data['Board/Institute']) ?
      //       substr($data['Board/Institute'], 0, 28) : '');

      //     $x += 33;
      //     $pdf->SetXY($x, $y);
      //     $pdf->Write(1, !empty($data['Board/Institute']) ?
      //       substr($data['Board/Institute'], 0, 28) : '');

      //     $x += 30;
      //     $pdf->SetXY($x, $y);
      //     $pdf->Write(1, !empty($data['Year']) ? $data['Year'] : '');

      //     $x += 11;
      //     $pdf->SetXY($x, $y);
      //     $pdf->Write(1, !empty($data['Subject']) ? $data['Subject'] : '');

      //     $x += 30;
      //     $pdf->SetXY($x, $y);
      //     $pdf->Write(1, !empty($data['Marks_Obtained']) ? $data['Marks_Obtained'] : '');

      //     $x += 15;
      //     $pdf->SetXY($x, $y);
      //     $pdf->Write(1, !empty($data['Total_Marks']) ? $data['Total_Marks'] : '');

      //     // Roll No
      //     $x += 48;
      //     $pdf->SetXY($x, $y);
      //     $pdf->Write(1, !empty($data['Marks_Obtained']) ? $data['Marks_Obtained'] : '');
      //   }
      //   $y += 8;
      // }

      $i = 0;
      $end = 3;
      while ($i < $end) {
        // Delete Photos
        if (!empty($student_photo)) {
          $filename = $id . "_Photo" . $file_extensions[$i];
          //$file_extensions loops through the file extensions
          unlink($filename);
        }

        // Delete Signatures
        if (!empty($student_signature)) {
          $filename = $id . "_Student_Signature" . $file_extensions[$i];
          //$file_extensions loops through the file extensions
          unlink($filename);
        }
        $i++;
      }

      $pdf->Output('I', ' Application Form.pdf');
    } else {
      $pdf = new Fpdi();
      $pdf->SetTitle('Application Form');
      $pageCount = $pdf->setSourceFile('GLOCAL APPLICATION FORM.pdf');
      $pdf->SetFont('Arial', 'B', 11);

      // Tick Image
      $check = 'checked.png';

      // Extensions
      $file_extensions = array('.png', '.jpg', '.jpeg');

      //this folder will have there images.
      $path = "photos/";

      // Photo
      $student_photo = "";
      $photo = "";
      $photo = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Photo'");
      if ($photo->num_rows > 0) {
        $photo = mysqli_fetch_assoc($photo);
        $photo = "../.." . $photo['Location'];
        $student_photo = base64_encode(file_get_contents($photo));
        $i = 0;
        $end = 3;
        while ($i < $end) {
          $data1 = base64_decode($student_photo);

          $filename1 = $id . "_Photo" . $file_extensions[$i];
          // print_r($filename1);
          // exit();
          //$file_extensions loops through the file extensions
          file_put_contents($filename1, $data1); //we save our new images to the path above
          $i++;
        }
      } else {
        $photo = "";
      }

      // Signature
      $student_signature = "";
      $signature = "";
      $signature = $conn->query("SELECT Location FROM Student_Documents
    WHERE Student_ID = $id AND Type = 'Student Signature'");
      if ($signature->num_rows > 0) {
        $signature = mysqli_fetch_assoc($signature);
        $signature = "../.." . $signature['Location'];
        $student_signature = base64_encode(file_get_contents($signature));
        $i = 0;
        $end = 3;
        while ($i < $end) {
          $data2 = base64_decode($student_signature);
          $filename2 = $id . "_Student_Signature" . $file_extensions[$i];
          //$file_extensions loops through the file extensions
          file_put_contents($filename2, $data2); //we save our new images to the path above
          $i++;
        }
      } else {
        $signature = "";
      }

      // Page 1
      $pageId = $pdf->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
      $pdf->addPage();
      $pdf->useImportedPage($pageId, 0, 0, 210);

      $pdf->SetFont('Arial', '', 10);

      // Session
    //   $pdf->SetXY(117, 59.5);
    //   $pdf->Write(1, $student['Session']);



      // Enrollment No.
    //   $enrollment_no = str_split($student['Enrollment_No']);
    //   $x = 120.8;
    //   foreach ($enrollment_no as $enroll) {
    //     $pdf->SetXY($x, 30.5);
    //     $pdf->Write(1, $enroll);
    //     $x += 3.97;
    //   }
    
      $pdf->SetXY(167, 23.5);
      $pdf->Write(1, $student['Enrollment_No']);
       $pdf->SetFont('Arial', '', 11);
      // Programme
      $pdf->SetXY(40, 63);
    //   $pdf->Write(1, $student['Course'] . ' (' . $student['Sub_Course'] . ')');
       $pdf->Write(1, $student['Course'] );
      //Modes
      $pdf->SetXY(40, 72);
      $pdf->Write(1, $student['Sub_Course']);
    //   $pdf->Write(1, "Regular");

      // Photo
      if (filetype($photo) === 'file' && file_exists($photo)) {
        try {
          $filename = $id . "_Photo" . $file_extensions[0];
          $image = $filename;
          $pdf->Image($image, 163, 65.3, 30.5, 35.9);
          $photo = $image;
        } catch (Exception $e) {
          try {
            $filename = $id . "_Photo" . $file_extensions[1];
            $image = $filename;
            $pdf->Image($image, 162, 56, 30.5, 35.9);
            $photo = $image;
          } catch (Exception $e) {
            try {
              $filename = $id . "_Photo" . $file_extensions[2];
              $image = $filename;
              $pdf->Image($image, 162, 56, 30.5, 35.9);
              $photo = $image;
            } catch (Exception $e) {
              echo 'Message: ' . $e->getMessage();
            }
          }
        }
      }

     

      // Student Name
      $student_name = str_split(str_replace(
        '  ',
        ' ',
        $student['First_Name'] . " " . $student['Middle_Name'] . "
    " . $student['Last_Name']
      ));
      $x = 40;
      foreach ($student_name as $name) {
        $pdf->SetXY($x, 47.5);
        $pdf->Write(1, $name);
        $x += 6.5;
      }

      // Father Name
      $father_name = str_split($student['Father_Name']);
      $x = 40;
      foreach ($father_name as $name) {
        $pdf->SetXY($x, 178);
        $pdf->Write(1, $name);
        $x += 6.3;
      }

      // Mother Name
      $mother_name = str_split($student['Mother_Name']);
      $x = 40;
      foreach ($mother_name as $name) {
        $pdf->SetXY($x, 191);
        $pdf->Write(1, $name);
        $x += 6.3;
      }

      // DOB
      $dob = str_split($student['DOB']);
      // Day
      $pdf->SetXY(40, 130.5);
      $pdf->Write(1, $dob[8]);
      $pdf->SetXY(45.5, 130.5);
      $pdf->Write(1, $dob[9]);
      // Month
      $pdf->SetXY(53, 130.5);
      $pdf->Write(1, $dob[5]);
      $pdf->SetXY(59.5, 130.5);
      $pdf->Write(1, $dob[6]);
      // Year
      $pdf->SetXY(68, 130.5);
      $pdf->Write(1, $dob[0]);
      $pdf->SetXY(73.5, 130.5);
      $pdf->Write(1, $dob[1]);
      $pdf->SetXY(79.5, 130.5);
      $pdf->Write(1, $dob[2]);
      $pdf->SetXY(85.5, 130.5);
      $pdf->Write(1, $dob[3]);

      // Adhar
      $aadhaar = str_split(str_replace('-','',$student['Aadhar_Number']));
      $x=40;
      foreach($aadhaar as $aad){
        $pdf->SetXY($x, 163);
        $pdf->Write(1, $aad);
        $x += 6.5;
      }
      

      // Gender
      $gender = $student['Gender'] == "Male" ? "Male" : "Female";
      if($gender=="Male"){
        $pdf->Image($check, 101.5, 130, 3, 3);    
      }else{
          $pdf->Image($check, 106.5, 130, 3, 3);
      }
      
      
    //   print_r($student);die;
      $Marital_Status = $student['Marital_Status'];
      if($Marital_Status=="Married"){
           $pdf->Image($check, 131.5, 144, 3, 3);  
           
      }else{
            $pdf->Image($check, 152.5, 144, 3, 3);  
      }
      
      
    //   $pdf->Write(1, $gender);

      // Country
      $nationality = str_split($student['Nationality']);
      $x = 122.5;
      foreach($nationality as $national){
          $pdf->SetXY($x, 131.5);
          $pdf->Write(1, $national);
          $x += 6.5;
      }
      

      

      // Category
      
      if($student['Category']=="General"){
          $pdf->Image($check, 49.5, 144, 3, 3);
          
      }elseif($student['Category']=="OBC"){
          $pdf->Image($check, 87.5, 144, 3, 3);
      }elseif($student['Category']=="SC"){
          $pdf->Image($check, 61.5, 144, 3, 3);
      }else{
          $pdf->Image($check, 73.5, 144, 3, 3);
      }
      
    //   $pdf->SetXY(130, 129);
    //   $pdf->Write(1, );

      // Permanent Address
      $addressLine = str_split($address['present_address'].' '.$address['present_city'].' '.$address['present_district'].' '.$address['present_state']);
      $x = 40;
      $y = 40;
      $z = 40;
    //   print_r($address);die;
      foreach($addressLine as $c => $add){
        if($c<=23){
            $pdf->SetXY($x, 203.5);
            $x += 6;
        }elseif($c>23 && $c<=48){
            $pdf->SetXY($y, 210.5);
            $y += 6;
        }elseif($c>48 && $c<63){
            $pdf->SetXY($z, 217.5);
            $z += 6;
        }
        $pdf->Write(1, $add);  
        
        
        
      }


      // Pincode
      $permanent_pincode = str_split($address['present_pincode']);
      $x = 150;
    //   print_r($address);die;
      for ($i = 0; $i < count($permanent_pincode); $i++) {
        $pdf->SetXY($x, 217.5);
        $pdf->Write(1, $permanent_pincode[$i]);
        $x += 6;
      }

      // Mobile
      $mobile = str_split($student['Contact']);
      $pdf->SetFont('Arial', '', 10);
      $x = 47.5;
      foreach($mobile as $mob){
         $pdf->SetXY($x, 268.5);
         $pdf->Write(1, $mob);  
         $x +=6.5;
      }

     



      // Page 2
      $pageId = $pdf->importPage(2, PdfReader\PageBoundaries::MEDIA_BOX);
      $pdf->addPage();
      $pdf->useImportedPage($pageId, 0, 0, 210);
        // Email
        $splitEmail = str_split($student['Email']);
        $x=38;
        foreach($splitEmail as $emiailid){
         $pdf->SetXY($x, 46.5);
         $pdf->Write(1, $emiailid);  
         $x +=6.3;
        }
         // Signature
      if (filetype($signature) === 'file' && file_exists($signature)) {
        try {
          $filename = $id . "_Student_Signature" . $file_extensions[0];
          $image = $filename;
          $pdf->Image($image, 30, 250, 30.2, 11.3);
          $student_signature = $image;
        } catch (Exception $e) {
          try {
            $filename = $id . "_Student_Signature" . $file_extensions[1];
            $image = $filename;
            $pdf->Image($image, 156, 59, 30.2, 11.3);
            $student_signature = $image;
          } catch (Exception $e) {
            try {
              $filename = $id . "_Signature" . $file_extensions[2];
              $image = $filename;
              $pdf->Image($image, 156, 59, 30.2, 11.3);
              $student_signature = $image;
            } catch (Exception $e) {
              echo 'Message: ' . $e->getMessage();
            }
          }
        }
      }
      
       // Academics
      $academis = array(
        'High School', 'Intermediate', 'Under Graduation',
        'Post Graduation'
      );
      $y = '85';
      foreach ($academis as $academic) {
        $x = '53';

        // Details
        $type = $academic == 'Under Graduation' ? 'UG' : ($academic ==
          'Post Graduation' ? 'PG' : $academic);
        $data = $conn->query("SELECT * FROM Student_Academics WHERE
      Student_ID = $id AND Type = '$type'");
        if ($data->num_rows > 0) {

          $data = mysqli_fetch_assoc($data);
            // print_r($data);die;
          // $pdf->SetXY($x, $y);
          // $pdf->Write(1, $academic);
          $x += 18;
          $pdf->SetXY($x, $y);
          $pdf->Write(1, !empty($data['Total_Marks']) ? $data['Total_Marks'] : '');
          
          $x += 30;
          $pdf->SetXY($x, $y);
          $pdf->Write(1, !empty($data['Board/Institute']) ?
            substr($data['Board/Institute'], 0, 28) : '');

        //   $x += 43;
        //   $pdf->SetXY($x, $y);
        //   $pdf->Write(1, !empty($data['Board/Institute']) ?
        //     substr($data['Board/Institute'], 0, 28) : '');

          $x += 38;
          $pdf->SetXY($x, $y);
          $pdf->Write(1, !empty($data['Year']) ? $data['Year'] : '');

        //   $x += 21;
        //   $pdf->SetXY($x, $y);
        //   $pdf->Write(1, !empty($data['Subject']) ? $data['Subject'] : '');

          $x += 20;
          $pdf->SetXY($x, $y);
          $pdf->Write(1, !empty($data['Marks_Obtained']) ? $data['Marks_Obtained'] : '');

          

          // Roll No
          $x += 48;
          $pdf->SetXY($x, $y);
          $pdf->Write(1, !empty($data['Marks_Obtained']) ? $data['Marks_Obtained'] : '');
        }
        $y += 10;
      }

      // Page 3
      // $pageId = $pdf->importPage(3, PdfReader\PageBoundaries::MEDIA_BOX);
      // $pdf->addPage();
      // $pdf->useImportedPage($pageId, 0, 0, 210);

      // // Date
      // $pdf->SetXY(100.5, 190.5);
      // $pdf->Write(1, date('d-m-Y'));


      $i = 0;
      $end = 3;
      while ($i < $end) {
        // Delete Photos
        if (!empty($student_photo)) {
          $filename = $id . "_Photo" . $file_extensions[$i];
          //$file_extensions loops through the file extensions
          unlink($filename);
        }

        // Delete Signatures
        if (!empty($student_signature)) {
          $filename = $id . "_Student_Signature" . $file_extensions[$i];
          //$file_extensions loops through the file extensions
          unlink($filename);
        }
        $i++;
      }

      $pdf->Output('I', 'Application Form.pdf');
    }
  } else {
    header('Location: /');
  }
}

<?php
require '../../includes/db-config.php';
require '../../includes/helpers.php';
ob_start();
$rowId = mysqli_real_escape_string($conn, $_GET['id']);
use setasign\Fpdi\PdfReader;
use setasign\Fpdi\Fpdi;
@ob_end_clean();
require_once('../../extras/TCPDF/tcpdf.php');
require_once('../../extras/vendor/setasign/fpdf/fpdf.php');
require_once('../../extras/vendor/setasign/fpdi/src/autoload.php');
require '../../extras/vendor/autoload.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
## Fetch records
$result_record = "SELECT Student_Ledgers.Type,Student_Ledgers.Fee,Student_Ledgers.date_of_transation,Student_Ledgers.mode_of_payment,Student_Ledgers.utr_no, 
CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name) as First_Name, Universities.Name as university_name, Courses.Name as courseName ,Students.Unique_ID
FROM Students 
LEFT JOIN Student_Ledgers on Students.ID = Student_Ledgers.Student_ID 
LEFT JOIN Universities on Students.University_ID = Universities.ID 
LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID 
LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID 
LEFT JOIN Courses ON Students.Course_ID = Courses.ID 
LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID 
LEFT JOIN Student_Documents ON Students.ID = Student_Documents.Student_ID AND Student_Documents.Type = 'Photo' 
LEFT JOIN Studnent_Sub_Course_Fee ON Students.ID = Studnent_Sub_Course_Fee.Student_ID 
WHERE Student_Ledgers.Type=2 AND Student_Ledgers.ID=$rowId";
$empRecords = mysqli_query($conn, $result_record);
$data = [];
while ($row = mysqli_fetch_assoc($empRecords)) {
    $data[] = array(
        "First_Name" => $row['First_Name'] ?? '',
        "courseName" => $row['courseName'] ?? '',
        "university_name" => $row['university_name'] ?? '',
        "mode_of_payment" => $row['mode_of_payment'] ?? '',
        "date_of_transation" => $row['date_of_transation'] ?? '',
        "utr_no" => $row['utr_no'] ?? '',
        "Fee" => $row['Fee'] ?? 0,
        "Unique_ID" => $row['Unique_ID'] ?? ""
    );
}
if (empty($data)) {
    die("No records found.");
}
$pdf = new \setasign\Fpdi\Fpdi();
$pdf->SetTitle('CASH RECEIPT');
$templatePath = 'receipt.pdf';
$pageCount = $pdf->setSourceFile($templatePath);
foreach ($data as $row) {
    $pageId = $pdf->importPage(1);
    $pdf->AddPage();
    $pdf->useImportedPage($pageId);
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->SetXY(50, 54);
    $pdf->Write(0, $row['Unique_ID'] . ' ' . $row['First_Name']);
    $pdf->SetXY(165, 54);
    $pdf->Write(0, rand(1000, 9999));
    $pdf->SetXY(160, 60);
    $pdf->Write(0, date('Y-m-d'));
    $pdf->SetXY(50, 60);
    $pdf->Write(1, $row['courseName']);
    $pdf->SetXY(50, 65);
    $pdf->Write(2, $row['university_name']);
    $pdf->SetXY(100, 117.5);
    $pdf->Write(0,   $row['utr_no']);
    $pdf->SetXY(170, 83.5);
    $pdf->Write(0, $row['Fee']);
    $pdf->SetXY(170, 106.5);
    $pdf->Write(0, $row['Fee']);
    $pdf->SetXY(170, 80);
    $pdf->Write(0, $row['Unique_ID']);
    $pdf->SetXY(160, 125);
    $pdf->Write(0, $row['date_of_transation']);
    $pdf->SetXY(25, 118);
    $pdf->Write(0, $row['mode_of_payment']);
    $feeInWords = numberToWords($row['Fee']);
    $pdf->SetXY(15, 112);
    $pdf->Write(0, ucfirst($feeInWords));
}
$filename = "transactions_data_" . date('Y-m-d') . ".pdf";
$pdf->Output($filename, 'I');
ob_end_flush();

<?php
require('fpdf.php');
include '../includes/db-connection.php';

// Active (not archived) member accounts, oldest first.
$result = mysqli_query($con, "
    SELECT signup_id, first_name, last_name, signup_email, mobile_number, date_created
    FROM tbl_signup_acc
    WHERE is_archived = 0
    ORDER BY date_created ASC
");
$rows = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}

// FPDF's built-in fonts are Latin-1 only.
function pdf_text($s)
{
    return iconv('UTF-8', 'windows-1252//TRANSLIT', (string) $s);
}

$pdf = new FPDF();
$pdf->AddPage();

// Header
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 10, 'CineVault', 0, 1, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, 'Online Movie Platform', 0, 1, 'L');
$pdf->Cell(0, 6, '', 0, 1, 'L');

// Details (top right)
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetXY(140, 10);
$pdf->Cell(0, 6, 'Report Date: ' . date('Y-m-d'), 0, 1, 'L');
$pdf->SetXY(140, 16);
$pdf->Cell(0, 6, 'Report Type: Member Accounts', 0, 1, 'L');

// Table header (194mm wide)
$pdf->SetY(35);
$pdf->SetX(8);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 8, '#', 1, 0, 'C');
$pdf->Cell(20, 8, 'Member ID', 1, 0, 'C');
$pdf->Cell(44, 8, 'Name', 1, 0, 'C');
$pdf->Cell(60, 8, 'Email', 1, 0, 'C');
$pdf->Cell(30, 8, 'Mobile', 1, 0, 'C');
$pdf->Cell(30, 8, 'Date Joined', 1, 0, 'C');
$pdf->Ln();

// Table body
$pdf->SetFont('Arial', '', 8);
$i = 1;
foreach ($rows as $row) {
    $pdf->SetX(8);
    $pdf->Cell(10, 8, $i++, 1, 0, 'C');
    $pdf->Cell(20, 8, $row['signup_id'], 1, 0, 'C');
    $pdf->Cell(44, 8, pdf_text($row['first_name'] . ' ' . $row['last_name']), 1, 0, 'C');
    $pdf->Cell(60, 8, pdf_text($row['signup_email']), 1, 0, 'C');
    $pdf->Cell(30, 8, pdf_text($row['mobile_number']), 1, 0, 'C');
    $pdf->Cell(30, 8, date('Y-m-d', strtotime($row['date_created'])), 1, 0, 'C');
    $pdf->Ln();
}

// Total
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetX(8);
$pdf->Cell(164, 8, 'Total Members', 1, 0, 'R');
$pdf->Cell(30, 8, count($rows), 1, 0, 'C');

$pdf->Output('D', 'cinevault_member_accounts_report.pdf');
exit;

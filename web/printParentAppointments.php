<?php
/*
 * Copyright 2014, Amos-Comenius-Gymnasium Bonn <http://www.acg-bonn.de/>
 * See LICENSE.md for licensing information. 
 *
 * author Peter Engels
 *
 * On this page parents can print all appointments as PDF.
 */

require_once "estap.php";
require_once "ESTAP/estapPDF.php";

use ESTAP\Appointment;
use ESTAP\Session;
use ESTAP\Teacher;
use PhoolKit\I18N;

$session = Session::get()->requireParent();
$teachers = Teacher::getAll();

$pdf = new PDF();

// Column headings
$header = array(utf8_decode(I18N::getMessage("printPDF.time")), utf8_decode(I18N::getMessage("printPDF.date")), utf8_decode(I18N::getMessage("printPDF.teacher")), utf8_decode(I18N::getMessage("printPDF.pupil")));
//$header = array(utf8_decode(I18N::getMessage("printPDF.time")), utf8_decode(I18N::getMessage("printPDF.date")), utf8_decode(I18N::getMessage("printPDF.teacher")),utf8_decode(I18N::getMessage("printPDF.pupil")),utf8_decode(I18N::getMessage("printPDF.room")));
$pdf->SetFont('Arial', '', 9);
$lines = array();
$data = array();

$pdf->headLine = sprintf(I18N::getMessage("printParent.headLine"));
$pdf->AddPage();
$appointments = Appointment::getForPupils($session->getPupilIds());
$i = 0;
foreach ($appointments as $appointment) {
    $lines[0] = $appointment->getTimeSlot()->getTimeString();

    $dateTime = new DateTime($appointment->getTimeSlot()->getDate());

    $lines[1] = utf8_decode($dateTime->format("d.m.Y"));
    $lines[2] = utf8_decode($appointment->getTeacher()->getName(Teacher::GENDER_LAST));
    $lines[3] = utf8_decode($appointment->getPupil()->getName());
    //$lines[4] = $appointment->getTeacher()->getRoom();
    $data[$i] = $lines;
    unset($lines);
    $i++;
}
$pdf->ParentAppointmentTable($header, $data);
unset($data);
unset($appointments);
$pdf->Output('PDF.pdf', 'I');

?>
<?php 
/*
 * Copyright 2014, Amos-Comenius-Gymnasium Bonn <http://www.acg-bonn.de/>
 * See LICENSE.md for licensing information. 
 *
 * author Peter Engels
 *
 * On this page the admin/teachers can print all teacher appointments as PDF.
 */

require_once "estap.php";
require_once "ESTAP/estapPDF.php";

use ESTAP\Session;
use ESTAP\Teacher;
use ESTAP\Pupil;
use ESTAP\Appointment;
use PhoolKit\I18N;

if (isset($_REQUEST["admin"]))
{
    $session = Session::get()->requireAdmin();
}
else
{
    $session = Session::get()->requireTeacher();
}
$teacherId = +$_REQUEST["teacher"];
$teacher = Teacher::getById($teacherId);

$pdf = new PDF();

// Column headings
$header = array(utf8_decode(I18N::getMessage("printPDF.time")),utf8_decode(I18N::getMessage("printPDF.pupil")),utf8_decode(I18N::getMessage("printPDF.class")));
$pdf->SetFont('Arial','',9);
$lines = array();
$data = array();

      $pdf->headLine = sprintf(I18N::getMessage("printTeacher.headLine"),$teacher->getName(Teacher::GENDER_ACC_LAST),$teacher->getRoom());
      $pdf->AddPage();
      $appointments = Appointment::getForTeacher($teacher->getId());
      $i = 0;
      foreach ($appointments as $appointment)
      {
        $lines[0] = $appointment->getTimeSlot()->getTimeString();
        if (!$appointment->isReserved())
        {
          $lines[1] = I18N::getMessage("printTeacher.free");
          $lines[2] = "";
        }
        elseif ($appointment->isLocked())
        {
          $lines[1] = I18N::getMessage("printTeacher.locked");
          $lines[2] = "";
        }
        else
        {
          $lines[1] = utf8_decode($appointment->getPupil()->getName());
          $lines[2] = $appointment->getPupil()->getClass();
        }
        $data[$i] = $lines;
        unset($lines);
        $i++;
      }
      $pdf->AppointmentTable($header,$data);
      unset($data);  
      unset($appointments);
      $pdf->Output(session_name("pageTitle").'.pdf','I');
?>
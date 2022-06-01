<?php
	include '../includes/session.php';

	function generateRow($conn){
		$contents = '';
		
    require_once "../../controllers/asistencia/asistencia_obtener.php";
    foreach($obtener as $row){

      $status = ($row['status'])?'<span class="label label-warning pull-right"> (A tiempo)</span>':'<span class="label label-danger pull-right"> (Tarde)</span>';
			$contents .= "
      <tr>
        <td>".date('M d, Y', strtotime($row['date']))."</td>
        <td>".$row['employee_id']."</td>
				<td>".$row['lastname'].", ".$row['firstname']."</td>
        <td>".$row['description']."</td>
        <td>".date('h:i A', strtotime($row['time_in'])).' - '. date('h:i A', strtotime($row['time_out'])).$status."</td>
			</tr>
    ";
		}

		return $contents;
	}

	require_once('../../tcpdf_min/tcpdf.php');  
    $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  
    $pdf->SetCreator(PDF_CREATOR);  
    $pdf->SetTitle('Lista de Empleados');  
    $pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
    $pdf->SetDefaultMonospacedFont('helvetica');  
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
    $pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);  
    $pdf->setPrintHeader(false);  
    $pdf->setPrintFooter(false);  
    $pdf->SetAutoPageBreak(TRUE, 10);  
    $pdf->SetFont('helvetica', '', 11);  
    $pdf->AddPage();  
    $content = '';  
    $content .= '
      	<h2 align="center">Asistencia</h2>
      	<table border="1" cellspacing="0" cellpadding="3">  
           <tr>  
                <th width="20%" align="center"><b>Fecha</b></th>
           		  <th width="20%" align="center"><b>Cédula de Identidad</b></th>
                <th width="20%" align="center"><b>Nombre Empleado</b></th>
                <th width="20%" align="center"><b>Cargo</b></th> 
                <th width="20%" align="center"><b>Hora de Asistencia</b></th>  
           </tr>
      ';  
    $content .= generateRow($conn); 
    $content .= '</table>';  
    $pdf->writeHTML($content);  
    $pdf->Output('Horario de Empleados.pdf', 'I');

?>
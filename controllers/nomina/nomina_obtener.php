<?php 
        require_once "../../config/conn.php";
        require_once "../../models/nomina_model.php";
        $nomina = new nomina_model();

        $from = 0;
        $to = 0;

        if(isset($_GET['range'])){
        $range = $_GET['range'];
        $ex = explode(' - ', $range);
        $from = date('Y-m-d', strtotime($ex[0]));
        $to = date('Y-m-d', strtotime($ex[1]));
        }

        //Se obtiene el monto para calcular la deduccion a cada sueldo
        $consulta_deducciones = $nomina->consulta_deducciones();
        foreach($consulta_deducciones as $drow){

                $deduction = $drow['total_amount'];
        };


        //Se obtiene el monto para calcular la deduccion a cada sueldo
        $consulta_deducciones2 = $nomina->consulta_deducciones2();
        foreach($consulta_deducciones2 as $drow2){

                $deduction2 = $drow2['total_amount2'];
        };


        //Obtiene los Empleados, sus horas trabajadas y el monto a cobrar
        //por esas horas
        $consulta_horas_trabajadas = $nomina->consulta_obtener_nomina($from, $to);
        foreach($consulta_horas_trabajadas as $row){

                $gross = $row['rate'] * $row['total_hr'];
                $empid = $row['empid'];   
        

        //Obtiene el efectivo prestado al empleado
        $consulta_avancefectivo = $nomina->consulta_avancefectivo($from, $to, $empid);
        foreach($consulta_avancefectivo as $deducrow){

                $deductionefectivo = $deducrow['cashamount'];    
        };


        //Cálculo de FAOV e IVSS
        $mensualgross = ($gross * 12)/52;
        $percentdeduction = $deduction * $mensualgross;
        $faovsso = $percentdeduction * 5;

        //Cálculo de Paro Forzoso
        $paroforzoso = $gross * $deduction2;

        //Suma de deducciones por ley
        $deductionley = $faovsso + $paroforzoso;

        //Suma de Deducciones por ley y Avance de Efectivo para descontar
        $total_deduction = $deductionley + $deductionefectivo;

        //Cálculo de Sueldo a cobrar, restando el total de deducciones al sueldo neto
        $net = $gross - $total_deduction;

        //Se obtiene la tasa del dolar para calcular el sueldo en Bs.D
        $tasadolar = $nomina->consulta_tasadolar();
        foreach($tasadolar as $dolrow){

                $dolarbcv = $dolrow['rate_dolar'];
        };

        
        //Cálculo de Sueldo en Dólares
        $bs = $dolarbcv * $net;

?>
        <tr>            
                <td><?php echo $row['lastname']." ".$row['firstname']?></td>
                <td><?php echo $row['ci']?></td>
                <td><?php echo '$ '.number_format($gross, 2)?></td>
                <td><?php echo '$ '.number_format($deductionley, 2)?></td>
                <td><?php echo '$ '.number_format($deductionefectivo, 2)?></td>
                <td><?php echo '$ '.number_format(1,2)." = Bs ".number_format($dolarbcv,2)?></td>
                <td><?php echo '$ '.number_format($net, 2)?></td>
                <td><?php echo 'Bs.D '.number_format($bs, 2)?></td> 
        </tr>

<?php };?>
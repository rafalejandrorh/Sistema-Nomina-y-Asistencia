<?php 

require_once "../config/conn.php";


class asistencia_model 
{

    private $db;
    private $asistencia;

    public function __construct()
    {
        
        $this->db = Conectar::conexion();
        $this->asistencia = array();

    }

    public function obtener_asistencia()
    {

        $sql = "SELECT *, empleados.employee_id AS empid, asistencia.id AS attid FROM asistencia LEFT JOIN empleados ON empleados.id=asistencia.employee_id ORDER BY asistencia.date DESC, asistencia.time_in DESC";
        $query = $this->db->query($sql);
        while($row = $query->fetch_assoc())
        {
            $this->asistencia[] = $row;

        }

        return $this->asistencia;

    }

    public function insertar_asistencia($employee, $date, $time_in, $time_out)
    {

        $sql = "SELECT * FROM empleados WHERE employee_id = '$employee'";
        $query = $this->db->query($sql);

        if($query->num_rows < 1){
			$_SESSION['error'] = 'Empleado no encontrado';
		}
		else{

        $row = $query->fetch_assoc();
	    $emp = $row['id'];

        $sql2 = "SELECT * FROM asistencia WHERE employee_id = '$emp' AND date = '$date'";
        $query2 = $this->db->query($sql2);

        if($query2->num_rows > 0){
            $_SESSION['error'] = 'Ya existe registro de este empleado en el día indicado.';
        }
        else{

        $sched = $row['schedule_id'];

        $sql3 = "SELECT * FROM horarios WHERE schedule_id = '$sched'";
        $query3 = $this->db->query($sql3);
		$scherow = $query3->fetch_assoc();
		$logstatus = ($time_in > $scherow['time_in']) ? 0 : 1;

        $sql4 = "INSERT INTO asistencia (employee_id, date, time_in, time_out, status) VALUES ('$emp', '$date', '$time_in', '$time_out', '$logstatus')";

        if($this->db->query($sql4)){
            $_SESSION['success'] = 'Asistencia añadida satisfactoriamente';

            $id = $this->db->insert_id;

					$sql5 = "SELECT * FROM empleados LEFT JOIN horarios ON horarios.schedule_id=empleados.schedule_id WHERE empleados.id = '$emp'";
					$query5 = $this->db->query($sql5);
					$srow = $query5->fetch_assoc();

					if($srow['time_in'] > $time_in){
						$time_in = $srow['time_in'];
					}

					if($srow['time_out'] < $time_out){
						$time_out = $srow['time_out'];
					}

					$time_in = new DateTime($time_in);
					$time_out = new DateTime($time_out);
					$interval = $time_in->diff($time_out);
					$hrs = $interval->format('%h');
					$mins = $interval->format('%i');
					$mins = $mins/60;
					$int = $hrs + $mins;
					if($int > 4){
						$int = $int - 1;
					}

					$sql6 = "UPDATE asistencia SET num_hr = '$int' WHERE id = '$id'";
					$this->db->query($sql6);

				}
				else{
					$_SESSION['error'] = $this->db->error;
				}
			}
		}

        return $this->$_SESSION;

    }

    public function editar_asistencia($date, $time_in, $time_out, $id)
    {

        $sql = "UPDATE asistencia SET date = '$date', time_in = '$time_in', time_out = '$time_out' WHERE id = '$id'";

        if($this->db->query($sql)){
			$_SESSION['success'] = 'Asistencia actualizada satisfactoriamente';

			$sql = "SELECT * FROM asistencia WHERE id = '$id'";
			$query = $this->db->query($sql);
			$row = $query->fetch_assoc();
			$emp = $row['employee_id'];

			$sql = "SELECT * FROM empleados LEFT JOIN horarios ON horarios.schedule_id=empleados.schedule_id WHERE empleados.id = '$emp'";
			$query = $this->db->query($sql);
			$srow = $query->fetch_assoc();

			//updates
			$logstatus = ($time_in > $srow['time_in']) ? 0 : 1;
			//

			if($srow['time_in'] > $time_in){
				$time_in = $srow['time_in'];
			}

			if($srow['time_out'] < $time_out){
				$time_out = $srow['time_out'];
			}

			$time_in = new DateTime($time_in);
			$time_out = new DateTime($time_out);
			$interval = $time_in->diff($time_out);
			$hrs = $interval->format('%h');
			$mins = $interval->format('%i');
			$mins = $mins/60;
			$int = $hrs + $mins;
			if($int > 4){
				$int = $int - 1;
			}

			$sql = "UPDATE asistencia SET num_hr = '$int', status = '$logstatus' WHERE id = '$id'";
			$this->db->query($sql);
		}
		else{
			$_SESSION['error'] = $this->db->error;
		}

        return $this->$_SESSION;

    }

    public function eliminar_asistencia($id)
    {

        $sql = "DELETE FROM asistencia WHERE id = '$id'";

        if($this->db->query($sql)){
			$_SESSION['success'] = 'Asistencia eliminada satisfactoriamente';
		}
		else{
			$_SESSION['error'] = $this->db->error;
		}

        return $this->$_SESSION;

    }

    public function editar_foto_asistencia($empid, $filename)
    {

        $sql = "UPDATE asistencia SET photo = '$filename' WHERE employee_id = '$empid'";
        $query = $this->db->query($sql);

        return $this->$query;

    }
}

?>

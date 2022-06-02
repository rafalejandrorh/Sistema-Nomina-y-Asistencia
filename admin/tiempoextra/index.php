<?php include '../includes/session.php'; ?>
<?php include '../includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include '../includes/navbar.php'; ?>
  <?php include '../includes/menubar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <h1><b>Tiempo Extra</b></h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
        <li>Empleados</li>
        <li class="active">Tiempo Extra</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <?php
        if(isset($_SESSION['error'])){
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              ".$_SESSION['error']."
            </div>
          ";
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])){
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Éxito!</h4>
              ".$_SESSION['success']."
            </div>
          ";
          unset($_SESSION['success']);
        }
      ?>
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-plus"></i> Nuevo</a>
              <div class="pull-right">
               <a href="../pdf/tiempoextra_print.php" class="btn btn-success btn-sm btn-flat"><span class="glyphicon glyphicon-print"></span> Imprimir</a>
              </div>
            </div>
            <div class="box-body">
              <table id="example1" class="table table-bordered">
                <thead>
                  <th class="hidden"></th>
                  <th>Fecha</th>
                  <th>Cédula de Identidad</th>
                  <th>Nombre</th>
                  <th>No. de Horas</th>
                  <th>Monto de Hora</th>
                  <th>Pago Total de Horas extra</th>
                  <th>Acción</th>
                </thead>
                <tbody>
                  <?php
                    require_once "../../controllers/tiempoextra/tiempoextra_obtener.php";

                    foreach($obtener as $row)
                    {
                      $gross = $row['rate'] * $row['hours'];?>
                        <tr>
                          <td class='hidden'></td>
                          <td><?php echo date('M d, Y', strtotime($row['date_overtime']))?></td>
                          <td><?php echo $row['employee_id']?></td>
                          <td><?php echo $row['firstname'].' '.$row['lastname']?></td>
                          <td><?php echo $row['hours']?></td>
                          <td><?php echo '$ '.$row['rate']?></td>
                          <td><?php echo '$ '.number_format($gross, 2)?></td>
                          <td>
                            <button class='btn btn-success btn-sm btn-flat edit' data-id='<?php echo $row['otid']?>'><i class='fa fa-edit'></i> Editar</button>
                            <button class='btn btn-danger btn-sm btn-flat delete' data-id='<?php echo $row['otid']?>'><i class='fa fa-trash'></i> Eliminar</button>
                          </td>
                        </tr>
                      <?php
                    }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>   
  </div>
    
  <?php include '../includes/footer.php'; ?>
  <?php include 'tiempoextra_modal.php'; ?>
</div>
<?php include '../includes/scripts.php'; ?>
<script>
$(function(){
  $('.edit').click(function(e){
    e.preventDefault();
    $('#edit').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $('.delete').click(function(e){
    e.preventDefault();
    $('#delete').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });
});

function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'tiempoextra_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){

      $('.employee_name').html(response.firstname+' '+response.lastname);
      $('#del_otid').val(response.otid);
      $('#edit_otid').val(response.otid);
      $('#datepicker_edit').val(response.date_overtime);
      $('#overtime_date').html(response.date_overtime);
      $('#hours_edit').val(response.hours);
      $('#rate_edit').val(response.rate);
    }
  });
}
</script>
</body>
</html>

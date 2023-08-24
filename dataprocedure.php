<?php
  include("credentialsrc.php");
  include("conexdb.php");
  if (isset ($_POST['token'])) {
    $googleToken = $_POST['token'];
  } elseif (isset($_POST['tokene'])) {
    $googleToken = $_POST['tokene'];
  }
  try {
    if (isset ($googleToken)) {
      $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $keys['privada'] . "&response={$googleToken}");
      $response = json_decode($response);
      $response = (array) $response;
      if ($response['success'] && ($response['score'] && $response['score'] > 0.5)) {
        if(isset($_POST['Tick-2021']) && $_POST['consulta'] == 'Tick2021') {
          $ticket_cg = $_POST['Tick-2021'];
          $query = "SELECT idsol, areacrea, usercrea, fecsol, tipdoc, numdoc, asunto, tipoente, numiden, nomente
          FROM dbo.v_workflow_seguimiento_origen_ant WHERE numticket = ?;";
          $data = $conex->prepare($query);
          $data->execute([$ticket_cg]);
          $origen = $data->fetch();
        }
        if(isset($_POST['Tick-2022']) && $_POST['consulta'] == 'Tick2022') {
          $ticket_cg = $_POST['Tick-2022'];
          $ticket_sg = str_replace("-","",$_POST['Tick-2022']);
          $query = "SELECT idsol, areacrea, usercrea, fecsol, tipdoc, numdoc, asunto, tipoente, numiden, nomente
          FROM dbo.v_workflow_seguimiento_origen WHERE numticket = ? OR  numticket = ?;";
          $data = $conex->prepare($query);
          $data->execute([$ticket_cg, $ticket_sg]);
          $origen = $data->fetch();
        }
        if (empty($origen)) {
          $resultado = array(
            'estado' => 'no-ubicado',
            'mensaje' => '<h4 class="fw-bold lh-1 mb-3 text-center text-danger">¡DOCUMENTO NO ENCONTRADO!</h4>
            <h5 class="fw-bold lh-1 mb-3 text-center text-danger">'.$ticket_cg.'</h5>
            <p class="text-justify">Nº de Ticket incorrecto, verifique bien los números a ingresar, clic en el boton para volver a consultar:</p>
            <div class="gap-2 d-flex justify-content-center">
              <button class="btn btn-danger col-6" onclick="redirec()">Nueva Consulta</button>
            </div>'
          );
        } else {
          $trazabilidad = '';
          $ubicacion = '';
          $query = "SELECT * FROM dbo.v_workflow_seguimiento_pasos WHERE idsol = ?;";
          $data = $conex->prepare($query);
          $data->execute([$origen['0']]);
          while ($pasos = $data->fetch(PDO::FETCH_ASSOC)) {
            $trazabilidad .= '<tr><th scope="row">'.$pasos['paso'].'</th><td>'.$pasos['forma'].'</td><td>'.$pasos['area_env'].'</td>
            <td>'.$pasos['user_env'].'</td><td>'.$pasos['accion'].'</td><td>'.substr($pasos['fec_env_rec'],0,19).'</td><td>'.$pasos['asunto_nota'].'</td>
            <td>'.$pasos['area_rec'].'</td><td>'.$pasos['user_rec'].'</td><td>'.substr($pasos['fec_lec'],0,19).'</td></tr>';
            if (is_null($pasos['fec_aten']) && $pasos['area_rec']=='ARCHIVO VIRTUAL') {
              $ubicacion .= '<li class="list-group-item d-flex justify-content-between lh-sm border-primary">
              <div><small><strong class="bg-danger text-white">Archivado</strong> por Unidad Orgánica / Usuario</small><h6 class="my-0">'.$pasos['area_env'].' - '.$pasos['user_env'].'</h6></div>
              <div><small>Fecha de Archivado</small><h6 class="my-0">'.substr($pasos['fec_env_rec'],0,19).'</h6></div></li>';
            } elseif (is_null($pasos['fec_aten'])) {
              $ubicacion .= '<li class="list-group-item d-flex justify-content-between lh-sm border-primary">
              <div><small><strong class="bg-success text-white">Pendiente</strong> en Unidad Orgánica y/o Usuario</small><h6 class="my-0">'.$pasos['area_rec'].' - '.$pasos['user_rec'].'</h6></div>
              <div><small>Fecha de Recibido</small><h6 class="my-0">'.substr($pasos['fec_env_rec'],0,19).'</h6></div></li>';
            }
          }
          $resultado = array(
            'estado' => 'ubicado',
            'mensaje' => '<h4 class="fw-bold lh-1 mb-3 text-center text-success">¡DOCUMENTO ENCONTRADO!</h4>
            <h5 class="fw-bold lh-1 mb-3 text-center text-success">'.$ticket_cg.'</h5>
            <p class="text-justify">Ya puede ver la información del expediente, clic en el boton para realizar otra busqueda:</p>
            <div class="gap-2 d-flex justify-content-center">
            <button class="btn btn-success col-6" onclick="redirec()">Nueva Consulta</button></div>',
            'titulo' => '<h4 class="list-group-item d-flex justify-content-center rounded-top m-3 bg-primary">
            <span class="text-white text-center">DATOS DEL EXPEDIENTE</span></h4>',
            'solicitante' => '<ul class="list-group mb-3">
            <span class="col-4 text-center bg-primary rounded-top text-white">SOLICITANTE</span>
            <li class="list-group-item d-flex justify-content-between lh-sm border-primary">
            <div><small>Tipo de Persona</small> <h6 class="my-0">'.$origen['7'].'</h6></div>
            <div><small>Nº de DNI / RUC</small><h6 class="my-0">'.$origen['8'].'</h6></div></li>
            <li class="list-group-item d-flex justify-content-between lh-sm border-primary"><div>
            <small>Nombres y Apellidos / Razón Social</small> <h6 class="my-0">'.$origen['9'].'</h6></div></li></ul>',
            'documento' => '<ul class="list-group mb-3">
            <span class="col-4 text-center bg-primary rounded-top text-white">DOCUMENTO</span>
            <li class="list-group-item d-flex justify-content-between lh-sm border-primary">
            <div><small>Tipo de Documento</small><h6 class="my-0">'.$origen['4'].'</h6></div>
            <div><small>Correlativo y Siglas</small><h6 class="my-0">'.$origen['5'].'</h6></div></li>
            <li class="list-group-item d-flex justify-content-between lh-sm border-primary">
            <div><small>Asunto / Nota</small><h6 class="my-0">'.$origen['6'].'</h6></div></li>
            <li class="list-group-item d-flex justify-content-between lh-sm border-primary">
            <div><small>Unidad Orgánica Remitente</small><h6 class="my-0">'.$origen['1'].'</h6></div></li>
            <li class="list-group-item d-flex justify-content-between lh-sm border-primary">
            <div><small>Usuario Remitente</small><h6 class="my-0">'.$origen['2'].'</h6></div>
            <div><small>Fecha y Hora</small><h6 class="my-0">'.substr($origen['3'],0,19).'</h6></div></li>',
            'ubicacion' => '<ul class="list-group mb-3">
            <span class="col-4 text-center bg-primary rounded-top text-white">UBICACIÓN</span>'.$ubicacion.'</ul>',
            'trazabilidad' => '<h4 class="list-group-item d-flex justify-content-center rounded-top m-3 bg-success">
            <span class="text-white text-center">DETALLE DE TRÁMITE</span></h4>
            <table class="table text-center align-middle table-hover table-striped table-bordered border-dark">
            <thead class="table-success border-dark">
            <tr><th rowspan="2">Nº</th><th colspan="3">DERIVA</th><th colspan="3">BITÁCORA</th><th colspan="3">RECEPCIONA</th></tr>
            <tr><th>Forma</th><th>Dependencia</th><th>Usuario</th><th>Acción</th><th>Fecha/Hora</th><th>Asunto/Nota</th><th>Dependencia</th>
            <th>Usuario</th><th>Fec.Lectura</th></tr></thead><tbody>'.$trazabilidad.'</tbody>
            <tfoot class="table-success border-dark">
            <tr><th rowspan="2">Nº</th><th>Forma</th><th>Dependencia</th><th>Usuario</th><th>Acción</th><th>Fecha/Hora</th>
            <th>Asunto/Nota</th><th>Dependencia</th><th>Usuario</th><th>Fec.Lectura</th></tr>
            <tr><th colspan="3">DERIVA</th><th colspan="3">BITÁCORA</th><th colspan="3">RECEPCIONA</th></tr></tfoot></table>'
          );
        }
      } else {
        $resultado = array(
          'estado' => 'error',
          'mensaje' => '<h4 class="fw-bold lh-1 mb-3 text-center text-danger">¡SE PRODUJO UN ERROR!</h4>
          <p class="text-justify">Se detecto actividad inusual, conmumente se debe a la demora en el proceso de digitar y consultar.</p>
          <p class="text-justify">Clic en el boton para recargar y volver a consultar:</p>
          <div class="gap-2 d-flex justify-content-center"><button class="btn btn-danger col-6" onclick="redirec()">Recargar</button></div>'
        );
      }
    }
  } catch (Exception $e) {
    echo "Error".$e->getMessage();
  }
  die(json_encode($resultado));
?>
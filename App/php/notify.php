<?php
function notify($alert, $msg = NULL)
{

  switch ($alert) {
    case 'danger':
      $msg_notify = 'Ops! Ocorreu um erro!';
      break;
    case 'warning':
      $msg_notify = 'Ops! Você esqueceu de algum campo!';
      break;
    case 'success':
      $msg_notify = 'Operação realizada com sucesso!';
      break;
    case 'repeat':
      $msg_notify = 'E-mail ou CPF ja cadastrado';
      break;
    default:
      $msg_notify = 'Algo saiu errado';
      break;
    case 'planoff':
      $msg_notify = 'Selecione um plano';
      break;
  }

  $msgs =  '<div class="alert text-white bg-' . $alert . ' alert-dismissible fade show notify" role="alert">
  ' . $msg_notify . '
  </div>

  <script>
  $(document).ready (function(){
    window.setTimeout(function () { 
      $(".notify").alert(\'close\'); }, 10000);               
      });       
      </script>';

  return $msgs;
}

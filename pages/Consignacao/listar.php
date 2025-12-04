<?php

use App\Models\Consignacao\Controller;

$controller = new Controller();
$consignacoes = $controller->listar();

?>

<!-- Custom styles for this page -->
<link href="<?php echo $url ?>vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

<!-- Page level plugins -->
<script src="<?php echo $url ?>vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo $url ?>vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script>
  $(document).ready(function() {
    $('#example1').DataTable({
      "language": {
        "sEmptyTable": "Nenhum dado disponível na tabela",
        "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ entradas",
        "sInfoEmpty": "Mostrando 0 até 0 de 0 entradas",
        "sInfoFiltered": "(filtrado de _MAX_ entradas totais)",
        "sInfoPostFix": "",
        "sLengthMenu": "Mostrar _MENU_ entradas",
        "sLoadingRecords": "Carregando...",
        "sProcessing": "Processando...",
        "sSearch": "Pesquisar:",
        "sZeroRecords": "Nenhum registro encontrado",
        "oPaginate": {
          "sFirst": "Primeiro",
          "sPrevious": "Anterior",
          "sNext": "Próximo",
          "sLast": "Último"
        },
        "oAria": {
          "sSortAscending": ": ativar para ordenar a coluna de forma ascendente",
          "sSortDescending": ": ativar para ordenar a coluna de forma descendente"
        }
      }
    });
  });
</script>


<div class="card">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h3 class="card-title">Lista de Consignações</h3>
    <a href="<?php echo "{$url}!/{$link[1]}/cadastro"; ?>" class="btn btn-white text-primary">Adicionar Consignação</a>
  </div>

  <div class="card-body">
    <table id="example1" class="table table-striped table-hover">
      <thead class="bg-light">
        <tr>
          <th>ID</th>
          <th>Cliente</th>
          <th>Data da Consignação</th>
          <th>Status</th>
          <th width="100px">Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($consignacoes as $consignacao): ?>
          <tr>
            <td><?= htmlspecialchars($consignacao['id']) ?></td>
            <td><?= htmlspecialchars($consignacao['nome_pf'] ?? $consignacao['nome_fantasia_pj'] ?? 'Não informado') ?></td>
            <td><?= date('d/m/Y', strtotime($consignacao['data_consignacao'])) ?></td>
            <td><span class="badge badge-<?= $consignacao['status'] == 'Aberta' ? 'success' : 'danger' ?>"><?= htmlspecialchars($consignacao['status']) ?></span></td>
            <td>
              <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Ação
                </button>
                <ul class="dropdown-menu">
                  <li><a href="<?= "{$url}!/{$link[1]}/ver/{$consignacao['id']}" ?>" class="dropdown-item">Ver</a></li>
                  <?php if ($consignacao['status'] == 'Aberta'): ?>
                    <li><a href="<?= "{$url}!/{$link[1]}/editar/{$consignacao['id']}" ?>" class="dropdown-item">Editar</a></li>
                  <?php endif; ?>
                  <li><a href="<?= "{$url}!/{$link[1]}/imprimir/{$consignacao['id']}" ?>" class="dropdown-item">Imprimir</a></li>
                  <li><a href="<?= "{$url}!/{$link[1]}/deletar/{$consignacao['id']}" ?>" class="dropdown-item text-danger">Excluir</a></li>
                </ul>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
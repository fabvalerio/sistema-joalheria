<?php

use App\Models\Usuarios\Controller; // Altere para o namespace correto

// Crie uma instância da classe do Controller
$controller = new Controller();

// Obter a lista de usuários
$return = $controller->listar();

// Obter todos os cargos
$cargos = $controller->cargos();

// Criar um mapa de cargos [id => nome] para facilitar o acesso
$cargoMap = [];
foreach ($cargos as $cargo) {
    $cargoMap[$cargo['id']] = $cargo['cargo'];
}

?>



<!-- Custom styles for this page -->
<link href="<?php echo $url?>vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

<!-- Page level plugins -->
<script src="<?php echo $url?>vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo $url?>vendor/datatables/dataTables.bootstrap4.min.js"></script>

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
        <h3 class="card-title">Usuários</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/cadastro" ?>" class="btn btn-white text-primary">Adicionar</a>
    </div>

    <div class="card-body">

        <table id="example1" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome Completo</th>
                    <th>Email</th>
                    <th>Cargo</th>
                    <th>Status</th>
                    <th width="220">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Loop para exibir os registros
                foreach ($return as $r) {
                    echo "<tr>";
                    echo "<td>" . $r['id'] . "</td>";
                    echo "<td>" . $r['nome_completo'] . "</td>";
                    echo "<td>" . $r['email'] . "</td>";

                    // Substituir o ID do cargo pelo nome do cargo usando o mapa
                    $nomeCargo = $cargoMap[$r['cargo']] ?? 'Cargo não encontrado';
                    echo "<td>" . htmlspecialchars($nomeCargo) . "</td>";

                    $valor_status =  $r['status'] == '1' ? 'Ativo' : 'Inativo';

                    echo "<td><span class=\"badge bg-" . ($r['status'] == '1' ? 'success' : 'danger') . "\">" . $valor_status . " </span></td>";
                    $valor_status = "";


                    echo "<td> 
                                <div class=\"dropdown\">
                                <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                                    Ação
                                </button>
                                <ul class=\"dropdown-menu\">
                                    <li><a href=\"{$url}!/{$link[1]}/ver/{$r['id']}\" class=\"dropdown-item\">Ver</a></li>
                                    <li><a href=\"{$url}!/{$link[1]}/editar/{$r['id']}\" class=\"dropdown-item\">Editar</a></li>
                                    <li><a href=\"{$url}!/{$link[1]}/deletar/{$r['id']}\" class=\"dropdown-item text-danger\">Excluir</a></li>
                                </ul>
                                </div>
                        </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

    </div>

</div>

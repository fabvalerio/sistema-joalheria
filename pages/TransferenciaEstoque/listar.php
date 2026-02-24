<?php

use App\Models\TransferenciaEstoque\Controller;

$controller = new Controller();

$inicio = $_GET['data_inicio'] ?? null;
$fim = $_GET['data_final'] ?? null;
$loja_origem = $_GET['loja_origem'] ?? null;
$loja_destino = $_GET['loja_destino'] ?? null;

$transferencias = $controller->listar($inicio, $fim, $loja_origem, $loja_destino);
$lojas = $controller->listarLojas();

?>

<link href="<?php echo $url?>vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<script src="<?php echo $url?>vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo $url?>vendor/datatables/dataTables.bootstrap4.min.js"></script>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Transferências de Estoque</h3>
        <a href="<?php echo "{$url}!/TransferenciaEstoque/cadastro" ?>" class="btn btn-warning text-primary">Nova Transferência</a>
    </div>

    <div class="m-3">
        <div class="card card-body">
            <h6 class="card-title">Filtros</h6>
            <form id="filtroForm">
                <div class="row g-3 d-flex align-items-end">
                    <div class="col-lg-3">
                        <label class="form-label fw-bold">Origem</label>
                        <select name="loja_origem" id="loja_origem" class="form-select">
                            <option value="">Todas</option>
                            <?php foreach ($lojas as $loja): ?>
                                <option value="<?= $loja['id'] ?>" <?= $loja_origem == $loja['id'] ? 'selected' : '' ?>><?= htmlspecialchars($loja['nome']) ?> (<?= $loja['tipo'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label fw-bold">Destino</label>
                        <select name="loja_destino" id="loja_destino" class="form-select">
                            <option value="">Todas</option>
                            <?php foreach ($lojas as $loja): ?>
                                <option value="<?= $loja['id'] ?>" <?= $loja_destino == $loja['id'] ? 'selected' : '' ?>><?= htmlspecialchars($loja['nome']) ?> (<?= $loja['tipo'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label fw-bold">Data Inicial</label>
                        <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="<?= $inicio ?>">
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label fw-bold">Data Final</label>
                        <input type="date" name="data_final" id="data_final" class="form-control" value="<?= $fim ?>">
                    </div>
                    <div class="col-lg-2">
                        <a class="btn btn-success submit">FILTRAR</a>
                        <a class="btn btn-danger" href="<?= "{$url}!/TransferenciaEstoque/listar" ?>">LIMPAR</a>
                    </div>
                </div>
            </form>
            <script>
                $(document).ready(function() {
                    $(".submit").click(function(event) {
                        event.preventDefault();
                        let lojaOrigem = $("#loja_origem").val();
                        let lojaDestino = $("#loja_destino").val();
                        let dataInicio = $("#data_inicio").val();
                        let dataFinal = $("#data_final").val();
                        let url = "<?= $url ?>!/TransferenciaEstoque/listar/&loja_origem=" + encodeURIComponent(lojaOrigem) +
                            "&loja_destino=" + encodeURIComponent(lojaDestino) +
                            "&data_inicio=" + encodeURIComponent(dataInicio) +
                            "&data_final=" + encodeURIComponent(dataFinal);
                        window.location.href = url;
                    });
                });
            </script>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="tabelaTransferencias" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Produto</th>
                        <th>Origem</th>
                        <th>Destino</th>
                        <th>Quantidade</th>
                        <th>Data</th>
                        <th>Usuário</th>
                        <th>Observação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transferencias as $t): ?>
                        <tr>
                            <td><?= $t['id'] ?></td>
                            <td><strong><?= $t['produto_id'] ?></strong> - <?= htmlspecialchars($t['nome_produto'] ?? '') ?></td>
                            <td><span class="badge bg-danger"><?= htmlspecialchars($t['loja_origem'] ?? '') ?></span></td>
                            <td><span class="badge bg-success"><?= htmlspecialchars($t['loja_destino'] ?? '') ?></span></td>
                            <td><span class="badge bg-primary"><?= $t['quantidade'] ?></span></td>
                            <td><?= date('d/m/Y H:i', strtotime($t['data_transferencia'])) ?></td>
                            <td><?= htmlspecialchars($t['usuario'] ?? '') ?></td>
                            <td><?= htmlspecialchars($t['observacao'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tabelaTransferencias').DataTable({
            "language": {
                "sEmptyTable": "Nenhuma transferência encontrada",
                "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                "sSearch": "Pesquisar:",
                "sZeroRecords": "Nenhum registro encontrado",
                "oPaginate": { "sFirst": "Primeiro", "sPrevious": "Anterior", "sNext": "Próximo", "sLast": "Último" }
            },
            "order": [[0, 'desc']]
        });
    });
</script>

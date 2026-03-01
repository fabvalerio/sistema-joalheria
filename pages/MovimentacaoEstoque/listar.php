<?php

use App\Models\MovimentacaoEstoque\Controller;

$produto = $_GET['produto'] ?? null;
$tipo = $_GET['tipo'] ?? null;
$inicio = $_GET['data_inicio'] ?? null;
$fim = $_GET['data_final'] ?? null;
$pagina = $_GET['pagina'] ?? 1;

$controller = new Controller();
$movimentacoes = $controller->listar($produto, $inicio, $fim, $pagina, 10, $url."!/MovimentacaoEstoque/listar&tipo=".$tipo."&data_inicio=".$inicio."&data_final=".$fim."&produto=".$produto);

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Movimentações de Estoque</h3>
    </div>

    <div class="card-body">

    <h6 class="card-title">Filtros</h6>

                <form id="filtroForm">
                    <div class="row g-3 d-flex align-items-end">
                        <!-- <div class="col-lg-4">
                            <label class="form-label fw-bold">Tipo</label>
                            <select name="tipo" id="tipo" class="form-select">
                                <option value="" <?php echo ($tipo == '') ? 'selected' : ''; ?>>Todos</option>
                                <option value="Entrada" <?php echo ($tipo == 'Entrada') ? 'selected' : ''; ?>>Entrada</option>
                                <option value="Saida" <?php echo ($tipo == 'Saida') ? 'selected' : ''; ?>>Saida</option>
                            </select>
                        </div> -->

                        <div class="col-lg-4">
                            <label class="form-label fw-bold">Produto</label>
                            <input type="text" name="produto" id="produto" class="form-control" value="<?php echo $produto; ?>">
                        </div>

                        <div class="col-lg-2">
                            <label class="form-label fw-bold">Período Inicial</label>
                            <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="<?php echo $inicio; ?>">
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label fw-bold">Período Final</label>
                            <input type="date" name="data_final" id="data_final" class="form-control" value="<?php echo $fim; ?>">
                        </div>
                        <div class="col-lg-2">
                            <a class="btn btn-success submit">FILTRAR</a>
                            <a class="btn btn-danger" href="<?php echo "{$url}!/MovimentacaoEstoque/listar"; ?>">LIMPAR</a>
                        </div>
                    </div>
                </form>
                <script>
                        $(document).ready(function() {
                            $(".submit").click(function(event) {
                                event.preventDefault(); // Evita que o link redirecione

                                let produto = $("#produto").val();
                                let dataInicio = $("#data_inicio").val();
                                let dataFinal = $("#data_final").val();

                                // Monta a URL com os parâmetros
                                let baseUrl = "<?= $url ?>!/MovimentacaoEstoque/listar";
                                let url = baseUrl + "&produto=" + encodeURIComponent(produto) + 
                                        "&data_inicio=" + encodeURIComponent(dataInicio) + 
                                        "&data_final=" + encodeURIComponent(dataFinal);

                                // Redireciona para a nova URL
                                window.location.href = url;
                            });
                        });
                </script>

                <hr class="my-5">




        <table id="example1" class="table table-striped">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Tipo</th>
                    <th>Quantidade</th>
                    <th>Documento</th>
                    <th>Data</th>
                    <th>Motivo</th>
                    <th>Estoque Antes</th>
                    <th>Estoque Atualizado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movimentacoes['registros'] as $movimentacao): ?>
                    <tr>
                        <td><?= htmlspecialchars($movimentacao['descricao_produto']) ?></td>
                        <td><span class="badge bg-<?= $movimentacao['tipo_movimentacao'] == 'Entrada' ? 'success' : 'danger' ?>"><?= htmlspecialchars($movimentacao['tipo_movimentacao']) ?></span></td>
                        <td><?= number_format($movimentacao['quantidade'], 2, ',', '.') ?></td>
                        <td><?= htmlspecialchars($movimentacao['documento'] ?? '-') ?></td>
                        <td><?= date("d/m/Y", strtotime($movimentacao['data_movimentacao'])) ?></td>
                        <td><?= htmlspecialchars($movimentacao['motivo']) ?></td>
                        <td><span class="badge bg-<?= $movimentacao['estoque_antes'] > $movimentacao['estoque_atualizado'] ? 'danger' : 'success' ?>"><?= number_format($movimentacao['estoque_antes'], 2, ',', '.') ?> </span></td>
                        <td><span class="badge bg-<?= $movimentacao['estoque_antes'] > $movimentacao['estoque_atualizado'] ? 'danger' : 'success' ?>"><?= number_format($movimentacao['estoque_atualizado'], 2, ',', '.') ?> </span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


<!-- Paginação -->
<?php echo $movimentacoes['navegacaoHtml']; ?>
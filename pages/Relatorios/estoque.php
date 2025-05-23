<?php

use App\Models\Relatorios\Controller;

$tipo = $_GET['tipo'] ?? null;
$inicio = $_GET['data_inicio'] ?? null;
$fim = $_GET['data_final'] ?? null;
$pagina = $_GET['pagina'] ?? 1;

$controller = new Controller();
$movimentacoes = $controller->movimentos($tipo, $inicio, $fim, $pagina, 10, $url."/!/Relatorios/estoque/&tipo=".$tipo."&data_inicio=".$inicio."&data_final=".$fim);


?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Movimentações de Estoque</h3>
    </div>

<div class="m-3">
    <div class="row g-3">
        <div class="col-12">
            <div class="card card-body">
                <h6 class="card-title">Filtros</h6>
                <form id="filtroForm">
                    <div class="row g-3 d-flex align-items-end">
                        <div class="col-lg-4">
                            <label class="form-label fw-bold">Tipo</label>
                            <select name="tipo" id="tipo" class="form-select">
                                <option value="" <?php echo ($tipo == '') ? 'selected' : ''; ?>>Todos</option>
                                <option value="Entrada" <?php echo ($tipo == 'Entrada') ? 'selected' : ''; ?>>Entrada</option>
                                <option value="Saida" <?php echo ($tipo == 'Saida') ? 'selected' : ''; ?>>Saida</option>
                            </select>
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
                            <a class="btn btn-danger" href="<?php echo "{$url}!/Relatorios/estoque"; ?>">LIMPAR</a>
                        </div>
                    </div>
                </form>
                <script>
                        $(document).ready(function() {
                            $(".submit").click(function(event) {
                                event.preventDefault(); // Evita que o link redirecione

                                let tipo = $("#tipo").val();
                                let dataInicio = $("#data_inicio").val();
                                let dataFinal = $("#data_final").val();

                                // Monta a URL com os parâmetros
                                let url = "/sistema-joias/!/Relatorios/estoque/&tipo=" + encodeURIComponent(tipo) + 
                                        "&data_inicio=" + encodeURIComponent(dataInicio) + 
                                        "&data_final=" + encodeURIComponent(dataFinal);

                                // Redireciona para a nova URL
                                window.location.href = url;
                            });
                        });
                </script>
            </div>
        </div>
    </div>
</div>

    <div class="card-body">
        <table id="example1" class="table table-striped">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Tipo</th>
                    <th>Movimentação</th>
                    <th>Estoque Atual</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movimentacoes['registros'] as $m): ?>
                    <tr>
                        <td><strong><?php echo ($m['produto_id']) ?></strong> - <?php echo ($m['descricao_produto']) ?></td>
                        <td><span class="badge bg-<?php echo $m['tipo_movimentacao'] == 'Entrada' ? 'success' : 'danger' ?>">
                                <?php echo ($m['tipo_movimentacao']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-success">
                                <?php echo $m['quantidade']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-success">
                                <?php echo $m['atual']; ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


<!-- Paginação -->
<?php echo $movimentacoes['navegacaoHtml']; ?>
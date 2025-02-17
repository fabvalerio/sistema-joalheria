<?php

use App\Models\Definicoes\Controller;

$controller = new Controller();

// Supondo que o filtro esteja no segmento 2 da URL:
$tipoFiltro = $link[3] ?? '';

// Se o filtro estiver definido (modelo ou pedra), use o método listarPorTipo; caso contrário, liste todos
if (!empty($tipoFiltro)) {
    $produtosDefinicoes = $controller->listarPorTipo($tipoFiltro);
} else {
    $produtosDefinicoes = $controller->listar();
}

?>
<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Produto Definições</h3>
        <a href="<?= "{$url}!/{$link[1]}/cadastro" ?>" class="btn btn-white text-primary">Adicionar</a>

    </div>

    <div class="card-body">
        <!-- Filtro via URL amigável -->
        <div class="row mb-3">
            <div class="col-auto">
                <label for="tipo" class="form-label">Filtrar por Tipo:</label>
                <select name="tipo" id="tipo" class="form-select" onchange="filtrarTipo(this.value)">
                    <option value="" <?php if ($tipoFiltro == '') echo 'selected'; ?>>Todos</option>
                    <option value="modelo" <?php if ($tipoFiltro == 'modelo') echo 'selected'; ?>>Modelo</option>
                    <option value="pedra" <?php if ($tipoFiltro == 'pedra') echo 'selected'; ?>>Pedra</option>
                </select>
            </div>
        </div>

        <table id="example1" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th width="220">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtosDefinicoes as $definicao): ?>
                    <tr>
                        <td><?= $definicao['id'] ?></td>
                        <td><?= htmlspecialchars($definicao['nome']) ?></td>
                        <td><?= htmlspecialchars($definicao['tipo']) ?></td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Ação
                                </button>
                                <ul class="dropdown-menu">

                                    <li>
                                        <a href="<?= "{$url}!/{$link[1]}/deletar/{$definicao['id']}" ?>" class="dropdown-item text-danger">
                                            Excluir
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    function filtrarTipo(tipo) {
        if (tipo === '') {
            // Se "Todos", redireciona para a listagem sem filtro
            window.location.href = "<?php echo $url . '/!/' . $link[1] . '/listar/'; ?>";
        } else {
            // Se selecionado "modelo" ou "pedra", monta a URL amigável
            window.location.href = "<?php echo $url . '/!/' . $link[1] . '/listar/'; ?>" + tipo;
        }
    }
</script>
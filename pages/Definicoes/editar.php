<?php

use App\Models\Definicoes\Controller;

$controller = new Controller();
$id = $link[3]; // Pega o ID da URL

// Lida com a submissão do formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dados = [
        'id'   => $id,
        'nome' => $_POST['nome'],
        'tipo' => $_POST['tipo']
    ];

    $return = $controller->editar($dados);

    if ($return) {
        echo notify('success', "Definição atualizada com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    } else {
        echo notify('danger', "Erro ao atualizar a definição.");
    }
}

// Busca os dados existentes
$definicao = $controller->ver($id);

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Editar Definição</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar"; ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <form method="POST" action="<?php echo "{$url}!/{$link[1]}/editar/{$id}"; ?>" class="needs-validation" novalidate>
            <div class="row g-3">
                <div class="col-lg-6">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($definicao['nome'] ?? '') ?>" required>
                </div>
                <div class="col-lg-6">
                    <label for="tipo" class="form-label">Tipo</label>
                    <select class="form-select" id="tipo" name="tipo" required>
                        <option value="modelo" <?= ($definicao['tipo'] ?? '') == 'modelo' ? 'selected' : '' ?>>Modelo</option>
                        <option value="pedra" <?= ($definicao['tipo'] ?? '') == 'pedra' ? 'selected' : '' ?>>Pedra</option>
                    </select>
                </div>
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary float-end">Salvar Alterações</button>
                </div>
            </div>
        </form>
    </div>
</div>

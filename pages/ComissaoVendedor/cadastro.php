<?php

use App\Models\ComissaoVendedor\Controller;

// Instanciar o Controller
$controller = new Controller();

// Obter listas de grupos de produtos e usuários
$grupos = $controller->listarGruposProdutos();
$usuarios = $controller->listarUsuarios();

// Variáveis de controle
$modo = $_GET['modo'] ?? null; // "grupo" ou "usuario"
$referenciaId = $_GET['referencia_id'] ?? null; // ID do grupo ou usuário
$comissoesExistentes = [];

// Carregar comissões baseadas no modo selecionado
if ($modo === 'grupo' && $referenciaId) {
    $comissoesExistentes = $controller->listarComissoesPorGrupo($referenciaId);
} elseif ($modo === 'usuario' && $referenciaId) {
    $comissoesExistentes = $controller->listarComissoesPorUsuario($referenciaId);
}

// Salvar comissões ao enviar o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_comissoes'])) {
    $modo = $_POST['modo'];
    $referenciaId = $_POST['referencia_id'];
    $comissoes = $_POST['comissoes'];

    $success = ($modo === 'grupo')
        ? $controller->salvarComissoesPorGrupo($referenciaId, $comissoes)
        : $controller->salvarComissoesPorUsuario($referenciaId, $comissoes);

    if ($success) {
        echo notify('success', "Comissões atualizadas com sucesso!");
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
        exit;
    } else {
        echo notify('danger', "Erro ao atualizar as comissões.");
    }
}

?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">Cadastro/Edição de Comissões</h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
    </div>

    <div class="card-body">
        <!-- Escolha de Modo -->
        <form method="GET" action="" id="modo-form">
            <div class="row g-3">
                <div class="col-lg-6">
                    <label class="form-label">Escolha o Modo</label>
                    <select class="form-select" name="modo" id="modo" required>
                        <option value="">Selecione o Modo</option>
                        <option value="grupo" <?= ($modo === 'grupo') ? 'selected' : '' ?>>Partir de Grupo de Produtos</option>
                        <option value="usuario" <?= ($modo === 'usuario') ? 'selected' : '' ?>>Partir de Usuário</option>
                    </select>
                </div>

                <div class="col-lg-6" id="referencia-container" style="<?= $modo ? 'display: block;' : 'display: none;' ?>">
                    <label class="form-label"><?= ($modo === 'grupo') ? 'Grupo de Produtos' : 'Usuário' ?></label>
                    <select class="form-select" name="referencia_id" id="referencia-id" required>
                        <option value="">Selecione</option>
                        <?php if ($modo === 'grupo'): ?>
                            <?php foreach ($grupos as $grupo): ?>
                                <option value="<?= $grupo['id'] ?>" <?= ($referenciaId == $grupo['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($grupo['nome_grupo']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php elseif ($modo === 'usuario'): ?>
                            <?php foreach ($usuarios as $usuario): ?>
                                <option value="<?= $usuario['id'] ?>" <?= ($referenciaId == $usuario['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($usuario['nome_completo']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary">Carregar</button>
                </div>
            </div>
        </form>

        <!-- Tabela Dinâmica -->
        <?php if ($referenciaId): ?>
            <form method="POST" action="">
                <input type="hidden" name="modo" value="<?= $modo ?>">
                <input type="hidden" name="referencia_id" value="<?= $referenciaId ?>">
                <div class="mt-4">
                    <h5>Comissões</h5>
                    <?php if ($modo === 'grupo'): ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <div class="row g-3 align-items-center mb-3">
                                <div class="col-lg-8">
                                    <label class="form-label"><?= htmlspecialchars($usuario['nome_completo']) ?></label>
                                </div>
                                <div class="col-lg-4">
                                    <input type="number" step="0.01" class="form-control" name="comissoes[<?= $usuario['id'] ?>]" 
                                    value="<?= $comissoesExistentes[$usuario['id']] ?? '' ?>" placeholder="Comissão (%)">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php elseif ($modo === 'usuario'): ?>
                        <?php foreach ($grupos as $grupo): ?>
                            <div class="row g-3 align-items-center mb-3">
                                <div class="col-lg-8">
                                    <label class="form-label"><?= htmlspecialchars($grupo['nome_grupo']) ?></label>
                                </div>
                                <div class="col-lg-4">
                                    <input type="number" step="0.01" class="form-control" name="comissoes[<?= $grupo['id'] ?>]" 
                                    value="<?= $comissoesExistentes[$grupo['id']] ?? '' ?>" placeholder="Comissão (%)">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <div class="mt-3">
                        <button type="submit" name="salvar_comissoes" class="btn btn-primary">Salvar</button>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
    // Atualizar a página ao mudar o modo
    document.getElementById('modo').addEventListener('change', function () {
        const referenciaContainer = document.getElementById('referencia-container');
        referenciaContainer.style.display = this.value ? 'block' : 'none';
        if (!this.value) {
            document.getElementById('referencia-id').value = '';
        }
    });

    // Submeter o formulário ao alterar a referência
    document.getElementById('referencia-id').addEventListener('change', function () {
        document.getElementById('modo-form').submit();
    });
</script>

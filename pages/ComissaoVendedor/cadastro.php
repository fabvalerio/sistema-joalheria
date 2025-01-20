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

                <div class="col-lg-6" id="referencia-container">
                    <div class="result">
                        <label class="form-label">Selecione o campo modo</label>
                        <select class="form-select" name="referencia_id" id="referencia-id" required>
                            <option value="">Selecione o campo modo primeiro</option>
                        </select>
                    </div>
                </div>

                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary">Carregar</button>
                </div>
            </div>
        </form>

        
    </div>
</div>

<script>

    $(document).ready(function () {
        // Máscaras
        $("#modo").change(function () {
            //alert( $("#modo").val());

            if ($("#modo").val() == 'grupo') {

                $.get('<?php echo $url;?>pages/ComissaoVendedor/grupo.php', function (data) {
                    $("#referencia-container").html(data);
                });
                
            } else if ($("#modo").val() == 'usuario') {

                $.get('<?php echo $url;?>pages/ComissaoVendedor/usuario.php', function (data) {
                    $("#referencia-container").html(data);
                });

            }
        });
    });

    //ENVIAR GET
    $("form").submit(function (e) {
        e.preventDefault();
        
        var modo = $("#modo").val();
        var usuario = $("#usuario").val();
        var grupo = $("#grupo").val();


        if (modo == 'grupo') {
            window.location.href = '<?php echo $url;?>!/ComissaoVendedor/cadastro/&modo=' + modo + '&grupo=' + grupo;
        } else if (modo == 'usuario') {
            window.location.href = '<?php echo $url;?>!/ComissaoVendedor/cadastro/&modo=' + modo + '&usuario=' + usuario;
        }

    });



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

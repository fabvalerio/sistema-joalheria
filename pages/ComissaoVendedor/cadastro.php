<?php

use App\Models\ComissaoVendedor\Controller;

// Instanciar o Controller
$controller = new Controller();

// Obter listas de grupos de produtos e usuários
$grupos = $controller->listarGruposProdutos();
$usuarios = $controller->listarUsuarios();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_comissoes'])) {
  error_log("POST recebido: " . print_r($_POST, true)); // Adicionar log

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
              <div class="row align-items-center mb-2 p-2 border rounded bg-light">
                <div class="col-lg-8">
                  <label class="form-label fw-semibold text-primary m-0"><?= htmlspecialchars($usuario['nome_completo']) ?></label>
                </div>
                <div class="col-lg-4">
                  <input type="number" step="0.01" class="form-control border-gray" name="comissoes[<?= $usuario['id'] ?>]"
                    value="<?= $comissoesExistentes[$usuario['id']] ?? '' ?>" placeholder="Comissão (%)">
                </div>
              </div>
            <?php endforeach; ?>
          <?php elseif ($modo === 'usuario'): ?>
            <?php foreach ($grupos as $grupo): ?>
              <div class="row align-items-center mb-2 p-2 border rounded bg-light">
                <div class="col-lg-8">
                  <label class="form-label fw-semibold text-primary m-0"><?= htmlspecialchars($grupo['nome_grupo']) ?></label>
                </div>
                <div class="col-lg-4">
                  <input type="number" step="0.01" class="form-control border-gray"
                    name="comissoes[<?= $grupo['id'] ?>]"
                    value="<?= $comissoesExistentes[$grupo['id']] ?? '' ?>"
                    placeholder="Comissão (%)">
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
  $(document).ready(function() {
    // Máscaras
    $("#modo").change(function() {
      //alert( $("#modo").val());

      if ($("#modo").val() == 'grupo') {

        $.get('<?php echo $url; ?>pages/ComissaoVendedor/grupo.php', function(data) {
          $("#referencia-container").html(data);
        });

      } else if ($("#modo").val() == 'usuario') {

        $.get('<?php echo $url; ?>pages/ComissaoVendedor/usuario.php', function(data) {
          $("#referencia-container").html(data);
        });

      }
    });
  });

  //ENVIAR GET
  $("#modo-form").submit(function(e) {
    e.preventDefault(); // Evitar recarregar a página

    var modo = $("#modo").val();
    var usuario = $("#usuario").val();
    var grupo = $("#grupo").val();

    // Verificar o modo selecionado e redirecionar com base nisso
    if (modo == 'grupo') {
      window.location.href = '<?php echo $url; ?>!/ComissaoVendedor/cadastro/&modo=' + modo + '&referencia_id=' + grupo;
    } else if (modo == 'usuario') {
      window.location.href = '<?php echo $url; ?>!/ComissaoVendedor/cadastro/&modo=' + modo + '&referencia_id=' + usuario;
    }
  });
  $(document).ready(function() {
    // Atualizar os selects dinâmicos ao mudar o modo
    $("#modo").change(function() {
      const modo = $(this).val();
      if (modo === "grupo") {
        $.get("<?php echo $url; ?>pages/ComissaoVendedor/grupo.php", function(data) {
          $("#referencia-container").html(data);
        });
      } else if (modo === "usuario") {
        $.get("<?php echo $url; ?>pages/ComissaoVendedor/usuario.php", function(data) {
          $("#referencia-container").html(data);
        });
      }
    });
  });
  $(document).ready(function() {
    // Atualizar o segundo select dinâmico ao mudar o modo
    $("#modo").change(function() {
      const modo = $(this).val();
      if (modo === "grupo") {
        $.get("<?php echo $url; ?>pages/ComissaoVendedor/grupo.php", function(data) {
          $("#referencia-container").html(data);
          $("#referencia-id").prop('disabled', false); // Ativar o select novamente
        });
      } else if (modo === "usuario") {
        $.get("<?php echo $url; ?>pages/ComissaoVendedor/usuario.php", function(data) {
          $("#referencia-container").html(data);
          $("#referencia-id").prop('disabled', false); // Ativar o select novamente
        });
      } else {
        $("#referencia-container").html('<div class="result"><label class="form-label">Selecione o campo modo</label><select class="form-select" name="referencia_id" id="referencia-id" disabled><option value="">Selecione o campo modo primeiro</option></select></div>');
      }
    });

    // Atualizar o URL ao selecionar um valor no segundo select (referencia-id)
    $(document).on("change", "#referencia-id", function() {
      const modo = $("#modo").val();
      const referenciaId = $(this).val();

      if (modo && referenciaId) {
        window.location.href = '<?php echo $url; ?>!/ComissaoVendedor/cadastro/&modo=' + modo + '&referencia_id=' + referenciaId;
      }
    });

    // Ativar ou desativar o segundo select ao carregar a página
    const inicialModo = $("#modo").val();
    if (!inicialModo) {
      $("#referencia-id").prop('disabled', true); // Desativar o select se o modo não estiver selecionado
    }
  });
</script>
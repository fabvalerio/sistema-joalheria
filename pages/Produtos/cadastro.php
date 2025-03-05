<?php

use App\Models\Produtos\Controller;

// Instanciar o Controller
$controller = new Controller();

// Obter listas de fornecedores, grupos, subgrupos e cotações
$fornecedores = $controller->listarFornecedores();
$grupos = $controller->listarGrupos();
$subgrupos = $controller->listarSubgrupos();
$cotacoes = $controller->listarCotacoes();
$modelos = $controller->listarModelos();
$pedras = $controller->listarPedras();



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $dados = [
    'descricao_etiqueta' => $_POST['descricao_etiqueta'],
    'fornecedor_id' => $_POST['fornecedor_id'],
    'grupo_id' => $_POST['grupo_id'],
    'subgrupo_id' => $_POST['subgrupo_id'],
    'modelo' => $_POST['modelo'] ?? null,
    'macica_ou_oca' => $_POST['macica_ou_oca'] ?? null,
    'numeros' => $_POST['numeros'] ?? null,
    'pedra' => $_POST['pedra'] ?? null,
    'nat_ou_sint' => $_POST['nat_ou_sint'] ?? null,
    'peso' => $_POST['peso'] ?? null,
    'aros' => $_POST['aros'] ?? null,
    'cm' => $_POST['cm'] ?? null,
    'pontos' => $_POST['pontos'] ?? null,
    'mm' => $_POST['mm'] ?? null,
    'unidade' => $_POST['unidade'] ?? null,
    'estoque_princ' => $_POST['estoque_princ'] ?? null,
    'cotacao' => $_POST['cotacao'] ?? null,
    'preco_ql' => $_POST['preco_ql'] ?? null,
    'peso_gr' => $_POST['peso_gr'] ?? null,
    'custo' => $_POST['custo'] ?? null,
    'margem' => $_POST['margem'] ?? null,
    'em_reais' => $_POST['em_reais'] ?? null,
    'capa' => $_POST['capa_base64'] ?? null
  ];

  $return = $controller->cadastro($dados);

  if ($return) {
    echo notify('success', "Produto cadastrado com sucesso!");
    echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    exit;
  } else {
    echo notify('danger', "Erro ao cadastrar o produto.");
  }
}

?>

<div class="card">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h3 class="card-title">Cadastro de Produto</h3>
    <a href="<?php echo "{$url}!/{$link[1]}/listar" ?>" class="btn btn-warning text-primary">Voltar</a>
  </div>

  <div class="card-body">
    <form method="POST" action="" class="needs-validation" novalidate>
      <div class="row g-3">
        <div class="col-12">
          <hr>
        </div>
        <!-- Fornecedor -->
        <div class="col-lg-4">
          <label class="form-label">Fornecedor</label>
          <select class="form-select" name="fornecedor_id" id="fornecedor" required>
            <option value="">Selecione o Fornecedor</option>
            <?php foreach ($fornecedores as $fornecedor): ?>
              <option value="<?= $fornecedor['id'] ?>"><?= htmlspecialchars($fornecedor['nome_fantasia']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Grupo -->
        <div class="col-lg-4">
          <label class="form-label">Grupo</label>
          <select class="form-select" name="grupo_id" id="grupo" required>
            <option value="">Selecione o Grupo</option>
            <?php foreach ($grupos as $grupo): ?>
              <option value="<?= $grupo['id'] ?>"><?= htmlspecialchars($grupo['nome_grupo']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Subgrupo -->
        <div class="col-lg-4">
          <label class="form-label">Subgrupo</label>
          <select class="form-select" name="subgrupo_id" id="subgrupo" required>
            <option value="">Selecione o Subgrupo</option>
          </select>
        </div>

        <div class="col-12">
          <hr>
        </div>
        <!-- Campos adicionais aparecem após seleção -->
        <div id="campos-adicionais" style="display: none;">

          <div class="row g-3">
            <div class="col-lg-12">
              <div id="preview-container" style="text-align: center;">
                <img id="preview-thumb" src="" alt="Preview da Imagem" style="max-width: 100%; max-height: 200px; display: none; border: 1px solid #ddd; padding: 5px; border-radius: 5px;">
              </div>
            </div>
            <div class="col-lg-12">
              <label class="form-label">Foto de Capa do Produto (Opcional) </label>
              <input type="file" class="form-control" name="capa" id="capa" accept="image/*">
              <input type="hidden" name="capa_base64" id="capa_base64">
            </div>
            <!-- Descrição Etiqueta (gerada automaticamente) -->
            <div class="col-lg-12">
              <label class="form-label">Descrição Etiqueta</label>
              <input type="text" class="form-control bg-secondary text-white" name="descricao_etiqueta" id="descricao_etiqueta" readonly>
            </div>
            <!-- Descrição Adicional Etiqueta (Manual) -->
            <div class="col-lg-12">
              <label class="form-label">Descrição Adicional Etiqueta (opcional)</label>
              <input type="text" class="form-control" name="descricao_etiqueta_manual" id="descricao_etiqueta_manual">
            </div>
            <!-- Modelo -->
            <div class="col-lg-2">
              <label class="form-label">Modelo</label>
              <div class="input-group">
                <select class="form-select" name="modelo" id="modelo">
                  <option value="">Selecione</option>
                  <?php
                  foreach ($modelos as $modelo) {
                    echo '<option value="' . htmlspecialchars($modelo['nome']) . '">' . htmlspecialchars($modelo['nome']) . '</option>';
                  }
                  ?>
                </select>
                <button type="button" class="btn bg-success text-white" data-bs-toggle="modal" data-bs-target="#modalNovoModelo">+</button>
              </div>
            </div>

            <script>
              // Monta a URL dinamicamente utilizando as variáveis PHP
              var caminhoAjax = "<?php echo $url . 'pages/' . $link[1] . '/adicionar_modelo.php'; ?>";

              function salvarModelo() {
                var novoModelo = $("#novoModelo").val().trim();
                var tipo = $("#tipo").val().trim();

                if (novoModelo === '') {
                  alert('Sr. Valério, por favor insira o nome do modelo.');
                  return;
                }

                $.ajax({
                  url: caminhoAjax,
                  type: 'POST',
                  data: {
                    novoModelo: novoModelo,
                    tipo: tipo
                  },
                  dataType: 'json',
                  success: function(response) {
                    if (response.success) {
                      // Fecha o modal
                      $("#modalNovoModelo").modal('hide');
                      // Exibe o alerta com a mensagem de sucesso
                      alert(response.message);
                      // Adiciona a nova opção no select
                      $("#modelo").append('<option value="' + novoModelo + '">' + novoModelo + '</option>');
                      $("#modelo").val(novoModelo);
                      atualizarDescricaoEtiqueta(); // Atualiza a etiqueta após fechar o modal
                    } else {
                      alert('Erro: ' + response.message);
                    }
                  },
                  error: function() {
                    alert('Sr. Valério, ocorreu um erro inesperado.');
                  }
                });
              }
            </script>
            <!-- Material (Maciça/Oca) -->
            <div class="col-lg-2">
              <label class="form-label">Material (Maciça/Oca)</label>
              <select class="form-select" name="macica_ou_oca" id="macica_ou_oca">
                <option value="">Selecione</option>
                <option value="Maciça">Maciça</option>
                <option value="Oca">Oca</option>
              </select>
            </div>

            <!-- Peso -->
            <div class="col-lg-2">
              <label class="form-label">Peso (g)</label>
              <input type="number" step="0.001" class="form-control" name="peso" id="peso">
            </div>

            <!-- Aros -->
            <div class="col-lg-2">
              <label class="form-label">Aros</label>
              <input type="number" step="0.001" class="form-control" name="aros" id="aros">
            </div>

            <div class="col-lg-2">
              <label class="form-label">Centímetros (cm)</label>
              <input type="number" class="form-control" name="cm" id="cm" placeholder="Digite o valor em centímetros">
            </div>

            <!-- Numero (Anel) -->
            <div class="col-lg-2">
              <label class="form-label">Número (Anel)</label>
              <input type="number" step="1.0" class="form-control" name="numeros" id="numeros">
            </div>

            <div class="col-lg-2">
              <label class="form-label">Pedra</label>
              <div class="input-group">
                <select class="form-select" name="pedra" id="pedra">
                  <option value="">Selecione</option>
                  <?php
                  foreach ($pedras as $pedra) {
                    echo '<option value="' . htmlspecialchars($pedra['nome']) . '">' . htmlspecialchars($pedra['nome']) . '</option>';
                  }
                  ?>
                </select>
                <!-- Botão para abrir o modal de nova pedra -->
                <button type="button" class="btn bg-success text-white" data-bs-toggle="modal" data-bs-target="#modalNovaPedra">+</button>
              </div>
            </div>

            <script>
              // Monta a URL dinamicamente para o mesmo arquivo PHP usado em "modelo"
              var caminhoAjaxPedra = "<?php echo $url . 'pages/' . $link[1] . '/adicionar_modelo.php'; ?>";

              function salvarPedra() {
                var novaPedra = $("#novaPedra").val().trim();
                var tipoPedra = $("#tipoPedra").val().trim();

                if (novaPedra === '') {
                  alert('Sr. Valério, por favor insira o nome da pedra.');
                  return;
                }

                $.ajax({
                  url: caminhoAjaxPedra,
                  type: 'POST',
                  data: {
                    // Repare que a chave é a mesma do arquivo PHP: 'novoModelo' e 'tipo'
                    // Estamos apenas reutilizando "novoModelo" para enviar o nome da pedra
                    novoModelo: novaPedra,
                    tipo: tipoPedra
                  },
                  dataType: 'json',
                  success: function(response) {
                    if (response.success) {
                      // Fecha o modal
                      $("#modalNovaPedra").modal('hide');
                      // Exibe o alerta com a mensagem de sucesso
                      alert(response.message);
                      // Adiciona a nova opção no select de pedras
                      $("#pedra").append('<option value="' + novaPedra + '">' + novaPedra + '</option>');
                      $("#pedra").val(novaPedra);
                      atualizarDescricaoEtiqueta(); // Atualiza a etiqueta após fechar o modal

                    } else {
                      alert('Erro: ' + response.message);
                    }
                  },
                  error: function() {
                    alert('Sr. Valério, ocorreu um erro inesperado.');
                  }
                });
              }
            </script>

            <!-- Natural ou Sintético -->
            <div class="col-lg-2">
              <label class="form-label">Natural ou Sintético</label>
              <select class="form-select" name="nat_ou_sint" id="nat_ou_sint">
                <option value="">Selecione</option>
                <option value="Natural">Natural</option>
                <option value="Sintético">Sintético</option>
              </select>
            </div>

            <!-- Pontos -->
            <div class="col-lg-2">
              <label class="form-label">Pontos</label>
              <input type="number" step="0.001" class="form-control" name="pontos" id="pontos">
            </div>



            <!-- Milímetros (mm) -->
            <div class="col-lg-2">
              <label class="form-label">Milímetros (mm)</label>
              <input type="number" class="form-control" name="mm" id="mm" placeholder="Digite o valor em milímetros">
            </div>


            <!-- Unidade -->
            <div class="col-lg-2">
              <label class="form-label">Unidade</label>
              <select class="form-select" name="unidade" id="unidade">
                <option value="unidade">Unidade</option>
                <option value="par">Par</option>
              </select>
            </div>



            <div class="col-lg-2">
              <label class="form-label">Quantidade</label>
              <input type="number" step="0.001" class="form-control" name="estoque_princ" id="estoque_princ">
            </div>
            <div class="col-12">
              <hr>
            </div>
            <!-- Cotação -->
            <div class="col-lg-4">
              <label class="form-label">Cotação</label>
              <select class="form-select" name="cotacao" id="cotacao" required>
                <option value="">Selecione a Cotação</option>
                <?php foreach ($cotacoes as $cotacao): ?>
                  <option value="<?= $cotacao['id'] ?>" data-valor="<?= $cotacao['valor'] ?>">
                    <?= htmlspecialchars($cotacao['nome']) ?> (<?= htmlspecialchars($cotacao['valor']) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Campos adicionais ao selecionar a cotação -->
            <div id="campos-cotacao" style="display: none;">
              <div class="row g-3">
                <div class="col-lg-2">
                  <label class="form-label">Preço QL</label>
                  <input type="number" step="0.01" class="form-control text-center" name="preco_ql" id="preco_ql">
                </div>
                <div class="col-lg-2">
                  <label class="form-label">Peso Gr</label>
                  <input type="number" step="0.001" class="form-control text-center" name="peso_gr" id="peso_gr">
                </div>
                <div class="col-lg-2">
                  <label class="form-label">Margem (%)</label>
                  <input type="number" step="0.01" class="form-control text-center" name="margem" id="margem">
                </div>
                <div class="col-lg-2">
                  <label class="form-label">Custo</label>
                  <input type="number" step="0.01" class="form-control bg-secondary text-white text-center" name="custo" id="custo" readonly>
                </div>
                <div class="col-lg-4">
                  <label class="form-label">Em Reais</label>
                  <input type="number" step="0.01" class="form-control bg-secondary text-white text-center" name="em_reais" id="em_reais" readonly>
                </div>
              </div>
            </div>
            <div class="col-lg-12">
                    <label for="observacoes" class="form-label">Observações</label>
                    <textarea class="form-control" id="observacoes" name="observacoes" rows="3"></textarea>
                </div>
          </div>
        </div>


      </div>

      <div class="mt-3">
        <button type="submit" class="btn btn-primary">Salvar</button>
      </div>
    </form>
    <!-- Modal para adicionar novo modelo -->
    <div class="modal fade" id="modalNovoModelo" tabindex="-1" aria-labelledby="modalNovoModeloLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalNovoModeloLabel">Adicionar Novo Modelo</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formNovoModelo">
              <div class="mb-3">
                <label for="novoModelo" class="form-label">Nome do Modelo</label>
                <input type="text" class="form-control" id="novoModelo" name="novoModelo" required>
                <input type="hidden" class="form-control" id="tipo" name="tipo" value="modelo" required>
              </div>
              <button type="button" class="btn btn-success" onclick="salvarModelo()">Salvar</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal para adicionar nova pedra -->
    <div class="modal fade" id="modalNovaPedra" tabindex="-1" aria-labelledby="modalNovaPedraLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalNovaPedraLabel">Adicionar Nova Pedra</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formNovaPedra">
              <div class="mb-3">
                <label for="novaPedra" class="form-label">Nome da Pedra</label>
                <input type="text" class="form-control" id="novaPedra" name="novaPedra" required>
                <input type="hidden" class="form-control" id="tipoPedra" name="tipoPedra" value="pedra" required>
              </div>
              <button type="button" class="btn btn-success" onclick="salvarPedra()">Salvar</button>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
<script>
  const cotacaoSelect = document.getElementById('cotacao');
  const precoQlInput = document.getElementById('preco_ql');
  const pesoGrInput = document.getElementById('peso_gr');
  const margemInput = document.getElementById('margem');
  const custoInput = document.getElementById('custo');
  const emReaisInput = document.getElementById('em_reais');
  const camposCotacao = document.getElementById('campos-cotacao');

  // Mostrar campos adicionais ao selecionar cotação
  cotacaoSelect.addEventListener('change', () => {
    if (cotacaoSelect.value) {
      camposCotacao.style.display = 'block';
    } else {
      camposCotacao.style.display = 'none';
      limparCamposCotacao();
    }
  });

  // Atualizar Custo e Em Reais automaticamente
  [precoQlInput, pesoGrInput, margemInput, cotacaoSelect].forEach(input => {
    input.addEventListener('input', calcularValores);
  });

  function calcularValores() {
    const cotacaoValor = parseFloat(cotacaoSelect.options[cotacaoSelect.selectedIndex]?.dataset?.valor || 0);
    const precoQl = parseFloat(precoQlInput.value || 0);
    const pesoGr = parseFloat(pesoGrInput.value || 0);
    const margem = parseFloat(margemInput.value || 0);

    // Calcular Custo
    const custo = precoQl * pesoGr * cotacaoValor;
    custoInput.value = custo.toFixed(2);

    // Calcular Em Reais
    const emReais = custo * (1 + margem / 100);
    emReaisInput.value = emReais.toFixed(2);
  }

  function limparCamposCotacao() {
    precoQlInput.value = '';
    pesoGrInput.value = '';
    margemInput.value = '';
    custoInput.value = '';
    emReaisInput.value = '';
  }
</script>
<script>
  // Mostrar campos adicionais ao selecionar fornecedor, grupo e subgrupo
  const fornecedor = document.getElementById('fornecedor');
  const grupo = document.getElementById('grupo');
  const subgrupo = document.getElementById('subgrupo');
  const modelo = document.getElementById('modelo');
  const camposAdicionais = document.getElementById('campos-adicionais');
  const descricaoEtiqueta = document.getElementById('descricao_etiqueta');
  const macica_ou_oca = document.getElementById('macica_ou_oca');
  const pedra = document.getElementById('pedra');
  const peso = document.getElementById('peso');
  const nat_ou_sint = document.getElementById('nat_ou_sint');
  const unidade = document.getElementById('unidade');
  const descricao_etiqueta_manual = document.getElementById('descricao_etiqueta_manual');
  const numeros = document.getElementById('numeros');


  // Adicionar listeners para atualização da descrição

  [fornecedor, grupo, subgrupo, modelo, macica_ou_oca, nat_ou_sint, unidade, peso, pedra, numeros].forEach(select => {
    select.addEventListener('change', () => {
      if (fornecedor.value && grupo.value && subgrupo.value) {
        camposAdicionais.style.display = 'block';
        atualizarDescricaoEtiqueta();
      } else {
        camposAdicionais.style.display = 'none';
        descricaoEtiqueta.value = '';
      }
    });
  });

  // Listener para o evento 'input' do campo manual
  descricao_etiqueta_manual.addEventListener('input', atualizarDescricaoEtiqueta);

  // Atualizar Descrição Etiqueta automaticamente
  function atualizarDescricaoEtiqueta() {
    const grupoText = grupo.options[grupo.selectedIndex]?.text || '';
    const subgrupoText = subgrupo.options[subgrupo.selectedIndex]?.text || '';
    const modeloText = modelo.options[modelo.selectedIndex]?.value || '';
    const macica_ou_ocaText = macica_ou_oca.options[macica_ou_oca.selectedIndex]?.value || '';
    const nat_ou_sintText = nat_ou_sint.options[nat_ou_sint.selectedIndex]?.value || '';
    const unidadeText = unidade.options[unidade.selectedIndex]?.text || '';
    const pesoValue = peso.value ? `${peso.value}g` : '';
    const descricao_etiqueta_manualValue = descricao_etiqueta_manual.value || '';
    const pedravalor = pedra.options[pedra.selectedIndex]?.value ? `- com ${pedra.options[pedra.selectedIndex]?.value}` : '';
    const numerosvalor = numeros.value ? `Nº${numeros.value}` : '';

    // Criar a string apenas com valores definidos
    descricaoEtiqueta.value = [
      `${grupoText} -`,
      subgrupoText,
      pedravalor,
      modeloText ? `- ${modeloText}` : '',
      numerosvalor ? `- ${numerosvalor}` : '',
      macica_ou_ocaText ? `- ${macica_ou_ocaText}` : '',
      nat_ou_sintText ? `- ${nat_ou_sintText}` : '',
      unidadeText ? `- ${unidadeText}` : '',
      pesoValue ? `- ${pesoValue}` : '',
      descricao_etiqueta_manualValue ? `- [ ${descricao_etiqueta_manualValue} ]` : ''
    ].filter(text => text.trim() !== '').join(' ');

  }

  // Atualizar subgrupos dinamicamente ao alterar o grupo
  document.getElementById('grupo').addEventListener('change', function() {
    const grupoId = this.value;
    const subgrupoSelect = document.getElementById('subgrupo');

    // Limpar o select de subgrupo
    subgrupoSelect.innerHTML = '<option value="">Selecione o Subgrupo</option>';

    if (!grupoId) {
      return; // Se nenhum grupo foi selecionado, parar aqui
    }

    // Fazer a requisição AJAX
    fetch(`<?php echo $url; ?>pages/Produtos/subgrupos.php?grupo_id=${grupoId}`)
      .then(response => response.json())
      .then(subgrupos => {
        // Preencher o select de subgrupos
        subgrupos.forEach(subgrupo => {
          const option = document.createElement('option');
          option.value = subgrupo.id;
          option.textContent = subgrupo.nome_subgrupo;
          subgrupoSelect.appendChild(option);
        });
      })
      .catch(error => console.error('Erro ao buscar subgrupos:', error));
  });
  document.getElementById('capa').addEventListener('change', function(event) {
    const file = event.target.files[0]; // Obtém o arquivo selecionado

    if (file) {
      const reader = new FileReader();

      // Quando a leitura estiver completa, exibe a imagem e converte para Base64
      reader.onload = function(e) {
        const base64 = e.target.result; // Base64 da imagem
        const previewThumb = document.getElementById('preview-thumb');
        const fotoCapaBase64 = document.getElementById('capa_base64');

        previewThumb.src = base64; // Define a imagem para exibição
        previewThumb.style.display = 'block'; // Exibe o thumbnail
        fotoCapaBase64.value = base64; // Armazena o Base64 no campo oculto
      };

      reader.readAsDataURL(file); // Lê o arquivo como Base64
    }
  });
</script>
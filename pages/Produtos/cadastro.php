<?php

use App\Models\Produtos\Controller;

// Instanciar o Controller
$controller = new Controller();

// Obter listas de fornecedores, grupos, subgrupos e cotações
$fornecedores = $controller->listarFornecedores();
$grupos = $controller->listarGrupos();
$subgrupos = $controller->listarSubgrupos();
$cotacoes = $controller->listarCotacoes();

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
        <!-- Descrição Etiqueta (gerada automaticamente) -->
        <div class="col-lg-12">
          <label class="form-label">Descrição Etiqueta</label>
          <input type="text" class="form-control" name="descricao_etiqueta" id="descricao_etiqueta" readonly>
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
            <?php foreach ($subgrupos as $subgrupo): ?>
              <option value="<?= $subgrupo['id'] ?>"><?= htmlspecialchars($subgrupo['nome_subgrupo']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12 pessoa-fisica" style="">
          <hr>
        </div>
        <!-- Campos adicionais aparecem após seleção -->
        <div id="campos-adicionais" style="display: none;">
          <div class="row g-3">
            <!-- Modelo -->
            <div class="col-lg-4">
              <label class="form-label">Modelo</label>
              <select class="form-select" name="modelo" id="modelo">
                <option value="">Selecione</option>
                <option value="362">3 Aros Liso 5 Com Pedras</option>
                <option value="370">Aro Entrelaçado Com</option>
                <option value="537">Baiano</option>
                <option value="374">Bola</option>
                <option value="346">Cartier</option>
                <option value="533">Elos 1 X 1</option>
                <option value="532">Elos 2 X 1</option>
                <option value="531">Elos 3 X 1</option>
                <option value="345">Grume</option>
                <option value="536">Piastrine</option>
                <option value="535">Singa Pura</option>
                <option value="534">Veneziana</option>
              </select>
            </div>

            <!-- Material (Maciça/Oca) -->
            <div class="col-lg-4">
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

            <!-- Unidade -->
            <div class="col-lg-2">
              <label class="form-label">Unidade</label>
              <select class="form-select" name="unidade" id="unidade">
                <option value="unidade">Unidade</option>
                <option value="par">Par</option>
              </select>
            </div>

            <!-- Natural ou Sintético -->
            <div class="col-lg-4">
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
              <select class="form-select" name="mm" id="mm">
                <option value="">Selecione</option>
                <option value=""></option>
                <option value="361"> 00,80 Mm </option>
                <option value="376"> 00.90 Mm </option>
                <option value="371"> 01,00 Mm </option>
                <option value="377"> 01,25 Mm </option>
                <option value="364"> 01,50 Mm </option>
                <option value="378"> 01,75 Mm </option>
                <option value="379"> 02,00 Mm </option>
                <option value="380"> 02,25 Mm </option>
                <option value="381"> 02,50 Mm </option>
                <option value="382"> 02,75 Mm </option>
                <option value="383"> 03,00 Mm </option>
                <option value="384"> 03,25 Mm </option>
                <option value="385"> 03,50 Mm </option>
                <option value="386"> 03,75 Mm </option>
                <option value="375"> 04,00 Mm </option>
                <option value="387"> 04,25 Mm </option>
                <option value="388"> 04,50 Mm </option>
                <option value="389"> 04,75 Mm </option>
                <option value="390"> 05,00 Mm </option>
                <option value="392"> 05,50 Mm </option>
                <option value="393"> 06,00 Mm </option>
                <option value="394"> 06,50 Mm </option>
                <option value="391"> 07,00 Mm </option>
                <option value="395"> 07,50 Mm </option>
                <option value="396"> 08,00 Mm </option>
                <option value="397"> 08,50 Mm </option>
                <option value="398"> 09,00 Mm </option>
                <option value="399"> 09,50 Mm </option>
                <option value="400"> 10,00 Mm </option>
                <option value="401"> 10,50 Mm </option>
                <option value="402"> 11,00 Mm </option>
                <option value="403"> 11,50 Mm </option>
                <option value="404"> 12,00 Mm </option>
                <option value="405"> 12,50 Mm </option>
                <option value="406"> 13,00 Mm </option>
                <option value="407"> 13,50 Mm </option>
                <option value="408"> 14,00 Mm </option>
                <option value="409"> 14,50 Mm </option>
                <option value="410"> 15,00 Mm </option>

              </select>

            </div>

            <!-- Centímetros (cm) -->
            <div class="col-lg-2">
              <label class="form-label">Centímetros (cm)</label>
              <select class="form-select" name="cm" id="cm">
                <option value="">Selecione</option>
                <option value="411"> 10,00 Cm </option>
                <option value="412"> 10,50 Cm </option>
                <option value="413"> 11,00 Cm </option>
                <option value="414"> 11,50 Cm </option>
                <option value="415"> 12,00 Cm </option>
                <option value="416"> 12,50 Cm </option>
                <option value="417"> 13,00 Cm </option>
                <option value="418"> 13,50 Mm </option>
                <option value="419"> 14,00 Mm </option>
                <option value="420"> 14,50 Cm </option>
                <option value="421"> 15,00 Cm </option>
                <option value="422"> 15,50 Cm </option>
                <option value="423"> 16,00 Cm </option>
                <option value="424"> 16,50 Cm </option>
                <option value="425"> 17,00 Cm </option>
                <option value="426"> 17,50 Cm </option>
                <option value="427"> 18,00 Cm </option>
                <option value="428"> 18,50 Cm </option>
                <option value="429"> 19,00 Cm </option>
                <option value="430"> 19,50 Cm </option>
                <option value="431"> 20,00 Cm </option>
                <option value="432"> 20,50 Cm </option>
                <option value="433"> 21,00 Cm </option>
                <option value="434"> 21,50 Cm </option>
                <option value="435"> 22,00 Cm </option>
                <option value="436"> 22,50 Cm </option>
                <option value="437"> 23,00 cm </option>
                <option value="438"> 23,50 Cm </option>
                <option value="439"> 24,00 Cm </option>
                <option value="440"> 24,50 Cm </option>
                <option value="441"> 25,00 Cm </option>
                <option value="442"> 25,50 Cm </option>
                <option value="443"> 26,00 Cm </option>
                <option value="444"> 26,50 Cm </option>
                <option value="445"> 27,00 Cm </option>
                <option value="446"> 27,50 Cm </option>
                <option value="447"> 28,00 Cm </option>
                <option value="448"> 28,50 Cm </option>
                <option value="449"> 29,00 Cm </option>
                <option value="450"> 29,50 Cm </option>
                <option value="451"> 30,00 Cm </option>
                <option value="452"> 30,50 Cm </option>
                <option value="453"> 31,00 Cm </option>
                <option value="454"> 31,50 Cm </option>
                <option value="455"> 32,00 Cm </option>
                <option value="456"> 32,50 Cm </option>
                <option value="457"> 33,00 Cm </option>
                <option value="458"> 33,50 Cm </option>
                <option value="459"> 34,00 Cm </option>
                <option value="460"> 34,50 Cm </option>
                <option value="461"> 35,00 Cm </option>
                <option value="462"> 35,50 Cm </option>
                <option value="463"> 36,00 Cm </option>
                <option value="464"> 36,50 m </option>
                <option value="465"> 37,00 Cm </option>
                <option value="466"> 37,50 Cm </option>
                <option value="467"> 38,00 Cm </option>
                <option value="468"> 38,50 Cm </option>
                <option value="469"> 39,00 Cm </option>
                <option value="470"> 39,50 Cm </option>
                <option value="356"> 40,00 Cm </option>
                <option value="471"> 40,50 Cm </option>
                <option value="472"> 41,00 Cm </option>
                <option value="473"> 41,50 Cm </option>
                <option value="474"> 42,00 Cm </option>
                <option value="475"> 42,50 Cm </option>
                <option value="476"> 43,00 Cm </option>
                <option value="477"> 43,50 Cm </option>
                <option value="478"> 44,00 Cm </option>
                <option value="479"> 44,50 Cm </option>
                <option value="480"> 45,00 Cm </option>
                <option value="481"> 45,50 Cm </option>
                <option value="482"> 46,00 Cm </option>
                <option value="483"> 46,50 Cm </option>
                <option value="484"> 47,00 Cm </option>
                <option value="485"> 47,50 Cm </option>
                <option value="486"> 48,00 Cm </option>
                <option value="487"> 48,50 Cm </option>
                <option value="488"> 49,00 Cm </option>
                <option value="489"> 49,50 Cm </option>
                <option value="490"> 50,00 Cm </option>
                <option value="491"> 50,50 Cm </option>
                <option value="492"> 51,00 Cm </option>
                <option value="493"> 51,50 Cm </option>
                <option value="494"> 52,00 Cm </option>
                <option value="495"> 52,50 Cm </option>
                <option value="496"> 53,00 Cm </option>
                <option value="497"> 53,50 Cm </option>
                <option value="498"> 54,00 Cm </option>
                <option value="499"> 54,50 Cm </option>
                <option value="500"> 55,00 Cm </option>
                <option value="501"> 55,50 Cm </option>
                <option value="502"> 56,00 Cm </option>
                <option value="503"> 56,50 Cm </option>
                <option value="504"> 57,00 Cm </option>
                <option value="505"> 57,50 Cm </option>
                <option value="506"> 58,00 Cm </option>
                <option value="507"> 58,50 Cm </option>
                <option value="508"> 59,00 Cm </option>
                <option value="509"> 59,50 Cm </option>
                <option value="510"> 60,00 Cm </option>
                <option value="511"> 60,50 Cm </option>
                <option value="512"> 61,00 Cm </option>
                <option value="513"> 61,50 Cm </option>
                <option value="514"> 62,00 Cm </option>
                <option value="515"> 62,50 Cm </option>
                <option value="516"> 63,00 Cm </option>
                <option value="517"> 63,50 Cm </option>
                <option value="518"> 64,00 Cm </option>
                <option value="519"> 64,50 Cm </option>
                <option value="520"> 65,00 Cm </option>
                <option value="521"> 65,50 Cm </option>
                <option value="522"> 66,00 Cm </option>
                <option value="523"> 66,50 Cm </option>
                <option value="524"> 67,00 Cm </option>
                <option value="525"> 67,50 Cm </option>
                <option value="526"> 68,00 Cm </option>
                <option value="527"> 68,50 Cm </option>
                <option value="528"> 69,00 Cm </option>
                <option value="529"> 69,50 Cm </option>
                <option value="530"> 70,00 Cm </option>

              </select>
            </div>
            <div class="col-lg-2">
              <label class="form-label">Quantidade</label>
              <input type="number" step="0.001" class="form-control" name="estoque_princ" id="estoque_princ">
            </div>
            <div class="col-12 pessoa-fisica" style="">
              <hr>
            </div>
            <!-- Cotação -->
            <div class="col-lg-4">
              <label class="form-label">Cotação</label>
              <select class="form-select" name="cotacao" id="cotacao" required>
                <option value="">Selecione a Cotação</option>
                <?php foreach ($cotacoes as $cotacao): ?>
                  <option value="<?= $cotacao['id'] ?>" data-valor="<?= $cotacao['valor'] ?>">
                    <?= htmlspecialchars($cotacao['nome']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Campos adicionais ao selecionar a cotação -->
            <div id="campos-cotacao" style="display: none;">
              <div class="row g-3">
                <div class="col-lg-2">
                  <label class="form-label">Preço QL</label>
                  <input type="number" step="0.01" class="form-control" name="preco_ql" id="preco_ql">
                </div>
                <div class="col-lg-2">
                  <label class="form-label">Peso Gr</label>
                  <input type="number" step="0.001" class="form-control" name="peso_gr" id="peso_gr">
                </div>
                <div class="col-lg-2">
                  <label class="form-label">Margem (%)</label>
                  <input type="number" step="0.01" class="form-control" name="margem" id="margem">
                </div>
                <div class="col-lg-2">
                  <label class="form-label">Custo</label>
                  <input type="number" step="0.01" class="form-control bg-secondary" name="custo" id="custo" readonly>
                </div>
                <div class="col-lg-4">
                  <label class="form-label">Em Reais</label>
                  <input type="number" step="0.01" class="form-control bg-light" name="em_reais" id="em_reais" readonly>
                </div>
              </div>
            </div>
          </div>
        </div>


      </div>

      <div class="mt-3">
        <button type="submit" class="btn btn-primary">Salvar</button>
      </div>
    </form>
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
  const camposAdicionais = document.getElementById('campos-adicionais');
  const descricaoEtiqueta = document.getElementById('descricao_etiqueta');

  [fornecedor, grupo, subgrupo].forEach(select => {
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

  // Atualizar Descrição Etiqueta automaticamente
  function atualizarDescricaoEtiqueta() {
    const fornecedorText = fornecedor.options[fornecedor.selectedIndex]?.text;
    const grupoText = grupo.options[grupo.selectedIndex]?.text;
    const subgrupoText = subgrupo.options[subgrupo.selectedIndex]?.text;

    descricaoEtiqueta.value = `${grupoText || ''} - ${subgrupoText || ''}`;
  }
</script>
<?php

use App\Models\Produtos\Controller;

// ID do produto a ser visualizado
$id = $link[3];

// Instanciar o Controller
$controller = new Controller();

// Buscar os dados do produto
$produto = $controller->ver($id);

// Verificar se o produto foi encontrado
if (!$produto) {
    echo notify('danger', "Produto não encontrado.");
    exit;
}
?>

<div class="card">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h3 class="card-title">Visualizar Produto</h3>
    <a href="<?php echo "{$url}!/{$link[1]}/listar"; ?>" class="btn btn-warning text-primary">Voltar</a>
  </div>

  <div class="card-body">
    <div class="row g-3">
    <div class="col-lg-12">
        <label class="form-label fw-bold">Capa</label><br>
        <img
                                src="<?= isset($produto['capa']) && !empty($produto['capa']) ? htmlspecialchars($produto['capa']) : $url . '/assets/img_padrao.webp'; ?>"
                                alt="Capa do Produto"
                                width="100"
                                style="height: 100px; object-fit: cover; border: 1px solid #ddd; border-radius: 5px;">
      </div>
      <!-- Descrição Etiqueta -->
      <div class="col-lg-12">
        <label class="form-label fw-bold">Descrição Etiqueta</label><br>
        <?= htmlspecialchars($produto['descricao_etiqueta'] ?? '') ?>
      </div>

      <div class="col-12"><hr></div>

      <!-- Fornecedor -->
      <div class="col-lg-4">
        <label class="form-label fw-bold">Fornecedor</label><br>
        <!-- Se no método ver() você estiver selecionando f.nome_fantasia como 'fornecedor_nome', então use $produto['fornecedor_nome'] -->
        <?= htmlspecialchars($produto['fornecedor_nome'] ?? '') ?>
      </div>

      <!-- Grupo -->
      <div class="col-lg-4">
        <label class="form-label fw-bold">Grupo</label><br>
        <?= htmlspecialchars($produto['grupo_nome'] ?? '') ?>
      </div>

      <!-- Subgrupo -->
      <div class="col-lg-4">
        <label class="form-label fw-bold">Subgrupo</label><br>
        <?= htmlspecialchars($produto['subgrupo_nome'] ?? '') ?>
      </div>

      <div class="col-12"><hr></div>

      <!-- Descrição Adicional Etiqueta (Manual) -->
      <div class="col-lg-12">
        <label class="form-label fw-bold">Descrição Adicional (Opcional)</label><br>
        <?= htmlspecialchars($produto['descricao_etiqueta_manual'] ?? '') ?>
      </div>

      <!-- Modelo -->
      <div class="col-lg-4">
        <label class="form-label fw-bold">Modelo</label><br>
        <?= htmlspecialchars($produto['modelo'] ?? '') ?>
      </div>

      <!-- Aros -->
      <div class="col-lg-2">
        <label class="form-label fw-bold">Aros</label><br>
        <?= htmlspecialchars($produto['aros'] ?? '') ?>
      </div>

      <!-- Pontos -->
      <div class="col-lg-2">
        <label class="form-label fw-bold">Pontos</label><br>
        <?= htmlspecialchars($produto['pontos'] ?? '') ?>
      </div>

      <!-- Maciça/Oca -->
      <div class="col-lg-2">
        <label class="form-label fw-bold">Material (Maciça/Oca)</label><br>
        <?= htmlspecialchars($produto['macica_ou_oca'] ?? '') ?>
      </div>

      <!-- Pedra -->
      <div class="col-lg-2">
        <label class="form-label fw-bold">Pedra</label><br>
        <?= htmlspecialchars($produto['pedra'] ?? '') ?>
      </div>

      <!-- Peso (g) -->
      <div class="col-lg-2">
        <label class="form-label fw-bold">Peso (g)</label><br>
        <?= htmlspecialchars($produto['peso'] ?? '') ?>
      </div>

      <!-- Unidade -->
      <div class="col-lg-2">
        <label class="form-label fw-bold">Unidade</label><br>
        <?= htmlspecialchars($produto['unidade'] ?? '') ?>
      </div>

      <!-- Natural ou Sintético -->
      <div class="col-lg-2">
        <label class="form-label fw-bold">Natural ou Sintético</label><br>
        <?= htmlspecialchars($produto['nat_ou_sint'] ?? '') ?>
      </div>

      <!-- Milímetros (mm) -->
      <div class="col-lg-2">
        <label class="form-label fw-bold">Milímetros (mm)</label><br>
        <?= htmlspecialchars($produto['mm'] ?? '') ?>
      </div>

      <!-- Centímetros (cm) -->
      <div class="col-lg-2">
        <label class="form-label fw-bold">Centímetros (cm)</label><br>
        <?= htmlspecialchars($produto['cm'] ?? '') ?>
      </div>

      <!-- Quantidade -->
      <div class="col-lg-2">
        <label class="form-label fw-bold">Quantidade</label><br>
        <?= htmlspecialchars($produto['estoque_princ'] ?? '') ?>
      </div>

      <div class="col-12"><hr></div>

      <!-- Cotação -->
      <div class="col-lg-4">
        <label class="form-label fw-bold">Cotação</label><br>
        <!-- Se no método ver() você estiver selecionando c.nome como 'cotacao_nome', então use $produto['cotacao_nome'] -->
        <?= htmlspecialchars($produto['cotacao_nome'] ?? '') ?>
      </div>

      <!-- Preço QL -->
      <div class="col-lg-2">
        <label class="form-label fw-bold">Preço QL</label><br>
        <?= htmlspecialchars($produto['preco_ql'] ?? '') ?>
      </div>

      <!-- Peso Gr -->
      <div class="col-lg-2">
        <label class="form-label fw-bold">Peso Gr</label><br>
        <?= htmlspecialchars($produto['peso_gr'] ?? '') ?>
      </div>

      <!-- Margem -->
      <div class="col-lg-2">
        <label class="form-label fw-bold">Margem (%)</label><br>
        <?= htmlspecialchars($produto['margem'] ?? '') ?>
      </div>

      <!-- Custo -->
      <div class="col-lg-2">
        <label class="form-label fw-bold">Custo</label><br>
        <?php
          // Se quiser formatar em R$ e duas casas decimais
          $custoFormatado = isset($produto['custo']) 
            ? 'R$ ' . number_format($produto['custo'], 2, ',', '.') 
            : 'R$ 0,00';
          echo $custoFormatado;
        ?>
      </div>

      <!-- Em Reais -->
      <div class="col-lg-4">
        <label class="form-label fw-bold">Em Reais</label><br>
        <?php
          $emReaisFormatado = isset($produto['em_reais']) 
            ? 'R$ ' . number_format($produto['em_reais'], 2, ',', '.') 
            : 'R$ 0,00';
          echo $emReaisFormatado;
        ?>
      </div>

    </div><!-- /.row -->
  </div><!-- /.card-body -->
</div><!-- /.card -->

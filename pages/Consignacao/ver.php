<?php

// Habilitar exibição de erros para debug (descomente se necessário)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

use App\Models\Consignacao\Controller;

$controller = new Controller();
$id = $link[3] ?? null;

if (!$id) {
    echo notify('danger', 'ID da consignação não informado.');
    echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    exit;
}

$dados = $controller->ver($id);

// Verificar se os dados foram retornados corretamente
if (!$dados || !isset($dados['consignacao']) || !isset($dados['itens'])) {
    echo notify('danger', 'Erro ao carregar os dados da consignação.');
    echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/' . $link[1] . '/listar">';
    exit;
}

$consignacao = $dados['consignacao'];
$itens = $dados['itens'];

?>



<div class="card print">
    <div class="card-header bg-light text-white d-flex justify-content-between align-items-center" >
        <h3 class="card-title text-dark">
            Detalhes da Consignação
        </h3>
        <a href="<?php echo "{$url}!/{$link[1]}/listar"; ?>" class="btn btn-warning text-primary mb-5">Voltar</a>
    </div>

    <div class="card-body">
        <h4 class="card-title">Dados da Consignação</h4>
        <div class="row g-3">
            <div class="col-lg-2">
                <strong>Cliente:</strong> 
                <br>
                <?= htmlspecialchars(
                    !empty($consignacao['nome_pf']) 
                    ? $consignacao['nome_pf'] 
                    : ($consignacao['nome_fantasia_pj'] ?? 'Não informado')
                ) ?>
            </div>
            <div class="col-lg-2">
                <strong>Whatsapp:</strong> 
                <br>
                <?= $consignacao['whatsapp'] ?? '-' ?>
            </div>
            <div class="col-lg-2">
                <strong>Telefone:</strong> 
                <br>
                <?= $consignacao['telefone'] ?? '-' ?>
            </div>
            <div class="col-lg-2">
                <strong>Data da Consignação:</strong> 
                <br>
                <?= htmlspecialchars(date('d/m/Y', strtotime($consignacao['data_consignacao']))) ?>
            </div>
            <div class="col-lg-2">
                <strong>Bonificação:</strong> 
                <br>
                <span class="badge badge-info d-inline-block">
                    <?=floatval($consignacao['bonificacao'] ?? 0); ?> %
                </span>
            </div>
            <div class="col-lg-2">
                <strong>Status:</strong> 
                <br>
                <span class="badge badge-info d-inline-block">
                    <?= htmlspecialchars($consignacao['status']) ?>
                </span>
            </div>
            
            <?php 
            // Calcular subtotal dos itens (considerando devoluções)
            $subtotal = 0;
            if (is_array($itens) && count($itens) > 0) {
                foreach ($itens as $item) {
                    $quantidade = floatval($item['quantidade'] ?? 0);
                    $qtd_devolvido = floatval($item['qtd_devolvido'] ?? 0);
                    $valor = floatval($item['valor'] ?? 0);
                    
                    $quantidade_final = $quantidade - $qtd_devolvido;
                    $subtotal += $quantidade_final * $valor;
                }
            }
            
            // Obter desconto percentual
            $desconto_percentual = floatval($consignacao['desconto_percentual'] ?? 0);
            
            // Calcular valor do desconto
            $valor_desconto = ($subtotal * $desconto_percentual) / 100;
            
            // Calcular total com desconto
            $valor_total = $subtotal - $valor_desconto;
            ?>
            <!-- <div class="col-lg-3">
                <strong>Valor Total:</strong> 
                <p class="mb-0 text-success fw-bold">R$ <?= number_format($valor_total, 2, ',', '.'); ?></p>
            </div> -->
            <!-- <div class="col-12">
                <strong>Observações:</strong>
                <p><?= $consignacao['observacao'] ?? 'Não informado' ?></p>
            </div> -->
        </div>

        <hr>
        <h4 class="card-title">Itens da Consignação</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Produto</th>
                    <th class="text-end">Peça</th>
                    <th class="text-end">Devolvida</th>
                    <th class="text-end">Vendidos</th>
                    <th class="text-end">Valor Unit.</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (is_array($itens) && count($itens) > 0): ?>
                    <?php 
                    $total_itens_vendidos = 0;
                    $total_valor_para_pagar = 0;
                    foreach ($itens as $item): ?>
                        <?php
                        $quantidade = floatval($item['quantidade'] ?? 0);
                        $valor = floatval($item['valor'] ?? 0);
                        $qtd_devolvido = floatval($item['qtd_devolvido'] ?? 0);
                        $subtotal_item = ($quantidade - $qtd_devolvido) * $valor;
                        ?>
                        <tr>
                            <td><?= str_pad($item['id'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td><?= htmlspecialchars($item['nome_produto'] ?? 'Produto não encontrado') ?></td>
                            <td class="text-end"><span class="badge badge-info"><?= $quantidade ?></span></td>
                            <td class="text-end"><span class="badge badge-danger"><?= ($qtd_devolvido) ?></span></td>
                            <td class="text-end"><span class="badge badge-success"><?php echo $quantidade - $qtd_devolvido ?></span></td>
                            <td class="text-end">R$ <?= number_format($valor, 2, ',', '.') ?></td>
                            <td class="text-end"><span class="badge badge-success">R$ <?= number_format($subtotal_item, 2, ',', '.') ?></span></td>
                        </tr>
                    <?php 
                            $total_itens_vendidos += $quantidade - $qtd_devolvido;
                            $total_valor_para_pagar += $subtotal_item;
                        endforeach; 
                    ?>
                    <tr>
                        <td colspan="5" class="text-end">Itens Vendidos: <span class="badge badge-warning"><?php echo $total_itens_vendidos ?></span></td>
                        <td colspan="2" class="text-end">Valor total: <span class="badge badge-warning">R$<?php echo number_format($total_valor_para_pagar, 2, ',', '.') ?></span></td>
                    </tr>
                    <?php
                        if( $consignacao['bonificacao'] > 0 ){
                            $valor_bonificacao = ($total_valor_para_pagar * $consignacao['bonificacao']) / 100;
                            $total_valor_para_pagar -= $valor_bonificacao;
                    ?>
                    
                    <tr>
                        <td colspan="5" class="text-end">Bonificação: <span class="badge badge-info"><?php echo $consignacao['bonificacao'] ?> %</span></td>
                        <td colspan="2" class="text-end">Bonificação <?php if($consignacao['status'] =='Aberta'){ echo " à receber";}?>: <span class="badge badge-info">R$<?php echo number_format($valor_bonificacao, 2, ',', '.') ?></span></td>
                    </tr>
                    <?php 
                        } //if
                    ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Nenhum item encontrado</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


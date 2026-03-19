<?php

use App\Models\Pedidos\Controller;
use App\Models\Caixa\Controller as CaixaController;

$controller = new Controller();

$id = $link[3] ?? null; // ID do pedido vindo da URL
$novoStatus = $link[4] ?? null; // Novo status vindo da URL
$caixa_drawer_id = $link[5] ?? ($_GET['caixa_drawer_id'] ?? null);

if (!$id || !$novoStatus) {
    echo notify('danger', 'Informações insuficientes para mudar o status.');
    echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/Pedidos/listar">';
    exit;
}

// Carrega estado atual do pedido (antes de alterar)
$db = new db();
$db->query("
    SELECT 
        id,
        loja_id,
        data_pedido,
        forma_pagamento,
        total,
        valor_pago,
        status_pedido
    FROM pedidos
    WHERE id = :id
    LIMIT 1
");
$db->bind(':id', $id);
$pedido = $db->single();

if (!$pedido) {
    echo notify('danger', 'Pedido não encontrado.');
    echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/Pedidos/listar">';
    exit;
}

$caixaController = new CaixaController();
$loja_id = (int)($pedido['loja_id'] ?? 0);
$data_caixa = $pedido['data_pedido'];
$statusAtual = $pedido['status_pedido'];

$redirect = $url . '!/Pedidos/listar';
if (!empty($caixa_drawer_id)) {
    $redirect .= '?caixa_drawer_id=' . (int)$caixa_drawer_id;
}

// Integrações do Caixa: só quando ocorre transição Pago <-> Pendente
if ($novoStatus === 'Pago' && $statusAtual !== 'Pago') {
    $caixa_drawer_id_val = !empty($caixa_drawer_id) ? (int)$caixa_drawer_id : 0;
    if (!$caixa_drawer_id_val) {
        echo notify('danger', 'Selecione a gaveta/cash drawer para lançar no Caixa.');
        echo '<meta http-equiv="refresh" content="2; url=' . $redirect . '">';
        exit;
    }

    $sessao = $caixaController->obterSessaoAberta($loja_id, $caixa_drawer_id_val, $data_caixa);
    if (!$sessao) {
        echo notify('danger', 'Não existe sessão de Caixa aberta para esta gaveta e data.');
        echo '<meta http-equiv="refresh" content="2; url=' . $redirect . '">';
        exit;
    }

    $tipo = $caixaController->mapearTipoPedidoParaCaixa($pedido['forma_pagamento']);
    $valor = (float)($pedido['valor_pago'] ?? 0);
    if ($valor <= 0) {
        $valor = (float)($pedido['total'] ?? 0);
    }

    if ($valor <= 0) {
        echo notify('danger', 'Valor pago do pedido inválido para lançar no Caixa.');
        echo '<meta http-equiv="refresh" content="2; url=' . $redirect . '">';
        exit;
    }

    $movId = $caixaController->registrarMovimento(
        (int)$sessao['id'],
        $loja_id,
        $caixa_drawer_id_val,
        $tipo,
        $valor,
        'Pedido',
        (int)$pedido['id'],
        null
    );

    if (!$movId) {
        echo notify('danger', 'Não foi possível registrar o movimento no Caixa.');
        echo '<meta http-equiv="refresh" content="2; url=' . $redirect . '">';
        exit;
    }
}

if ($novoStatus === 'Pendente' && $statusAtual === 'Pago') {
    // Reverte em qualquer sessão (evita erro caso a gaveta selecionada mude)
    $okRevert = $caixaController->reverterMovimentosPorOrigemGlobal('Pedido', (int)$pedido['id']);
    if ($okRevert === false) {
        echo notify('danger', 'Não foi possível reverter o movimento do Caixa.');
        echo '<meta http-equiv="refresh" content="2; url=' . $redirect . '">';
        exit;
    }
}

// Atualiza o status do pedido
$db->query("
    UPDATE pedidos 
    SET status_pedido = :status 
    WHERE id = :id
");
$db->bind(':status', $novoStatus);
$db->bind(':id', $id);
$db->execute();

echo notify('success', 'Status do pedido atualizado com sucesso.');
echo '<meta http-equiv="refresh" content="2; url=' . $redirect . '">';
exit;

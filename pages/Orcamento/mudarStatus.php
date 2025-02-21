<?php

use App\Models\Orcamento\Controller;

$controller = new Controller();

$id = $link[3] ?? null; // ID do pedido vindo da URL
$novoStatus = $link[4] ?? null; // Novo status vindo da URL

if (!$id || !$novoStatus) {
    echo notify('danger', 'Informações insuficientes para mudar o status.');
    echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/Pedidos/listar">';
    exit;
}

// Atualiza o status do pedido
$db = new db();
$db->query("
    UPDATE pedidos 
    SET status_pedido = :status 
    WHERE id = :id
");
$db->bind(':status', $novoStatus);
$db->bind(':id', $id);
$db->execute();

// Notifica o usuário e redireciona de volta para a lista
echo notify('success', 'Status do pedido atualizado com sucesso.');
echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/Pedidos/listar">';
exit;

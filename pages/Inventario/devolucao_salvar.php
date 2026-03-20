<?php

$url = $url ?? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['SERVER_NAME'] ?? 'localhost') . '/';

if (!isset($_COOKIE['id']) || empty($_COOKIE['id'])) {
    header("Location: {$url}login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: {$url}!/Inventario/cadastro");
    exit;
}

use App\Models\Inventario\Controller;

try {
    $controller = new Controller();
    $dados = [
        'produto_id' => $_POST['produto_id'] ?? null,
        'quantidade' => $_POST['quantidade'] ?? null,
        'motivo' => $_POST['motivo'] ?? null,
        'loja_id' => $_POST['loja_id'] ?? null,
        'pedido_id' => $_POST['pedido_id'] ?? null,
        'tipo' => $_POST['tipo'] ?? 'Entrada'
    ];

    $result = $controller->registrarDevolucao($dados);

    if ($result['ok']) {
        echo notify('success', $result['msg']);
        echo '<meta http-equiv="refresh" content="2; url=' . $url . '!/Inventario/listar">';
    } else {
        echo notify('danger', $result['msg']);
        echo '<meta http-equiv="refresh" content="3; url=' . $url . '!/Inventario/cadastro">';
    }
} catch (Throwable $e) {
    error_log('Inventario devolucao_salvar: ' . $e->getMessage());
    echo notify('danger', 'Erro ao processar devolução. Verifique se a tabela inventario_devolucoes existe (execute a migration).');
    echo '<meta http-equiv="refresh" content="5; url=' . $url . '!/Inventario/cadastro">';
}

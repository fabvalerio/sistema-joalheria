<?php

require_once dirname(__DIR__, 2) . '/db/db.class.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if (!isset($_COOKIE['id']) || empty($_COOKIE['id'])) {
        http_response_code(401);
        echo json_encode(['ok' => false, 'msg' => 'Não autorizado']);
        exit;
    }

    if (($_COOKIE['nivel_acesso'] ?? '') !== 'Administrador') {
        http_response_code(403);
        echo json_encode(['ok' => false, 'msg' => 'Acesso negado']);
        exit;
    }

    $controller = new \App\Models\Estoque\Controller();

    $acao = $_POST['acao'] ?? '';
    $produto_id = (int)($_POST['produto_id'] ?? 0);
    $quantidade = $_POST['quantidade'] ?? '';
    $quantidade_minima_raw = $_POST['quantidade_minima'] ?? '';
    $descricao_produto = $_POST['descricao_produto'] ?? '';

    $quantidade_minima = null;
    if ($quantidade_minima_raw !== '' && $quantidade_minima_raw !== null) {
        $val = (float)str_replace(',', '.', (string)$quantidade_minima_raw);
        if ($val < 0 || $val > 99999) {
            $val = 0;
        }
        $quantidade_minima = $val;
    }

    if (!$produto_id) {
        echo json_encode(['ok' => false, 'msg' => 'Produto inválido.']);
        exit;
    }

    if ($acao === 'adicionar') {
        $quantidade = str_replace(',', '.', $quantidade);
        $qtd = (float)$quantidade;
        if ($qtd <= 0) {
            echo json_encode(['ok' => false, 'msg' => 'Informe uma quantidade válida.']);
            exit;
        }
        $result = $controller->adicionarEstoqueCD($produto_id, $qtd, $descricao_produto, $quantidade_minima);
    } elseif ($acao === 'editar') {
        $quantidade = str_replace(',', '.', $quantidade);
        $qtd = (float)$quantidade;
        if ($qtd <= 0) {
            echo json_encode(['ok' => false, 'msg' => 'Informe uma quantidade válida para adicionar.']);
            exit;
        }
        // Editar = adicionar quantidade ao estoque atual (soma)
        $result = $controller->adicionarEstoqueCD($produto_id, $qtd, $descricao_produto, $quantidade_minima);
    } else {
        echo json_encode(['ok' => false, 'msg' => 'Ação inválida.']);
        exit;
    }

    echo json_encode($result);
} catch (\Throwable $e) {
    echo json_encode(['ok' => false, 'msg' => 'Erro: ' . $e->getMessage()]);
}

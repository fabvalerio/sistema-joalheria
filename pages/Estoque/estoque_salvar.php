<?php

require_once dirname(__DIR__, 2) . '/db/db.class.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if (!isset($_COOKIE['id']) || empty($_COOKIE['id'])) {
        http_response_code(401);
        echo json_encode(['ok' => false, 'msg' => 'Não autorizado']);
        exit;
    }

    $isAdmin = isset($_COOKIE['nivel_acesso']) && $_COOKIE['nivel_acesso'] === 'Administrador';
    $usuario_loja_id = isset($_COOKIE['loja_id']) ? (int)$_COOKIE['loja_id'] : null;

    $controller = new \App\Models\Estoque\Controller();

    $acao = $_POST['acao'] ?? '';
    $produto_id = (int)($_POST['produto_id'] ?? 0);
    $loja_id = isset($_POST['loja_id']) ? (int)$_POST['loja_id'] : 0;
    $quantidade = $_POST['quantidade'] ?? '';
    $quantidade_minima_raw = $_POST['quantidade_minima'] ?? '';
    $descricao_produto = $_POST['descricao_produto'] ?? '';
    $entrada_mercadorias_id_raw = $_POST['entrada_mercadorias_id'] ?? '';

    $quantidade_minima = null;
    if ($quantidade_minima_raw !== '' && $quantidade_minima_raw !== null) {
        $val = (float)str_replace(',', '.', (string)$quantidade_minima_raw);
        if ($val < 0 || $val > 99999) {
            $val = 0;
        }
        $quantidade_minima = $val;
    }

    $entrada_mercadorias_id = null;
    if ($entrada_mercadorias_id_raw !== '' && $entrada_mercadorias_id_raw !== null) {
        $entrada_mercadorias_id = (int)$entrada_mercadorias_id_raw;
        if ($entrada_mercadorias_id <= 0) {
            $entrada_mercadorias_id = null;
        }
    }

    if (!$produto_id) {
        echo json_encode(['ok' => false, 'msg' => 'Produto inválido.']);
        exit;
    }

    // Estoque Principal (loja_id=0): apenas Admin. Loja: admin ou usuário da própria loja
    if ($loja_id === 0) {
        if (!$isAdmin) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'msg' => 'Acesso negado. Apenas administradores podem editar o Estoque Principal.']);
            exit;
        }
    } else {
        if (!$isAdmin && $usuario_loja_id !== $loja_id) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'msg' => 'Acesso negado. Você só pode editar o estoque da sua loja.']);
            exit;
        }
    }

    if ($acao === 'adicionar' || $acao === 'editar') {
        $quantidade = str_replace(',', '.', $quantidade);
        $qtd = (float)$quantidade;
        if ($qtd <= 0) {
            echo json_encode(['ok' => false, 'msg' => 'Informe uma quantidade válida para adicionar.']);
            exit;
        }

        if ($loja_id === 0) {
            $result = $controller->adicionarEstoqueCD($produto_id, $qtd, $descricao_produto, $quantidade_minima, $entrada_mercadorias_id);
        } else {
            $result = $controller->adicionarEstoqueLoja($loja_id, $produto_id, $qtd, $descricao_produto, $quantidade_minima);
        }
    } else {
        echo json_encode(['ok' => false, 'msg' => 'Ação inválida.']);
        exit;
    }

    echo json_encode($result);
} catch (\Throwable $e) {
    echo json_encode(['ok' => false, 'msg' => 'Erro: ' . $e->getMessage()]);
}

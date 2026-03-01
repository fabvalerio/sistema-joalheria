<?php

require_once dirname(__DIR__, 2) . '/db/db.class.php';

header('Content-Type: application/json');

$busca = trim($_GET['busca'] ?? '');
$loja_id = $_GET['loja_id'] ?? $_COOKIE['loja_id'] ?? null;
$loja_id = (isset($loja_id) && $loja_id !== '') ? $loja_id : null;
$limite = 50;

$db = new db();

$condicao_busca = "";
$bind_busca = [];
if (!empty($busca)) {
    $condicao_busca = " AND (p.descricao_etiqueta LIKE :busca OR p.id LIKE :busca2)";
    $bind_busca = [':busca' => '%' . $busca . '%', ':busca2' => '%' . $busca . '%'];
}

// Se loja_id informado: usar estoque da loja (estoque_loja) ou CD (estoque)
if ($loja_id) {
    $db->query("SELECT id, tipo FROM loja WHERE id = :loja_id");
    $db->bind(':loja_id', $loja_id);
    $loja = $db->single();
    $tipo = (is_array($loja) && isset($loja['tipo'])) ? $loja['tipo'] : '';

    if ($tipo === 'CD') {
        // CD usa tabela estoque (produtos_id)
        $db->query("
            SELECT 
                p.id, 
                p.descricao_etiqueta AS nome_produto, 
                p.em_reais AS preco, 
                e.quantidade AS estoque, 
                p.capa,
                c.valor AS cotacao_valor,
                p.peso_gr,
                p.margem,
                p.preco_ql
            FROM produtos p
            LEFT JOIN cotacoes c ON p.cotacao = c.id
            INNER JOIN estoque e ON p.id = e.produtos_id
            WHERE e.quantidade > 0 {$condicao_busca}
            ORDER BY p.descricao_etiqueta ASC
            LIMIT $limite
        ");
    } else {
        // Loja comum usa estoque_loja
        $db->query("
            SELECT 
                p.id, 
                p.descricao_etiqueta AS nome_produto, 
                p.em_reais AS preco, 
                COALESCE(el.quantidade, 0) AS estoque, 
                p.capa,
                c.valor AS cotacao_valor,
                p.peso_gr,
                p.margem,
                p.preco_ql
            FROM produtos p
            LEFT JOIN cotacoes c ON p.cotacao = c.id
            INNER JOIN estoque_loja el ON p.id = el.produto_id AND el.loja_id = :loja_id
            WHERE el.quantidade > 0 {$condicao_busca}
            ORDER BY p.descricao_etiqueta ASC
            LIMIT $limite
        ");
        $db->bind(':loja_id', $loja_id);
    }

    foreach ($bind_busca as $k => $v) {
        $db->bind($k, $v);
    }
} else {
    // Sem loja_id (ex.: admin): fallback para estoque global
    if (!empty($busca)) {
        $db->query("
            SELECT 
                p.id, 
                p.descricao_etiqueta AS nome_produto, 
                p.em_reais AS preco, 
                e.quantidade AS estoque, 
                p.capa,
                c.valor AS cotacao_valor,
                p.peso_gr,
                p.margem,
                p.preco_ql
            FROM produtos p
            LEFT JOIN cotacoes c ON p.cotacao = c.id
            LEFT JOIN estoque e ON p.id = e.produtos_id
            WHERE p.descricao_etiqueta LIKE :busca OR p.id LIKE :busca2
            ORDER BY p.descricao_etiqueta ASC
            LIMIT $limite
        ");
        $db->bind(':busca', '%' . $busca . '%');
        $db->bind(':busca2', '%' . $busca . '%');
    } else {
        $db->query("
            SELECT 
                p.id, 
                p.descricao_etiqueta AS nome_produto, 
                p.em_reais AS preco, 
                e.quantidade AS estoque, 
                p.capa,
                c.valor AS cotacao_valor,
                p.peso_gr,
                p.margem,
                p.preco_ql
            FROM produtos p
            LEFT JOIN cotacoes c ON p.cotacao = c.id
            LEFT JOIN estoque e ON p.id = e.produtos_id
            ORDER BY p.descricao_etiqueta ASC
            LIMIT $limite
        ");
    }
}

$produtos = $db->resultSet();

// Mesma fórmula de App/php/function.php -> cotacao()
function calcularPreco($preco_ql, $peso_gr, $cotacao_valor, $margem) {
    return ($preco_ql * $peso_gr * $cotacao_valor) * (1 + $margem / 100);
}

$resultado = [];
foreach ($produtos as $produto) {
    $preco = calcularPreco(
        $produto['preco_ql'] ?? 0,
        $produto['peso_gr'] ?? 0,
        $produto['cotacao_valor'] ?? 0,
        $produto['margem'] ?? 0
    );

    $resultado[] = [
        'id'           => $produto['id'],
        'nome_produto' => $produto['nome_produto'],
        'preco'        => $preco > 0 ? $preco : ($produto['preco'] ?? 0),
        'estoque'      => $produto['estoque'] ?? 0,
        'capa'         => $produto['capa'],
    ];
}

echo json_encode($resultado);

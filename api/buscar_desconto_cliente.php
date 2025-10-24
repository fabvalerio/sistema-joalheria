<?php
// Arquivo API para buscar o desconto do grupo do cliente via AJAX
// Este arquivo não passa pelo roteamento do index.php

// Limpar qualquer saída anterior
if (ob_get_level()) {
    ob_clean();
}

// Inicia a sessão apenas se ainda não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está autenticado
if (!isset($_COOKIE['id']) || empty($_COOKIE['id'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => 'Usuário não autenticado'
    ]);
    exit;
}

require_once __DIR__ . '/../db/db.class.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

try {
    if (isset($_GET['cliente_id']) && !empty($_GET['cliente_id'])) {
        $cliente_id = (int)$_GET['cliente_id'];
        
        $db = new db();
        
        // Buscar o grupo do cliente e o desconto (comissao_vendedores) do grupo
        $db->query("
            SELECT 
                gc.comissao_vendedores as desconto,
                gc.nome_grupo,
                c.grupo
            FROM 
                clientes c
            LEFT JOIN 
                grupo_clientes gc ON c.grupo = gc.id
            WHERE 
                c.id = :cliente_id
        ");
        $db->bind(':cliente_id', $cliente_id);
        $resultado = $db->single();
        
        if ($resultado) {
            // Se o cliente tem grupo e o grupo tem desconto
            $desconto = isset($resultado['desconto']) && $resultado['desconto'] !== null 
                ? floatval($resultado['desconto']) 
                : 0;
            
            echo json_encode([
                'success' => true,
                'desconto' => $desconto,
                'grupo_id' => $resultado['grupo'],
                'grupo_nome' => $resultado['nome_grupo'] ?? 'Sem grupo',
                'debug' => [
                    'cliente_id' => $cliente_id,
                    'resultado_completo' => $resultado
                ]
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => true,
                'desconto' => 0,
                'message' => 'Cliente não encontrado',
                'debug' => [
                    'cliente_id' => $cliente_id
                ]
            ]);
            exit;
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'ID do cliente não fornecido',
            'debug' => [
                'get_params' => $_GET
            ]
        ]);
        exit;
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao buscar desconto',
        'error' => $e->getMessage(),
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
    exit;
}


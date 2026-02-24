<?php

namespace App\Models\ImpressaoEtiquetas;

use db;

class Controller
{
    // Listar produtos para impressão de etiquetas com paginação
    public function listar($filtro = '', $paginaAtual = 1, $itensPorPagina = 20)
    {
        $db = new db();
        
        // Criar cláusula WHERE para filtro
        $where = "WHERE 1=1";
        if (!empty($filtro)) {
            $where .= " AND (p.descricao_etiqueta LIKE :filtro OR p.id LIKE :filtro)";
        }
        
        // Calcular o número total de registros
        $queryTotal = "SELECT COUNT(*) as total FROM produtos p {$where}";
        $db->query($queryTotal);
        if (!empty($filtro)) {
            $db->bind(":filtro", "%{$filtro}%");
        }
        $totalRegistros = $db->resultSet()[0]['total'];
        
        // Calcular o número total de páginas
        $totalPaginas = ceil($totalRegistros / $itensPorPagina);
        $paginaAtual = max(1, min($paginaAtual, max(1, $totalPaginas)));
        $offset = ($paginaAtual - 1) * $itensPorPagina;
        
        // Query paginada
        $query = "SELECT 
                    p.id,
                    p.descricao_etiqueta
                FROM produtos p
                {$where}
                ORDER BY p.id
                LIMIT {$itensPorPagina} OFFSET {$offset}";
        
        $db->query($query);
        if (!empty($filtro)) {
            $db->bind(":filtro", "%{$filtro}%");
        }
        $registros = $db->resultSet();
        
        return [
            'registros' => $registros,
            'paginaAtual' => $paginaAtual,
            'totalPaginas' => $totalPaginas,
            'totalRegistros' => $totalRegistros,
            'itensPorPagina' => $itensPorPagina
        ];
    }
    
    // Buscar produtos por IDs para impressão
    public function buscarPorIds($ids)
    {
        if (empty($ids)) {
            return [];
        }
        
        $db = new db();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        $query = "SELECT 
                    p.id,
                    p.descricao_etiqueta
                FROM produtos p
                WHERE p.id IN ({$placeholders})
                ORDER BY p.descricao_etiqueta";
        
        $db->query($query);
        
        // Bind dos parâmetros
        foreach ($ids as $index => $id) {
            $db->bind($index + 1, $id);
        }
        
        return $db->resultSet();
    }
    
    // Gerar código de barras EAN-13
    public function gerarEAN13($id)
    {
        // Formatar o ID para 12 dígitos (EAN-13 sem o dígito verificador)
        $codigo = str_pad($id, 12, '0', STR_PAD_LEFT);
        
        // Calcular dígito verificador
        $soma = 0;
        for ($i = 0; $i < 12; $i++) {
            $multiplicador = ($i % 2 == 0) ? 1 : 3;
            $soma += (int)$codigo[$i] * $multiplicador;
        }
        $digitoVerificador = (10 - ($soma % 10)) % 10;
        
        return $codigo . $digitoVerificador;
    }
}


<?php

namespace App\Models\Estoque;

use db;

class Controller
{
    /**
     * Lista lojas disponíveis para o usuário.
     * Admin vê todas; usuário de loja vê apenas a sua.
     */
    public function listarLojas($usuario_loja_id = null, $somente_ativas = true)
    {
        $db = new db();
        $where = $somente_ativas ? " WHERE status = 1" : "";
        if (!empty($usuario_loja_id)) {
            $where .= $where ? " AND id = " . (int)$usuario_loja_id : " WHERE id = " . (int)$usuario_loja_id;
        }
        $db->query("SELECT id, nome, tipo FROM loja {$where} ORDER BY tipo ASC, nome ASC");
        return $db->resultSet();
    }

    /**
     * Retorna estoque da tabela principal 'estoque' (estoque global/CD).
     * Produtos com quantidade > 0.
     */
    public function estoquePrincipal()
    {
        $db = new db();
        $db->query("
            SELECT 
                0 AS loja_id,
                'Estoque Principal' AS loja_nome,
                'CD' AS loja_tipo,
                e.produtos_id AS produto_id,
                p.descricao_etiqueta AS nome_produto,
                p.codigo_fabricante,
                e.quantidade,
                e.quantidade_minima
            FROM estoque e
            LEFT JOIN produtos p ON e.produtos_id = p.id
            WHERE e.quantidade > 0
            ORDER BY p.descricao_etiqueta ASC
        ");
        return $db->resultSet();
    }

    /**
     * Retorna estoque agrupado por loja (estoque_loja).
     * Se $loja_id informado, filtra por essa loja.
     */
    public function estoquePorLoja($loja_id = null)
    {
        $db = new db();
        $where = "";
        if (!empty($loja_id)) {
            $where = " AND el.loja_id = " . (int)$loja_id;
        }

        $db->query("
            SELECT 
                el.loja_id,
                l.nome AS loja_nome,
                l.tipo AS loja_tipo,
                el.produto_id,
                p.descricao_etiqueta AS nome_produto,
                p.codigo_fabricante,
                el.quantidade,
                el.quantidade_minima
            FROM estoque_loja el
            INNER JOIN loja l ON el.loja_id = l.id
            LEFT JOIN produtos p ON el.produto_id = p.id
            WHERE el.quantidade > 0 {$where}
            ORDER BY l.tipo ASC, l.nome ASC, p.descricao_etiqueta ASC
        ");
        return $db->resultSet();
    }

    /**
     * Retorna estoque unificado: tabela 'estoque' + estoque_loja.
     * - loja_id=null ou '': Todas (Estoque Principal + estoque_loja)
     * - loja_id='0': Apenas Estoque Principal (tabela estoque)
     * - loja_id=id: Apenas essa loja (estoque_loja)
     */
    public function estoqueUnificado($loja_id = null)
    {
        $resultado = [];

        // Apenas Estoque Principal
        if ($loja_id === '0' || $loja_id === 0) {
            return $this->estoquePrincipal();
        }

        // Todas: Estoque Principal + estoque_loja
        if (empty($loja_id)) {
            $resultado = array_merge($resultado, $this->estoquePrincipal());
        }

        // estoque_loja (todas as lojas ou loja específica)
        $porLoja = $this->estoquePorLoja(empty($loja_id) ? null : $loja_id);
        $resultado = array_merge($resultado, $porLoja);

        return $resultado;
    }
}

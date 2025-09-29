<?php

namespace App\Models\Caixa;

use db;

class Controller
{
    // Listar movimentação de caixa por período
    public function listarMovimentacao($data_inicio = null, $data_fim = null, $loja_id = 1)
    {
        $db = new db();
        
        // Se não foram fornecidas datas, usar o dia atual
        if (!$data_inicio) {
            $data_inicio = date('Y-m-d');
        }
        if (!$data_fim) {
            $data_fim = date('Y-m-d');
        }

      $db->query("
            SELECT 
                p.id as pedido_id,
                p.data_pedido,
                '00:00' as hora_pedido,
                p.forma_pagamento,
                p.total,
                p.valor_pago,
                p.acrescimo,
                p.desconto,
                p.status_pedido,
                p.troco_abertura,
                p.troco_fechamento,
                p.data_caixa,
                p.observacoes_caixa,
                c.nome_pf,
                c.nome_fantasia_pj,
                c.cpf,
                c.cnpj_pj,
                u.nome_completo as vendedor_nome,
                -- Campos para diferentes formas de pagamento
                CASE 
                    WHEN p.forma_pagamento = 'Dinheiro' THEN p.valor_pago
                    ELSE 0
                END as dinheiro,
                CASE 
                    WHEN p.forma_pagamento = 'Cheque' THEN p.valor_pago
                    ELSE 0
                END as cheque,
                CASE 
                    WHEN p.forma_pagamento LIKE '%Cartão%' OR p.forma_pagamento LIKE '%Parcelado%' THEN p.valor_pago
                    ELSE 0
                END as parc_cartao,
                -- Campos adicionais para controle
                CASE 
                    WHEN p.forma_pagamento LIKE '%Parcelado%' THEN 
                        SUBSTRING_INDEX(SUBSTRING_INDEX(p.forma_pagamento, 'x', 1), ' ', -1)
                    ELSE 1
                END as parcela,
                CASE 
                    WHEN p.forma_pagamento LIKE '%Carnê%' THEN p.valor_pago
                    ELSE 0
                END as carnet,
                -- Valor vendido em ouro (assumindo que produtos de ouro têm categoria específica)
                CASE 
                    WHEN EXISTS (
                        SELECT 1 FROM pedidos_itens pi 
                        JOIN produtos pr ON pi.produto_id = pr.id 
                        WHERE pi.pedido_id = p.id 
                        AND (pr.descricao_etiqueta LIKE '%ouro%' OR pr.descricao_etiqueta LIKE '%gold%')
                    ) THEN p.valor_pago
                    ELSE 0
                END as val_vend_ouro,
                -- Códigos de controle (usando IDs como referência)
                p.id as dp_banc,
                p.cod_vendedor as vend,
                p.cod_vendedor as lib,
                p.cod_vendedor as rec
            FROM 
                pedidos p
            LEFT JOIN 
                clientes c ON p.cliente_id = c.id
            LEFT JOIN 
                usuarios u ON p.cod_vendedor = u.id
            WHERE 
                p.data_pedido BETWEEN :data_inicio AND :data_fim
                AND p.orcamento IS NULL
                AND p.status_pedido IN ('Pago', 'Entregue', 'Finalizado', 'Pendente')
            ORDER BY 
                p.data_pedido DESC, p.id DESC
        ");

        $db->bind(':data_inicio', $data_inicio);
        $db->bind(':data_fim', $data_fim);
        
        return $db->resultSet();
    }

    // Obter totais do fluxo de caixa
    public function obterTotais($data_inicio = null, $data_fim = null, $loja_id = 1)
    {
        $db = new db();
        
        if (!$data_inicio) {
            $data_inicio = date('Y-m-d');
        }
        if (!$data_fim) {
            $data_fim = date('Y-m-d');
        }

        $db->query("
            SELECT 
                COUNT(*) as total_pedidos,
                SUM(p.total) as total_pedidos_valor,
                SUM(CASE WHEN p.forma_pagamento = 'Dinheiro' THEN p.valor_pago ELSE 0 END) as total_dinheiro,
                SUM(CASE WHEN p.forma_pagamento = 'Cheque' THEN p.valor_pago ELSE 0 END) as total_cheque,
                SUM(CASE WHEN p.forma_pagamento LIKE '%Cartão%' OR p.forma_pagamento LIKE '%Parcelado%' THEN p.valor_pago ELSE 0 END) as total_parc_cartao,
                SUM(CASE WHEN p.forma_pagamento LIKE '%Carnê%' THEN p.valor_pago ELSE 0 END) as total_carnes,
                SUM(p.valor_pago) as total_liquido,
                -- Valor vendido em ouro
                SUM(CASE 
                    WHEN EXISTS (
                        SELECT 1 FROM pedidos_itens pi 
                        JOIN produtos pr ON pi.produto_id = pr.id 
                        WHERE pi.pedido_id = p.id 
                        AND (pr.descricao_etiqueta LIKE '%ouro%' OR pr.descricao_etiqueta LIKE '%gold%')
                    ) THEN p.valor_pago
                    ELSE 0
                END) as total_val_vend_ouro
            FROM 
                pedidos p
            WHERE 
                p.data_pedido BETWEEN :data_inicio AND :data_fim
                AND p.orcamento IS NULL
                AND p.status_pedido IN ('Pago', 'Entregue', 'Finalizado', 'Pendente')
        ");

        $db->bind(':data_inicio', $data_inicio);
        $db->bind(':data_fim', $data_fim);
        
        return $db->single();
    }

    // Obter configurações de troco do dia
    public function obterConfiguracaoTroco($data = null)
    {
        $db = new db();
        
        if (!$data) {
            $data = date('Y-m-d');
        }

        // Buscar configuração de troco para a data específica na tabela pedidos
        $db->query("
            SELECT 
                MAX(troco_abertura) as troco_abertura,
                MAX(troco_fechamento) as troco_fechamento
            FROM 
                pedidos
            WHERE 
                data_pedido = :data
                AND orcamento IS NULL
            LIMIT 1
        ");

        $db->bind(':data', $data);
        $resultado = $db->single();
        
        // Se não encontrar configuração, retornar valores padrão
        if (!$resultado) {
            return [
                'troco_abertura' => 0.00,
                'troco_fechamento' => 0.00
            ];
        }
        
        return $resultado;
    }

    // Salvar configuração de troco (atualizar todos os pedidos do dia)
    public function salvarConfiguracaoTroco($data, $troco_abertura, $troco_fechamento)
    {
        $db = new db();
        
        // Atualizar todos os pedidos do dia com as configurações de troco
        $db->query("
            UPDATE pedidos 
            SET 
                troco_abertura = :troco_abertura, 
                troco_fechamento = :troco_fechamento,
                data_caixa = :data
            WHERE 
                data_pedido = :data
                AND orcamento IS NULL
        ");
        
        $db->bind(':data', $data);
        $db->bind(':troco_abertura', $troco_abertura);
        $db->bind(':troco_fechamento', $troco_fechamento);
        
        return $db->execute();
    }

    // Obter recebimento de parcelas
    public function obterRecebimentoParcelas($data_inicio = null, $data_fim = null)
    {
        $db = new db();
        
        if (!$data_inicio) {
            $data_inicio = date('Y-m-d');
        }
        if (!$data_fim) {
            $data_fim = date('Y-m-d');
        }

        $db->query("
            SELECT 
                SUM(valor) as total_receb_parcela
            FROM 
                financeiro_contas
            WHERE 
                data_pagamento BETWEEN :data_inicio AND :data_fim
                AND status = 'Pago'
                AND tipo = 'Receita'
        ");

        $db->bind(':data_inicio', $data_inicio);
        $db->bind(':data_fim', $data_fim);
        
        $resultado = $db->single();
        return $resultado ? $resultado['total_receb_parcela'] : 0;
    }

    // Listar clientes para o relatório
    public function listarClientes()
    {
        $db = new db();
        $db->query("
            SELECT 
                id,
                nome_pf,
                nome_fantasia_pj,
                cpf,
                cnpj_pj
            FROM 
                clientes 
            ORDER BY 
                nome_pf ASC
        ");
        return $db->resultSet();
    }

    // Listar vendedores
    public function listarVendedores()
    {
        $db = new db();
        $db->query("
            SELECT 
                id,
                nome_completo as nome
            FROM 
                usuarios 
            WHERE 
                status = 1
            ORDER BY 
                nome_completo ASC
        ");
        return $db->resultSet();
    }
}

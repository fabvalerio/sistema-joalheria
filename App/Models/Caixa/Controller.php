<?php

namespace App\Models\Caixa;

use db;

class Controller
{
    private function agoraYmdHm()
    {
        // Mantém padrão do app (America/Sao_Paulo definido no db.class.php)
        return date('Y-m-d H:i:s');
    }

    // Listar gavetas/cash drawers de uma loja
    public function listarCaixasPorLoja($loja_id)
    {
        $db = new db();
        $db->query("
            SELECT 
                id,
                numero,
                status
            FROM caixa_drawers
            WHERE loja_id = :loja_id
            ORDER BY numero ASC
        ");
        $db->bind(':loja_id', $loja_id);
        return $db->resultSet();
    }

    // Garantir criação de N gavetas para a loja (1..N)
    public function garantirCaixasPorLoja($loja_id, $quantidade)
    {
        $loja_id = (int)$loja_id;
        $quantidade = (int)$quantidade;
        if ($loja_id <= 0 || $quantidade <= 0) {
            return false;
        }

        $db = new db();

        for ($numero = 1; $numero <= $quantidade; $numero++) {
            $db->query("
                INSERT INTO caixa_drawers (loja_id, numero, status)
                VALUES (:loja_id, :numero, 'Ativo')
                ON DUPLICATE KEY UPDATE status = 'Ativo'
            ");
            $db->bind(':loja_id', $loja_id);
            $db->bind(':numero', $numero);
            $db->execute();
        }

        return true;
    }

    // Obter sessão aberta (se existir) para uma gaveta e data
    public function obterSessaoAberta($loja_id, $caixa_drawer_id, $data_caixa)
    {
        $db = new db();
        $db->query("
            SELECT *
            FROM caixa_sessoes
            WHERE loja_id = :loja_id
              AND caixa_drawer_id = :caixa_drawer_id
              AND data_caixa = :data_caixa
              AND status = 'Aberta'
            LIMIT 1
        ");
        $db->bind(':loja_id', $loja_id);
        $db->bind(':caixa_drawer_id', $caixa_drawer_id);
        $db->bind(':data_caixa', $data_caixa);
        return $db->single();
    }

    // Obter sessão existente (qualquer status) para uma gaveta e data
    public function obterSessaoExistente($loja_id, $caixa_drawer_id, $data_caixa)
    {
        $db = new db();
        $db->query("
            SELECT *
            FROM caixa_sessoes
            WHERE loja_id = :loja_id
              AND caixa_drawer_id = :caixa_drawer_id
              AND data_caixa = :data_caixa
            LIMIT 1
        ");
        $db->bind(':loja_id', $loja_id);
        $db->bind(':caixa_drawer_id', $caixa_drawer_id);
        $db->bind(':data_caixa', $data_caixa);
        return $db->single();
    }

    // Listar sessões abertas para o dashboard (quantidade, valor, alerta se passou o dia)
    public function listarSessoesAbertasParaDashboard($loja_id = null)
    {
        $db = new db();
        $sql = "
            SELECT 
                cs.id,
                cs.loja_id,
                cs.caixa_drawer_id,
                cs.data_caixa,
                cs.troco_abertura,
                cd.numero,
                COALESCE(
                    (SELECT SUM(cm.valor) FROM caixa_movimentos cm 
                     WHERE cm.caixa_sessao_id = cs.id AND cm.status = 'Ativo'),
                    0
                ) AS soma_movimentos,
                CASE WHEN cs.data_caixa < CURDATE() THEN 1 ELSE 0 END AS passou_dia
            FROM caixa_sessoes cs
            INNER JOIN caixa_drawers cd ON cd.id = cs.caixa_drawer_id
            WHERE cs.status = 'Aberta'
        ";
        $params = [];
        if ($loja_id !== null && $loja_id !== '') {
            $sql .= " AND cs.loja_id = :loja_id";
            $params[':loja_id'] = (int)$loja_id;
        }
        $sql .= " ORDER BY cs.loja_id, cd.numero ASC";
        $db->query($sql);
        foreach ($params as $k => $v) {
            $db->bind($k, $v);
        }
        $rows = $db->resultSet();
        $hoje = date('Y-m-d');
        foreach ($rows as &$r) {
            $r['saldo_esperado'] = (float)($r['troco_abertura'] ?? 0) + (float)($r['soma_movimentos'] ?? 0);
            $r['passou_dia'] = ($r['data_caixa'] ?? '') < $hoje;
        }
        return $rows;
    }

    // Listar todas as sessões abertas para uma loja e data (para escolher gaveta ao lançar pedido)
    public function listarSessoesAbertasPorLojaData($loja_id, $data_caixa)
    {
        $db = new db();
        $db->query("
            SELECT cs.id, cs.caixa_drawer_id, cd.numero
            FROM caixa_sessoes cs
            INNER JOIN caixa_drawers cd ON cd.id = cs.caixa_drawer_id
            WHERE cs.loja_id = :loja_id
              AND cs.data_caixa = :data_caixa
              AND cs.status = 'Aberta'
            ORDER BY cd.numero ASC
        ");
        $db->bind(':loja_id', $loja_id);
        $db->bind(':data_caixa', $data_caixa);
        return $db->resultSet();
    }

    // Obter dados do pedido para lançar no caixa (loja_id, data_pedido, forma_pagamento, valor_pago, total)
    public function obterPedidoParaCaixa($pedido_id)
    {
        $db = new db();
        $db->query("
            SELECT id, loja_id, data_pedido, forma_pagamento, valor_pago, total, status_pedido
            FROM pedidos
            WHERE id = :id
            LIMIT 1
        ");
        $db->bind(':id', $pedido_id);
        return $db->single();
    }

    // Abrir sessão de caixa (troco de abertura)
    public function abrirSessao($loja_id, $caixa_drawer_id, $data_caixa, $troco_abertura, $operador_id = null, $observacoes = null)
    {
        $db = new db();

        $sessaoAberta = $this->obterSessaoAberta($loja_id, $caixa_drawer_id, $data_caixa);
        if ($sessaoAberta) {
            return (int)$sessaoAberta['id'];
        }

        // Verifica se já existe sessão fechada (evita erro de chave duplicada)
        $sessaoExistente = $this->obterSessaoExistente($loja_id, $caixa_drawer_id, $data_caixa);
        if ($sessaoExistente && ($sessaoExistente['status'] ?? '') === 'Fechada') {
            // Reabre a sessão fechada
            $db->query("
                UPDATE caixa_sessoes
                SET status = 'Aberta',
                    troco_abertura = :troco_abertura,
                    operador_id = :operador_id,
                    observacoes = :observacoes,
                    data_hora_fechamento = NULL
                WHERE id = :id
            ");
            $db->bind(':troco_abertura', $troco_abertura);
            $db->bind(':operador_id', $operador_id);
            $db->bind(':observacoes', $observacoes);
            $db->bind(':id', (int)$sessaoExistente['id']);
            $db->execute();
            return (int)$sessaoExistente['id'];
        }

        $db->beginTransaction();
        try {
            $db->query("
                INSERT INTO caixa_sessoes (
                    loja_id,
                    caixa_drawer_id,
                    data_caixa,
                    troco_abertura,
                    status,
                    operador_id,
                    observacoes
                ) VALUES (
                    :loja_id,
                    :caixa_drawer_id,
                    :data_caixa,
                    :troco_abertura,
                    'Aberta',
                    :operador_id,
                    :observacoes
                )
            ");

            $db->bind(':loja_id', $loja_id);
            $db->bind(':caixa_drawer_id', $caixa_drawer_id);
            $db->bind(':data_caixa', $data_caixa);
            $db->bind(':troco_abertura', $troco_abertura);
            $db->bind(':operador_id', $operador_id);
            $db->bind(':observacoes', $observacoes);

            $db->execute();
            $id = (int)$db->lastInsertId();
            $db->endTransaction();

            return $id;
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->cancelTransaction();
            }
            throw $e;
        }
    }

    // Registrar movimento de caixa (vendas, contas, sangria/reforço)
    public function registrarMovimento($caixa_sessao_id, $loja_id, $caixa_drawer_id, $tipo, $valor, $origem_tipo, $origem_id = null, $observacoes = null, $data_hora = null)
    {
        $db = new db();
        $data_hora = $data_hora ?: $this->agoraYmdHm();

        // Bloqueia se a sessão não estiver aberta
        $db->query("
            SELECT id
            FROM caixa_sessoes
            WHERE id = :caixa_sessao_id
              AND status = 'Aberta'
            LIMIT 1
        ");
        $db->bind(':caixa_sessao_id', $caixa_sessao_id);
        $sessao = $db->single();
        if (!$sessao) {
            return false;
        }

        // Idempotência: se o movimento já existe (mesma origem e mesma sessão), apenas reativa/atualiza.
        // Isso evita violação da UNIQUE quando o usuário alterna Pago <-> Pendente.
        if ($origem_id !== null) {
            $db->query("
                SELECT id, status
                FROM caixa_movimentos
                WHERE origem_tipo = :origem_tipo
                  AND origem_id = :origem_id
                  AND caixa_sessao_id = :caixa_sessao_id
                LIMIT 1
            ");
            $db->bind(':origem_tipo', $origem_tipo);
            $db->bind(':origem_id', $origem_id);
            $db->bind(':caixa_sessao_id', $caixa_sessao_id);
            $existente = $db->single();

            if ($existente) {
                $db->query("
                    UPDATE caixa_movimentos
                    SET
                        loja_id = :loja_id,
                        caixa_drawer_id = :caixa_drawer_id,
                        data_hora = :data_hora,
                        tipo = :tipo,
                        valor = :valor,
                        status = 'Ativo',
                        observacoes = :observacoes,
                        data_hora_reversao = NULL
                    WHERE id = :id
                ");
                $db->bind(':loja_id', $loja_id);
                $db->bind(':caixa_drawer_id', $caixa_drawer_id);
                $db->bind(':data_hora', $data_hora);
                $db->bind(':tipo', $tipo);
                $db->bind(':valor', $valor);
                $db->bind(':observacoes', $observacoes);
                $db->bind(':id', (int)$existente['id']);
                $db->execute();

                return (int)$existente['id'];
            }
        }

        $db->query("
            INSERT INTO caixa_movimentos (
                caixa_sessao_id,
                loja_id,
                caixa_drawer_id,
                data_hora,
                tipo,
                valor,
                status,
                origem_tipo,
                origem_id,
                observacoes,
                data_hora_reversao
            ) VALUES (
                :caixa_sessao_id,
                :loja_id,
                :caixa_drawer_id,
                :data_hora,
                :tipo,
                :valor,
                'Ativo',
                :origem_tipo,
                :origem_id,
                :observacoes,
                NULL
            )
        ");
        $db->bind(':caixa_sessao_id', $caixa_sessao_id);
        $db->bind(':loja_id', $loja_id);
        $db->bind(':caixa_drawer_id', $caixa_drawer_id);
        $db->bind(':data_hora', $data_hora);
        $db->bind(':tipo', $tipo);
        $db->bind(':valor', $valor);
        $db->bind(':origem_tipo', $origem_tipo);
        $db->bind(':origem_id', $origem_id);
        $db->bind(':observacoes', $observacoes);
        $db->execute();

        return (int)$db->lastInsertId();
    }

    // Reverter movimento(s) de uma origem (ex: Pedido->Pendente)
    public function reverterMovimentosPorOrigem($origem_tipo, $origem_id, $caixa_sessao_id)
    {
        $db = new db();
        $db->query("
            UPDATE caixa_movimentos
            SET 
                status = 'Revertido',
                data_hora_reversao = :agora
            WHERE origem_tipo = :origem_tipo
              AND origem_id = :origem_id
              AND caixa_sessao_id = :caixa_sessao_id
              AND status = 'Ativo'
        ");
        $db->bind(':origem_tipo', $origem_tipo);
        $db->bind(':origem_id', $origem_id);
        $db->bind(':caixa_sessao_id', $caixa_sessao_id);
        $db->bind(':agora', $this->agoraYmdHm());
        return $db->execute();
    }

    // Reverter movimento(s) de uma origem em qualquer sessão (útil para reversões quando a gaveta selecionada mudou)
    public function reverterMovimentosPorOrigemGlobal($origem_tipo, $origem_id)
    {
        $db = new db();
        $db->query("
            UPDATE caixa_movimentos
            SET 
                status = 'Revertido',
                data_hora_reversao = :agora
            WHERE origem_tipo = :origem_tipo
              AND origem_id = :origem_id
              AND status = 'Ativo'
        ");
        $db->bind(':origem_tipo', $origem_tipo);
        $db->bind(':origem_id', $origem_id);
        $db->bind(':agora', $this->agoraYmdHm());
        return $db->execute();
    }

    // Fechar sessão e calcular saldo esperado
    public function fecharSessao($loja_id, $caixa_drawer_id, $data_caixa, $saldo_fisico_informado, $operador_id = null, $observacoes = null)
    {
        $db = new db();

        $db->query("
            SELECT *
            FROM caixa_sessoes
            WHERE loja_id = :loja_id
              AND caixa_drawer_id = :caixa_drawer_id
              AND data_caixa = :data_caixa
              AND status = 'Aberta'
            LIMIT 1
        ");
        $db->bind(':loja_id', $loja_id);
        $db->bind(':caixa_drawer_id', $caixa_drawer_id);
        $db->bind(':data_caixa', $data_caixa);
        $sessao = $db->single();
        if (!$sessao) {
            return false;
        }

        $caixa_sessao_id = (int)$sessao['id'];
        $troco_abertura = (float)($sessao['troco_abertura'] ?? 0);

        $db->query("
            SELECT COALESCE(SUM(valor), 0) AS soma_movimentos
            FROM caixa_movimentos
            WHERE caixa_sessao_id = :caixa_sessao_id
              AND status = 'Ativo'
        ");
        $db->bind(':caixa_sessao_id', $caixa_sessao_id);
        $row = $db->single();
        $soma_movimentos = (float)($row['soma_movimentos'] ?? 0);

        $saldo_esperado = $troco_abertura + $soma_movimentos;
        $saldo_fisico_informado = (float)$saldo_fisico_informado;
        $diferenca = $saldo_fisico_informado - $saldo_esperado;

        $db->beginTransaction();
        try {
            $db->query("
                UPDATE caixa_sessoes
                SET
                    saldo_fisico_informado = :saldo_fisico_informado,
                    saldo_esperado = :saldo_esperado,
                    diferenca = :diferenca,
                    status = 'Fechada',
                    operador_id = :operador_id,
                    observacoes = :observacoes,
                    data_hora_fechamento = :agora
                WHERE id = :id
            ");
            $db->bind(':saldo_fisico_informado', $saldo_fisico_informado);
            $db->bind(':saldo_esperado', $saldo_esperado);
            $db->bind(':diferenca', $diferenca);
            $db->bind(':operador_id', $operador_id);
            $db->bind(':observacoes', $observacoes);
            $db->bind(':agora', $this->agoraYmdHm());
            $db->bind(':id', $caixa_sessao_id);
            $db->execute();
            $db->endTransaction();

            return [
                'caixa_sessao_id' => $caixa_sessao_id,
                'saldo_esperado' => $saldo_esperado,
                'diferenca' => $diferenca,
            ];
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->cancelTransaction();
            }
            throw $e;
        }
    }

    // Listar movimentações de caixa por período (caixa_movimentos + sessão)
    public function listarMovimentacaoCaixa($data_inicio, $data_fim, $loja_id = null, $caixa_drawer_id = null)
    {
        $db = new db();
        $query = "
            SELECT
                cm.id,
                cm.data_hora,
                cm.tipo,
                cm.valor,
                cm.origem_tipo,
                cm.origem_id,
                cm.observacoes,
                cs.data_caixa,
                cs.status AS status_sessao,
                cd.numero AS caixa_numero
            FROM caixa_movimentos cm
            INNER JOIN caixa_sessoes cs ON cs.id = cm.caixa_sessao_id
            INNER JOIN caixa_drawers cd ON cd.id = cm.caixa_drawer_id
            WHERE cs.data_caixa BETWEEN :data_inicio AND :data_fim
        ";

        if ($loja_id !== null) {
            $query .= " AND cm.loja_id = :loja_id ";
        }
        if ($caixa_drawer_id !== null) {
            $query .= " AND cm.caixa_drawer_id = :caixa_drawer_id ";
        }

        $query .= " ORDER BY cm.data_hora DESC, cm.id DESC ";

        $db->query($query);
        $db->bind(':data_inicio', $data_inicio);
        $db->bind(':data_fim', $data_fim);
        if ($loja_id !== null) {
            $db->bind(':loja_id', $loja_id);
        }
        if ($caixa_drawer_id !== null) {
            $db->bind(':caixa_drawer_id', $caixa_drawer_id);
        }
        return $db->resultSet();
    }

    // Mapear forma de pagamento do Pedido para um tipo de movimento de Caixa
    public function mapearTipoPedidoParaCaixa($forma_pagamento)
    {
        $fp = strtolower((string)$forma_pagamento);

        if (strpos($fp, 'dinheiro') !== false) {
            return 'VendaDinheiro';
        }
        if (strpos($fp, 'cheque') !== false) {
            return 'VendaCheque';
        }
        if (strpos($fp, 'pix') !== false) {
            return 'VendaPix';
        }
        if (strpos($fp, 'material') !== false) {
            return 'VendaMaterial';
        }

        // Cartão (crédito/débito) e parcelados ficam como VendaCartao
        if (strpos($fp, 'cart') !== false || strpos($fp, 'parcel') !== false) {
            return 'VendaCartao';
        }

        // Fallback: trata como cartão (melhor do que perder o valor no fechamento)
        return 'VendaCartao';
    }

    // Mapear tipo de conta (R/P) para tipo de movimento de Caixa e sinal
    public function mapearTipoContaParaCaixa($tipo_conta)
    {
        $tipo_conta = (string)$tipo_conta;
        if ($tipo_conta === 'R') {
            return [
                'tipo' => 'RecebimentoConta',
                'fator' => 1,
            ];
        }

        // Contas a pagar: esperado diminui
        return [
            'tipo' => 'PagamentoConta',
            'fator' => -1,
        ];
    }

    // Totais por gaveta/data (inclui troco de abertura e saldo esperado)
    public function obterTotaisPorGavetaData($loja_id, $caixa_drawer_id, $data_caixa)
    {
        $db = new db();

        $db->query("
            SELECT *
            FROM caixa_sessoes
            WHERE loja_id = :loja_id
              AND caixa_drawer_id = :caixa_drawer_id
              AND data_caixa = :data_caixa
            ORDER BY id DESC
            LIMIT 1
        ");
        $db->bind(':loja_id', $loja_id);
        $db->bind(':caixa_drawer_id', $caixa_drawer_id);
        $db->bind(':data_caixa', $data_caixa);
        $sessao = $db->single();
        if (!$sessao) {
            return false;
        }

        $caixa_sessao_id = (int)$sessao['id'];
        $troco_abertura = (float)($sessao['troco_abertura'] ?? 0);

        $db->query("
            SELECT 
                COALESCE(SUM(valor), 0) AS soma_movimentos
            FROM caixa_movimentos
            WHERE caixa_sessao_id = :caixa_sessao_id
              AND status = 'Ativo'
        ");
        $db->bind(':caixa_sessao_id', $caixa_sessao_id);
        $row = $db->single();
        $soma_movimentos = (float)($row['soma_movimentos'] ?? 0);

        $saldo_esperado = $troco_abertura + $soma_movimentos;

        $db->query("
            SELECT
                tipo,
                COALESCE(SUM(valor), 0) AS soma_valor
            FROM caixa_movimentos
            WHERE caixa_sessao_id = :caixa_sessao_id
              AND status = 'Ativo'
            GROUP BY tipo
        ");
        $db->bind(':caixa_sessao_id', $caixa_sessao_id);
        $porTipo = $db->resultSet();

        $totaisTipo = [];
        foreach ($porTipo as $item) {
            $totaisTipo[$item['tipo']] = (float)$item['soma_valor'];
        }

        return [
            'caixa_sessao_id' => $caixa_sessao_id,
            'status' => $sessao['status'],
            'troco_abertura' => $troco_abertura,
            'saldo_esperado' => $saldo_esperado,
            'saldo_fisico_informado' => $sessao['saldo_fisico_informado'],
            'diferenca' => $sessao['diferenca'],
            'totais_por_tipo' => $totaisTipo,
        ];
    }

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
                    WHEN p.forma_pagamento LIKE '%Cheque%' THEN p.valor_pago
                    ELSE 0
                END as cheque,
                CASE 
                    WHEN p.forma_pagamento LIKE '%Cartão%' OR p.forma_pagamento LIKE '%Parcelado%' THEN p.valor_pago
                    ELSE 0
                END as parc_cartao,
                CASE 
                    WHEN p.forma_pagamento LIKE '%Material%' THEN p.valor_pago
                    ELSE 0
                END as material,
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
                SUM(CASE WHEN p.forma_pagamento LIKE '%Cheque%' THEN p.valor_pago ELSE 0 END) as total_cheque,
                SUM(CASE WHEN p.forma_pagamento LIKE '%Cartão%' OR p.forma_pagamento LIKE '%Parcelado%' THEN p.valor_pago ELSE 0 END) as total_parc_cartao,
                SUM(CASE WHEN p.forma_pagamento LIKE '%Material%' THEN p.valor_pago ELSE 0 END) as total_material,
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

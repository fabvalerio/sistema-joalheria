<?php

namespace App\Models\Pedidos;

use db; // Classe de conexão com o banco

class Controller
{
    /** @var string|null Mensagem para exibir quando cadastro() retorna false */
    public $cadastroErro = null;

    private static function formasPagamentoPermitidas(): array
    {
        return ['Dinheiro', 'Pix', 'Cartão de Crédito', 'Cartão de Débito', 'Cheque', 'Material'];
    }

    /**
     * Linhas de pagamento persistidas do pedido.
     */
    public function listarPagamentosPorPedido($pedidoId)
    {
        $db = new db();
        $db->query(
            'SELECT id, forma, valor, parcelas, observacao, cartao_id
             FROM pedido_pagamentos WHERE pedido_id = :id ORDER BY id ASC'
        );
        $db->bind(':id', $pedidoId);
        return $db->resultSet();
    }

    // Listar todos os pedidos
    public function listar()
    {
        $db = new db();
        $db->query("SELECT 
                        p.id, 
                        p.data_pedido,
                        p.forma_pagamento,
                        p.acrescimo,
                        p.desconto,
                        p.total,
                        p.valor_pago,
                        p.status_pedido,
                        p.data_entrega,
                        c.nome_pf,
                        c.nome_fantasia_pj,
                        f.status as status_fabrica
                    FROM 
                        pedidos p
                    LEFT JOIN 
                        clientes c ON p.cliente_id = c.id
                    LEFT JOIN 
                        fabrica as f ON p.id = f.pedido_id
                        WHERE 
                        p.orcamento is null
                    ORDER BY 
                        p.data_pedido DESC
                ");
        return $db->resultSet();
    }


    // Visualizar um pedido e seus itens
    public function ver($id)
    {
        $db = new db();

        // Consulta principal do pedido com os dados do cliente
        $db->query("SELECT 
                            p.id, 
                            p.data_pedido,
                            p.forma_pagamento,
                            p.acrescimo,
                            p.desconto,
                            p.total,
                            p.valor_pago,
                            p.status_pedido,
                            p.data_entrega,
                            p.observacoes,
                            c.id as idCliente,
                            c.nome_pf,
                            c.cpf,
                            c.telefone,
                            c.whatsapp,
                            c.nome_fantasia_pj
                            -- f.status as status_fabrica
                        FROM 
                            pedidos p
                        LEFT JOIN 
                            clientes c ON p.cliente_id = c.id
                        WHERE 
                            p.id = :id
                    ");
        $db->bind(':id', $id);
        $pedido = $db->single(); // Retorna uma única linha

        // Consulta para os itens do pedido
        $db->query("
        SELECT 
            pi.produto_id, 
            pi.quantidade, 
            pi.valor_unitario, 
            pi.desconto_percentual, 
            pr.descricao_etiqueta AS nome_produto
        FROM 
            pedidos_itens pi
        LEFT JOIN 
            produtos pr ON pi.produto_id = pr.id
        WHERE 
            pi.pedido_id = :pedido_id
    ");
        $db->bind(':pedido_id', $id);
        $itens = $db->resultSet(); // Retorna uma lista de itens

        $pagamentos = $this->listarPagamentosPorPedido($id);

        // Retorna os dados combinados
        return [
            'pedido' => $pedido,
            'itens' => $itens,
            'pagamentos' => $pagamentos,
        ];
    }





    // Cadastrar um novo pedido
    public function cadastro($dados)
    {
        $this->cadastroErro = null;
        $db = new db();

        $pagamentos = $dados['pagamentos'] ?? null;
        if (!is_array($pagamentos) || count($pagamentos) === 0) {
            $this->cadastroErro = 'Informe ao menos uma forma de pagamento.';
            return false;
        }

        $permitidas = self::formasPagamentoPermitidas();
        $totalPedido = round((float)($dados['total'] ?? 0), 2);
        $totalPago = 0.0;
        $linhasCheque = 0;
        $somaLinhasMaterial = 0.0;

        foreach ($pagamentos as $row) {
            $forma = isset($row['forma']) ? (string)$row['forma'] : '';
            if (!in_array($forma, $permitidas, true)) {
                $this->cadastroErro = 'Forma de pagamento inválida.';
                return false;
            }
            $valorLinha = round((float)($row['valor'] ?? 0), 2);
            if ($valorLinha <= 0) {
                $this->cadastroErro = 'Cada pagamento deve ter valor maior que zero.';
                return false;
            }
            if ($forma === 'Cartão de Crédito') {
                $parc = (int)($row['parcelas'] ?? 1);
                if ($parc < 1) {
                    $this->cadastroErro = 'Informe o número de parcelas do cartão de crédito.';
                    return false;
                }
            }
            if ($forma === 'Cheque') {
                $linhasCheque++;
            }
            if ($forma === 'Material') {
                $somaLinhasMaterial += $valorLinha;
            }
            $totalPago += $valorLinha;
        }

        if ($linhasCheque > 1) {
            $this->cadastroErro = 'Permitido apenas um pagamento por cheque neste cadastro.';
            return false;
        }
        if ($linhasCheque === 1 && empty($dados['numero_parcelas'])) {
            $this->cadastroErro = 'Informe as parcelas do cheque.';
            return false;
        }

        if (abs($totalPago - $totalPedido) > 0.01) {
            $this->cadastroErro = 'A soma dos pagamentos (R$ '
                . number_format($totalPago, 2, ',', '.')
                . ') deve ser igual ao total do pedido (R$ '
                . number_format($totalPedido, 2, ',', '.')
                . ').';
            return false;
        }

        $totalMateriaisCalculado = 0.0;
        $temMateriaisPost = false;
        if (!empty($dados['materiais']) && is_array($dados['materiais'])) {
            foreach ($dados['materiais'] as $m) {
                $material_id = (int)($m['material_id'] ?? 0);
                $gramas = (float)($m['gramas'] ?? 0);
                if ($material_id > 0 && $gramas > 0) {
                    $temMateriaisPost = true;
                    $db->query('SELECT valor_por_grama FROM forma_pagamento_material WHERE id = :id');
                    $db->bind(':id', $material_id);
                    $mat = $db->single();
                    $valor_por_grama = (float)($mat['valor_por_grama'] ?? 0);
                    $totalMateriaisCalculado += $gramas * $valor_por_grama;
                }
            }
        }

        if ($temMateriaisPost) {
            if ($somaLinhasMaterial <= 0) {
                $this->cadastroErro = 'Inclua uma linha de pagamento em Material compatível com os materiais informados.';
                return false;
            }
            if (abs($somaLinhasMaterial - $totalMateriaisCalculado) > 0.01) {
                $this->cadastroErro = 'O total nas linhas de Material deve coincidir com o valor calculado pelos materiais (gramas).';
                return false;
            }
        }

        $labelsResumo = [];
        foreach ($pagamentos as $p) {
            $f = $p['forma'];
            if ($f === 'Cheque' && !empty($dados['numero_parcelas'])) {
                $labelsResumo[] = 'Cheque ' . (int)$dados['numero_parcelas'] . 'x Parcelas';
            } elseif ($f === 'Cartão de Crédito' && (int)($p['parcelas'] ?? 1) > 1) {
                $labelsResumo[] = 'Cartão de Crédito ' . (int)$p['parcelas'] . 'x';
            } else {
                $labelsResumo[] = $f;
            }
        }
        $labelsResumo = array_unique($labelsResumo);
        $forma_pagamento = count($pagamentos) > 1
            ? ('Misto: ' . implode('; ', $labelsResumo))
            : ($labelsResumo[0] ?? '');

        if (($dados['status_pedido'] ?? '') === 'Pago') {
            $dados['valor_pago'] = $totalPedido;
        }

        $loja_id = $dados['loja_id'] ?? null;
        $tipo_loja = null;
        if ($loja_id) {
            $db->query("SELECT tipo FROM loja WHERE id = :loja_id");
            $db->bind(':loja_id', $loja_id);
            $loja_row = $db->single();
            $tipo_loja = (is_array($loja_row) && isset($loja_row['tipo'])) ? $loja_row['tipo'] : null;
        }

        $db->beginTransaction();
        try {
            $db->query("
            INSERT INTO pedidos (
                cliente_id, data_pedido, forma_pagamento, acrescimo, desconto,
                observacoes, total, valor_pago, cod_vendedor, status_pedido, data_entrega, loja_id
            ) VALUES (
                :cliente_id, :data_pedido, :forma_pagamento, :acrescimo, :desconto,
                :observacoes, :total, :valor_pago, :cod_vendedor, :status_pedido, :data_entrega, :loja_id
            )
        ");

            $campos = [
                'cliente_id',
                'data_pedido',
                'acrescimo',
                'desconto',
                'observacoes',
                'total',
                'valor_pago',
                'cod_vendedor',
                'status_pedido',
                'data_entrega',
                'loja_id',
            ];

            foreach ($campos as $campo) {
                $valor = isset($dados[$campo]) && $dados[$campo] !== '' ? $dados[$campo] : null;
                $db->bind(":$campo", $valor);
            }
            $db->bind(':forma_pagamento', $forma_pagamento);

            if (!$db->execute()) {
                throw new \RuntimeException('insert_pedido');
            }

            $pedidoId = (int)$db->lastInsertId();

            foreach ($pagamentos as $row) {
                $cartaoId = isset($row['cartao_id']) && $row['cartao_id'] !== '' && $row['cartao_id'] !== null
                    ? (int)$row['cartao_id'] : null;
                if ($cartaoId !== null && $cartaoId <= 0) {
                    $cartaoId = null;
                }
                $parc = (int)($row['parcelas'] ?? 1);
                if ($parc < 1) {
                    $parc = 1;
                }
                $obs = isset($row['observacao']) ? trim((string)$row['observacao']) : '';
                $obs = $obs === '' ? null : substr($obs, 0, 255);

                $db->query(
                    'INSERT INTO pedido_pagamentos (pedido_id, forma, valor, parcelas, observacao, cartao_id)
                     VALUES (:pedido_id, :forma, :valor, :parcelas, :observacao, :cartao_id)'
                );
                $db->bind(':pedido_id', $pedidoId);
                $db->bind(':forma', $row['forma']);
                $db->bind(':valor', round((float)$row['valor'], 2));
                $db->bind(':parcelas', $parc);
                $db->bind(':observacao', $obs);
                $db->bind(':cartao_id', $cartaoId);
                if (!$db->execute()) {
                    throw new \RuntimeException('insert_pagamento');
                }
            }

            if (!empty($dados['numero_cheque']) && is_array($dados['numero_cheque'])) {
                foreach ($dados['numero_cheque'] as $parcela_numero => $numero_cheque) {
                    $parcela_numero = (int)$parcela_numero;
                    if ($parcela_numero > 0 && trim((string)$numero_cheque) !== '') {
                        $db->query('INSERT INTO pedidos_cheques (pedido_id, parcela_numero, numero_cheque) VALUES (:pedido_id, :parcela_numero, :numero_cheque)');
                        $db->bind(':pedido_id', $pedidoId);
                        $db->bind(':parcela_numero', $parcela_numero);
                        $db->bind(':numero_cheque', trim((string)$numero_cheque));
                        if (!$db->execute()) {
                            throw new \RuntimeException('insert_cheque');
                        }
                    }
                }
            }

            if (!empty($dados['materiais']) && is_array($dados['materiais'])) {
                foreach ($dados['materiais'] as $m) {
                    $material_id = (int)($m['material_id'] ?? 0);
                    $gramas = (float)($m['gramas'] ?? 0);
                    if ($material_id > 0 && $gramas > 0) {
                        $db->query('SELECT valor_por_grama FROM forma_pagamento_material WHERE id = :id');
                        $db->bind(':id', $material_id);
                        $mat = $db->single();
                        $valor_por_grama = (float)($mat['valor_por_grama'] ?? 0);
                        $valor_calculado = $gramas * $valor_por_grama;

                        $db->query('INSERT INTO pedidos_materiais (pedido_id, forma_pagamento_material_id, gramas, valor_calculado) VALUES (:pedido_id, :forma_pagamento_material_id, :gramas, :valor_calculado)');
                        $db->bind(':pedido_id', $pedidoId);
                        $db->bind(':forma_pagamento_material_id', $material_id);
                        $db->bind(':gramas', $gramas);
                        $db->bind(':valor_calculado', $valor_calculado);
                        if (!$db->execute()) {
                            throw new \RuntimeException('insert_material');
                        }
                    }
                }
            }

            foreach ($dados['itens'] as $item) {
                if (!isset($item['produto_id'], $item['quantidade'], $item['valor_unitario'])) {
                    continue;
                }

                $db->query("
                    INSERT INTO pedidos_itens (
                        pedido_id, produto_id, quantidade, valor_unitario, desconto_percentual
                    ) VALUES (
                        :pedido_id, :produto_id, :quantidade, :valor_unitario, :desconto_percentual
                    )
                ");
                $db->bind(':pedido_id', $pedidoId);
                $db->bind(':produto_id', $item['produto_id']);
                $db->bind(':quantidade', $item['quantidade']);
                $db->bind(':valor_unitario', $item['valor_unitario']);
                $db->bind(':desconto_percentual', $item['desconto_percentual'] ?? 0);

                if (!$db->execute()) {
                    throw new \RuntimeException('insert_item');
                }

                $db->query("
                    INSERT INTO movimentacao_estoque (
                        produto_id, descricao_produto, quantidade, tipo_movimentacao, data_movimentacao, motivo, estoque_antes, estoque_atualizado, pedido_id, loja_id
                    ) VALUES (
                        :produto_id, :descricao_produto, :quantidade, :tipo_movimentacao, :data_movimentacao, :motivo, :estoque_antes, :estoque_atualizado, :pedido_id, :loja_id
                    )
                ");

                $db->bind(':produto_id', $item['produto_id']);
                $db->bind(':descricao_produto', $item['descricao_produto']);
                $db->bind(':quantidade', $item['quantidade']);
                $db->bind(':tipo_movimentacao', 'Saida');
                $db->bind(':data_movimentacao', date('Y-m-d'));
                $db->bind(':motivo', 'pedido');
                $db->bind(':estoque_antes', $item['estoque_antes']);
                $db->bind(':estoque_atualizado', $item['quantidade']);
                $db->bind(':pedido_id', $pedidoId);
                $db->bind(':loja_id', $loja_id);

                if (!$db->execute()) {
                    throw new \RuntimeException('insert_mov');
                }

                if ($loja_id && $tipo_loja === 'Loja') {
                    $db->query("
                        UPDATE estoque_loja
                        SET quantidade = quantidade - :quantidade
                        WHERE produto_id = :produto_id AND loja_id = :loja_id AND quantidade >= :quantidade_check
                    ");
                    $db->bind(':quantidade', $item['quantidade']);
                    $db->bind(':produto_id', $item['produto_id']);
                    $db->bind(':loja_id', $loja_id);
                    $db->bind(':quantidade_check', $item['quantidade']);
                    $db->execute();
                    if ($db->rowCount() === 0) {
                        throw new \RuntimeException('estoque_loja');
                    }
                } else {
                    $db->query("
                        UPDATE estoque
                        SET quantidade = quantidade - :quantidade
                        WHERE produtos_id = :produto_id
                    ");
                    $db->bind(':quantidade', $item['quantidade']);
                    $db->bind(':produto_id', $item['produto_id']);
                    if (!$db->execute() || $db->rowCount() === 0) {
                        throw new \RuntimeException('estoque');
                    }
                    if ($loja_id && $tipo_loja === 'CD') {
                        $db->query("
                            UPDATE estoque_loja
                            SET quantidade = quantidade - :quantidade
                            WHERE produto_id = :produto_id AND loja_id = :loja_id
                        ");
                        $db->bind(':quantidade', $item['quantidade']);
                        $db->bind(':produto_id', $item['produto_id']);
                        $db->bind(':loja_id', $loja_id);
                        $db->execute();
                    }
                }

                if (!empty($dados['fabrica']) && $dados['fabrica'] == true) {
                    $dbFabrica = new db();
                    $dbFabrica->query("
                        INSERT INTO fabrica (
                            pedido_id , data_solicitacao , data_entrega
                        ) VALUES (
                            :pedido_id, :data_solicitacao , :data_entrega
                        )
                    ");
                    $dbFabrica->bind(':pedido_id', $pedidoId);
                    $dbFabrica->bind(':data_solicitacao', $dados['data_pedido']);
                    $dbFabrica->bind(':data_entrega', $dados['data_entrega']);
                    $dbFabrica->execute();
                }
            }

            $db->endTransaction();
            return $pedidoId;
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->cancelTransaction();
            }
            $this->cadastroErro = 'Não foi possível concluir o cadastro. Verifique os dados e tente novamente.';
            error_log('pedidos.cadastro: ' . $e->getMessage());
            return false;
        }
    }

    // Editar um pedido existente
    public function editar($id, $dados)
    {
        $db = new db();

        // Atualizar o pedido na tabela "pedidos"
        $db->query("
            UPDATE pedidos
            SET 
                cliente_id = :cliente_id,
                data_pedido = :data_pedido,
                forma_pagamento = :forma_pagamento,
                acrescimo = :acrescimo,
                desconto = :desconto,
                observacoes = :observacoes,
                total = :total,
                valor_pago = :valor_pago,
                cod_vendedor = :cod_vendedor,
                status_pedido = :status_pedido,
                data_entrega = :data_entrega
            WHERE id = :id
        ");

        $campos = [
            'cliente_id',
            'data_pedido',
            'forma_pagamento',
            'acrescimo',
            'desconto',
            'observacoes',
            'total',
            'valor_pago',
            'cod_vendedor',
            'status_pedido',
            'data_entrega'
        ];

        foreach ($campos as $campo) {
            $valor = isset($dados[$campo]) && $dados[$campo] !== '' ? $dados[$campo] : null;
            $db->bind(":$campo", $valor);
        }
        $db->bind(":id", $id);

        if ($db->execute()) {
            // Excluir os itens antigos do pedido
            $db->query("DELETE FROM pedidos_itens WHERE pedido_id = :pedido_id");
            $db->bind(":pedido_id", $id);
            $db->execute();

            // Inserir os itens atualizados
            foreach ($dados['itens'] as $item) {
                if (!isset($item['produto_id'], $item['quantidade'], $item['valor_unitario'])) {
                    continue;
                }

                $db->query("
                    INSERT INTO pedidos_itens (
                        pedido_id, produto_id, quantidade, valor_unitario, desconto_percentual
                    ) VALUES (
                        :pedido_id, :produto_id, :quantidade, :valor_unitario, :desconto_percentual
                    )
                ");
                $db->bind(":pedido_id", $id);
                $db->bind(":produto_id", $item['produto_id']);
                $db->bind(":quantidade", $item['quantidade']);
                $db->bind(":valor_unitario", $item['valor_unitario']);
                $db->bind(":desconto_percentual", $item['desconto_percentual'] ?? 0);

                if (!$db->execute()) {
                    return false;
                }
            }

            return true; // Atualização bem-sucedida
        }

        return false; // Falha na atualização
    }

    // Deletar um pedido e seus itens
    public function deletar($id)
    {
        $db = new db();

        // Obter loja_id e tipo da loja do pedido
        $db->query("SELECT p.loja_id, l.tipo FROM pedidos p LEFT JOIN loja l ON p.loja_id = l.id WHERE p.id = :id");
        $db->bind(":id", $id);
        $pedido = $db->single();
        $loja_id = $pedido['loja_id'] ?? null;
        $tipo_loja = ($pedido && isset($pedido['tipo'])) ? $pedido['tipo'] : null;

        $db->query("SELECT * FROM pedidos_itens WHERE pedido_id = :pedido_id");
        $db->bind(":pedido_id", $id);
        $itens = $db->resultSet();

        foreach ($itens as $item) {
            if ($loja_id && $tipo_loja === 'Loja') {
                $db->query("UPDATE estoque_loja SET quantidade = quantidade + :quantidade WHERE produto_id = :produto_id AND loja_id = :loja_id");
                $db->bind(":quantidade", $item['quantidade']);
                $db->bind(":produto_id", $item['produto_id']);
                $db->bind(":loja_id", $loja_id);
                $db->execute();
            } else {
                $db->query("UPDATE estoque SET quantidade = quantidade + :quantidade WHERE produtos_id = :produto_id");
                $db->bind(":quantidade", $item['quantidade']);
                $db->bind(":produto_id", $item['produto_id']);
                $db->execute();
                if ($loja_id && $tipo_loja === 'CD') {
                    $db->query("UPDATE estoque_loja SET quantidade = quantidade + :quantidade WHERE produto_id = :produto_id AND loja_id = :loja_id");
                    $db->bind(":quantidade", $item['quantidade']);
                    $db->bind(":produto_id", $item['produto_id']);
                    $db->bind(":loja_id", $loja_id);
                    $db->execute();
                }
            }
        }

        // Excluir os itens do pedido
        $db->query("DELETE FROM pedidos_itens WHERE pedido_id = :pedido_id");
        $db->bind(":pedido_id", $id);
        $db->execute();

        // Excluir as movimentações de estoque relacionadas ao pedido
        $db->query("DELETE FROM movimentacao_estoque WHERE pedido_id = :pedido_id");
        $db->bind(":pedido_id", $id);
        $db->execute();

        $db->query("DELETE FROM pedidos_cheques WHERE pedido_id = :pedido_id");
        $db->bind(":pedido_id", $id);
        $db->execute();

        $db->query("DELETE FROM pedidos_materiais WHERE pedido_id = :pedido_id");
        $db->bind(":pedido_id", $id);
        $db->execute();

        // Por último, excluir o pedido
        $db->query("DELETE FROM pedidos WHERE id = :id");
        $db->bind(":id", $id);

        return $db->execute(); // Retorna true se a exclusão do pedido for bem-sucedida
    }

    public function listarClientes()
    {
        $db = new db();

        // Consulta SQL para listar os clientes
        $db->query("
        SELECT 
            id, nome_pf,
            nome_fantasia_pj,
            cpf, 
            cnpj_pj
        FROM clientes 
        ORDER BY id ASC
    ");

        return $db->resultSet(); // Retorna todos os resultados
    }
    public function listarProdutos()
    {
        $db = new db();

        // Consulta SQL para listar os produtos
        $db->query("
        SELECT 
            p.id, 
            p.descricao_etiqueta AS nome_produto, 
            p.em_reais AS preco, 
            e.quantidade AS estoque, 
            p.capa as capa,
            c.valor AS cotacao_valor,
                p.peso_gr AS peso_gr,
                p.custo AS custo,
                p.margem AS margem,
                p.preco_ql
        FROM 
            produtos p
        LEFT JOIN cotacoes c ON p.cotacao = c.id
        LEFT JOIN 
            estoque e ON p.id = e.produtos_id
        ORDER BY 
            p.descricao_etiqueta ASC
    ");

        return $db->resultSet(); // Retorna todos os resultados
    }
    public function listarCartoes()
    {
        $db = new db();

        // Consulta SQL para listar os cartões
        $db->query("
        SELECT *
        FROM cartoes
    ");

        return $db->resultSet(); // Retorna todos os resultados
    }
    public function mudarStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $novoStatus = $_POST['status'] ?? null;

            if ($id && $novoStatus) {
                $db = new db();
                $db->query("
                UPDATE pedidos 
                SET status_pedido = :status 
                WHERE id = :id
            ");
                $db->bind(':status', $novoStatus);
                $db->bind(':id', $id);
                $db->execute();

                // Redireciona de volta para a lista
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }
        }

        // Caso algo dê errado, redirecione para a lista com erro
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}

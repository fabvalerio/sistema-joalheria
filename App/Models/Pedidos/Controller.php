<?php

namespace App\Models\Pedidos;

use db; // Classe de conexão com o banco

class Controller
{
    // Listar todos os pedidos
    public function listar()
    {
        $db = new db();
        $db->query("
            SELECT 
                p.id, 
                p.cliente_id, 
                c.nome_fantasia AS cliente_nome,
                p.data_pedido, 
                p.total, 
                p.status_pedido, 
                p.data_entrega
            FROM 
                pedidos p
            LEFT JOIN 
                clientes c ON p.cliente_id = c.id
            ORDER BY 
                p.data_pedido DESC
        ");
        return $db->resultSet();
    }

    // Visualizar um pedido e seus itens
    public function ver($id)
    {
        $db = new db();

        // Buscar os dados do pedido
        $db->query("
            SELECT 
                p.id, 
                p.cliente_id, 
                c.nome_fantasia AS cliente_nome,
                p.data_pedido, 
                p.forma_pagamento, 
                p.acrescimo, 
                p.desconto, 
                p.observacoes, 
                p.total, 
                p.valor_pago, 
                p.cod_vendedor, 
                p.status_pedido, 
                p.data_entrega
            FROM 
                pedidos p
            LEFT JOIN 
                clientes c ON p.cliente_id = c.id
            WHERE 
                p.id = :id
        ");
        $db->bind(":id", $id);
        $pedido = $db->single();

        if (!$pedido) {
            return false; // Retorna falso se o pedido não for encontrado
        }

        // Buscar os itens do pedido
        $db->query("
            SELECT 
                pi.id, 
                pi.produto_id, 
                pr.descricao_etiqueta AS produto_nome, 
                pi.quantidade, 
                pi.valor_unitario, 
                pi.desconto_percentual
            FROM 
                pedidos_itens pi
            LEFT JOIN 
                produtos pr ON pi.produto_id = pr.id
            WHERE 
                pi.pedido_id = :pedido_id
        ");
        $db->bind(":pedido_id", $id);
        $pedido['itens'] = $db->resultSet();

        return $pedido;
    }

    // Cadastrar um novo pedido
    public function cadastro($dados)
    {
        $db = new db();

        // Inserir o pedido na tabela "pedidos"
        $db->query("
            INSERT INTO pedidos (
                cliente_id, data_pedido, forma_pagamento, acrescimo, desconto, 
                observacoes, total, valor_pago, cod_vendedor, status_pedido, data_entrega
            ) VALUES (
                :cliente_id, :data_pedido, :forma_pagamento, :acrescimo, :desconto, 
                :observacoes, :total, :valor_pago, :cod_vendedor, :status_pedido, :data_entrega
            )
        ");

        $campos = [
            'cliente_id', 'data_pedido', 'forma_pagamento', 'acrescimo', 'desconto',
            'observacoes', 'total', 'valor_pago', 'cod_vendedor', 'status_pedido', 'data_entrega'
        ];

        foreach ($campos as $campo) {
            $valor = isset($dados[$campo]) && $dados[$campo] !== '' ? $dados[$campo] : null;
            $db->bind(":$campo", $valor);
        }

        if ($db->execute()) {
            $pedidoId = $db->lastInsertId(); // Recuperar o ID do pedido recém-cadastrado

            // Inserir os itens do pedido
            foreach ($dados['itens'] as $item) {
                if (!isset($item['produto_id'], $item['quantidade'], $item['valor_unitario'])) {
                    continue; // Ignorar itens inválidos
                }

                $db->query("
                    INSERT INTO pedidos_itens (
                        pedido_id, produto_id, quantidade, valor_unitario, desconto_percentual
                    ) VALUES (
                        :pedido_id, :produto_id, :quantidade, :valor_unitario, :desconto_percentual
                    )
                ");
                $db->bind(":pedido_id", $pedidoId);
                $db->bind(":produto_id", $item['produto_id']);
                $db->bind(":quantidade", $item['quantidade']);
                $db->bind(":valor_unitario", $item['valor_unitario']);
                $db->bind(":desconto_percentual", $item['desconto_percentual'] ?? 0);

                if (!$db->execute()) {
                    return false; // Retorna falso se a inserção de um item falhar
                }
            }

            return true; // Cadastro bem-sucedido
        }

        return false; // Falha no cadastro do pedido
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
            'cliente_id', 'data_pedido', 'forma_pagamento', 'acrescimo', 'desconto',
            'observacoes', 'total', 'valor_pago', 'cod_vendedor', 'status_pedido', 'data_entrega'
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

        // Excluir os itens do pedido
        $db->query("DELETE FROM pedidos_itens WHERE pedido_id = :pedido_id");
        $db->bind(":pedido_id", $id);
        $db->execute();

        // Excluir o pedido
        $db->query("DELETE FROM pedidos WHERE id = :id");
        $db->bind(":id", $id);

        return $db->execute(); // Retorna true se a exclusão for bem-sucedida
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
            e.quantidade AS estoque
        FROM 
            produtos p
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


}

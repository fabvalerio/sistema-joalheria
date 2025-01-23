<?php

namespace App\Models\EntradaMercadorias;

use db; // Classe de conexão com o banco

class Controller
{
  // Listar todas as entradas de mercadorias com o nome do fornecedor
  public function listar()
  {
    $db = new db();
    $db->query("
            SELECT 
                e.id,
                e.nf_fiscal,
                e.data_pedido,
                e.data_prevista_entrega,
                e.data_entregue,
                e.transportadora,
                e.valor,
                e.observacoes,
                f.nome_fantasia AS fornecedor_nome
            FROM 
                entrada_mercadorias e
            LEFT JOIN fornecedores f ON e.fornecedor_id = f.id
            ORDER BY e.data_pedido DESC
        ");
    return $db->resultSet();
  }

  // Obter uma entrada de mercadoria pelo ID com o nome do fornecedor
  public function ver($id)
  {
    $db = new db();
    $db->query("
            SELECT 
                e.id,
                e.nf_fiscal,
                e.data_pedido,
                e.data_prevista_entrega,
                e.data_entregue,
                e.transportadora,
                e.valor,
                e.observacoes,
                f.nome_fantasia AS fornecedor_nome,
                e.fornecedor_id
            FROM 
                entrada_mercadorias e
            LEFT JOIN fornecedores f ON e.fornecedor_id = f.id
            WHERE e.id = :id
        ");
    $db->bind(":id", $id);
    return $db->single();
  }

  // Cadastrar uma nova entrada de mercadoria
  public function cadastro($dados)
  {
    $db = new db();

    // Inserir a entrada de mercadoria na tabela "entrada_mercadorias"
    $db->query("
        INSERT INTO entrada_mercadorias (
            nf_fiscal, data_pedido, fornecedor_id, data_prevista_entrega,
            data_entregue, transportadora, valor, observacoes
        ) VALUES (
            :nf_fiscal, :data_pedido, :fornecedor_id, :data_prevista_entrega,
            :data_entregue, :transportadora, :valor, :observacoes
        )
    ");

    // Garantir que campos opcionais sejam tratados como NULL se estiverem vazios
    $campos = [
      'nf_fiscal',
      'data_pedido',
      'fornecedor_id',
      'data_prevista_entrega',
      'data_entregue',
      'transportadora',
      'valor',
      'observacoes'
    ];

    foreach ($campos as $campo) {
      $valor = isset($dados[$campo]) && $dados[$campo] !== '' ? $dados[$campo] : null;
      $db->bind(":$campo", $valor);
    }

    // Executar a inserção na tabela "entrada_mercadorias"
    if ($db->execute()) {
      // Recuperar o ID da entrada de mercadoria recém-cadastrada
      $entradaMercadoriaId = $db->lastInsertId();

      // Inserção bem-sucedida da entrada, agora insere os produtos
      foreach ($dados['produtos'] as $produto) {
        // Validação básica para evitar erros
        if (!isset($produto['id'], $produto['nome_produto'], $produto['quantidade'])) {
          continue; // Ignorar produtos inválidos
        }

        // Inserir na tabela movimentacao_estoque
        $db->query("
            INSERT INTO movimentacao_estoque (
                produto_id, descricao_produto, tipo_movimentacao, quantidade, documento, 
                data_movimentacao, motivo, estoque_antes, estoque_atualizado
            ) VALUES (
                :produto_id, :descricao_produto, :tipo_movimentacao, :quantidade, :documento, 
                :data_movimentacao, :motivo, :estoque_antes, :estoque_atualizado
            )
        ");
        $db->bind(":produto_id", $produto['id']);
        $db->bind(":descricao_produto", $produto['nome_produto']);
        $db->bind(":tipo_movimentacao", 'Entrada');
        $db->bind(":quantidade", $produto['quantidade']);
        $db->bind(":documento", $dados['nf_fiscal']);
        $db->bind(":data_movimentacao", date('Y-m-d H:i:s'));
        $db->bind(":motivo", 'Cadastro de Entrada de Mercadoria');
        $db->bind(":estoque_antes", $produto['estoque']); // Estoque atual
        $db->bind(":estoque_atualizado", $produto['estoque'] + $produto['quantidade']); // Estoque atualizado

        if (!$db->execute()) {
          return false; // Se falhar, interrompe e retorna falso
        }

        // Atualizar o estoque na tabela "estoque"
        $db->query("
            UPDATE estoque
            SET quantidade = :nova_quantidade
            WHERE produtos_id = :produto_id
        ");
        $db->bind(":nova_quantidade", $produto['estoque'] + $produto['quantidade']);
        $db->bind(":produto_id", $produto['id']);

        if (!$db->execute()) {
          return false; // Retorna falso se o update falhar
        }
      }


      return true; // Sucesso em todas as inserções
    }

    return false; // Falha na inserção da entrada de mercadoria
  }





  // Editar uma entrada de mercadoria pelo ID
  public function editar($id, $dados)
  {
    $db = new db();
    $db->query("
            UPDATE entrada_mercadorias 
            SET 
                nf_fiscal = :nf_fiscal, 
                data_pedido = :data_pedido, 
                fornecedor_id = :fornecedor_id, 
                data_prevista_entrega = :data_prevista_entrega,
                data_entregue = :data_entregue, 
                transportadora = :transportadora, 
                valor = :valor, 
                observacoes = :observacoes 
            WHERE id = :id
        ");
    $db->bind(":nf_fiscal", $dados['nf_fiscal']);
    $db->bind(":data_pedido", $dados['data_pedido']);
    $db->bind(":fornecedor_id", $dados['fornecedor_id']);
    $db->bind(":data_prevista_entrega", $dados['data_prevista_entrega']);
    $db->bind(":data_entregue", $dados['data_entregue']);
    $db->bind(":transportadora", $dados['transportadora']);
    $db->bind(":valor", $dados['valor']);
    $db->bind(":observacoes", $dados['observacoes']);
    $db->bind(":id", $id);
    return $db->execute();
  }

  // Deletar uma entrada de mercadoria pelo ID
  public function deletar($id)
  {
    $db = new db();
    $db->query("DELETE FROM entrada_mercadorias WHERE id = :id");
    $db->bind(":id", $id);
    return $db->execute();
  }

  // Listar todos os fornecedores
  public function listarFornecedores()
  {
    $db = new db();
    $db->query("SELECT id, nome_fantasia FROM fornecedores ORDER BY nome_fantasia ASC");
    return $db->resultSet();
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
}

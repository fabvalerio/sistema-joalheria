<?php

namespace App\Models\MovimentacaoEstoque;

use db;

class Controller
{
  public function listar()
  {
    $db = new db();
    $db->query("
            SELECT 
                id, 
                produto_id, 
                descricao_produto, 
                tipo_movimentacao, 
                quantidade, 
                documento, 
                data_movimentacao, 
                motivo, 
                estoque_antes, 
                estoque_atualizado
            FROM movimentacao_estoque
            ORDER BY id DESC
        ");
    return $db->resultSet();
  }
}

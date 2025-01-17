<?php

namespace App\Models\Cartoes;

use db;

class Controller
{
  public function listar()
  {
    $db = new db();
    $db->query("SELECT * FROM cartoes ORDER BY nome_cartao ASC");
    return $db->resultSet();
  }

  public function ver($id)
  {
    $db = new db();
    $db->query("SELECT * FROM cartoes WHERE id = :id");
    $db->bind(":id", $id);
    return $db->single();
  }

  public function cadastro($dados)
  {
    $db = new db();

    $db->query("INSERT INTO cartoes (nome_cartao, taxa_administradora, tipo, bandeira, max_parcelas, juros_parcela_1, juros_parcela_2, juros_parcela_3, juros_parcela_4, juros_parcela_5, juros_parcela_6, juros_parcela_7, juros_parcela_8, juros_parcela_9, juros_parcela_10, juros_parcela_11, juros_parcela_12) 
                    VALUES (:nome_cartao, :taxa_administradora, :tipo, :bandeira, :max_parcelas, :juros_parcela_1, :juros_parcela_2, :juros_parcela_3, :juros_parcela_4, :juros_parcela_5, :juros_parcela_6, :juros_parcela_7, :juros_parcela_8, :juros_parcela_9, :juros_parcela_10, :juros_parcela_11, :juros_parcela_12)");

    // Verificar cada campo e substituir valores vazios por NULL
    foreach ($dados as $key => $value) {
      $db->bind(":$key", $value === '' ? null : $value);
    }

    return $db->execute();
  }


  public function editar($id, $dados)
  {
    $db = new db();
    $setFields = [];

    foreach ($dados as $key => $value) {
      $setFields[] = "$key = :$key";
    }

    $setQuery = implode(", ", $setFields);

    $db->query("UPDATE cartoes SET $setQuery WHERE id = :id");

    foreach ($dados as $key => $value) {
      $db->bind(":$key", $value);
    }
    $db->bind(":id", $id);

    return $db->execute();
  }

  public function deletar($id)
  {
    $db = new db();
    $db->query("DELETE FROM cartoes WHERE id = :id");
    $db->bind(":id", $id);
    return $db->execute();
  }
}

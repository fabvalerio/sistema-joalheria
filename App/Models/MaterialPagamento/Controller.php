<?php

namespace App\Models\MaterialPagamento;

use db;

class Controller
{
    public function listar()
    {
        $db = new db();
        $db->query("SELECT * FROM forma_pagamento_material ORDER BY tipo_material ASC");
        return $db->resultSet();
    }

    public function ver($id)
    {
        $db = new db();
        $db->query("SELECT * FROM forma_pagamento_material WHERE id = :id");
        $db->bind(":id", $id);
        return $db->single();
    }

    public function cadastro($dados)
    {
        $db = new db();
        $db->query("INSERT INTO forma_pagamento_material (tipo_material, valor_por_grama) VALUES (:tipo_material, :valor_por_grama)");
        $db->bind(":tipo_material", $dados['tipo_material']);
        $db->bind(":valor_por_grama", $dados['valor_por_grama']);
        return $db->execute();
    }

    public function editar($id, $dados)
    {
        $db = new db();
        $db->query("UPDATE forma_pagamento_material SET tipo_material = :tipo_material, valor_por_grama = :valor_por_grama WHERE id = :id");
        $db->bind(":tipo_material", $dados['tipo_material']);
        $db->bind(":valor_por_grama", $dados['valor_por_grama']);
        $db->bind(":id", $id);
        return $db->execute();
    }

    public function deletar($id)
    {
        $db = new db();
        $db->query("DELETE FROM forma_pagamento_material WHERE id = :id");
        $db->bind(":id", $id);
        return $db->execute();
    }
}

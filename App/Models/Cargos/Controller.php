<?php

namespace App\Models\Cargos;

use db; // Classe de conexão com o banco

class Controller
{
    // Listar todos os cargos
    public function listar()
    {
        $db = new db();
        $db->query("SELECT * FROM cargos ORDER BY id ASC");
        return $db->resultSet();
    }

    // Obter um cargo pelo ID
    public function ver($id)
    {
        $db = new db();
        $db->query("SELECT * FROM cargos WHERE id = :id");
        $db->bind(":id", $id);
        return $db->single();
    }

    // Cadastrar um novo cargo
    public function cadastro($dados)
    {
        $db = new db();
        $db->query("INSERT INTO cargos (cargo) VALUES (:cargo)");
        $db->bind(":cargo", $dados['cargo']);
        return $db->execute();
    }

    // Editar um cargo pelo ID
    public function editar($id, $dados)
    {
        $db = new db();
        $db->query("UPDATE cargos SET cargo = :cargo WHERE id = :id");
        $db->bind(":cargo", $dados['cargo']);
        $db->bind(":id", $id);
        return $db->execute();
    }

    // Deletar um cargo pelo ID
    public function deletar($id)
    {
        $db = new db();
        $db->query("DELETE FROM cargos WHERE id = :id");
        $db->bind(":id", $id);
        return $db->execute();
    }
}

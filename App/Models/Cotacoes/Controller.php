<?php

namespace App\Models\Cotacoes;

use db;

class Controller
{
    // Listar todas as cotações
    public function listar()
    {
        $db = new db();
        $db->query("SELECT * FROM cotacoes ORDER BY id ASC");
        return $db->resultSet();
    }

    // Ver uma cotação específica
    public function ver($id)
    {
        $db = new db();
        $db->query("SELECT * FROM cotacoes WHERE id = :id");
        $db->bind(":id", $id);
        return $db->single();
    }

    // Cadastro de cotação
    public function cadastro($dados)
    {
        $db = new db();
        $db->query("INSERT INTO cotacoes (nome, valor) VALUES (:nome, :valor)");
        $db->bind(":nome", $dados['nome']);
        $db->bind(":valor", $dados['valor']);
        return $db->execute();
    }

    // Editar uma cotação
    public function editar($id, $dados)
    {
        $db = new db();
        $db->query("UPDATE cotacoes SET nome = :nome, valor = :valor WHERE id = :id");
        $db->bind(":nome", $dados['nome']);
        $db->bind(":valor", $dados['valor']);
        $db->bind(":id", $id);
        return $db->execute();
    }

    // Deletar uma cotação
    public function deletar($id)
    {
        $db = new db();
        $db->query("DELETE FROM cotacoes WHERE id = :id");
        $db->bind(":id", $id);
        return $db->execute();
    }
}

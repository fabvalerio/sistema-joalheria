<?php

namespace App\Models\GrupoClientes; // Namespace adequado à tabela 'grupo_clientes'
use db; // Importa a classe de conexão com o banco de dados

class Controller
{
    public function listar()
    {
        $db = new db();
        $db->query("SELECT * FROM grupo_clientes ORDER BY nome_grupo ASC");
        return $db->resultSet();
    }

    public function ver($id)
    {
        $db = new db();
        $db->query("SELECT * FROM grupo_clientes WHERE id = :id");
        $db->bind(":id", $id);
        return $db->single();
    }

    public function cadastro($dados)
    {
        $db = new db();
        $db->query("INSERT INTO grupo_clientes (nome_grupo, comissao_vendedores) VALUES (:nome_grupo, :comissao_vendedores)");
        $db->bind(":nome_grupo", $dados['nome_grupo']);
        $db->bind(":comissao_vendedores", $dados['comissao_vendedores']);
        return $db->execute();
    }

    public function editar($id, $dados)
    {
        $db = new db();
        $db->query("UPDATE grupo_clientes SET nome_grupo = :nome_grupo, comissao_vendedores = :comissao_vendedores WHERE id = :id");
        $db->bind(":nome_grupo", $dados['nome_grupo']);
        $db->bind(":comissao_vendedores", $dados['comissao_vendedores']);
        $db->bind(":id", $id);
        return $db->execute();
    }

    public function deletar($id)
    {
        $db = new db();
        $db->query("DELETE FROM grupo_clientes WHERE id = :id");
        $db->bind(":id", $id);
        return $db->execute();
    }
}
?>

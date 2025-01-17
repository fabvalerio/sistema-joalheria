<?php

namespace App\Models\Usuarios; // Namespace adequado à tabela 'usuario'
use db; // Importa a classe de conexão com o banco de dados

class Controller
{
    // Listar todos os registros
    public function listar()
    {
        $lista = new db();
        $lista->query("SELECT * FROM usuarios ORDER BY id DESC");
        $resultados = $lista->resultSet();
        return $resultados;
    }

    // Ver um registro específico por ID
    public function ver($id)
    {
        $ver = new db();
        $ver->query("SELECT * FROM usuarios WHERE id = :id");
        $ver->bind(":id", $id);
        $resultado = $ver->single();
        return $resultado ?: false;
    }

    // Cadastrar um novo registro
    public function cadastro($dados)
    {
        $lista = new db();

        // Criação dinâmica de placeholders
        $campos = implode(", ", array_keys($dados));
        $placeholders = ":" . implode(", :", array_keys($dados));

        $sql = "INSERT INTO usuarios ($campos) VALUES ($placeholders)";
        $lista->query($sql);

        foreach ($dados as $campo => $valor) {
            $lista->bind(":$campo", $valor);
        }

        return $lista->execute();
    }

    // Editar um registro específico por ID
    public function editar($id, $dados)
    {
        $editar = new db();
        $setPlaceholders = [];

        foreach ($dados as $campo => $valor) {
            $setPlaceholders[] = "$campo = :$campo";
        }

        $setQuery = implode(", ", $setPlaceholders);
        $sql = "UPDATE usuarios SET $setQuery WHERE id = :id";
        $editar->query($sql);

        foreach ($dados as $campo => $valor) {
            $editar->bind(":$campo", $valor);
        }
        $editar->bind(":id", $id);

        return $editar->execute();
    }

    // Deletar um registro específico por ID
    public function deletar($id)
    {
        $deletar = new db();
        $deletar->query("DELETE FROM usuarios WHERE id = :id");
        $deletar->bind(":id", $id);

        return $deletar->execute();
    }
    //chama lista de cargos
    public function cargos()
{
    $db = new db(); // Instanciar a conexão com o banco de dados
    $db->query("SELECT * FROM cargos ORDER BY id ASC"); // Consultar todos os cargos
    $resultados = $db->resultSet(); // Armazenar os resultados
    return $resultados;
}

}
?>

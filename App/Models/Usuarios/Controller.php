<?php

namespace App\Models\Usuarios; // Namespace adequado à tabela 'usuario'
use db; // Importa a classe de conexão com o banco de dados

class Controller
{
    // PERMISSOES
    private $diretorioBase;
    private $arquivoPermissoes;

    public function __construct($diretorioBase = "pages", $arquivoPermissoes = "permissoes.json")
    {
        $this->diretorioBase = $diretorioBase;
        $this->arquivoPermissoes = $arquivoPermissoes;
    }

    // Lista os diretórios dentro de "pages"
    public function listarDiretorios()
    {
        $diretorios = array_filter(glob($this->diretorioBase . '/*'), 'is_dir');
        return array_map('basename', $diretorios);
    }

    // Salva as permissões em JSON
    public function salvarPermissoes($usuario, $permissoes)
    {
        $dados = $this->carregarPermissoes();
        $dados[$usuario] = $permissoes;
        file_put_contents($this->arquivoPermissoes, json_encode($dados, JSON_PRETTY_PRINT));
    }

    // Carrega as permissões existentes
    public function carregarPermissoes()
    {
        if (file_exists($this->arquivoPermissoes)) {
            return json_decode(file_get_contents($this->arquivoPermissoes), true);
        }
        return [];
    }

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

<?php

namespace App\Models\Clientes;

use db;

class Controller
{
    public function listar()
    {
        $db = new db();
        $db->query("SELECT * FROM clientes ORDER BY id ASC");
        return $db->resultSet();
    }

    public function ver($id)
    {
        $db = new db();
        $db->query("SELECT * FROM clientes WHERE id = :id");
        $db->bind(":id", $id);
        return $db->single();
    }

    /**
     * Valida os dados obrigatórios do cadastro/edição.
     * Retorna array de mensagens de erro ou array vazio se válido.
     */
    public function validarDados(array $dados): array
    {
        $erros = [];

        if (empty($dados['tipo_cliente'])) {
            $erros[] = 'Tipo de cliente é obrigatório.';
        } elseif ($dados['tipo_cliente'] === 'PF') {
            if (empty(trim($dados['nome_pf'] ?? ''))) {
                $erros[] = 'Nome completo é obrigatório para Pessoa Física.';
            }
        } elseif ($dados['tipo_cliente'] === 'PJ') {
            if (empty(trim($dados['razao_social_pj'] ?? '')) && empty(trim($dados['nome_fantasia_pj'] ?? ''))) {
                $erros[] = 'Razão Social ou Nome Fantasia é obrigatório para Pessoa Jurídica.';
            }
        }

        return $erros;
    }

    /**
     * Normaliza os dados para o banco (corporativo ENUM, grupo FK, etc.)
     */
    private function normalizarDados(array $dados): array
    {
        // corporativo: ENUM('S','N') - aceita apenas S, N ou NULL
        $dados['corporativo'] = in_array($dados['corporativo'] ?? '', ['S', 'N']) ? $dados['corporativo'] : null;

        // grupo: INT com FK - vazio ou 0 = NULL
        $dados['grupo'] = !empty($dados['grupo']) && $dados['grupo'] !== '0' ? (int) $dados['grupo'] : null;

        // data_nascimento
        $dados['data_nascimento'] = !empty($dados['data_nascimento']) ? $dados['data_nascimento'] : null;

        // Campos string: converter '0' em string vazia para consistência
        $camposString = ['nome_pf', 'razao_social_pj', 'nome_fantasia_pj', 'perfil', 'telefone', 'whatsapp', 'email', 'rg', 'cpf', 'ie_pj', 'cnpj_pj', 'cep', 'endereco', 'bairro', 'cidade', 'estado', 'tags', 'origem_contato', 'estado_civil'];
        foreach ($camposString as $campo) {
            if (isset($dados[$campo]) && $dados[$campo] === '0') {
                $dados[$campo] = '';
            }
        }

        return $dados;
    }

    public function cadastro($dados)
    {
        $db = new db();
        $dados = $this->normalizarDados($dados);

        $db->query("INSERT INTO clientes (tipo_cliente, nome_pf, razao_social_pj, nome_fantasia_pj, perfil, telefone, whatsapp, email, rg, cpf, ie_pj, cnpj_pj, cep, endereco, bairro, cidade, estado, data_nascimento, tags, origem_contato, estado_civil, corporativo, grupo) 
                VALUES (:tipo_cliente, :nome_pf, :razao_social_pj, :nome_fantasia_pj, :perfil, :telefone, :whatsapp, :email, :rg, :cpf, :ie_pj, :cnpj_pj, :cep, :endereco, :bairro, :cidade, :estado, :data_nascimento, :tags, :origem_contato, :estado_civil, :corporativo, :grupo)");

        foreach ($dados as $key => $value) {
            $db->bind(":$key", $value);
        }

        return $db->execute();
    }


    public function editar($id, $dados)
    {
        $db = new db();
        $dados = $this->normalizarDados($dados);

        $fields = [];
        foreach ($dados as $key => $value) {
            $fields[] = "$key = :$key";
        }
        $sql = "UPDATE clientes SET " . implode(", ", $fields) . " WHERE id = :id";
        $db->query($sql);
        foreach ($dados as $key => $value) {
            $db->bind(":$key", $value);
        }
        $db->bind(":id", $id);
        return $db->execute();
    }

    public function deletar($id)
    {
        $db = new db();
        $db->query("DELETE FROM clientes WHERE id = :id");
        $db->bind(":id", $id);
        return $db->execute();
    }

    public function listarGrupos()
    {
        $db = new db();
        $db->query("SELECT * FROM grupo_clientes ORDER BY id ASC");
        return $db->resultSet();
    }
}
?>

<?php

namespace App\Models\Log;
use db;

class Controller {
    private $db;

    public function __construct() {
        $this->db = new db();
    }

    public function logAlteracao($usuario, $ip, $acao, $valorAnterior, $valorAtual, $url) {
        // Converte arrays para JSON
        $valorAnterior = is_array($valorAnterior) ? json_encode($valorAnterior, JSON_UNESCAPED_UNICODE) : $valorAnterior;
        $valorAtual = is_array($valorAtual) ? json_encode($valorAtual, JSON_UNESCAPED_UNICODE) : $valorAtual;
    
        $sql = "INSERT INTO logs (usuario, ip, acao, valor_anterior, valor_atual, url) 
                VALUES (:usuario, :ip, :acao, :valorAnterior, :valorAtual, :url)";
    
        $this->db->query($sql);
        $this->db->bind(":usuario", $usuario);
        $this->db->bind(":ip", $ip);
        $this->db->bind(":acao", $acao);
        $this->db->bind(":valorAnterior", $valorAnterior ?? null);
        $this->db->bind(":valorAtual", $valorAtual ?? null);
        $this->db->bind(":url", $url);
        $this->db->execute();
    }
    
}

// Exemplo de uso:
// $log = new LogController();
// $log->logAlteracao("admin", "192.168.1.1", "Acesso página relatório", "", "", "/relatorio");

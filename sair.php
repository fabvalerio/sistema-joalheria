<?php

// Inicia a saída do buffer para evitar problemas de envio de cabeçalho
ob_start();
session_start();

include 'php/htaccess.php';

// Remove o cookie "admin_user" definindo-o com valor vazio e tempo expirado no passado
if (isset($_COOKIE['admin_user'])) {
    setcookie('admin_user', '', time() - 3600, '/'); // O último parâmetro '/' garante que o cookie seja removido em toda a aplicação
}

if (isset($_COOKIE['admin_nivel'])) {
    setcookie('admin_nivel', '', time() - 3600, '/'); // O último parâmetro '/' garante que o cookie seja removido em toda a aplicação
}

// Destrói a sessão para remover todos os dados de sessão armazenados
if (session_status() === PHP_SESSION_ACTIVE) {
    session_unset();
    session_destroy();
}

echo '<h2>Saindo...</h2>';

// Redireciona para a página de login
header("Location: {$url}admin/login.php");
exit;

ob_end_flush();

<?php
ob_start();
session_start();

include 'db/db.class.php';
include 'app/php/htaccess.php';

$_cpf = trim($_POST['cpf']);
$_senha = $_POST['senha'];

// Debug: Confirme que os dados foram recebidos corretamente
echo "<script>console.log('CPF recebido: {$_cpf}');</script>";
echo "<script>console.log('Senha recebida: {$_senha}');</script>";

// Conectar ao banco e buscar usuário
$db = new db();
$sql = "SELECT id, senha, nivel_acesso, nome_completo, permissoes FROM usuarios WHERE cpf = :cpf AND status = 1";
$db->query($sql);
$db->bind(":cpf", $_cpf);
$db->execute();
$user = $db->single();

// Debug: Confirme se o usuário foi encontrado
if (!$user) {
    error_log("Usuário com CPF {$_cpf} não encontrado.");
    header("Location: login.php?alert=error");
    exit;
}

// **Correção: Acesse a senha corretamente, independente do tipo de retorno**
$senha_armazenada = is_array($user) ? $user['senha'] : $user->senha;

// Debug: Verificar senha armazenada
error_log("Senha armazenada no banco: " . $senha_armazenada);
error_log("Senha digitada: " . $_senha);

// Verificar a senha com `password_verify()`
if (password_verify($_senha, $senha_armazenada)) {
    // Criar cookies seguros
    setcookie("id", $user['id'], time() + (3600 * 24 * 7), "/", "", false, true);
    setcookie("nome", $user['nome_completo'], time() + (3600 * 24 * 7), "/", "", false, true);
    setcookie("nivel_acesso", $user['nivel_acesso'], time() + (3600 * 24 * 7), "/", "", false, true);
    $permissoesJson = json_encode($user['permissoes']); // Garante que é uma string JSON válida
    setcookie("permissoes", $permissoesJson, time() + (3600 * 24 * 7), "/", "", false, true);
    

    error_log("Login bem-sucedido para CPF: " . $_cpf);
    header("Location: {$url}");
    exit;
} else {
    error_log("Falha na verificação da senha para CPF: " . $_cpf);
}

// Se a senha estiver errada, redireciona para a tela de login
header("Location: login.php?alert=error");
exit;

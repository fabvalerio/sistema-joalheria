<?php


// Função para criptografar os dados
function encryptData($data) {
    $encryption_key = $_ENV['ENCRYPTION_KEY'];
    $cipher = "AES-256-CBC";
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $encrypted = openssl_encrypt($data, $cipher, $encryption_key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

// Função para descriptografar os dados
function decryptData($encryptedData) {
    $encryption_key = $_ENV['ENCRYPTION_KEY'];
    $cipher = "AES-256-CBC";
    $data = base64_decode($encryptedData);
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = substr($data, 0, $ivlen);
    $encrypted = substr($data, $ivlen);
    return openssl_decrypt($encrypted, $cipher, $encryption_key, 0, $iv);
}

// Função para gerar um hash para pesquisas
function generateHash($data) {
    return hash('sha256', $data);
}

// Vamos testar a criptografia, descriptografia e gerar hash
$data = "valerio.fabio@gmail.com";
$encryptedData = encryptData($data);
$decryptedData = decryptData($encryptedData);
$hashData = generateHash($data);

// Mostrando os resultados

// echo "Dados Originais: " . $data . "<br>";
// echo "Dados Criptografados: " . $encryptedData . "<br>";
// echo "Dados Descriptografados: " . $decryptedData . "<br>";
// echo "Hash dos Dados: " . $hashData . "<br>";

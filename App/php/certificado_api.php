<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_COOKIE['id']) || empty($_COOKIE['id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'mensagem' => 'Não autorizado']);
    exit;
}

if ($_COOKIE['nivel_acesso'] != 'Administrador') {
    http_response_code(403);
    echo json_encode(['success' => false, 'mensagem' => 'Apenas administradores podem alterar o certificado']);
    exit;
}

$certDir = $_SERVER['DOCUMENT_ROOT'] . '/pages/Notas';
$certPath = $certDir . '/certificado.pfx';

$action = $_GET['action'] ?? $_POST['action'] ?? 'status';

if ($action === 'status') {
    include_once __DIR__ . '/function.php';
    $resultado = verificarCertificadoDigital();
    echo json_encode(['success' => true, 'certificado' => $resultado]);
    exit;
}

if ($action === 'upload') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'mensagem' => 'Método não permitido']);
        exit;
    }

    if (!isset($_FILES['certificado']) || $_FILES['certificado']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'mensagem' => 'Nenhum arquivo enviado ou erro no upload']);
        exit;
    }

    $senha = $_POST['senha'] ?? '';
    if (empty($senha)) {
        echo json_encode(['success' => false, 'mensagem' => 'A senha do certificado é obrigatória']);
        exit;
    }

    $ext = strtolower(pathinfo($_FILES['certificado']['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pfx' && $ext !== 'p12') {
        echo json_encode(['success' => false, 'mensagem' => 'Formato inválido. Envie um arquivo .pfx ou .p12']);
        exit;
    }

    $tmpContent = file_get_contents($_FILES['certificado']['tmp_name']);
    $certData = [];

    if (!openssl_pkcs12_read($tmpContent, $certData, $senha)) {
        echo json_encode(['success' => false, 'mensagem' => 'Não foi possível ler o certificado. Verifique se a senha está correta.']);
        exit;
    }

    $certInfo = openssl_x509_parse($certData['cert']);
    $validTo = $certInfo['validTo_time_t'];

    if (time() > $validTo) {
        echo json_encode([
            'success' => false,
            'mensagem' => 'Este certificado já está vencido (validade: ' . date('d/m/Y', $validTo) . '). Envie um certificado válido.'
        ]);
        exit;
    }

    if (file_exists($certPath)) {
        $backupPath = $certDir . '/certificado_backup_' . date('Y-m-d_His') . '.pfx';
        copy($certPath, $backupPath);
    }

    if (!move_uploaded_file($_FILES['certificado']['tmp_name'], $certPath)) {
        echo json_encode(['success' => false, 'mensagem' => 'Erro ao salvar o certificado no servidor']);
        exit;
    }

    $emitirNotaPath = $certDir . '/emitir-nota.php';
    if (file_exists($emitirNotaPath)) {
        $conteudo = file_get_contents($emitirNotaPath);
        $conteudo = preg_replace("/\\\$senhaCert\s*=\s*'[^']*'/", "\$senhaCert = '$senha'", $conteudo);
        file_put_contents($emitirNotaPath, $conteudo);
    }

    $diasRestantes = floor(($validTo - time()) / 86400);

    echo json_encode([
        'success' => true,
        'mensagem' => 'Certificado atualizado com sucesso!',
        'certificado' => [
            'status' => 'valido',
            'validade' => date('d/m/Y H:i:s', $validTo),
            'dias' => $diasRestantes
        ]
    ]);
    exit;
}

http_response_code(400);
echo json_encode(['success' => false, 'mensagem' => 'Ação inválida']);

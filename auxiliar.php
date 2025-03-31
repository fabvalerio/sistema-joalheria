<?php
require_once __DIR__ . '/vendor/autoload.php';

use NFePHP\Common\Certificate;

$pfxPath = __DIR__ . '/certificado.pfx';
$senha = '123456';

try {
    $cert = Certificate::readPfx(file_get_contents($pfxPath), $senha);
    echo "✅ Certificado carregado com sucesso!\n\n";

    echo "🔍 Conteúdo completo do certificado:\n\n";
    print_r($cert);

} catch (Exception $e) {
    echo "❌ Erro ao ler certificado: " . $e->getMessage() . "\n";
}

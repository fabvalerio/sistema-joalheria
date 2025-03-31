<?php
require_once __DIR__ . '/vendor/autoload.php';

use NFePHP\Common\Certificate;
use NFePHP\NFe\Tools;
use NFePHP\DA\NFe\Danfe;

// Leitura do config com diagnóstico
$configJson = file_get_contents(__DIR__ . '/config.json');
$configData = json_decode($configJson, false);

// Diagnóstico de configuração
echo "Diagnóstico de Configuração:\n";
echo "CSC presente: " . (!empty($configData->CSC) ? 'Sim' : 'Não') . "\n";
echo "CSCId presente: " . (!empty($configData->CSCId) ? 'Sim' : 'Não') . "\n";

// Verificações adicionais
if (empty($configData->CSC) || empty($configData->CSCId)) {
    die("❌ Erro: CSC ou CSCId não configurados corretamente.\n");
}

try {
    $pfx = file_get_contents(__DIR__ . '/certificado.pfx');
    $senha = '123456';
    $certificado = Certificate::readPfx($pfx, $senha);

    $tools = new Tools($configJson, $certificado);
    $tools->model('65'); // NFC-e

    // Leitura do XML de exemplo com diagnóstico
    $xmlNfe = file_get_contents(__DIR__ . '/exemplo-nfce.xml');
    echo "Tamanho do XML: " . strlen($xmlNfe) . " bytes\n";

    // Tentar assinar o XML
    $xmlAssinado = $tools->signNFe($xmlNfe);
    echo "✅ XML assinado com sucesso!\n";

} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    print_r($e->getTrace());
}
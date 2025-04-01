<?php
require_once __DIR__ . '/vendor/autoload.php';

use NFePHP\Common\Certificate;
use NFePHP\NFe\Tools;
use NFePHP\NFe\Make;
use NFePHP\DA\NFe\Danfe;

$configPath = __DIR__ . '/config.json';
$certPath = __DIR__ . '/certificado.pfx';
$senhaCert = '123456'; // Substitua pela senha real

echo "✅ Iniciando emissão da NFC-e...\n";

// Verificações básicas
if (!file_exists($configPath)) die("❌ Arquivo config.json não encontrado.\n");
if (!file_exists($certPath)) die("❌ Certificado digital (.pfx) não encontrado.\n");

$configJson = file_get_contents($configPath);
$configData = json_decode($configJson);

echo "🔍 Diagnóstico:\n";
echo "CSC: " . (!empty($configData->CSC) ? '✔️' : '❌ Faltando') . "\n";
echo "CSCid: " . (!empty($configData->CSCid) ? '✔️' : '❌ Faltando') . "\n";

$certificado = Certificate::readPfx(file_get_contents($certPath), $senhaCert);
define('SOAP_1_2', 2);
$tools = new Tools($configJson, $certificado);
$tools->model('65'); // NFC-e

$nfe = new Make();

// 1. Identificação da NFe
$nfe->taginfNFe((object)['versao' => '4.00']);
$nfe->tagide((object)[
    'cUF' => 35,
    'cNF' => rand(10000000, 99999999),
    'natOp' => 'Venda ao Consumidor',
    'mod' => 65,
    'serie' => 1,
    'nNF' => rand(124, 9999),
    'dhEmi' => date('Y-m-d\TH:i:sP'),
    'tpNF' => 1,
    'idDest' => 1,
    'cMunFG' => 3552205,
    'tpImp' => 4,
    'tpEmis' => 1,
    'tpAmb' => $configData->tpAmb,
    'finNFe' => 1,
    'indFinal' => 1,
    'indPres' => 1,
    'procEmi' => 0,
    'verProc' => '1.0'
]);

// 2. Emitente
$nfe->tagemit((object)[
    'CNPJ' => $configData->cnpj,
    'xNome' => $configData->razaosocial,
    'xFant' => 'J.G. Sorocaba Joias',
    'IE' => $configData->ie,
    'CRT' => 1
]);

$nfe->tagenderEmit((object)[
    'xLgr' => 'Rua Dr. Braguinha',
    'nro' => '333',
    'xCpl' => 'Sala 12',
    'xBairro' => 'Centro',
    'cMun' => 3552205,
    'xMun' => 'Sorocaba',
    'UF' => 'SP',
    'CEP' => '18035300',
    'cPais' => '1058',
    'xPais' => 'Brasil',
    'fone' => '1533333333'
]);

// 3. Produto
$nfe->tagprod((object)[
    'item' => 1,
    'cProd' => '001',
    'cEAN' => 'SEM GTIN',
    'xProd' => 'Pulseira de Prata',
    'NCM' => '71131100',
    'CFOP' => '5101',
    'uCom' => 'UN',
    'qCom' => '1.0000',
    'vUnCom' => '100.00',
    'vProd' => '100.00',
    'cEANTrib' => 'SEM GTIN',
    'uTrib' => 'UN',
    'qTrib' => '1.0000',
    'vUnTrib' => '100.00',
    'indTot' => 1
]);

// 4. Impostos
$nfe->tagimposto((object)['item' => 1]);
$nfe->tagICMSSN((object)[
    'item' => 1,
    'orig' => 0,
    'CSOSN' => '102'
]);
$nfe->tagimposto((object)['item' => 1, 'vTotTrib' => 0.00]);

// 5. Totais
$nfe->tagICMSTot((object)[
    'vBC' => 0.00,
    'vICMS' => 0.00,
    'vProd' => 100.00,
    'vNF' => 100.00,
    'vPIS' => 0.00,
    'vCOFINS' => 0.00,
    'vST' => 0.00,
    'vDesc' => 0.00,
    'vOutro' => 0.00,
    'vTotTrib' => 0.00
]);

// 6. Transporte
$nfe->tagtransp((object)['modFrete' => 9]);

// 7. Pagamento
$nfe->tagpag((object)['vTroco' => 0.00]);
$nfe->tagdetPag((object)[
    'indPag' => 0,
    'tPag' => '01',
    'vPag' => 100.00
]);

// 8. Montar e assinar XML
$nfe->montaNFe();
$xml = $nfe->getXML();
$xmlAssinado = $tools->signNFe($xml);

echo "📄 XML gerado e assinado com sucesso.\n";

// 9. Transmitir para a SEFAZ
$resp = $tools->sefazEnviaLote([$xmlAssinado], 123, 1); // 1 = síncrono

// Salvar XML em disco para análise posterior
file_put_contents(__DIR__ . '/nfc-e-assinada.xml', $xmlAssinado);

// Verifica se há resposta válida com status 100 (Autorizado)
$statusAutorizado = isset($resp->cStat) && $resp->cStat == '100';

if (!$statusAutorizado) {
    echo "⚠️ A transmissão foi processada, mas não autorizada:\n";
    echo "Código: " . ($resp->cStat ?? '---') . "\n";
    echo "Mensagem: " . ($resp->xMotivo ?? 'Erro desconhecido') . "\n";

    echo "\n📤 XML Enviado:\n";
    echo htmlentities($xmlAssinado); // Mostra XML formatado na tela

    echo "\n\n📥 JSON da Resposta Completa:\n";
    echo json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

echo "🚀 NFC-e AUTORIZADA COM SUCESSO!\n";
echo "📌 Protocolo: {$resp->nProt}\n";

// 10. Geração do DANFE
$danfe = new Danfe($xmlAssinado);
$pdf = $danfe->render();

// Exibir em tela e permitir impressão
echo '<html><body style="text-align:center">';
echo '<h2>✅ NFC-e Autorizada</h2>';
echo '<p><strong>Protocolo:</strong> ' . $resp->nProt . '</p>';

// 11. Exibe o QR Code
$xmlObj = new SimpleXMLElement($xmlAssinado);
$qrCode = (string)$xmlObj->infNFeSupl->qrCode;

echo "<h3>QR Code da NFC-e</h3>";
echo "<img src='https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=" . urlencode($qrCode) . "'><br>";
echo "<p>Escaneie com o aplicativo da SEFAZ ou 'De Olho na Nota'</p>";

// 12. Botão para imprimir DANFE
file_put_contents(__DIR__ . '/danfe_nfce.pdf', $pdf);
echo '<p><a href="danfe_nfce.pdf" target="_blank">🖨️ Imprimir DANFE (PDF)</a></p>';

echo '</body></html>';
exit;
<?php
require_once __DIR__ . '/vendor/autoload.php';

use NFePHP\Common\Certificate;
use NFePHP\NFe\Tools;
use NFePHP\NFe\Make;
use NFePHP\DA\NFe\Danfe;

$configPath = __DIR__ . '/config.json';
$certPath = __DIR__ . '/certificado.pfx';
$senhaCert = '123456'; // Substitua pela senha real do seu .pfx

echo "✅ Iniciando emissão da NFC-e...\n";

// Validar arquivos necessários
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

// Montagem do XML com a classe Make
$nfe = new Make();

// 1. Identificação da nota
$nfe->taginfNFe((object)['versao' => '4.00']);

$nfe->tagide((object)[
    'cUF' => 35,
    'cNF' => rand(10000000, 99999999),
    'natOp' => 'Venda ao Consumidor',
    'mod' => 65,
    'serie' => 1,
    'nNF' => 123,
    'dhEmi' => date('Y-m-d\TH:i:sP'),
    'tpNF' => 1,
    'idDest' => 1,
    'cMunFG' => 3552205
,
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
    'cMun' => 3552205
,
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

// 6. Transporte obrigatório (mesmo para NFC-e sem frete)
$nfe->tagtransp((object)[
    'modFrete' => 9 // 9 = sem frete
]);

// 7. Pagamento
$nfe->tagpag((object)['vTroco' => 0.00]);
$nfe->tagdetPag((object)[
    'indPag' => 0,
    'tPag' => '01',
    'vPag' => 100.00
]);

// 8. Monta o XML
$nfe->montaNFe();
$xml = $nfe->getXML();
echo "📄 XML gerado com sucesso.\n";

// 9. Assina o XML
$xmlAssinado = $tools->signNFe($xml);
echo "🖊️  XML assinado com sucesso.\n";

// 10. Transmite para a SEFAZ
$resp = $tools->sefazEnviaLote([$xmlAssinado], 123, 1); // 1 = síncrono



if (!isset($resp->success) || !$resp->success) {
  echo "❌ Erro na transmissão da NFC-e:\n";
  echo "Código: " . ($resp->cStat ?? '---') . "\n";
  echo "Mensagem: " . ($resp->xMotivo ?? 'Erro desconhecido') . "\n";
  echo "\n📤 XML Enviado:\n" . $xmlAssinado . "\n";
  echo "\n📥 Resposta Completa:\n";
  print_r($resp);
  exit;
}


echo "🚀 NFC-e enviada com sucesso!\n";
echo "📌 Protocolo: {$resp->protocol}\n";

// 11. Geração do DANFE
$danfe = new Danfe($xmlAssinado);
$pdf = $danfe->render();

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename=\"danfe_nfce.pdf\"');
echo $pdf;
exit;

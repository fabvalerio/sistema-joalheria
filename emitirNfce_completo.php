<?php
require_once 'vendor/autoload.php'; // Ajuste o caminho conforme necessário

use NFePHP\Common\Certificate;
use NFePHP\NFe\Tools;
use NFePHP\NFe\Make;

$configPath = __DIR__ . '/config.json';
$certPath = __DIR__ . '/certificado.pfx';
$senhaCert = '123456'; // Substitua pela senha real do seu .pfx


date_default_timezone_set('America/Sao_Paulo'); // Adicionado



//echo "✅ Iniciando emissão da NFC-e...<br>";

// Validar arquivos necessários
if (!file_exists($configPath)) die("❌ Arquivo config.json não encontrado.<br>");
if (!file_exists($certPath)) die("❌ Certificado digital (.pfx) não encontrado.<br>");

$configJson = file_get_contents($configPath);
$configData = json_decode($configJson);

// echo "🔍 Diagnóstico:<br>";
// echo "CSC: " . (!empty($configData->CSC) ? '✔️' : '❌ Faltando') . "<br>";
// echo "CSCid: " . (!empty($configData->CSCid) ? '✔️' : '❌ Faltando') . "<br>";

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
    'nNF' => rand(124, 9999), // em vez de usar fixo 123

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
// echo "📄 XML gerado com sucesso.<br>";

// 9. Assina o XML
$xmlAssinado = $tools->signNFe($xml);
// echo "🖊️ XML assinado com sucesso.<br>";

// 10. Transmite para a SEFAZ
$respRaw = $tools->sefazEnviaLote([$xmlAssinado], 123, 1); // 1 = síncrono

// echo '<br><br> >>> respRaw: ';
// echo "<pre>";
// print_r($respRaw);
// echo "</pre>";

// Suponha que $respRaw seja o retorno da SEFAZ (em XML string)
$xml = simplexml_load_string($respRaw);

// Registra os namespaces existentes
$namespaces = $xml->getNamespaces(true);

// Acessa o conteúdo do corpo SOAP
$body = $xml->children($namespaces['soap'])->Body;

// Acessa o conteúdo do nfeResultMsg
$nfeResult = $body->children($namespaces[''])->nfeResultMsg;

// Acessa o conteúdo da NFe (namespace da NF-e)
$retEnviNFe = $nfeResult->children('http://www.portalfiscal.inf.br/nfe')->retEnviNFe;

// Extrai os dados principais
$tpAmb      = (string) $retEnviNFe->tpAmb;
$verAplic   = (string) $retEnviNFe->verAplic;
$cStat      = (string) $retEnviNFe->cStat;
$xMotivo    = (string) $retEnviNFe->xMotivo;
$dhRecbto   = (string) $retEnviNFe->dhRecbto;

// Dados da NF-e autorizada
$protNFe    = $retEnviNFe->protNFe;
$infProt    = $protNFe->infProt;

$chNFe      = (string) $infProt->chNFe;
$nProt      = (string) $infProt->nProt;
$digVal     = (string) $infProt->digVal;
$cStatNFe   = (string) $infProt->cStat;
$xMotivoNFe = (string) $infProt->xMotivo;

// Exibe os dados
// echo "Ambiente: $tpAmb<br>";
// echo "Aplicação: $verAplic<br>";
// echo "Status do Lote: $cStat - $xMotivo<br>";
// echo "Data de Recebimento: $dhRecbto<br><br>";

// echo "Chave da NF-e: $chNFe<br>";
// echo "Protocolo: $nProt<br>";
// echo "Hash: $digVal<br>";
// echo "Status NF-e: $cStatNFe - $xMotivoNFe<br>";

if ($xMotivoNFe != 'Autorizado o uso da NF-e') {
    echo "<br>❌ Erro na transmissão da NFC-e:<br>";
    echo "<br>Código: " . ($cStatNFe ?? '---') . "<br>";
    echo "<br>Mensagem: " . ($xMotivoNFe ?? 'Erro desconhecido') . "<br>";
    echo "<br>📤 XML Enviado:<br>" . $xmlAssinado . "<br>";
    echo "<br>📥 Resposta Completa:<br>";
    exit;
}

// echo "<br>🚀 NFC-e enviada com sucesso!<br>";

// 11. Salvar XML e fornecer link
$xmlFileName = 'nfce_' . $chNFe . '.xml';
$xmlDirPath = __DIR__ . '/xml/';
$xmlFilePath = $xmlDirPath . $xmlFileName;

// Certifique-se de que o diretório existe
if (!is_dir($xmlDirPath)) {
    mkdir($xmlDirPath, 0777, true);
}

// Salvar o XML
file_put_contents($xmlFilePath, $xmlAssinado);

// Gerar HTML para exibição de informações e links
echo "<div style='margin-top: 20px; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>";
echo "<h2 style='color: #2c7be5;'>📋 NFC-e Emitida com Sucesso</h2>";
echo "<p><strong>Status:</strong> " . $xMotivoNFe . "</p>";
echo "<p><strong>Chave de acesso:</strong> " . $chNFe . "</p>";
echo "<p><strong>Protocolo de autorização:</strong> " . $nProt . "</p>";
echo "<p><strong>Data/Hora:</strong> " . $dhRecbto . "</p>";

// Link para download do XML
echo "<div style='margin-top: 15px;'>";
echo "<a href='xml/" . $xmlFileName . "' download style='padding: 10px 15px; background-color: #2c7be5; color: white; text-decoration: none; border-radius: 4px;'>
      <span style='font-size: 1.2em;'>⬇️</span> Baixar XML da NFC-e</a>";
echo "</div>";

// Link para consulta no portal da SEFAZ
$urlConsulta = "https://www.homologacao.nfce.fazenda.sp.gov.br/NFCeConsultaPublica/Paginas/ConsultaQRCode.aspx?chNFe=" . $chNFe;
echo "<div style='margin-top: 15px;'>";
echo "<a href='" . $urlConsulta . "' target='_blank' style='padding: 10px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 4px;'>
      <span style='font-size: 1.2em;'>🔍</span> Consultar NFC-e no Portal da SEFAZ</a>";
echo "</div>";

// Informações sobre o DANFE
echo "<div style='margin-top: 20px; padding: 15px; background-color: #fff3cd; border-radius: 4px;'>";
echo "<p><strong>⚠️ Nota:</strong> O DANFE PDF não pôde ser gerado automaticamente devido à falta da extensão GD no servidor.</p>";
echo "<p>Para visualizar o DANFE, você pode:</p>";
echo "<ol>";
echo "<li>Consultar a NFC-e no portal da SEFAZ usando o link acima</li>";
echo "<li>Instalar a extensão PHP-GD no servidor (requer acesso ao servidor):<br>";
echo "<code>sudo apt-get install php-gd</code> (Ubuntu/Debian)<br>";
echo "<code>sudo yum install php-gd</code> (CentOS/RHEL)</li>";
echo "</ol>";
echo "</div>";

echo "</div>";

exit;
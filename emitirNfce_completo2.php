<?php
require_once 'vendor/autoload.php';

use NFePHP\Common\Certificate;
use NFePHP\NFe\Tools;
use NFePHP\NFe\Make;
use Dompdf\Dompdf;

// Caminhos
$configPath = 'config.json';
$certPath = 'certificado.pfx';
$senhaCert = '123456';

// 1. Verificações iniciais
echo "✅ Iniciando emissão da NFC-e...\n";
if (!file_exists($configPath)) die("❌ config.json não encontrado.\n");
if (!file_exists($certPath)) die("❌ Certificado digital não encontrado.\n");

// 2. Carregar configurações
$configJson = file_get_contents($configPath);
$configData = json_decode($configJson);
echo "🔍 Diagnóstico:\n";
echo "CSC: " . (!empty($configData->CSC) ? '✔' : '❌') . "\n";
echo "CSCid: " . (!empty($configData->CSCid) ? '✔' : '❌') . "\n";

// 3. Inicializar ferramentas
if (!defined('SOAP_1_2')) define('SOAP_1_2', 2);
$cert = Certificate::readPfx(file_get_contents($certPath), $senhaCert);
$tools = new Tools($configJson, $cert);
$tools->model('65');

// 4. Montar a NFC-e
$nfe = new Make();
$nfe->taginfNFe((object)['versao' => '4.00']);
$nfe->tagide((object)[
    'cUF' => 35, 'cNF' => rand(10000000, 99999999), 'natOp' => 'Venda ao Consumidor',
    'mod' => 65, 'serie' => 1, 'nNF' => rand(1000,9999), 'dhEmi' => date('Y-m-d\TH:i:sP'),
    'tpNF' => 1, 'idDest' => 1, 'cMunFG' => 3552205, 'tpImp' => 4, 'tpEmis' => 1,
    'tpAmb' => $configData->tpAmb, 'finNFe' => 1, 'indFinal' => 1, 'indPres' => 1,
    'procEmi' => 0, 'verProc' => '1.0'
]);
$nfe->tagemit((object)[
    'CNPJ' => $configData->cnpj, 'xNome' => $configData->razaosocial,
    'xFant' => 'J.G. Sorocaba Joias', 'IE' => $configData->ie, 'CRT' => 1
]);
$nfe->tagenderEmit((object)[
    'xLgr' => 'Rua Dr. Braguinha', 'nro' => '333', 'xCpl' => 'Sala 12', 'xBairro' => 'Centro',
    'cMun' => 3552205, 'xMun' => 'Sorocaba', 'UF' => 'SP', 'CEP' => '18035300',
    'cPais' => '1058', 'xPais' => 'Brasil', 'fone' => '1533333333'
]);
$nfe->tagprod((object)[
    'item' => 1, 'cProd' => '001', 'cEAN' => 'SEM GTIN', 'xProd' => 'Pulseira de Prata',
    'NCM' => '71131100', 'CFOP' => '5101', 'uCom' => 'UN', 'qCom' => '1.0000',
    'vUnCom' => '100.00', 'vProd' => '100.00', 'cEANTrib' => 'SEM GTIN',
    'uTrib' => 'UN', 'qTrib' => '1.0000', 'vUnTrib' => '100.00', 'indTot' => 1
]);
$nfe->tagimposto((object)['item' => 1]);
$nfe->tagICMSSN((object)['item' => 1, 'orig' => 0, 'CSOSN' => '102']);
$nfe->tagimposto((object)['item' => 1, 'vTotTrib' => 0.00]);
$nfe->tagICMSTot((object)[
    'vBC' => 0.00, 'vICMS' => 0.00, 'vProd' => 100.00, 'vNF' => 100.00,
    'vPIS' => 0.00, 'vCOFINS' => 0.00, 'vST' => 0.00, 'vDesc' => 0.00,
    'vOutro' => 0.00, 'vTotTrib' => 0.00
]);
$nfe->tagtransp((object)['modFrete' => 9]);
$nfe->tagpag((object)['vTroco' => 0.00]);
$nfe->tagdetPag((object)[ 'indPag' => 0, 'tPag' => '01', 'vPag' => 100.00 ]);

// 5. Assinar XML
$nfe->montaNFe();
$xml = $nfe->getXML();
$xmlAssinado = $tools->signNFe($xml);
file_put_contents('nfc-e-assinada.xml', $xmlAssinado);
echo "📄 XML gerado e assinado com sucesso.\n";

// 6. Testar conexão com SEFAZ
echo "🔌 Testando conexão com a SEFAZ...\n";
$statusRaw = $tools->sefazStatus();
file_put_contents('status_sefaz_raw.xml', $statusRaw);
$status = simplexml_load_string($statusRaw);
$ns = $status->getNamespaces(true);
$body = $status->children($ns['soap'])->Body;
$nfeResult = $body->children($ns[''])->nfeResultMsg;
$ret = $nfeResult->children('')->retConsStatServ;
echo "✅ Conexão com SEFAZ funcionando\n";
echo "Status do serviço: {$ret->cStat} - {$ret->xMotivo}\n";

// 7. Enviar e verificar retorno
echo "📤 Enviando NFC-e para SEFAZ...\n";
$resp = $tools->sefazEnviaLote([$xmlAssinado], rand(1000, 9999), 1);

// Salva resposta para análise
file_put_contents('resposta_sefaz_raw.xml', $resp);

// Trata a resposta
if (is_string($resp)) {
    file_put_contents('resposta_sefaz_debug.txt', $resp); // Salva resposta bruta

    libxml_use_internal_errors(true);
    $xmlResp = simplexml_load_string($resp);

    if ($xmlResp === false) {
        $erros = libxml_get_errors();
        $msg = "❌ Erro ao interpretar a resposta da SEFAZ. Detalhes:\n";
        foreach ($erros as $erro) {
            $msg .= trim($erro->message) . "\n";
        }
        libxml_clear_errors();
        throw new Exception($msg);
    }

    $ns = $xmlResp->getNamespaces(true);
    $body = $xmlResp->children($ns['soap'])->Body ?? null;
    if (!$body) {
        throw new Exception("❌ Não foi possível localizar o nó <soap:Body> na resposta.");
    }

    $nfeResult = $body->children($ns[''])->nfeResultMsg ?? null;
    if (!$nfeResult) {
        throw new Exception("❌ Não foi possível localizar o nó <nfeResultMsg> na resposta.");
    }

    $ret = $nfeResult->children('')->retEnviNFe ?? null;
    if (!$ret) {
        throw new Exception("❌ Não foi possível localizar o nó <retEnviNFe> na resposta.");
    }

    if (isset($ret->protNFe->infProt)) {
        $prot = $ret->protNFe->infProt;
    } else {
        throw new Exception("❌ Estrutura da resposta inválida.");
    }

    $cStat = (string)$prot->cStat;
    $xMotivo = (string)$prot->xMotivo;
    $protocolo = (string)$prot->nProt;
    $chave = (string)$prot->chNFe;

    if ($cStat != '100') {
        throw new Exception("NFC-e não autorizada: [$cStat] $xMotivo");
    }

    echo "🚀 NFC-e autorizada com sucesso!\n📌 Protocolo: $protocolo\n🔑 Chave: $chave\n";
} else {
    throw new Exception("❌ Resposta inesperada da SEFAZ (não é string).");
}


// 8. Obter QR Code
$xmlObj = simplexml_load_string($xmlAssinado);
$qrCode = (string)$xmlObj->infNFeSupl->qrCode;

// 9. Gerar DANFE simplificado com DOMPDF
$htmlDanfe = "
<html><head><meta charset='UTF-8'></head><body style='font-family:sans-serif;'>
    <h2 style='text-align:center;'>NFC-e Autorizada</h2>
    <p><strong>Chave de Acesso:</strong><br>" . chunk_split($chave, 4, ' ') . "</p>
    <p><strong>Protocolo:</strong><br>$protocolo</p>
    <p><strong>Data/Hora:</strong><br>" . date('d/m/Y H:i:s') . "</p>
    <p><strong>QR Code:</strong><br><img src='https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=" . urlencode($qrCode) . "'></p>
    <p style='text-align:center;'>Use o app da SEFAZ ou 'De Olho na Nota'</p>
</body></html>";

$dompdf = new Dompdf();
$dompdf->loadHtml($htmlDanfe);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
file_put_contents('danfe_nfce_simplificado.pdf', $dompdf->output());
echo "✓ DANFE NFC-e simplificado gerado com sucesso!\n";

// 10. Exibir todos os dados da nota (após autorização)
echo "\n📋 DADOS DA NFC-e\n";

echo "Emitente: {$xmlObj->emit->xNome} ({$xmlObj->emit->CNPJ})\n";
echo "Endereço: {$xmlObj->emit->enderEmit->xLgr}, {$xmlObj->emit->enderEmit->nro} - {$xmlObj->emit->enderEmit->xBairro}, {$xmlObj->emit->enderEmit->xMun}/{$xmlObj->emit->enderEmit->UF}\n";
echo "Inscrição Estadual: {$xmlObj->emit->IE}\n";

echo "\n🧾 Produtos:\n";
foreach ($xmlObj->det as $item) {
    $xProd = $item->prod->xProd;
    $qCom = $item->prod->qCom;
    $vUnCom = $item->prod->vUnCom;
    $vProd = $item->prod->vProd;
    echo "- {$xProd} | Qtde: {$qCom} | Valor Unitário: R$ {$vUnCom} | Total: R$ {$vProd}\n";
}

echo "\n💰 Totais:\n";
$tot = $xmlObj->total->ICMSTot;
echo "Total Produtos: R$ {$tot->vProd}\n";
echo "Descontos: R$ {$tot->vDesc}\n";
echo "Total NF-e: R$ {$tot->vNF}\n";

echo "\n💳 Pagamento:\n";
foreach ($xmlObj->pag->detPag as $pg) {
    echo "- Tipo: {$pg->tPag} | Valor: R$ {$pg->vPag}\n";
}

echo "\n🔒 QR Code: {$qrCode}\n";
echo "🔑 Chave de Acesso: {$chave}\n";
echo "📌 Protocolo: {$protocolo}\n";
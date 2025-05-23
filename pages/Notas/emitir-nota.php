<?php

require_once 'vendor/autoload.php';

use NFePHP\Common\Certificate;
use NFePHP\NFe\Tools;
use NFePHP\NFe\Make;
use Dompdf\Dompdf;

$configPath = __DIR__ . '/config.json';
$certPath = __DIR__ . '/certificado.pfx';
$senhaCert = '123456';

//dados do pedido
use App\Models\Pedidos\Controller;

$controller = new Controller();
$id = $link[3] ?? null;

$dados = $controller->ver($id);
$pedido = $dados['pedido'];
$itens = $dados['itens'];
// fim dados do pedido

//echo "\u2705 Iniciando emissão da NFC-e...\n";
if (!file_exists($configPath)) die("\u274c config.json não encontrado.\n");
if (!file_exists($certPath)) die("\u274c Certificado digital não encontrado.\n");

$configJson = file_get_contents($configPath);
$configData = json_decode($configJson);
//echo "🔍 Diagnóstico:\n";
//echo "CSC: " . (!empty($configData->CSC) ? '✔️' : '❌') . "\n";
//echo "CSCid: " . (!empty($configData->CSCid) ? '✔️' : '❌') . "\n";

if (!defined('SOAP_1_2')) define('SOAP_1_2', 2);
$cert = Certificate::readPfx(file_get_contents($certPath), $senhaCert);
$tools = new Tools($configJson, $cert);
$tools->model('65');

$numeroIdVenda = rand(10000000, 99999999);

$nfe = new Make();
$nfe->taginfNFe((object)['versao' => '4.00']);
$nfe->tagide((object)[
  'cUF' => 35,
  'cNF' => $numeroIdVenda,
  'natOp' => 'Venda ao Consumidor',
  'mod' => 65,
  'serie' => 1,
  'nNF' => rand(1000, 9999),
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
$nfe->tagemit((object)[
  'CNPJ' => $configData->cnpj,
  'xNome' => $configData->razaosocial,
  'xFant' => 'J.G. Sorocaba Joias',
  'IE' => $configData->ie,
  'CRT' => 1
]);
$nfe->tagenderEmit((object)[
  'xLgr' => 'Rua Dr. Braguinha',
  'nro' => '338',
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
$val_anterior = 0;
foreach ($itens as $item): 
  $val_total = ($item['valor_unitario'] * $item['quantidade']) + $val_anterior;
  $val_anterior = $val_total;
$nfe->tagprod((object)[
  'item' => 1,
  'cProd' => $item['produto_id'],
 
  'cEAN' => 'SEM GTIN',
  'xProd' => $item['nome_produto'],

  'NCM' => '71131100',
  'CFOP' => '5101',
  'uCom' => 'UN',
  'qCom' => '1.0000',
  'vUnCom' => $item['valor_unitario'] * $item['quantidade'],
  'vProd' => $item['valor_unitario'] * $item['quantidade'],
  
  'cEANTrib' => 'SEM GTIN',
  'uTrib' => 'UN',
  'qTrib' => '1.0000',
  'vUnTrib' => $item['valor_unitario'] * $item['quantidade'],
  'indTot' => 1

]);
endforeach; 
$nfe->tagimposto((object)['item' => 1]);
$nfe->tagICMSSN((object)['item' => 1, 'orig' => 0, 'CSOSN' => '102']);
$nfe->tagimposto((object)['item' => 1, 'vTotTrib' => 0.00]);
$nfe->tagICMSTot((object)[
  'vBC' => 0.00,
  'vICMS' => 0.00,
  'vProd' => $val_total,
  'vNF' => $val_total,
  'vPIS' => 0.00,
  'vCOFINS' => 0.00,
  'vST' => 0.00,
  'vDesc' => 0.00,
  'vOutro' => 0.00,
  'vTotTrib' => 0.00
]);
$nfe->tagtransp((object)['modFrete' => 9]);
$nfe->tagpag((object)['vTroco' => 0.00]);
$nfe->tagdetPag((object)['indPag' => 0, 'tPag' => '01', 'vPag' => $val_total]);

$nfe->montaNFe();
$xml = $nfe->getXML();
$xmlAssinado = $tools->signNFe($xml);
file_put_contents(__DIR__ . '/xml/nfc-e-assinada[' . $numeroIdVenda . '].xml', $xmlAssinado);
//echo "📄 XML gerado e assinado com sucesso.\n";

$statusRaw = $tools->sefazStatus();
$status = simplexml_load_string($statusRaw);
$ns = $status->getNamespaces(true);
$body = $status->children($ns['soap'])->Body;
$ret = $body->children($ns[''])->nfeResultMsg->children('')->retConsStatServ;
//echo "✅ Conexão com SEFAZ funcionando\n";
//echo "Status do serviço: {$ret->cStat} - {$ret->xMotivo}\n";

$resp = $tools->sefazEnviaLote([$xmlAssinado], rand(1000, 9999), 1);
$xmlResp = simplexml_load_string($resp);
$ns = $xmlResp->getNamespaces(true);
$ret = $xmlResp->children($ns['soap'])->Body->children($ns[''])->nfeResultMsg->children('')->retEnviNFe;
$prot = $ret->protNFe->infProt;
$cStat = (string)$prot->cStat;
$protocolo = (string)$prot->nProt;
$chave = (string)$prot->chNFe;
//echo "🚀 NFC-e autorizada com sucesso!\n📌 Protocolo: $protocolo\n🔑 Chave: $chave\n";

$xmlProc = new DOMDocument('1.0', 'UTF-8');
$xmlProc->formatOutput = true;
$proc = $xmlProc->createElement('nfeProc');
$proc->setAttribute('xmlns', 'http://www.portalfiscal.inf.br/nfe');
$proc->setAttribute('versao', '4.00');
$domNfe = new DOMDocument();
$domNfe->loadXML($xmlAssinado);
$nodeNfe = $xmlProc->importNode($domNfe->documentElement, true);
$proc->appendChild($nodeNfe);
$domProt = new DOMDocument();
$domProt->loadXML($prot->asXML());
$nodeProt = $xmlProc->importNode($domProt->documentElement, true);
$proc->appendChild($nodeProt);
$xmlProc->appendChild($proc);
file_put_contents(__DIR__ . '/xml/nfc-e-autorizada[' . $numeroIdVenda . '].xml', $xmlProc->saveXML());

$xmlObj = simplexml_load_string($xmlProc->saveXML());

$emitente = $xmlObj->NFe->infNFe->emit;
$enderEmit = $emitente->enderEmit;
$totais = $xmlObj->NFe->infNFe->total->ICMSTot;
$pagamentos = $xmlObj->NFe->infNFe->pag->detPag;
$produtos = $xmlObj->NFe->infNFe->det;

$chaveNum = preg_replace('/[^0-9]/', '', $chave);
$paramStr = "$chaveNum|2|{$configData->tpAmb}|{$configData->CSCid}";
$hash = hash('sha1', $paramStr . $configData->CSC);
$baseUrl = $configData->tpAmb == 1
  ? 'https://www.nfce.fazenda.sp.gov.br/qrcode.aspx'
  : 'https://www.homologacao.nfce.fazenda.sp.gov.br/NFCeConsultaPublica/Paginas/ConsultaQRCode.aspx';
$qrCodeUrl = "$baseUrl?p={$paramStr}|{$hash}";
$qrCodeParaImagem = str_replace('|', '%7C', $qrCodeUrl);


//gerar qrcode
include "phpqrcode/qrlib.php";
$text = $qrCodeParaImagem;
// $file = "{$url}pages/Notas/qrcode/qrcode[{$numeroIdVenda}].png";
$file = __DIR__ . "/qrcode/qrcode[{$numeroIdVenda}].png";
QRcode::png($text, $file, QR_ECLEVEL_H, 10);
$file = "{$url}pages/Notas/qrcode/qrcode[{$numeroIdVenda}].png";

//fim gerar qr code

?>

<style>
  .nota {
    font-family: monospace;
    background-color: #fff;
    padding: 20px;
    width: 320px;
    margin: auto;
    border: 1px solid #000;
    font-size: 11px;

  }

  .center {
    text-align: center;
    word-break: break-word;
    /* quebra palavra longa */
    overflow-wrap: break-word;
    /* suporte adicional */
  }

  .line {
    border-top: 1px dashed #000;
    margin: 8px 0;
  }

  .right {
    text-align: right;
  }

  .bold {
    font-weight: bold;
  }

  .item-table {
    width: 100%;
  }

  .item-table td {
    padding: 2px 0;
  }
</style>
<div class="nota">
  <div class="center bold"><?= $emitente->xFant ?></div>
  <div class="center"><?= "{$enderEmit->xLgr}, {$enderEmit->nro}" ?><?= isset($enderEmit->xCpl) ? " - {$enderEmit->xCpl}" : '' ?></div>
  <div class="center"><?= "{$enderEmit->xBairro} - {$enderEmit->xMun}/{$enderEmit->UF} - CEP: " . preg_replace('/(\d{5})(\d{3})/', '$1-$2', $enderEmit->CEP) ?></div>
  <div class="center">CNPJ: <?= $emitente->CNPJ ?> - IE: <?= $emitente->IE ?></div>
  <div class="center line"></div>
  <div class="center">NFC-e - Nota Fiscal de Consumidor Eletrônica</div>
  <div class="center">Série <?= $xmlObj->NFe->infNFe->ide->serie ?> - Nº <?= $xmlObj->NFe->infNFe->ide->nNF ?></div>
  <div class="center">Data: <?= date('d/m/Y H:i:s', strtotime($xmlObj->NFe->infNFe->ide->dhEmi)) ?></div>
  <div class="center line"></div>
  <div>CPF/CNPJ do Consumidor: <?= htmlspecialchars(
                                  !empty($pedido['cpf'])
                                    ? $pedido['cpf']
                                    : ($pedido['cpf'] ?? 'Não informado')
                                ) ?></div>
  <div class="line"></div>

  <table class="item-table">
    <tr>
      <th>COD</th>
      <th>DESC</th>
      <th class="right">VL ITEM R$ </th>
    </tr>
    <?php foreach ($produtos as $item): ?>
      <?php $prod = $item->prod; ?>
      <tr>
        <td><?= $prod->cProd ?></td>
        <td><?= $prod->xProd ?></td>
        <td class="right"><?= number_format((float)$prod->vProd, 2, ',', '.') ?></td>
      </tr>
    <?php endforeach; ?>
  </table>

  <div class="line"></div>
  <div class="right bold">TOTAL R$: <?= number_format((float)$totais->vNF, 2, ',', '.') ?></div>

  <?php foreach ($pagamentos as $pg): ?>
    <!-- <div class="right"><?= $pg->tPag == '01' ? 'Cartão de Débito' : 'Pagamento' ?>: <?= number_format((float)$pg->vPag, 2, ',', '.') ?></div> -->
  <?php endforeach; ?>

  <div class="line"></div>
  <div>ICMS: Regime Simples Nacional - CSOSN 102</div>
  <div>Valor dos Tributos Aproximado: R$ 0,00</div>
  <div class="line"></div>
  <div class="center">Forma de Pagamento: <?= $pagamentos[0]->tPag == '01' ? 'Cartão de Débito' : 'Outro' ?></div>
  <div class="center">Troco: R$ 0,00</div>
  <div class="line"></div>
  <div class="center">Protocolo de Autorização:</div>
  <div class="center"><?= $protocolo ?></div>
  <div class="center"><?= $chave ?></div>
  <div class="center">
    <img src="<?= $file ?>" style="width:150px;" alt="QR Code">
  </div>
  <div class="center">Consulte a validade em:</div>
  <div class="center"><a href="<?= $qrCodeUrl ?>" target="_blank">nfce.fazenda.sp.gov.br</a></div>

</div>

<div class="d-flex justify-content-center mt-3 mb-3">
    <button onclick="imprimirNota()" class="btn btn-primary">
        <i class="fas fa-print"></i> Imprimir Nota
    </button>
</div>

<script>
function imprimirNota() {
    // Cria um clone da div.nota
    const conteudo = document.querySelector('.nota').cloneNode(true);
    
    // Cria uma nova janela
    const janela = window.open('', '', 'width=800,height=600');
    
    // Adiciona o estilo necessário
    janela.document.write('<html><head>');
    janela.document.write('<style>');
    janela.document.write(`
        body { font-family: monospace; }
        .nota { width: 300px; margin: 0 auto; padding: 10px; font-size: 11px; }
        .center { text-align: center; }
        .right { text-align: right; }
        .line { border-bottom: 1px dashed #000; margin: 5px 0; }
        .bold { font-weight: bold; }
        .item-table { width: 100%; }
        .item-table th, .item-table td { text-align: left; padding: 2px; }
        .item-table th:last-child, .item-table td:last-child { text-align: right; }
    `);
    janela.document.write('</style></head><body>');
    
    // Adiciona o conteúdo
    janela.document.write(conteudo.outerHTML);
    janela.document.write('</body></html>');
    
    // Fecha o documento
    janela.document.close();
    
    // Imprime após carregar todo o conteúdo
    janela.onload = function() {
        janela.focus();
        janela.print();
        janela.close();
    }
}
</script>

<?php die(); ?>
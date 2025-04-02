<?php
// Caminho do XML
$xmlFile = 'xml/nfc-e-assinada[99722026].xml';

// Carrega o XML
$xml = simplexml_load_file($xmlFile);
$namespaces = $xml->getNamespaces(true);
$xml->registerXPathNamespace('n', $namespaces['']);

$infNFe = $xml->xpath('//n:infNFe')[0];
$emitente = $infNFe->emit;
$produto = $infNFe->det->prod;
$total = $infNFe->total->ICMSTot;
$pagamento = $infNFe->pag->detPag;
$qrCode = $xml->infNFeSupl->qrCode;

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Visualizar NFC-e</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    h1 { color: #333; }
    .bloco { margin-bottom: 20px; padding: 10px; border: 1px solid #ccc; border-radius: 10px; }
    strong { display: inline-block; width: 150px; }
  </style>
</head>
<body>
  <h1>NFC-e Assinada - Pedido 99722026</h1>

  <div class="bloco">
    <h2>Emitente</h2>
    <p><strong>Nome:</strong> <?= $emitente->xNome ?></p>
    <p><strong>Fantasia:</strong> <?= $emitente->xFant ?></p>
    <p><strong>CNPJ:</strong> <?= $emitente->CNPJ ?></p>
    <p><strong>Endereço:</strong> <?= $emitente->enderEmit->xLgr ?>, <?= $emitente->enderEmit->nro ?> - <?= $emitente->enderEmit->xBairro ?>, <?= $emitente->enderEmit->xMun ?>/<?= $emitente->enderEmit->UF ?></p>
  </div>

  <div class="bloco">
    <h2>Produto</h2>
    <p><strong>Código:</strong> <?= $produto->cProd ?></p>
    <p><strong>Descrição:</strong> <?= $produto->xProd ?></p>
    <p><strong>Quantidade:</strong> <?= $produto->qCom ?></p>
    <p><strong>Valor Unitário:</strong> R$ <?= number_format((float)$produto->vUnCom, 2, ',', '.') ?></p>
    <p><strong>Valor Total:</strong> R$ <?= number_format((float)$produto->vProd, 2, ',', '.') ?></p>
  </div>

  <div class="bloco">
    <h2>Totais</h2>
    <p><strong>Valor da Nota:</strong> R$ <?= number_format((float)$total->vNF, 2, ',', '.') ?></p>
    <p><strong>Forma de Pagamento:</strong> <?= $pagamento->tPag == '01' ? 'Dinheiro' : 'Outros' ?></p>
    <p><strong>Valor Pago:</strong> R$ <?= number_format((float)$pagamento->vPag, 2, ',', '.') ?></p>
  </div>

  <div class="bloco">
    <h2>QR Code</h2>
    <img src="https://api.qrserver.com/v1/create-qr-code/?data=<?= urlencode($qrCode) ?>&size=200x200" alt="QR Code NFC-e">
    <p><a href="<?= $qrCode ?>" target="_blank"><?= $qrCode ?></a></p>
  </div>

</body>
</html>

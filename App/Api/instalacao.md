
API gratuita fornecida pelo governo brasileiro para emissão de NFC-e (Nota Fiscal do Consumidor Eletrônica), mas ela não é diretamente oferecida por um único órgão federal. A emissão de NFC-e é gerenciada pelos **Estados** e pelo  **Distrito Federal** , cada um com sua própria solução.

### **Onde encontrar APIs gratuitas para NFC-e?**

1. **SEFAZ de cada Estado**
   Cada Secretaria da Fazenda (SEFAZ) estadual oferece um ambiente próprio para emissão de NFC-e, incluindo APIs e sistemas web. Algumas disponibilizam acesso gratuito para contribuintes, enquanto outras exigem credenciamento ou uso de sistemas terceiros (ACCs).
   * **Exemplos de ambientes estaduais** :
   * **SP** : [https://www.nfce.fazenda.sp.gov.br](https://www.nfce.fazenda.sp.gov.br/)
   * **RJ** : [https://www.nfce.rj.gov.br](https://www.nfce.rj.gov.br/)
   * **MG** : [https://nfce.fazenda.mg.gov.br](https://nfce.fazenda.mg.gov.br/)
   * **RS** : [https://www.sefaz.rs.gov.br](https://www.sefaz.rs.gov.br/)
2. **Projeto Nacional NFC-e (Modelo 65)**
   O governo federal padronizou a NFC-e (Modelo 65) para simplificar a adoção pelos estados. A documentação técnica está disponível no portal da  **Nota Fiscal Eletrônica** :
   * [https://www.gov.br/nfe](https://www.gov.br/nfe)
3. **API do Ambiente Nacional (AN)**
   Para consulta e validação de NFC-e, o **Ambiente Nacional** da Receita Federal oferece endpoints públicos:
   * [https://www.nfe.fazenda.gov.br/portal/principal.aspx](https://www.nfe.fazenda.gov.br/portal/principal.aspx)

### **Como acessar a API gratuitamente?**

* **Credenciamento na SEFAZ** :
  Você precisa se credenciar como emitente de NFC-e na Secretaria da Fazenda do seu estado.
* **Usar Emissor Gratuito** :
  Alguns estados oferecem emissores web gratuitos (ex.: SP, MG).
* **API com Certificado Digital** :
  A maioria das APIs exige certificado digital (e-CPF ou e-CNPJ, modelo A1 ou A3).

### **Alternativas sem custo direto (mas com limitações)**

* **Sistema próprio** : Desenvolva integração direta com a SEFAZ usando as especificações técnicas.
* **Soluções de terceiros** : Alguns softwares gratuitos (como "NFC-e Easy") oferecem emissão limitada.

### **Conclusão**

Não há uma **API única e totalmente gratuita** para todos os estados, mas cada SEFAZ disponibiliza suas próprias ferramentas. Consulte o portal da sua Secretaria da Fazenda para obter a documentação oficial.

Precisa de ajuda para um estado específico? Posso buscar detalhes!

poderia gerar o codigo do estado de SP em php com a API

Aqui está um exemplo básico em **PHP** para emitir uma **NFC-e no estado de São Paulo** usando a API da  **SEFAZ-SP** . Este código demonstra como enviar uma requisição para o **Web Service** da NFC-e, mas lembre-se de que você precisará de:

* **Certificado Digital** (e-CNPJ ou e-CPF, formato `.pfx` ou `.pem`).
* **Credenciais de acesso** (token ou usuário/senha, se aplicável).
* **Dados válidos da NFC-e** (produtos, cliente, etc.).

---

### **1. Configuração Inicial (Certificado e Ambiente)**

php

Copy

```php
<?php
// Dados do certificado digital (caminho e senha)
$certificado_pfx = '/caminho/para/seu/certificado.pfx';
$senha_certificado = 'sua_senha';

// Configurações da SEFAZ-SP (Homologação ou Produção)
$ambiente = 'homologacao'; // Ou 'producao'
$url = ($ambiente == 'homologacao') 
    ? 'https://homologacao.nfce.fazenda.sp.gov.br/NFCeWS/WS/NfeAutorizacao.asmx' 
    : 'https://nfce.fazenda.sp.gov.br/NFCeWS/WS/NfeAutorizacao.asmx';

// Dados da NFC-e (XML mínimo exemplo)
$xml_nfce = '
<enviNFe xmlns="http://www.portalfiscal.inf.br/nfe" versao="4.00">
    <idLote>1</idLote>
    <NFe xmlns="http://www.portalfiscal.inf.br/nfe">
        <infNFe Id="NFe123456789" versao="4.00">
            <ide>
                <cUF>35</cUF> <!-- Código de SP -->
                <natOp>Venda de produto</natOp>
                <mod>65</mod> <!-- Modelo NFC-e -->
                <serie>1</serie>
                <nNF>123456</nNF>
                <dhEmis>' . date('Y-m-d\TH:i:sP') . '</dhEmis>
                <tpNF>1</tpNF> <!-- 1=Saída -->
                <idDest>1</idDest> <!-- 1=Consumidor Final -->
                <tpAmb>2</tpAmb> <!-- 2=Homologação -->
                <tpEmis>1</tpEmis> <!-- 1=Normal -->
                <cDV>1</cDV>
                <finNFe>1</finNFe> <!-- 1=Normal -->
                <indFinal>1</indFinal> <!-- 1=Operação final -->
                <indPres>1</indPres> <!-- 1=Presencial -->
            </ide>
            <emit>
                <CNPJ>12345678000199</CNPJ>
                <xNome>Empresa Teste Ltda</xNome>
                <xFant>Loja Teste</xFant>
                <enderEmit>
                    <xLgr>Rua Teste</xLgr>
                    <nro>123</nro>
                    <xBairro>Centro</xBairro>
                    <cMun>3550308</cMun> <!-- Código da cidade (Ex: São Paulo) -->
                    <xMun>SÃO PAULO</xMun>
                    <UF>SP</UF>
                    <CEP>01001000</CEP>
                </enderEmit>
                <IE>123456789</IE>
                <IM>12345</IM>
                <CNAE>6201500</CNAE>
                <CRT>1</CRT> <!-- 1=Simples Nacional -->
            </emit>
            <dest>
                <CNPJ>99999999000199</CNPJ> <!-- Ou CPF para pessoa física -->
                <xNome>Cliente Teste</xNome>
                <enderDest>
                    <xLgr>Rua Cliente</xLgr>
                    <nro>456</nro>
                    <xBairro>Centro</xBairro>
                    <cMun>3550308</cMun>
                    <xMun>SÃO PAULO</xMun>
                    <UF>SP</UF>
                </enderDest>
                <indIEDest>9</indIEDest> <!-- 9=Não contribuinte -->
            </dest>
            <det nItem="1">
                <prod>
                    <cProd>001</cProd>
                    <xProd>Produto Teste</xProd>
                    <NCM>99999999</NCM>
                    <CFOP>5102</CFOP>
                    <uCom>UN</uCom>
                    <qCom>1.0000</qCom>
                    <vUnCom>100.00</vUnCom>
                    <vProd>100.00</vProd>
                </prod>
                <imposto>
                    <ICMS>
                        <ICMS00>
                            <orig>0</orig>
                            <CST>00</CST>
                            <modBC>3</modBC>
                            <vBC>100.00</vBC>
                            <pICMS>18.00</pICMS>
                            <vICMS>18.00</vICMS>
                        </ICMS00>
                    </ICMS>
                    <PIS>
                        <PISNT>
                            <CST>07</CST>
                        </PISNT>
                    </PIS>
                    <COFINS>
                        <COFINSNT>
                            <CST>07</CST>
                        </COFINSNT>
                    </COFINS>
                </imposto>
            </det>
            <total>
                <ICMSTot>
                    <vBC>100.00</vBC>
                    <vICMS>18.00</vICMS>
                    <vNF>100.00</vNF>
                    <vTotTrib>18.00</vTotTrib>
                </ICMSTot>
            </total>
            <transp>
                <modFrete>9</modFrete> <!-- 9=Sem frete -->
            </transp>
            <pag>
                <detPag>
                    <tPag>01</tPag> <!-- 01=Dinheiro -->
                    <vPag>100.00</vPag>
                </detPag>
            </pag>
            <infAdic>
                <infCpl>NFC-e teste, sem valor fiscal.</infCpl>
            </infAdic>
        </infNFe>
    </NFe>
</enviNFe>';

// Assinar o XML (requer OpenSSL e biblioteca como "NFePHP" ou "CertificateUtility")
// Aqui é um exemplo simplificado (na prática, use uma biblioteca específica)
$xml_assinado = assinarXML($xml_nfce, $certificado_pfx, $senha_certificado);

// Enviar para a SEFAZ-SP via SOAP
$client = new SoapClient($url, [
    'local_cert' => $certificado_pfx,
    'passphrase' => $senha_certificado,
    'stream_context' => stream_context_create([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ])
]);

try {
    $response = $client->nfeAutorizacaoLote([
        'nfeDadosMsg' => $xml_assinado
    ]);
    echo "<pre>Resposta SEFAZ-SP:\n" . print_r($response, true) . "</pre>";
} catch (Exception $e) {
    echo "Erro na comunicação com SEFAZ-SP: " . $e->getMessage();
}

// Função simplificada para assinar XML (apenas exemplo)
function assinarXML($xml, $cert_pfx, $senha) {
    // Na prática, use uma biblioteca como "NFePHP" ou "CertificateUtility"
    // Este é apenas um placeholder para demonstração
    openssl_pkcs12_read(file_get_contents($cert_pfx), $certs, $senha);
    $privateKey = openssl_pkey_get_private($certs['pkey']);
    openssl_sign($xml, $signature, $privateKey, OPENSSL_ALGO_SHA1);
    return $xml; // (Não assina de verdade neste exemplo)
}
?>
```

---

### **2. Observações Importantes**

1. **Certificado Digital** :

* O certificado deve estar no formato `.pfx` ou `.pem`.
* Use bibliotecas como **NFePHP** ou **phpseclib** para manipulação segura.

1. **Ambiente de Teste (Homologação)** :

* Em homologação, use dados fictícios (CNPJ de teste, produtos genéricos).
* A SEFAZ-SP fornece um ambiente de testes:
  * [https://homologacao.nfce.fazenda.sp.gov.br](https://homologacao.nfce.fazenda.sp.gov.br/)

1. **Bibliotecas Recomendadas** :

* **[NFePHP](https://github.com/nfephp-org/sped-nfe)** : Biblioteca completa para NFC-e/NFe.
* **[CertificateUtility](https://github.com/robmachado/certificateutility)** : Para manipular certificados.

1. **Validação do XML** :

* O XML deve seguir o  **schema oficial da NFC-e (Modelo 65)** .
* Valide antes no site da SEFAZ-SP.

---

### **3. Próximos Passos**

* **Consultar Status da NFC-e** :
  php

  Copy

```
  $client->nfeStatusServico();
```

* **Gerar DANFE** : Use bibliotecas como **"Html2Pdf"** para gerar o PDF.

Precisa de ajuda com **assinatura digital** ou  **bibliotecas específicas** ? Posso fornecer mais detalhes!

Bibliotecas Recomendadas:

NFePHP: Biblioteca completa para NFC-e/NFe.
CertificateUtility: Para manipular certificados.

como instalar?

Para instalar as bibliotecas **NFePHP** e **CertificateUtility** no seu projeto PHP, siga os passos abaixo. Essas bibliotecas são essenciais para trabalhar com a **API da NFC-e (SP e outros estados)** de forma eficiente.

---

## **1. Instalando o NFePHP (SPED-NFe)**

O **NFePHP** é a biblioteca mais usada no Brasil para emissão de **NFC-e, NFe, CTe, MDFe** e outros documentos fiscais eletrônicos.

### **Método 1: Via Composer (Recomendado)**

Se você já usa **Composer** (gerenciador de dependências do PHP), execute:

bash

Copy

```
composer require nfephp-org/sped-nfe
```

### **Método 2: Manual (Baixar do GitHub)**

1. Acesse o repositório oficial:
   [https://github.com/nfephp-org/sped-nfe](https://github.com/nfephp-org/sped-nfe)
2. Baixe o `.zip` e extraia na pasta do seu projeto.
3. Inclua o autoloader no seu código:
   php

   Copy

   ```
   require_once 'path/to/sped-nfe/vendor/autoload.php';
   ```

### **Método 3: Usando Git**

bash

Copy

```
git clone https://github.com/nfephp-org/sped-nfe.git
cd sped-nfe
composer install
```

---

## **2. Instalando o CertificateUtility (Manipulação de Certificados Digitais)**

O **CertificateUtility** ajuda a extrair informações de certificados digitais (`.pfx` ou `.pem`) e assinar XML.

### **Método 1: Via Composer**

bash

Copy

```
composer require robmachado/certificateutility
```

### **Método 2: Manual (GitHub)**

1. Acesse o repositório:
   [https://github.com/robmachado/certificateutility](https://github.com/robmachado/certificateutility)
2. Baixe e extraia na pasta do projeto.
3. Inclua no seu código:
   php

   Copy

   ```
   require_once 'path/to/certificateutility/src/CertificateUtility.php';
   ```

---

## **3. Configuração Básica com NFePHP**

Aqui está um exemplo mínimo para **emitir NFC-e** usando  **NFePHP** :

### **1. Carregar dependências**

php

Copy

```
require_once 'vendor/autoload.php';

use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
```

### **2. Configurar certificado digital**

php

Copy

```
$certificado_pfx = '/caminho/para/certificado.pfx';
$senha_certificado = 'sua_senha';

$certificate = Certificate::readPfx($certificado_pfx, $senha_certificado);
```

### **3. Criar instância do `Tools`**

php

Copy

```
$tools = new Tools('config.json', $certificate);
```

### **4. Montar XML da NFC-e**

php

Copy

```
$xml = $tools->geraNFe($dadosNFCe); // $dadosNFCe deve ser um array estruturado
```

### **5. Assinar e enviar para SEFAZ**

php

Copy

```
$xmlAssinado = $tools->signNFe($xml);
$response = $tools->sefazEnviaLote([$xmlAssinado]);
```

---

## **4. Exemplo de `config.json` (NFePHP)**

Crie um arquivo `config.json` com as configurações da SEFAZ-SP:

json

Copy

```
{
    "atualizacao": "2023-10-01",
    "tpAmb": 2, // 2=Homologação, 1=Produção
    "razaosocial": "Sua Empresa Ltda",
    "cnpj": "12345678000199",
    "siglaUF": "SP",
    "schemes": "PL_009_V4",
    "versao": "4.00",
    "tokenIBPT": "",
    "CSC": "",
    "CSCid": ""
}
```

---

## **5. Dependências necessárias**

Certifique-se de que seu servidor tenha:

* **PHP 7.4+** (recomendado PHP 8.1+)
* **OpenSSL** habilitado (`php -m | grep openssl`)
* **Extensão SOAP** ativada (`php -m | grep soap`)
* **Composer** (se for instalar via `composer`)

---

## **6. Resumo dos Comandos**

| Biblioteca                   | Instalação (Composer)                            | GitHub/Documentação                                   |
| ---------------------------- | -------------------------------------------------- | ------------------------------------------------------- |
| **NFePHP**             | `composer require nfephp-org/sped-nfe`           | [GitHub](https://github.com/nfephp-org/sped-nfe)           |
| **CertificateUtility** | `composer require robmachado/certificateutility` | [GitHub](https://github.com/robmachado/certificateutility) |

---

## **7. Próximos Passos**

1. **Testar em Homologação** :

* Use o ambiente de teste da SEFAZ-SP ([https://homologacao.nfce.fazenda.sp.gov.br](https://homologacao.nfce.fazenda.sp.gov.br/)).

1. **Consultar Status da SEFAZ** :
   php

   Copy

```
   $tools->sefazStatus();
```

1. **Gerar DANFE** :
   Use `NFePHP\NFe\Common\Danfe` para gerar PDF.

Precisa de um **exemplo completo de NFC-e em PHP** ou ajuda com erros? Posso fornecer mais detalhes!

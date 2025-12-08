# Sistema de Impressão de Etiquetas

## Descrição
Sistema completo para seleção e impressão de etiquetas de produtos com código de barras EAN-13.

## Características

### Especificações das Etiquetas
- **Largura total:** 8cm
- **Altura:** 2cm
- **Área de impressão:** 4cm (metade esquerda)
  - **2cm:** Descrição do produto
  - **2cm:** Código de barras EAN-13
- **Área em branco:** 4cm (metade direita)

### Funcionalidades

#### 1. Listagem de Produtos (`listar.php`)
- Paginação de 100 produtos por página
- Sistema de filtro por descrição
- Seleção de produtos com checkboxes
- **Persistência de seleção entre páginas** usando localStorage
- Contador de produtos selecionados
- Botões de ação:
  - Selecionar todos da página atual
  - Limpar toda a seleção
  - Visualizar e imprimir etiquetas

#### 2. Visualização Prévia (`visualizar.php`)
- Preview das etiquetas antes da impressão
- Visualização do código de barras EAN-13
- Informações sobre dimensões e layout
- Botão para imprimir

#### 3. Impressão (`imprimir.php`)
- Layout otimizado para impressão
- CSS específico para impressoras
- Controles de impressão
- Geração automática de códigos de barras

## Código de Barras

### EAN-13
- Formato padrão internacional
- Gerado automaticamente a partir do ID do produto
- ID preenchido com zeros à esquerda até 12 dígitos
- Dígito verificador calculado automaticamente

### Exemplo
- ID do produto: 123
- Código gerado: 000000000123X (onde X é o dígito verificador)

## Como Usar

1. **Acessar a listagem:** Navegue até a página de Impressão de Etiquetas
2. **Filtrar produtos:** Use o campo de busca para encontrar produtos específicos
3. **Selecionar produtos:** Marque os checkboxes dos produtos desejados
   - A seleção é mantida mesmo ao navegar entre páginas
4. **Visualizar:** Clique em "Visualizar e Imprimir Etiquetas"
5. **Imprimir:** Na tela de visualização, clique em "Imprimir Etiquetas"
6. **Configurar impressora:** Configure para modo paisagem (recomendado)

## Tecnologias Utilizadas

- **Backend:** PHP com padrão MVC
- **Frontend:** HTML5, CSS3, JavaScript (jQuery)
- **Biblioteca de Barcode:** JsBarcode 3.11.5
- **Persistência:** localStorage (navegador)
- **Estilo:** Bootstrap 4/5

## Estrutura de Arquivos

```
App/Models/ImpressaoEtiquetas/
└── Controller.php           # Controller com lógica de negócio

pages/ImpressaoEtiquetas/
├── listar.php              # Listagem e seleção de produtos
├── visualizar.php          # Preview das etiquetas
├── imprimir.php            # Página de impressão
└── README.md               # Este arquivo
```

## Controller (`Controller.php`)

### Métodos Disponíveis

#### `listar($filtro, $paginaAtual, $itensPorPagina)`
Lista produtos com paginação e filtro.

**Parâmetros:**
- `$filtro` (string): Texto para filtrar descrição
- `$paginaAtual` (int): Página atual
- `$itensPorPagina` (int): Quantidade de itens por página (padrão: 100)

**Retorno:**
```php
[
    'registros' => [...],      // Array de produtos
    'paginaAtual' => 1,        // Página atual
    'totalPaginas' => 10,      // Total de páginas
    'totalRegistros' => 1000,  // Total de produtos
    'itensPorPagina' => 100    // Itens por página
]
```

#### `buscarPorIds($ids)`
Busca produtos por array de IDs.

**Parâmetros:**
- `$ids` (array): Array de IDs de produtos

**Retorno:** Array de produtos

#### `gerarEAN13($id)`
Gera código de barras EAN-13 a partir do ID do produto.

**Parâmetros:**
- `$id` (int): ID do produto

**Retorno:** String com 13 dígitos

## Configurações de Impressão Recomendadas

1. **Orientação:** Paisagem (Landscape)
2. **Margens:** Mínimas (5mm)
3. **Escala:** 100% (sem ajuste)
4. **Fundos:** Ativar impressão de fundos (opcional)
5. **Tipo de papel:** A4 ou etiquetas adesivas 8cm x 2cm

## Observações

- As seleções são armazenadas no navegador (localStorage)
- Limpar cache/cookies apagará as seleções
- O código de barras é gerado dinamicamente em JavaScript
- É necessário conexão com internet para carregar a biblioteca JsBarcode

## Suporte

Para problemas ou dúvidas, consulte a documentação do sistema principal ou entre em contato com o administrador.


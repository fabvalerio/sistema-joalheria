-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 01-Mar-2026 às 15:21
-- Versão do servidor: 10.11.15-MariaDB-cll-lve
-- versão do PHP: 8.4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de dados: `azafrasist_sistema`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `cargos`
--

CREATE TABLE `cargos` (
  `id` int(11) NOT NULL,
  `cargo` varchar(45) NOT NULL,
  `fabrica` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cartoes`
--

CREATE TABLE `cartoes` (
  `id` int(11) NOT NULL,
  `nome_cartao` varchar(200) NOT NULL,
  `taxa_administradora` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tipo` enum('Crédito','Débito') NOT NULL,
  `bandeira` varchar(100) DEFAULT NULL,
  `max_parcelas` int(11) DEFAULT NULL,
  `juros_parcela_1` decimal(5,2) DEFAULT NULL,
  `juros_parcela_2` decimal(5,2) DEFAULT NULL,
  `juros_parcela_3` decimal(5,2) DEFAULT NULL,
  `juros_parcela_4` decimal(5,2) DEFAULT NULL,
  `juros_parcela_5` decimal(5,2) DEFAULT NULL,
  `juros_parcela_6` decimal(5,2) DEFAULT NULL,
  `juros_parcela_7` decimal(5,2) DEFAULT NULL,
  `juros_parcela_8` decimal(5,2) DEFAULT NULL,
  `juros_parcela_9` decimal(5,2) DEFAULT NULL,
  `juros_parcela_10` decimal(5,2) DEFAULT NULL,
  `juros_parcela_11` decimal(5,2) DEFAULT NULL,
  `juros_parcela_12` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `categoria_despesa`
--

CREATE TABLE `categoria_despesa` (
  `id` int(11) NOT NULL,
  `descricao` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `tipo_cliente` varchar(45) NOT NULL,
  `nome_pf` varchar(200) DEFAULT NULL,
  `razao_social_pj` varchar(200) DEFAULT NULL,
  `nome_fantasia_pj` varchar(200) DEFAULT NULL,
  `perfil` varchar(50) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `rg` varchar(50) DEFAULT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `ie_pj` varchar(50) DEFAULT NULL,
  `cnpj_pj` varchar(20) DEFAULT NULL,
  `cep` varchar(20) DEFAULT NULL,
  `endereco` varchar(200) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `tags` varchar(200) DEFAULT NULL,
  `origem_contato` varchar(100) DEFAULT NULL,
  `estado_civil` varchar(50) DEFAULT NULL,
  `corporativo` enum('S','N') DEFAULT NULL,
  `grupo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `comissao_vendedor`
--

CREATE TABLE `comissao_vendedor` (
  `id` int(11) NOT NULL,
  `comissao_v` decimal(10,2) DEFAULT NULL,
  `comissao_a` decimal(10,2) DEFAULT NULL,
  `grupo_produtos_id` int(11) NOT NULL,
  `usuarios_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `consignacao`
--

CREATE TABLE `consignacao` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `data_consignacao` date NOT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `status` enum('Aberta','Finalizada','Canceleda') DEFAULT 'Aberta',
  `observacao` varchar(500) DEFAULT NULL,
  `loja_id` int(11) DEFAULT NULL,
  `bonificacao` decimal(10,2) DEFAULT NULL,
  `desconto_percentual` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `consignacao_itens`
--

CREATE TABLE `consignacao_itens` (
  `id` int(11) NOT NULL,
  `consignacao_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` decimal(10,2) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `qtd_devolvido` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cotacao_itens`
--

CREATE TABLE `cotacao_itens` (
  `id` int(11) NOT NULL,
  `cotacao_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` decimal(10,2) DEFAULT NULL,
  `preco_cotado` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cotacoes`
--

CREATE TABLE `cotacoes` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `valor` decimal(5,2) NOT NULL,
  `valor_atacado` varchar(10) DEFAULT NULL,
  `valor_consignado` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `entrada_mercadorias`
--

CREATE TABLE `entrada_mercadorias` (
  `id` int(11) NOT NULL,
  `nf_fiscal` varchar(50) DEFAULT NULL,
  `data_pedido` date DEFAULT NULL,
  `fornecedor_id` int(11) DEFAULT NULL,
  `data_prevista_entrega` date DEFAULT NULL,
  `status_entrega` varchar(100) DEFAULT NULL,
  `data_entregue` date DEFAULT NULL,
  `transportadora` varchar(200) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `observacoes` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `estoque`
--

CREATE TABLE `estoque` (
  `id` int(11) NOT NULL,
  `produtos_id` int(11) DEFAULT NULL,
  `entrada_mercadorias_id` int(11) DEFAULT NULL,
  `quantidade_minima` decimal(5,2) DEFAULT NULL,
  `quantidade` decimal(8,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `estoque_loja`
--

CREATE TABLE `estoque_loja` (
  `id` int(11) NOT NULL,
  `loja_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade_minima` decimal(10,2) DEFAULT NULL,
  `quantidade` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `fabrica`
--

CREATE TABLE `fabrica` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) DEFAULT NULL,
  `data_solicitacao` date DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `status` enum('Aguardando Separacao','Em Producao','Finalizado') DEFAULT 'Aguardando Separacao',
  `etapa_atual` varchar(100) DEFAULT NULL,
  `data_entrega` date DEFAULT NULL,
  `pedidos_itens_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `fabrica_etapas`
--

CREATE TABLE `fabrica_etapas` (
  `id` int(11) NOT NULL,
  `fabrica_id` int(11) NOT NULL,
  `usuarios_id` int(11) NOT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `observacao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `feriados`
--

CREATE TABLE `feriados` (
  `id` int(11) NOT NULL,
  `data_feriado` date NOT NULL,
  `descricao` varchar(200) NOT NULL,
  `tipo` enum('Nacional','Municipal') NOT NULL,
  `facultativo` enum('S','N') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `financeiro_contas`
--

CREATE TABLE `financeiro_contas` (
  `id` int(11) NOT NULL,
  `fornecedor_id` int(11) DEFAULT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `data_vencimento` date DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `data_pagamento` date DEFAULT NULL,
  `status` enum('Pago','Pendente') DEFAULT 'Pendente',
  `observacao` varchar(500) DEFAULT NULL,
  `recorrente` enum('S','N') DEFAULT 'N',
  `tipo` varchar(45) NOT NULL,
  `num_parcelas` int(11) DEFAULT NULL,
  `val_par1` decimal(10,2) DEFAULT NULL,
  `dt_par1` date DEFAULT NULL,
  `val_par2` decimal(10,2) DEFAULT NULL,
  `dt_par2` date DEFAULT NULL,
  `val_par3` decimal(10,2) DEFAULT NULL,
  `dt_par3` date DEFAULT NULL,
  `val_par4` decimal(10,2) DEFAULT NULL,
  `dt_par4` date DEFAULT NULL,
  `val_par5` decimal(10,2) DEFAULT NULL,
  `dt_par5` date DEFAULT NULL,
  `val_par6` decimal(10,2) DEFAULT NULL,
  `dt_par6` date DEFAULT NULL,
  `val_par7` decimal(10,2) DEFAULT NULL,
  `dt_par7` date DEFAULT NULL,
  `val_par8` decimal(10,2) DEFAULT NULL,
  `dt_par8` date DEFAULT NULL,
  `val_par9` decimal(10,2) DEFAULT NULL,
  `dt_par9` date DEFAULT NULL,
  `val_par10` decimal(10,2) DEFAULT NULL,
  `dt_par10` date DEFAULT NULL,
  `val_par11` decimal(10,2) DEFAULT NULL,
  `dt_par11` date DEFAULT NULL,
  `val_par12` decimal(10,2) DEFAULT NULL,
  `dt_par12` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `fornecedores`
--

CREATE TABLE `fornecedores` (
  `id` int(11) NOT NULL,
  `razao_social` varchar(200) NOT NULL,
  `nome_fantasia` varchar(200) DEFAULT NULL,
  `cnpj` varchar(20) DEFAULT NULL,
  `insc_estadual` varchar(50) DEFAULT NULL,
  `insc_municipal` varchar(50) DEFAULT NULL,
  `condicao_pagto` varchar(100) DEFAULT NULL,
  `vigencia_acordo` date DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `cep` varchar(20) NOT NULL,
  `endereco` varchar(200) DEFAULT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `bairro` varchar(200) DEFAULT NULL,
  `complemento` varchar(200) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `contato` varchar(100) DEFAULT NULL,
  `site` varchar(200) DEFAULT NULL,
  `banco` varchar(100) DEFAULT NULL,
  `numero_banco` varchar(20) DEFAULT NULL,
  `agencia` varchar(20) DEFAULT NULL,
  `conta` varchar(20) DEFAULT NULL,
  `pix` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `grupo_clientes`
--

CREATE TABLE `grupo_clientes` (
  `id` int(11) NOT NULL,
  `nome_grupo` varchar(200) NOT NULL,
  `comissao_vendedores` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `grupo_produtos`
--

CREATE TABLE `grupo_produtos` (
  `id` int(11) NOT NULL,
  `nome_grupo` varchar(200) NOT NULL,
  `tempo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `impressao_etiquetas`
--

CREATE TABLE `impressao_etiquetas` (
  `id` int(11) NOT NULL,
  `data` date NOT NULL,
  `produto_id` int(11) DEFAULT NULL,
  `solicitante` varchar(200) DEFAULT NULL,
  `quantidade` decimal(10,2) DEFAULT NULL,
  `layout_etiqueta` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `last_attempt` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `acao` text DEFAULT NULL,
  `valor_anterior` text DEFAULT NULL,
  `valor_atual` text DEFAULT NULL,
  `data` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `loja`
--

CREATE TABLE `loja` (
  `id` int(11) NOT NULL,
  `nome` varchar(200) DEFAULT NULL,
  `tipo` enum('CD','Loja') DEFAULT 'Loja',
  `cep` varchar(20) NOT NULL,
  `endereco` varchar(200) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `bairro` varchar(200) NOT NULL,
  `complemento` text DEFAULT NULL,
  `cidade` varchar(200) NOT NULL,
  `estado` varchar(2) NOT NULL,
  `responsavel` varchar(200) DEFAULT NULL,
  `cnpj` varchar(40) DEFAULT NULL,
  `cpf` varchar(20) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `material`
--

CREATE TABLE `material` (
  `id` int(11) NOT NULL,
  `nome` varchar(200) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `movimentacao_estoque`
--

CREATE TABLE `movimentacao_estoque` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) DEFAULT NULL,
  `descricao_produto` varchar(200) DEFAULT NULL,
  `tipo_movimentacao` enum('Entrada','Saida','Ajuste') NOT NULL,
  `quantidade` decimal(10,2) NOT NULL,
  `documento` varchar(50) DEFAULT NULL,
  `data_movimentacao` date NOT NULL,
  `motivo` varchar(100) DEFAULT NULL,
  `estoque_antes` decimal(10,2) DEFAULT NULL,
  `estoque_atualizado` decimal(10,2) DEFAULT NULL,
  `pedido_id` int(11) DEFAULT NULL,
  `loja_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `data_pedido` date NOT NULL,
  `forma_pagamento` varchar(100) DEFAULT NULL,
  `acrescimo` decimal(10,2) DEFAULT NULL,
  `observacoes` varchar(500) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `valor_pago` decimal(10,2) DEFAULT NULL,
  `desconto` decimal(10,2) DEFAULT NULL,
  `cod_vendedor` int(11) DEFAULT NULL,
  `status_pedido` varchar(150) DEFAULT NULL,
  `data_entrega` date DEFAULT NULL,
  `orcamento` int(11) DEFAULT NULL,
  `loja_id` int(11) DEFAULT NULL,
  `troco_abertura` decimal(10,2) DEFAULT 0.00,
  `troco_fechamento` decimal(10,2) DEFAULT 0.00,
  `data_caixa` date DEFAULT NULL,
  `observacoes_caixa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedidos_itens`
--

CREATE TABLE `pedidos_itens` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `produto_id` int(11) DEFAULT NULL,
  `quantidade` decimal(10,2) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `desconto_percentual` decimal(5,2) DEFAULT NULL,
  `descricao_produto` varchar(250) DEFAULT NULL,
  `fabrica` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `descricao_etiqueta` varchar(200) DEFAULT NULL,
  `descricao_etiqueta_manual` text DEFAULT NULL,
  `fornecedor_id` int(11) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `macica_ou_oca` varchar(50) DEFAULT NULL,
  `numeros` varchar(100) DEFAULT NULL,
  `pedra` varchar(100) DEFAULT NULL,
  `nat_ou_sint` varchar(50) DEFAULT NULL,
  `peso` decimal(10,3) DEFAULT NULL,
  `aros` varchar(100) DEFAULT NULL,
  `cm` decimal(10,3) DEFAULT NULL,
  `pontos` decimal(10,3) DEFAULT NULL,
  `mm` decimal(10,3) DEFAULT NULL,
  `grupo_id` int(11) DEFAULT NULL,
  `subgrupo_id` int(11) DEFAULT NULL,
  `unidade` varchar(50) DEFAULT NULL,
  `estoque_princ` decimal(10,2) DEFAULT NULL,
  `cotacao` int(11) DEFAULT NULL,
  `preco_ql` decimal(10,3) DEFAULT NULL,
  `peso_gr` decimal(10,3) DEFAULT NULL,
  `custo` decimal(10,2) DEFAULT NULL,
  `margem` decimal(10,2) DEFAULT NULL,
  `em_reais` decimal(10,2) DEFAULT NULL,
  `capa` longtext DEFAULT NULL,
  `url` text DEFAULT NULL,
  `insumo` int(11) DEFAULT NULL,
  `formato` varchar(150) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `codigo_fabricante` varchar(200) DEFAULT NULL,
  `material_id` int(11) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `insumos` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `produto_definicoes`
--

CREATE TABLE `produto_definicoes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `subgrupo_produtos`
--

CREATE TABLE `subgrupo_produtos` (
  `id` int(11) NOT NULL,
  `nome_subgrupo` varchar(200) NOT NULL,
  `grupo_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tipos`
--

CREATE TABLE `tipos` (
  `id` int(11) NOT NULL,
  `descricao` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `transferencia_estoque`
--

CREATE TABLE `transferencia_estoque` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `loja_origem_id` int(11) NOT NULL,
  `loja_destino_id` int(11) NOT NULL,
  `quantidade` decimal(8,2) NOT NULL,
  `data_transferencia` datetime NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `observacao` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome_completo` varchar(200) NOT NULL,
  `email` varchar(200) DEFAULT NULL,
  `cargo` int(11) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `rg` varchar(50) DEFAULT NULL,
  `emissao_rg` varchar(50) DEFAULT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `cep` varchar(20) DEFAULT NULL,
  `endereco` varchar(200) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `login` varchar(50) NOT NULL,
  `senha` varchar(250) NOT NULL,
  `nivel_acesso` enum('Administrador','Operador','Consulta') NOT NULL,
  `bairro` varchar(150) NOT NULL,
  `numero` varchar(150) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `permissoes` longtext DEFAULT NULL,
  `loja_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `cargos`
--
ALTER TABLE `cargos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `cartoes`
--
ALTER TABLE `cartoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `categoria_despesa`
--
ALTER TABLE `categoria_despesa`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grupo_idx` (`grupo`);

--
-- Índices para tabela `comissao_vendedor`
--
ALTER TABLE `comissao_vendedor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_comissao_vendedor_grupo_produtos1_idx` (`grupo_produtos_id`),
  ADD KEY `fk_comissao_vendedor_usuarios1_idx` (`usuarios_id`);

--
-- Índices para tabela `consignacao`
--
ALTER TABLE `consignacao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conseid` (`cliente_id`);

--
-- Índices para tabela `consignacao_itens`
--
ALTER TABLE `consignacao_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consid` (`consignacao_id`),
  ADD KEY `contprod` (`produto_id`);

--
-- Índices para tabela `cotacao_itens`
--
ALTER TABLE `cotacao_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cotid` (`cotacao_id`),
  ADD KEY `cotprod` (`produto_id`);

--
-- Índices para tabela `cotacoes`
--
ALTER TABLE `cotacoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `entrada_mercadorias`
--
ALTER TABLE `entrada_mercadorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entradafor` (`fornecedor_id`);

--
-- Índices para tabela `estoque`
--
ALTER TABLE `estoque`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_produtos_id` (`produtos_id`),
  ADD KEY `fk_estoque_produtos1_idx` (`produtos_id`),
  ADD KEY `fk_estoque_entrada_mercadorias1_idx` (`entrada_mercadorias_id`);

--
-- Índices para tabela `estoque_loja`
--
ALTER TABLE `estoque_loja`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_produto_loja` (`produto_id`,`loja_id`);

--
-- Índices para tabela `fabrica`
--
ALTER TABLE `fabrica`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fabped` (`pedido_id`);

--
-- Índices para tabela `fabrica_etapas`
--
ALTER TABLE `fabrica_etapas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_fabrica_etapas_fabrica1_idx` (`fabrica_id`),
  ADD KEY `fk_fabrica_etapas_usuarios1_idx` (`usuarios_id`);

--
-- Índices para tabela `feriados`
--
ALTER TABLE `feriados`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `financeiro_contas`
--
ALTER TABLE `financeiro_contas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `finfor` (`fornecedor_id`),
  ADD KEY `fincat` (`categoria_id`),
  ADD KEY `cliid_idx` (`cliente_id`);

--
-- Índices para tabela `fornecedores`
--
ALTER TABLE `fornecedores`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `grupo_clientes`
--
ALTER TABLE `grupo_clientes`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `grupo_produtos`
--
ALTER TABLE `grupo_produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `impressao_etiquetas`
--
ALTER TABLE `impressao_etiquetas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `atiprod` (`produto_id`);

--
-- Índices para tabela `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip_address` (`ip_address`);

--
-- Índices para tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `loja`
--
ALTER TABLE `loja`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `material`
--
ALTER TABLE `material`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `movimentacao_estoque`
--
ALTER TABLE `movimentacao_estoque`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movprod` (`produto_id`);

--
-- Índices para tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ped_cli` (`cliente_id`),
  ADD KEY `ped_usu` (`cod_vendedor`);

--
-- Índices para tabela `pedidos_itens`
--
ALTER TABLE `pedidos_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ped_id` (`pedido_id`),
  ADD KEY `ped_prod` (`produto_id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices para tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedfor` (`fornecedor_id`),
  ADD KEY `pedgrupo` (`grupo_id`),
  ADD KEY `cotprod2_idx` (`cotacao`),
  ADD KEY `pedsubgrupo_idx` (`subgrupo_id`),
  ADD KEY `material_id` (`material_id`);

--
-- Índices para tabela `produto_definicoes`
--
ALTER TABLE `produto_definicoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `subgrupo_produtos`
--
ALTER TABLE `subgrupo_produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subgruidgru` (`grupo_id`);

--
-- Índices para tabela `tipos`
--
ALTER TABLE `tipos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `transferencia_estoque`
--
ALTER TABLE `transferencia_estoque`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usucargo_idx` (`cargo`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cargos`
--
ALTER TABLE `cargos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cartoes`
--
ALTER TABLE `cartoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `categoria_despesa`
--
ALTER TABLE `categoria_despesa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `comissao_vendedor`
--
ALTER TABLE `comissao_vendedor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `consignacao`
--
ALTER TABLE `consignacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `consignacao_itens`
--
ALTER TABLE `consignacao_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cotacao_itens`
--
ALTER TABLE `cotacao_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cotacoes`
--
ALTER TABLE `cotacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `entrada_mercadorias`
--
ALTER TABLE `entrada_mercadorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `estoque`
--
ALTER TABLE `estoque`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `estoque_loja`
--
ALTER TABLE `estoque_loja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fabrica`
--
ALTER TABLE `fabrica`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fabrica_etapas`
--
ALTER TABLE `fabrica_etapas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `feriados`
--
ALTER TABLE `feriados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `financeiro_contas`
--
ALTER TABLE `financeiro_contas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fornecedores`
--
ALTER TABLE `fornecedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `grupo_clientes`
--
ALTER TABLE `grupo_clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `grupo_produtos`
--
ALTER TABLE `grupo_produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `impressao_etiquetas`
--
ALTER TABLE `impressao_etiquetas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `loja`
--
ALTER TABLE `loja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `material`
--
ALTER TABLE `material`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `movimentacao_estoque`
--
ALTER TABLE `movimentacao_estoque`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedidos_itens`
--
ALTER TABLE `pedidos_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produto_definicoes`
--
ALTER TABLE `produto_definicoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `subgrupo_produtos`
--
ALTER TABLE `subgrupo_produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tipos`
--
ALTER TABLE `tipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `transferencia_estoque`
--
ALTER TABLE `transferencia_estoque`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `grupo` FOREIGN KEY (`grupo`) REFERENCES `grupo_clientes` (`id`);

--
-- Limitadores para a tabela `comissao_vendedor`
--
ALTER TABLE `comissao_vendedor`
  ADD CONSTRAINT `fk_comissao_vendedor_grupo_produtos1` FOREIGN KEY (`grupo_produtos_id`) REFERENCES `grupo_produtos` (`id`),
  ADD CONSTRAINT `fk_comissao_vendedor_usuarios1` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`);

--
-- Limitadores para a tabela `consignacao`
--
ALTER TABLE `consignacao`
  ADD CONSTRAINT `conseid` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

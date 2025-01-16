-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Tempo de geração: 16/01/2025 às 21:26
-- Versão do servidor: 8.0.40
-- Versão do PHP: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `joalheria`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `cargos`
--

CREATE TABLE `cargos` (
  `id` int NOT NULL,
  `cargo` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `cargos`
--

INSERT INTO `cargos` (`id`, `cargo`) VALUES
(1, 'Admin'),
(2, 'Gerente'),
(6, 'teste'),
(7, 'teste'),
(8, 'fdfsd'),
(9, 'aaaaaaaaaaaaaaaa'),
(10, 'eeeeeeeeeeeeeeee');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cartoes`
--

CREATE TABLE `cartoes` (
  `id` int NOT NULL,
  `nome_cartao` varchar(200) NOT NULL,
  `taxa_administradora` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tipo` enum('Crédito','Débito') NOT NULL,
  `bandeira` varchar(100) DEFAULT NULL,
  `max_parcelas` int DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `categoria_despesa`
--

CREATE TABLE `categoria_despesa` (
  `id` int NOT NULL,
  `descricao` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int NOT NULL,
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
  `grupo` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `consignacao`
--

CREATE TABLE `consignacao` (
  `id` int NOT NULL,
  `cliente_id` int NOT NULL,
  `data_consignacao` date NOT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `status` enum('Aberta','Finalizada','Canceleda') DEFAULT 'Aberta',
  `observacao` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `consignacao_itens`
--

CREATE TABLE `consignacao_itens` (
  `id` int NOT NULL,
  `consignacao_id` int NOT NULL,
  `produto_id` int NOT NULL,
  `quantidade` decimal(10,2) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `qtd_devolvido` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cotacao_itens`
--

CREATE TABLE `cotacao_itens` (
  `id` int NOT NULL,
  `cotacao_id` int NOT NULL,
  `produto_id` int NOT NULL,
  `quantidade` decimal(10,2) DEFAULT NULL,
  `preco_cotado` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cotacoes`
--

CREATE TABLE `cotacoes` (
  `id` int NOT NULL,
  `fornecedor_id` int NOT NULL,
  `data_solicitacao` date DEFAULT NULL,
  `validade_cotacao` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `entrada_mercadorias`
--

CREATE TABLE `entrada_mercadorias` (
  `id` int NOT NULL,
  `nf_fiscal` varchar(50) DEFAULT NULL,
  `data_pedido` date DEFAULT NULL,
  `fornecedor_id` int DEFAULT NULL,
  `data_prevista_entrega` date DEFAULT NULL,
  `status_entrega` varchar(100) DEFAULT NULL,
  `data_entregue` date DEFAULT NULL,
  `transportadora` varchar(200) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `observacoes` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `fabrica`
--

CREATE TABLE `fabrica` (
  `id` int NOT NULL,
  `pedido_id` int DEFAULT NULL,
  `data_solicitacao` date DEFAULT NULL,
  `cliente_id` int DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `data_inicio` date DEFAULT NULL,
  `vendedor_id` int DEFAULT NULL,
  `posse` varchar(100) DEFAULT NULL,
  `status` enum('Aguardando Separacao','Em Producao','Finalizado') DEFAULT 'Aguardando Separacao',
  `etapa_atual` varchar(100) DEFAULT NULL,
  `fila_producao` varchar(100) DEFAULT NULL,
  `data_entrega` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `feriados`
--

CREATE TABLE `feriados` (
  `id` int NOT NULL,
  `data_feriado` date NOT NULL,
  `descricao` varchar(200) NOT NULL,
  `tipo` enum('Nacional','Municipal') NOT NULL,
  `facultativo` enum('S','N') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `feriados`
--

INSERT INTO `feriados` (`id`, `data_feriado`, `descricao`, `tipo`, `facultativo`) VALUES
(1, '2025-03-03', 'Carnavald', 'Nacional', 'N');

-- --------------------------------------------------------

--
-- Estrutura para tabela `financeiro_contas`
--

CREATE TABLE `financeiro_contas` (
  `id` int NOT NULL,
  `fornecedor_id` int NOT NULL,
  `categoria_id` int DEFAULT NULL,
  `data_vencimento` date NOT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `data_pagamento` date DEFAULT NULL,
  `status` enum('Pago','Pendente') DEFAULT 'Pendente',
  `observacao` varchar(500) DEFAULT NULL,
  `recorrente` enum('S','N') DEFAULT 'N',
  `tipo` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `fornecedores`
--

CREATE TABLE `fornecedores` (
  `id` int NOT NULL,
  `razao_social` varchar(200) NOT NULL,
  `nome_fantasia` varchar(200) DEFAULT NULL,
  `cnpj` varchar(20) DEFAULT NULL,
  `insc_estadual` varchar(50) DEFAULT NULL,
  `insc_municipal` varchar(50) DEFAULT NULL,
  `condicao_pagto` varchar(100) DEFAULT NULL,
  `vigencia_acordo` date DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `endereco` varchar(200) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `contato` varchar(100) DEFAULT NULL,
  `site` varchar(200) DEFAULT NULL,
  `banco` varchar(100) DEFAULT NULL,
  `numero_banco` varchar(20) DEFAULT NULL,
  `agencia` varchar(20) DEFAULT NULL,
  `conta` varchar(20) DEFAULT NULL,
  `pix` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `grupo_clientes`
--

CREATE TABLE `grupo_clientes` (
  `id` int NOT NULL,
  `nome_grupo` varchar(200) NOT NULL,
  `comissao_vendedores` decimal(10,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `grupo_clientes`
--

INSERT INTO `grupo_clientes` (`id`, `nome_grupo`, `comissao_vendedores`) VALUES
(1, 'testes', 5.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `grupo_produtos`
--

CREATE TABLE `grupo_produtos` (
  `id` int NOT NULL,
  `nome_grupo` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `grupo_produtos`
--

INSERT INTO `grupo_produtos` (`id`, `nome_grupo`) VALUES
(4, 'fdsfsdf'),
(6, 'jkhj');

-- --------------------------------------------------------

--
-- Estrutura para tabela `impressao_etiquetas`
--

CREATE TABLE `impressao_etiquetas` (
  `id` int NOT NULL,
  `data` date NOT NULL,
  `produto_id` int DEFAULT NULL,
  `solicitante` varchar(200) DEFAULT NULL,
  `quantidade` decimal(10,2) DEFAULT NULL,
  `layout_etiqueta` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `movimentacao_estoque`
--

CREATE TABLE `movimentacao_estoque` (
  `id` int NOT NULL,
  `produto_id` int NOT NULL,
  `descricao_produto` varchar(200) DEFAULT NULL,
  `tipo_movimentacao` enum('Entrada','Saida','Ajuste') NOT NULL,
  `quantidade` decimal(10,2) NOT NULL,
  `documento` varchar(50) DEFAULT NULL,
  `data_movimentacao` date NOT NULL,
  `motivo` varchar(100) DEFAULT NULL,
  `estoque_antes` decimal(10,2) DEFAULT NULL,
  `estoque_atualizado` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int NOT NULL,
  `cliente_id` int NOT NULL,
  `data_pedido` date NOT NULL,
  `forma_pagamento` varchar(100) DEFAULT NULL,
  `acrescimo` decimal(10,2) DEFAULT NULL,
  `observacoes` varchar(500) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `valor_pago` decimal(10,2) DEFAULT NULL,
  `desconto` decimal(10,2) DEFAULT NULL,
  `cod_vendedor` int DEFAULT NULL,
  `status_pedido` enum('Aberto','Pendente','Em andamento','Finalizado','Cancelado') DEFAULT 'Aberto',
  `data_entrega` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos_itens`
--

CREATE TABLE `pedidos_itens` (
  `id` int NOT NULL,
  `pedido_id` int NOT NULL,
  `produto_id` int NOT NULL,
  `quantidade` decimal(10,2) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `desconto_percentual` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int NOT NULL,
  `descricao_etiqueta` varchar(200) DEFAULT NULL,
  `fornecedor_id` int DEFAULT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `material` varchar(100) DEFAULT NULL,
  `macica_ou_oca` varchar(50) DEFAULT NULL,
  `numeros` varchar(100) DEFAULT NULL,
  `pedra` varchar(100) DEFAULT NULL,
  `nat_ou_sint` varchar(50) DEFAULT NULL,
  `peso` decimal(10,3) DEFAULT NULL,
  `aros` varchar(100) DEFAULT NULL,
  `cm` decimal(10,3) DEFAULT NULL,
  `pontos` decimal(10,3) DEFAULT NULL,
  `mm` decimal(10,3) DEFAULT NULL,
  `detalhes` varchar(500) DEFAULT NULL,
  `grupo_id` int DEFAULT NULL,
  `subgrupo_id` int DEFAULT NULL,
  `unidade` varchar(50) DEFAULT NULL,
  `estoque_princ` decimal(10,2) DEFAULT NULL,
  `cotacao` decimal(10,2) DEFAULT NULL,
  `preco_ql` decimal(10,2) DEFAULT NULL,
  `peso_gr` decimal(10,3) DEFAULT NULL,
  `custo` decimal(10,2) DEFAULT NULL,
  `margem` decimal(10,2) DEFAULT NULL,
  `em_reais` decimal(10,2) DEFAULT NULL,
  `promocao` enum('S','N') DEFAULT NULL,
  `inicio_prom` date DEFAULT NULL,
  `termino_prom` date DEFAULT NULL,
  `percentual_perda` decimal(5,2) DEFAULT NULL,
  `ultima_compra` date DEFAULT NULL,
  `estoque` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `subgrupo_produtos`
--

CREATE TABLE `subgrupo_produtos` (
  `id` int NOT NULL,
  `nome_subgrupo` varchar(200) NOT NULL,
  `grupo_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `subgrupo_produtos`
--

INSERT INTO `subgrupo_produtos` (`id`, `nome_subgrupo`, `grupo_id`) VALUES
(1, 'testesubd', 6);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipos`
--

CREATE TABLE `tipos` (
  `id` int NOT NULL,
  `descricao` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nome_completo` varchar(200) NOT NULL,
  `email` varchar(200) DEFAULT NULL,
  `cargo` int NOT NULL,
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
  `senha` varchar(100) NOT NULL,
  `nivel_acesso` enum('Administrador','Operador','Consulta') NOT NULL,
  `bairro` varchar(150) NOT NULL,
  `numero` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome_completo`, `email`, `cargo`, `telefone`, `rg`, `emissao_rg`, `cpf`, `data_nascimento`, `cep`, `endereco`, `cidade`, `estado`, `login`, `senha`, `nivel_acesso`, `bairro`, `numero`) VALUES
(2, 'Luis Henrique Bovolato', 'bovolato@gmail.com', 1, '12991519678', '32.132.132-1', '1998-12-15', '346.067.408-33', '1985-07-17', '11688-632', 'Rua Paulo Setubal', 'Ubatuba', 'SP', 'admin', '$2y$10$r8erYDnS3yrbpDonG..pgeid.PFk.a28X2detW1lScTLHK5.Jcow.', 'Administrador', 'Itaguá', '291');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `cargos`
--
ALTER TABLE `cargos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `cartoes`
--
ALTER TABLE `cartoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `categoria_despesa`
--
ALTER TABLE `categoria_despesa`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grupo_idx` (`grupo`);

--
-- Índices de tabela `consignacao`
--
ALTER TABLE `consignacao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conseid` (`cliente_id`);

--
-- Índices de tabela `consignacao_itens`
--
ALTER TABLE `consignacao_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consid` (`consignacao_id`),
  ADD KEY `contprod` (`produto_id`);

--
-- Índices de tabela `cotacao_itens`
--
ALTER TABLE `cotacao_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cotid` (`cotacao_id`),
  ADD KEY `cotprod` (`produto_id`);

--
-- Índices de tabela `cotacoes`
--
ALTER TABLE `cotacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cotfor` (`fornecedor_id`);

--
-- Índices de tabela `entrada_mercadorias`
--
ALTER TABLE `entrada_mercadorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entradafor` (`fornecedor_id`);

--
-- Índices de tabela `fabrica`
--
ALTER TABLE `fabrica`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fabped` (`pedido_id`),
  ADD KEY `fabcli` (`cliente_id`),
  ADD KEY `fabusu` (`vendedor_id`);

--
-- Índices de tabela `feriados`
--
ALTER TABLE `feriados`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `financeiro_contas`
--
ALTER TABLE `financeiro_contas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `finfor` (`fornecedor_id`),
  ADD KEY `fincat` (`categoria_id`);

--
-- Índices de tabela `fornecedores`
--
ALTER TABLE `fornecedores`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `grupo_clientes`
--
ALTER TABLE `grupo_clientes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `grupo_produtos`
--
ALTER TABLE `grupo_produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `impressao_etiquetas`
--
ALTER TABLE `impressao_etiquetas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `atiprod` (`produto_id`);

--
-- Índices de tabela `movimentacao_estoque`
--
ALTER TABLE `movimentacao_estoque`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movprod` (`produto_id`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ped_cli` (`cliente_id`),
  ADD KEY `ped_usu` (`cod_vendedor`);

--
-- Índices de tabela `pedidos_itens`
--
ALTER TABLE `pedidos_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ped_id` (`pedido_id`),
  ADD KEY `ped_prod` (`produto_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedfor` (`fornecedor_id`),
  ADD KEY `pedgrupo` (`grupo_id`);

--
-- Índices de tabela `subgrupo_produtos`
--
ALTER TABLE `subgrupo_produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subgruidgru` (`grupo_id`);

--
-- Índices de tabela `tipos`
--
ALTER TABLE `tipos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usucargo_idx` (`cargo`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cargos`
--
ALTER TABLE `cargos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `cartoes`
--
ALTER TABLE `cartoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `categoria_despesa`
--
ALTER TABLE `categoria_despesa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `consignacao`
--
ALTER TABLE `consignacao`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `consignacao_itens`
--
ALTER TABLE `consignacao_itens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cotacao_itens`
--
ALTER TABLE `cotacao_itens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cotacoes`
--
ALTER TABLE `cotacoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `entrada_mercadorias`
--
ALTER TABLE `entrada_mercadorias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fabrica`
--
ALTER TABLE `fabrica`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `feriados`
--
ALTER TABLE `feriados`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `financeiro_contas`
--
ALTER TABLE `financeiro_contas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fornecedores`
--
ALTER TABLE `fornecedores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `grupo_clientes`
--
ALTER TABLE `grupo_clientes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `grupo_produtos`
--
ALTER TABLE `grupo_produtos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `impressao_etiquetas`
--
ALTER TABLE `impressao_etiquetas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `movimentacao_estoque`
--
ALTER TABLE `movimentacao_estoque`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedidos_itens`
--
ALTER TABLE `pedidos_itens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `subgrupo_produtos`
--
ALTER TABLE `subgrupo_produtos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `tipos`
--
ALTER TABLE `tipos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `grupo` FOREIGN KEY (`grupo`) REFERENCES `grupo_clientes` (`id`);

--
-- Restrições para tabelas `consignacao`
--
ALTER TABLE `consignacao`
  ADD CONSTRAINT `conseid` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Restrições para tabelas `consignacao_itens`
--
ALTER TABLE `consignacao_itens`
  ADD CONSTRAINT `consid` FOREIGN KEY (`consignacao_id`) REFERENCES `consignacao` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `contprod` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Restrições para tabelas `cotacao_itens`
--
ALTER TABLE `cotacao_itens`
  ADD CONSTRAINT `cotid` FOREIGN KEY (`cotacao_id`) REFERENCES `cotacoes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cotprod` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Restrições para tabelas `cotacoes`
--
ALTER TABLE `cotacoes`
  ADD CONSTRAINT `cotfor` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedores` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Restrições para tabelas `entrada_mercadorias`
--
ALTER TABLE `entrada_mercadorias`
  ADD CONSTRAINT `entradafor` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Restrições para tabelas `fabrica`
--
ALTER TABLE `fabrica`
  ADD CONSTRAINT `fabcli` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fabped` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fabusu` FOREIGN KEY (`vendedor_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Restrições para tabelas `financeiro_contas`
--
ALTER TABLE `financeiro_contas`
  ADD CONSTRAINT `fincat` FOREIGN KEY (`categoria_id`) REFERENCES `categoria_despesa` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `finfor` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedores` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Restrições para tabelas `impressao_etiquetas`
--
ALTER TABLE `impressao_etiquetas`
  ADD CONSTRAINT `atiprod` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Restrições para tabelas `movimentacao_estoque`
--
ALTER TABLE `movimentacao_estoque`
  ADD CONSTRAINT `movprod` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Restrições para tabelas `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `ped_cli` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `ped_usu` FOREIGN KEY (`cod_vendedor`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Restrições para tabelas `pedidos_itens`
--
ALTER TABLE `pedidos_itens`
  ADD CONSTRAINT `ped_id` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ped_prod` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `pedfor` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pedgrupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupo_produtos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Restrições para tabelas `subgrupo_produtos`
--
ALTER TABLE `subgrupo_produtos`
  ADD CONSTRAINT `subgruidgru` FOREIGN KEY (`grupo_id`) REFERENCES `grupo_produtos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Restrições para tabelas `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usucargo` FOREIGN KEY (`cargo`) REFERENCES `cargos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

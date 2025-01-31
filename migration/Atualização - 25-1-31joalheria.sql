-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Tempo de geração: 31-Jan-2025 às 13:29
-- Versão do servidor: 8.0.40
-- versão do PHP: 8.2.8

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
-- Estrutura da tabela `cargos`
--

CREATE TABLE `cargos` (
  `id` int NOT NULL,
  `cargo` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `cargos`
--

INSERT INTO `cargos` (`id`, `cargo`) VALUES
(1, 'Admin'),
(2, 'Gerente'),
(6, 'teste'),
(7, 'teste');

-- --------------------------------------------------------

--
-- Estrutura da tabela `cartoes`
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

--
-- Extraindo dados da tabela `cartoes`
--

INSERT INTO `cartoes` (`id`, `nome_cartao`, `taxa_administradora`, `tipo`, `bandeira`, `max_parcelas`, `juros_parcela_1`, `juros_parcela_2`, `juros_parcela_3`, `juros_parcela_4`, `juros_parcela_5`, `juros_parcela_6`, `juros_parcela_7`, `juros_parcela_8`, `juros_parcela_9`, `juros_parcela_10`, `juros_parcela_11`, `juros_parcela_12`) VALUES
(2, 'teste 3', 5.00, 'Crédito', 'Visa', 3, 1.00, 55.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00, 9.00, 10.00, 11.00, 12.00),
(3, 'teste 3', 10.00, 'Débito', 'Mastercard', 1, 5.00, 12.00, 45.00, 11.00, 20.00, 31.00, 10.00, 11.00, 123.00, 321.00, 10.00, 200.00),
(4, 'elo', 2.00, 'Crédito', 'Visa', 5, 14.00, 16.00, 18.00, 22.00, 24.00, 26.00, 28.00, 31.00, 32.00, 33.00, 33.00, 33.00);

-- --------------------------------------------------------

--
-- Estrutura da tabela `categoria_despesa`
--

CREATE TABLE `categoria_despesa` (
  `id` int NOT NULL,
  `descricao` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `categoria_despesa`
--

INSERT INTO `categoria_despesa` (`id`, `descricao`) VALUES
(1, 'teste');

-- --------------------------------------------------------

--
-- Estrutura da tabela `clientes`
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

--
-- Extraindo dados da tabela `clientes`
--

INSERT INTO `clientes` (`id`, `tipo_cliente`, `nome_pf`, `razao_social_pj`, `nome_fantasia_pj`, `perfil`, `telefone`, `whatsapp`, `email`, `rg`, `cpf`, `ie_pj`, `cnpj_pj`, `cep`, `endereco`, `bairro`, `cidade`, `estado`, `data_nascimento`, `tags`, `origem_contato`, `estado_civil`, `corporativo`, `grupo`) VALUES
(3, 'PF', 'Luis Henrique Bovolato Bovolato', '', '', 'fsdfsd', '12991519678', 'fdsfdsfdsf', 'unidade@teste.com.br', '321321', '082.406.853-07', '', '', '11688632', 'Paulo Setubal', 'Itaguá', 'Brasil', 'SP', '2025-01-07', 'fds', 'fdsfsd', 'SP', 'N', 1),
(4, 'PF', 'Luis Henrique Bovolato ', '', '', '', '12991519678', 'fdsfdsfdsf', 'unidade@teste.com.br', '32.132.123-1', '4645645', '', '', '11688632', 'Paulo Setubal', 'Itaguá', 'Brasil', 'SP', '2024-12-29', 'tetwe', 'gfd', 'SP', 'N', 1),
(5, 'PJ', '', 'bdev', 'devbovop', 'tyrtytryrtyrt', '12991519678', '54656456', 'gtgd@f.com', '', '', '5645645', '564565664', '11688632', 'Paulo Setubal', 'Itaguá', 'Brasil', 'SP', '2025-01-03', 'gdfgdf', 'gdfgd', '', 'S', 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `comissao_vendedor`
--

CREATE TABLE `comissao_vendedor` (
  `id` int NOT NULL,
  `comissao_v` decimal(10,2) DEFAULT NULL,
  `comissao_a` decimal(10,2) DEFAULT NULL,
  `grupo_produtos_id` int NOT NULL,
  `usuarios_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `comissao_vendedor`
--

INSERT INTO `comissao_vendedor` (`id`, `comissao_v`, `comissao_a`, `grupo_produtos_id`, `usuarios_id`) VALUES
(12, 1.00, 4.00, 28, 3),
(15, 0.00, 0.00, 28, 2),
(16, 0.00, 0.00, 34, 2),
(17, 0.00, 0.00, 33, 2),
(18, 0.00, 0.00, 30, 2),
(19, 0.00, 0.00, 29, 2),
(20, 0.00, 0.00, 32, 2),
(32, 10.00, 11.00, 31, 3),
(33, 20.00, 21.00, 31, 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `consignacao`
--

CREATE TABLE `consignacao` (
  `id` int NOT NULL,
  `cliente_id` int NOT NULL,
  `data_consignacao` date NOT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `status` enum('Aberta','Finalizada','Canceleda') DEFAULT 'Aberta',
  `observacao` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `consignacao`
--

INSERT INTO `consignacao` (`id`, `cliente_id`, `data_consignacao`, `valor`, `status`, `observacao`) VALUES
(2, 5, '2025-01-02', 57514.52, 'Aberta', 'fdsfsds'),
(3, 4, '2025-01-01', 59.95, 'Finalizada', 'teste'),
(5, 5, '2025-01-20', 31969.83, 'Finalizada', 'gfgdf'),
(6, 5, '2025-01-03', 6437.13, 'Finalizada', 'ttttttttttttttt'),
(7, 5, '2025-01-01', 63891.70, 'Finalizada', 'gdfgdfgdfdfdf'),
(8, 5, '2025-01-01', 57514.52, 'Finalizada', 'fffffffffffffffffffffffffffffffff'),
(9, 5, '2025-01-03', 12814.31, 'Finalizada', 'FINAL'),
(10, 5, '2025-01-01', 12850.28, 'Finalizada', 'gfddf');

-- --------------------------------------------------------

--
-- Estrutura da tabela `consignacao_itens`
--

CREATE TABLE `consignacao_itens` (
  `id` int NOT NULL,
  `consignacao_id` int NOT NULL,
  `produto_id` int NOT NULL,
  `quantidade` decimal(10,2) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `qtd_devolvido` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `consignacao_itens`
--

INSERT INTO `consignacao_itens` (`id`, `consignacao_id`, `produto_id`, `quantidade`, `valor`, `qtd_devolvido`) VALUES
(3, 2, 11, 10.00, 6377.18, 1.00),
(4, 2, 10, 10.00, 11.99, 0.00),
(5, 3, 10, 10.00, 11.99, 5.00),
(8, 5, 11, 7.00, 6377.18, 2.00),
(9, 5, 10, 9.00, 11.99, 0.00),
(10, 6, 11, 2.00, 6377.18, 1.00),
(11, 6, 10, 10.00, 11.99, 0.00),
(12, 7, 11, 11.00, 6377.18, 1.00),
(13, 7, 10, 15.00, 11.99, 0.00),
(14, 8, 11, 11.00, 6377.18, 2.00),
(15, 8, 10, 15.00, 11.99, 5.00),
(16, 9, 11, 7.00, 6377.18, 5.00),
(17, 9, 10, 8.00, 11.99, 3.00),
(18, 10, 11, 12.00, 6377.18, 10.00),
(19, 10, 10, 18.00, 11.99, 10.00);

-- --------------------------------------------------------

--
-- Estrutura da tabela `cotacao_itens`
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
-- Estrutura da tabela `cotacoes`
--

CREATE TABLE `cotacoes` (
  `id` int NOT NULL,
  `nome` varchar(150) NOT NULL,
  `valor` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `cotacoes`
--

INSERT INTO `cotacoes` (`id`, `nome`, `valor`) VALUES
(1, 'Cotação Teste', 9.10),
(3, 'cot 4', 10.80);

-- --------------------------------------------------------

--
-- Estrutura da tabela `entrada_mercadorias`
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

--
-- Extraindo dados da tabela `entrada_mercadorias`
--

INSERT INTO `entrada_mercadorias` (`id`, `nf_fiscal`, `data_pedido`, `fornecedor_id`, `data_prevista_entrega`, `status_entrega`, `data_entregue`, `transportadora`, `valor`, `observacoes`) VALUES
(40, 'TT-589', '2025-01-01', 2, '2025-02-08', NULL, NULL, 'trans vale', 300.00, 'fdfsd'),
(43, 'TT-589rrrrr', '2025-01-08', 2, '2025-01-21', NULL, NULL, 'gdrdh', 200.00, 'gfdf');

-- --------------------------------------------------------

--
-- Estrutura da tabela `estoque`
--

CREATE TABLE `estoque` (
  `id` int NOT NULL,
  `produtos_id` int DEFAULT NULL,
  `entrada_mercadorias_id` int DEFAULT NULL,
  `quantidade_minima` decimal(5,2) DEFAULT NULL,
  `quantidade` decimal(8,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `estoque`
--

INSERT INTO `estoque` (`id`, `produtos_id`, `entrada_mercadorias_id`, `quantidade_minima`, `quantidade`) VALUES
(5, 10, NULL, 0.00, 44.00),
(6, 11, NULL, 0.00, 51.00);

-- --------------------------------------------------------

--
-- Estrutura da tabela `fabrica`
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
-- Estrutura da tabela `feriados`
--

CREATE TABLE `feriados` (
  `id` int NOT NULL,
  `data_feriado` date NOT NULL,
  `descricao` varchar(200) NOT NULL,
  `tipo` enum('Nacional','Municipal') NOT NULL,
  `facultativo` enum('S','N') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `feriados`
--

INSERT INTO `feriados` (`id`, `data_feriado`, `descricao`, `tipo`, `facultativo`) VALUES
(1, '2025-03-03', 'Carnaval', 'Nacional', 'S');

-- --------------------------------------------------------

--
-- Estrutura da tabela `financeiro_contas`
--

CREATE TABLE `financeiro_contas` (
  `id` int NOT NULL,
  `fornecedor_id` int DEFAULT NULL,
  `cliente_id` int DEFAULT NULL,
  `categoria_id` int DEFAULT NULL,
  `data_vencimento` date DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `data_pagamento` date DEFAULT NULL,
  `status` enum('Pago','Pendente') DEFAULT 'Pendente',
  `observacao` varchar(500) DEFAULT NULL,
  `recorrente` enum('S','N') DEFAULT 'N',
  `tipo` varchar(45) NOT NULL,
  `num_parcelas` int DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `financeiro_contas`
--

INSERT INTO `financeiro_contas` (`id`, `fornecedor_id`, `cliente_id`, `categoria_id`, `data_vencimento`, `valor`, `data_pagamento`, `status`, `observacao`, `recorrente`, `tipo`, `num_parcelas`, `val_par1`, `dt_par1`, `val_par2`, `dt_par2`, `val_par3`, `dt_par3`, `val_par4`, `dt_par4`, `val_par5`, `dt_par5`, `val_par6`, `dt_par6`, `val_par7`, `dt_par7`, `val_par8`, `dt_par8`, `val_par9`, `dt_par9`, `val_par10`, `dt_par10`, `val_par11`, `dt_par11`, `val_par12`, `dt_par12`) VALUES
(1, NULL, 4, NULL, '2025-01-30', 21.00, NULL, 'Pendente', 'fdfs', 'N', 'R', 1, 21.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 2, NULL, 1, '2025-01-31', 200.00, NULL, 'Pendente', 'fdfs', 'N', 'P', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 2, NULL, 1, '2025-01-30', 90.00, NULL, 'Pendente', 'gdfgdf', 'S', 'P', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 2, NULL, 1, '2025-01-24', 20.00, NULL, 'Pendente', 'fdfs', 'N', 'P', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 2, NULL, 1, '2025-01-24', 300.00, NULL, 'Pendente', 'dsdsads', 'S', 'P', 3, 100.00, '2025-01-17', 100.00, '2025-01-22', 100.00, '2025-01-30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 2, NULL, 1, '2025-01-30', 200.00, NULL, 'Pendente', 'fdfdsf', 'S', 'P', 3, 66.67, '2025-01-30', 66.67, '2025-03-02', 66.67, '2025-03-30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 2, NULL, 1, '2025-03-27', 200.00, NULL, 'Pago', 'ouibh', 'S', 'P', 10, 20.00, '2025-03-27', 20.00, '2025-04-27', 20.00, '2025-05-27', 20.00, '2025-06-27', 20.00, '2025-07-27', 20.00, '2025-08-27', 20.00, '2025-09-27', 20.00, '2025-10-27', 20.00, '2025-11-27', 20.00, '2025-12-27', NULL, NULL, NULL, NULL),
(10, NULL, 4, NULL, '2025-01-28', 50.00, NULL, 'Pendente', 'fds', 'N', 'R', 1, 50.00, '2025-01-28', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 2, NULL, 1, '2025-01-31', 200.00, NULL, 'Pendente', 'fdfs', 'N', 'P', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 2, NULL, 1, '2025-01-30', 90.00, NULL, 'Pendente', 'gdfgdf', 'S', 'P', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 2, NULL, 1, '2025-01-24', 20.00, NULL, 'Pendente', 'fdfs', 'N', 'P', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 2, NULL, 1, '2025-01-24', 300.00, NULL, 'Pendente', 'dsdsads', 'S', 'P', 3, 100.00, '2025-01-17', 100.00, '2025-01-22', 100.00, '2025-01-30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `fornecedores`
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

--
-- Extraindo dados da tabela `fornecedores`
--

INSERT INTO `fornecedores` (`id`, `razao_social`, `nome_fantasia`, `cnpj`, `insc_estadual`, `insc_municipal`, `condicao_pagto`, `vigencia_acordo`, `telefone`, `email`, `endereco`, `cidade`, `estado`, `contato`, `site`, `banco`, `numero_banco`, `agencia`, `conta`, `pix`) VALUES
(2, 'fornecedor 1', 'fornecedor 1', '52.266.404/0001-25', '3535345453', '6456544', '645645', '2025-02-06', '64565546', 'unidade@teste.com.br', 'Paulo Setubal', 'Brasil', 'SP', '645645', '645645', 'bb', '6', '645645', '654645', '64545');

-- --------------------------------------------------------

--
-- Estrutura da tabela `grupo_clientes`
--

CREATE TABLE `grupo_clientes` (
  `id` int NOT NULL,
  `nome_grupo` varchar(200) NOT NULL,
  `comissao_vendedores` decimal(10,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `grupo_clientes`
--

INSERT INTO `grupo_clientes` (`id`, `nome_grupo`, `comissao_vendedores`) VALUES
(1, 'testes', 5.00),
(2, 'tttt', 4.00);

-- --------------------------------------------------------

--
-- Estrutura da tabela `grupo_produtos`
--

CREATE TABLE `grupo_produtos` (
  `id` int NOT NULL,
  `nome_grupo` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `grupo_produtos`
--

INSERT INTO `grupo_produtos` (`id`, `nome_grupo`) VALUES
(28, 'Ouro'),
(29, 'Prata'),
(30, 'Platina'),
(31, 'Diamantes'),
(32, 'Relógios'),
(33, 'Pérolas'),
(34, 'Pedras Preciosas');

-- --------------------------------------------------------

--
-- Estrutura da tabela `impressao_etiquetas`
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
-- Estrutura da tabela `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempts` int NOT NULL DEFAULT '0',
  `last_attempt` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `movimentacao_estoque`
--

CREATE TABLE `movimentacao_estoque` (
  `id` int NOT NULL,
  `produto_id` int DEFAULT NULL,
  `descricao_produto` varchar(200) DEFAULT NULL,
  `tipo_movimentacao` enum('Entrada','Saida','Ajuste') NOT NULL,
  `quantidade` decimal(10,2) NOT NULL,
  `documento` varchar(50) DEFAULT NULL,
  `data_movimentacao` date NOT NULL,
  `motivo` varchar(100) DEFAULT NULL,
  `estoque_antes` decimal(10,2) DEFAULT NULL,
  `estoque_atualizado` decimal(10,2) DEFAULT NULL,
  `pedido_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `movimentacao_estoque`
--

INSERT INTO `movimentacao_estoque` (`id`, `produto_id`, `descricao_produto`, `tipo_movimentacao`, `quantidade`, `documento`, `data_movimentacao`, `motivo`, `estoque_antes`, `estoque_atualizado`, `pedido_id`) VALUES
(35, 10, 'Brinco com Diamante - Diamantes - com Nenhuma Pedra - Selecione - Unidade', 'Entrada', 30.00, NULL, '2025-01-23', 'Cadastro de produto', 0.00, 30.00, NULL),
(36, 11, 'Anel - Ouro - com Nenhuma Pedra - Selecione - Unidade', 'Entrada', 20.00, NULL, '2025-01-23', 'Cadastro de produto', 0.00, 20.00, NULL),
(37, 11, 'Anel - Ouro - com Nenhuma Pedra - Selecione - Unidade', 'Entrada', 10.00, 'TT-589', '2025-01-23', 'Cadastro de Entrada de Mercadoria', 20.00, 30.00, NULL),
(38, 10, 'Brinco com Diamante - Diamantes - com Nenhuma Pedra - Selecione - Unidade', 'Entrada', 25.00, 'TT-589', '2025-01-23', 'Cadastro de Entrada de Mercadoria', 30.00, 55.00, NULL),
(43, 11, 'Anel - Ouro - com Nenhuma Pedra - Selecione - Unidade', 'Entrada', 1.00, NULL, '2025-01-24', 'pedido', 1.00, 1.00, NULL),
(44, 10, 'Brinco com Diamante - Diamantes - com Nenhuma Pedra - Selecione - Unidade', 'Entrada', 2.00, NULL, '2025-01-24', 'pedido', 2.00, 2.00, NULL),
(47, 11, 'Anel - Ouro - com Nenhuma Pedra - Selecione - Unidade', 'Entrada', 50.00, 'TT-589rrrrr', '2025-01-27', 'Cadastro de Entrada de Mercadoria', 11.00, 61.00, NULL),
(52, 11, 'Anel - Ouro - com Nenhuma Pedra - Selecione - Unidade', 'Saida', 2.00, 'Consignação-10', '2025-01-28', 'Consignação', 46.00, 48.00, NULL),
(53, 10, 'Brinco com Diamante - Diamantes - com Nenhuma Pedra - Selecione - Unidade', 'Saida', 8.00, 'Consignação-10', '2025-01-28', 'Consignação', 34.00, 42.00, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedidos`
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
  `status_pedido` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `data_entrega` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `cliente_id`, `data_pedido`, `forma_pagamento`, `acrescimo`, `observacoes`, `total`, `valor_pago`, `desconto`, `cod_vendedor`, `status_pedido`, `data_entrega`) VALUES
(2, 3, '2025-01-01', 'Cartão de Crédito', 0.00, 'teste', 8984.44, NULL, 0.00, 2, 'Pendente', '2025-01-30'),
(3, 4, '2025-01-01', 'Cartão de Crédito', 0.00, NULL, 6465.17, NULL, 0.00, 2, 'Pago', '2025-01-30');

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedidos_itens`
--

CREATE TABLE `pedidos_itens` (
  `id` int NOT NULL,
  `pedido_id` int NOT NULL,
  `produto_id` int NOT NULL,
  `quantidade` decimal(10,2) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `desconto_percentual` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `pedidos_itens`
--

INSERT INTO `pedidos_itens` (`id`, `pedido_id`, `produto_id`, `quantidade`, `valor_unitario`, `desconto_percentual`) VALUES
(2, 2, 11, 1.00, 6377.18, 10.00),
(3, 2, 10, 5.00, 11.99, 5.00),
(4, 3, 11, 1.00, 6377.18, 0.00),
(5, 3, 10, 2.00, 11.99, 0.00);

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int NOT NULL,
  `descricao_etiqueta` varchar(200) DEFAULT NULL,
  `fornecedor_id` int DEFAULT NULL,
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
  `grupo_id` int DEFAULT NULL,
  `subgrupo_id` int DEFAULT NULL,
  `unidade` varchar(50) DEFAULT NULL,
  `estoque_princ` decimal(10,2) DEFAULT NULL,
  `cotacao` int DEFAULT NULL,
  `preco_ql` decimal(10,2) DEFAULT NULL,
  `peso_gr` decimal(10,3) DEFAULT NULL,
  `custo` decimal(10,2) DEFAULT NULL,
  `margem` decimal(10,2) DEFAULT NULL,
  `em_reais` decimal(10,2) DEFAULT NULL,
  `capa` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `produtos`
--

INSERT INTO `produtos` (`id`, `descricao_etiqueta`, `fornecedor_id`, `modelo`, `macica_ou_oca`, `numeros`, `pedra`, `nat_ou_sint`, `peso`, `aros`, `cm`, `pontos`, `mm`, `grupo_id`, `subgrupo_id`, `unidade`, `estoque_princ`, `cotacao`, `preco_ql`, `peso_gr`, `custo`, `margem`, `em_reais`, `capa`) VALUES
(10, 'Brinco com Diamante - Diamantes - com Nenhuma Pedra - Baiano - Selecione - Unidade', 2, 'Baiano', NULL, NULL, 'Nenhuma Pedra', NULL, NULL, NULL, NULL, NULL, NULL, 31, 111, 'unidade', 30.00, 3, 1.00, 1.000, 10.80, 11.00, 11.99, NULL),
(11, 'Anel - Ouro - com Nenhuma Pedra - Grume - Selecione - Unidade', 2, 'Grume', NULL, NULL, 'Nenhuma Pedra', NULL, NULL, NULL, NULL, NULL, NULL, 28, 95, 'unidade', 20.00, 3, 22.00, 22.000, 5227.20, 22.00, 6377.18, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `subgrupo_produtos`
--

CREATE TABLE `subgrupo_produtos` (
  `id` int NOT NULL,
  `nome_subgrupo` varchar(200) NOT NULL,
  `grupo_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `subgrupo_produtos`
--

INSERT INTO `subgrupo_produtos` (`id`, `nome_subgrupo`, `grupo_id`) VALUES
(95, 'Anel', 28),
(96, 'Brinco', 28),
(97, 'Pulseira', 28),
(98, 'Corrente', 28),
(99, 'Pingente', 28),
(100, 'Anel', 29),
(101, 'Brinco', 29),
(102, 'Pulseira', 29),
(103, 'Corrente', 29),
(104, 'Pingente', 29),
(105, 'Anel', 30),
(106, 'Brinco', 30),
(107, 'Pulseira', 30),
(108, 'Corrente', 30),
(109, 'Pingente', 30),
(110, 'Anel com Diamante', 31),
(111, 'Brinco com Diamante', 31),
(112, 'Pingente com Diamante', 31),
(113, 'Pulseira com Diamante', 31),
(114, 'Relógio de Pulso', 32),
(115, 'Relógio de Bolso', 32),
(116, 'Colar de Pérolas', 33),
(117, 'Brinco de Pérolas', 33),
(118, 'Pulseira de Pérolas', 33),
(119, 'Anel com Rubi', 34),
(120, 'Anel com Safira', 34),
(121, 'Anel com Esmeralda', 34),
(122, 'Pingente com Rubi', 34),
(123, 'Pingente com Safira', 34),
(124, 'Pingente com Esmeralda', 34);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tipos`
--

CREATE TABLE `tipos` (
  `id` int NOT NULL,
  `descricao` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
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
  `numero` varchar(150) NOT NULL,
  `status` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome_completo`, `email`, `cargo`, `telefone`, `rg`, `emissao_rg`, `cpf`, `data_nascimento`, `cep`, `endereco`, `cidade`, `estado`, `login`, `senha`, `nivel_acesso`, `bairro`, `numero`, `status`) VALUES
(2, 'Luis Henrique Bovolato', 'bovolato@gmail.com', 1, '12991519678', '32.132.132-1', '1998-12-15', '34606740833', '1985-07-17', '11688-632', 'Rua Paulo Setubal', 'Ubatuba', 'SP', 'admin', '$2y$10$/8QoRMZyx3BpAmuUhOBfEeZ813o.zpIWi9CuWntv0tJe7FwXXdeqe', 'Administrador', 'Itaguá', '291', 0),
(3, 'Bruna MArtins', 'bauru@evoestagios.com.br', 2, '12991519678', '32.132.132-1', '2025-01-02', '4645645', '2025-01-10', '11688-632', 'Rua Paulo Setubal', 'Ubatuba', 'SP', 'fsdfsdd', '$2y$10$VPlN8ocIq5CWjU.qZ0yKheHmy1kKpW7cb9wRIQFZRZpWGjbwWQxwS', 'Operador', 'Itaguá', '435', 1);

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
  ADD KEY `fk_estoque_produtos1_idx` (`produtos_id`),
  ADD KEY `fk_estoque_entrada_mercadorias1_idx` (`entrada_mercadorias_id`);

--
-- Índices para tabela `fabrica`
--
ALTER TABLE `fabrica`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fabped` (`pedido_id`),
  ADD KEY `fabcli` (`cliente_id`),
  ADD KEY `fabusu` (`vendedor_id`);

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
  ADD KEY `ped_prod` (`produto_id`);

--
-- Índices para tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedfor` (`fornecedor_id`),
  ADD KEY `pedgrupo` (`grupo_id`),
  ADD KEY `cotprod2_idx` (`cotacao`),
  ADD KEY `pedsubgrupo_idx` (`subgrupo_id`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `cartoes`
--
ALTER TABLE `cartoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `categoria_despesa`
--
ALTER TABLE `categoria_despesa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `comissao_vendedor`
--
ALTER TABLE `comissao_vendedor`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de tabela `consignacao`
--
ALTER TABLE `consignacao`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `consignacao_itens`
--
ALTER TABLE `consignacao_itens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `cotacao_itens`
--
ALTER TABLE `cotacao_itens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cotacoes`
--
ALTER TABLE `cotacoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `entrada_mercadorias`
--
ALTER TABLE `entrada_mercadorias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de tabela `estoque`
--
ALTER TABLE `estoque`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `fornecedores`
--
ALTER TABLE `fornecedores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `grupo_clientes`
--
ALTER TABLE `grupo_clientes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `grupo_produtos`
--
ALTER TABLE `grupo_produtos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de tabela `impressao_etiquetas`
--
ALTER TABLE `impressao_etiquetas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `movimentacao_estoque`
--
ALTER TABLE `movimentacao_estoque`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `pedidos_itens`
--
ALTER TABLE `pedidos_itens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `subgrupo_produtos`
--
ALTER TABLE `subgrupo_produtos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT de tabela `tipos`
--
ALTER TABLE `tipos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  ADD CONSTRAINT `conseid` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Limitadores para a tabela `consignacao_itens`
--
ALTER TABLE `consignacao_itens`
  ADD CONSTRAINT `consid` FOREIGN KEY (`consignacao_id`) REFERENCES `consignacao` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `contprod` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Limitadores para a tabela `cotacao_itens`
--
ALTER TABLE `cotacao_itens`
  ADD CONSTRAINT `cotid` FOREIGN KEY (`cotacao_id`) REFERENCES `cotacoes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cotprod` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Limitadores para a tabela `entrada_mercadorias`
--
ALTER TABLE `entrada_mercadorias`
  ADD CONSTRAINT `entradafor` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limitadores para a tabela `estoque`
--
ALTER TABLE `estoque`
  ADD CONSTRAINT `fk_estoque_entrada_mercadorias1` FOREIGN KEY (`entrada_mercadorias_id`) REFERENCES `entrada_mercadorias` (`id`),
  ADD CONSTRAINT `fk_estoque_produtos1` FOREIGN KEY (`produtos_id`) REFERENCES `produtos` (`id`);

--
-- Limitadores para a tabela `fabrica`
--
ALTER TABLE `fabrica`
  ADD CONSTRAINT `fabcli` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fabped` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fabusu` FOREIGN KEY (`vendedor_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limitadores para a tabela `financeiro_contas`
--
ALTER TABLE `financeiro_contas`
  ADD CONSTRAINT `cliid` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `fincat` FOREIGN KEY (`categoria_id`) REFERENCES `categoria_despesa` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `finfor` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedores` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Limitadores para a tabela `impressao_etiquetas`
--
ALTER TABLE `impressao_etiquetas`
  ADD CONSTRAINT `atiprod` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limitadores para a tabela `movimentacao_estoque`
--
ALTER TABLE `movimentacao_estoque`
  ADD CONSTRAINT `movprod` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Limitadores para a tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `ped_cli` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `ped_usu` FOREIGN KEY (`cod_vendedor`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limitadores para a tabela `pedidos_itens`
--
ALTER TABLE `pedidos_itens`
  ADD CONSTRAINT `ped_id` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ped_prod` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Limitadores para a tabela `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `cotprod2` FOREIGN KEY (`cotacao`) REFERENCES `cotacoes` (`id`),
  ADD CONSTRAINT `pedfor` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pedgrupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupo_produtos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pedsubgrupo` FOREIGN KEY (`subgrupo_id`) REFERENCES `subgrupo_produtos` (`id`);

--
-- Limitadores para a tabela `subgrupo_produtos`
--
ALTER TABLE `subgrupo_produtos`
  ADD CONSTRAINT `subgruidgru` FOREIGN KEY (`grupo_id`) REFERENCES `grupo_produtos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Limitadores para a tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usucargo` FOREIGN KEY (`cargo`) REFERENCES `cargos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

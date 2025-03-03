-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Tempo de geração: 24/02/2025 às 16:27
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
  `cargo` varchar(45) NOT NULL,
  `fabrica` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cargos`
--

INSERT INTO `cargos` (`id`, `cargo`, `fabrica`) VALUES
(1, 'Admin', 0),
(2, 'Gerente', 0);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cartoes`
--

INSERT INTO `cartoes` (`id`, `nome_cartao`, `taxa_administradora`, `tipo`, `bandeira`, `max_parcelas`, `juros_parcela_1`, `juros_parcela_2`, `juros_parcela_3`, `juros_parcela_4`, `juros_parcela_5`, `juros_parcela_6`, `juros_parcela_7`, `juros_parcela_8`, `juros_parcela_9`, `juros_parcela_10`, `juros_parcela_11`, `juros_parcela_12`) VALUES
(2, 'teste 3', 5.00, 'Crédito', 'Visa', 3, 1.00, 55.00, 3.00, 4.00, 5.00, 6.00, 7.00, 8.00, 9.00, 10.00, 11.00, 12.00),
(3, 'teste 3', 10.00, 'Débito', 'Mastercard', 1, 5.00, 12.00, 45.00, 11.00, 20.00, 31.00, 10.00, 11.00, 123.00, 321.00, 10.00, 200.00),
(4, 'elo', 2.00, 'Crédito', 'Visa', 5, 14.00, 16.00, 18.00, 22.00, 24.00, 26.00, 28.00, 31.00, 32.00, 33.00, 33.00, 33.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `categoria_despesa`
--

CREATE TABLE `categoria_despesa` (
  `id` int NOT NULL,
  `descricao` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `comissao_vendedor`
--

CREATE TABLE `comissao_vendedor` (
  `id` int NOT NULL,
  `comissao_v` decimal(10,2) DEFAULT NULL,
  `comissao_a` decimal(10,2) DEFAULT NULL,
  `grupo_produtos_id` int NOT NULL,
  `usuarios_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cotacoes`
--

CREATE TABLE `cotacoes` (
  `id` int NOT NULL,
  `nome` varchar(150) NOT NULL,
  `valor` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cotacoes`
--

INSERT INTO `cotacoes` (`id`, `nome`, `valor`) VALUES
(1, 'Cotação 1', 9.10),
(3, 'Cotação 2', 10.80);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estoque`
--

CREATE TABLE `estoque` (
  `id` int NOT NULL,
  `produtos_id` int DEFAULT NULL,
  `entrada_mercadorias_id` int DEFAULT NULL,
  `quantidade_minima` decimal(5,2) DEFAULT NULL,
  `quantidade` decimal(8,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `fabrica`
--

CREATE TABLE `fabrica` (
  `id` int NOT NULL,
  `pedido_id` int DEFAULT NULL,
  `data_solicitacao` date DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `status` enum('Aguardando Separacao','Em Producao','Finalizado') DEFAULT 'Aguardando Separacao',
  `etapa_atual` varchar(100) DEFAULT NULL,
  `data_entrega` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `fabrica_etapas`
--

CREATE TABLE `fabrica_etapas` (
  `id` int NOT NULL,
  `fabrica_id` int NOT NULL,
  `usuarios_id` int NOT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `status` int DEFAULT NULL,
  `observacao` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `feriados`
--

INSERT INTO `feriados` (`id`, `data_feriado`, `descricao`, `tipo`, `facultativo`) VALUES
(1, '2025-03-03', 'Carnaval', 'Nacional', 'S');

-- --------------------------------------------------------

--
-- Estrutura para tabela `financeiro_contas`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `fornecedores`
--

INSERT INTO `fornecedores` (`id`, `razao_social`, `nome_fantasia`, `cnpj`, `insc_estadual`, `insc_municipal`, `condicao_pagto`, `vigencia_acordo`, `telefone`, `email`, `endereco`, `cidade`, `estado`, `contato`, `site`, `banco`, `numero_banco`, `agencia`, `conta`, `pix`) VALUES
(2, 'Fornecedor 1', 'Fornecedor 1', '52.266.404/0001-25', '3535345453', '6456544', '645645', '2025-02-06', '64565546', 'unidade@teste.com.br', 'Paulo Setubal', 'Brasil', 'SP', '645645', '645645', 'bb', '6', '645645', '654645', '64545'),
(3, 'Mão De Obra', 'Mão De Obra', '52.266.404/0001-25', '3535345453', '6456544', '5435345345', '2025-07-03', '12991519678', 'unidade@teste.com.br', 'Paulo Setubal', 'Brasil', 'SP', '53453454', '645645', 'bb', '01', '27480', '119008', '64545');

-- --------------------------------------------------------

--
-- Estrutura para tabela `grupo_clientes`
--

CREATE TABLE `grupo_clientes` (
  `id` int NOT NULL,
  `nome_grupo` varchar(200) NOT NULL,
  `comissao_vendedores` decimal(10,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `grupo_clientes`
--

INSERT INTO `grupo_clientes` (`id`, `nome_grupo`, `comissao_vendedores`) VALUES
(1, 'Grupo 1 Clientes', 5.00),
(2, 'Grupo 2 Clientes', 4.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `grupo_produtos`
--

CREATE TABLE `grupo_produtos` (
  `id` int NOT NULL,
  `nome_grupo` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `grupo_produtos`
--

INSERT INTO `grupo_produtos` (`id`, `nome_grupo`) VALUES
(28, 'Ouro'),
(29, 'Prata'),
(30, 'Platina'),
(31, 'Diamantes'),
(35, 'Confecção'),
(36, 'Conserto');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempts` int NOT NULL DEFAULT '0',
  `last_attempt` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs`
--

CREATE TABLE `logs` (
  `id` int NOT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `acao` text,
  `valor_anterior` text,
  `valor_atual` text,
  `data` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `movimentacao_estoque`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `status_pedido` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `data_entrega` date DEFAULT NULL,
  `orcamento` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos_itens`
--

CREATE TABLE `pedidos_itens` (
  `id` int NOT NULL,
  `pedido_id` int NOT NULL,
  `produto_id` int DEFAULT NULL,
  `quantidade` decimal(10,2) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `desconto_percentual` decimal(5,2) DEFAULT NULL,
  `descricao_produto` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
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
  `capa` longtext,
  `url` text,
  `insumo` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produto_definicoes`
--

CREATE TABLE `produto_definicoes` (
  `id` int NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produto_definicoes`
--

INSERT INTO `produto_definicoes` (`id`, `nome`, `tipo`) VALUES
(1, 'Diamante', 'pedra'),
(2, 'Safira', 'pedra'),
(3, 'Rubi', 'pedra'),
(4, 'Esmeralda', 'pedra'),
(5, 'Ametista', 'pedra'),
(6, 'Topázio', 'pedra'),
(7, 'Turmalina', 'pedra'),
(8, 'Quartzo', 'pedra'),
(9, 'Âmbar', 'pedra'),
(10, 'Opala', 'pedra'),
(11, 'Jade', 'pedra'),
(12, 'Turquesa', 'pedra'),
(13, 'Zircônia', 'pedra'),
(14, 'Lápis-lazúli', 'pedra'),
(15, 'Cristal', 'pedra'),
(16, '3 Aros Liso 5 Com Pedras', 'modelo'),
(17, 'Aro Entrelaçado Com', 'modelo'),
(18, 'Baiano', 'modelo'),
(19, 'Bola', 'modelo'),
(20, 'Cartier', 'modelo'),
(21, 'Elos 1 X 1', 'modelo'),
(22, 'Elos 2 X 1', 'modelo'),
(23, 'Elos 3 X 1', 'modelo'),
(24, 'Grume', 'modelo'),
(25, 'Piastrine', 'modelo'),
(26, 'Singa Pura', 'modelo'),
(27, 'Veneziana', 'modelo'),
(28, 'teste', 'modelo'),
(29, 'teste2', 'modelo'),
(31, 'bbbbbbbbbbbbbb', 'modelo'),
(32, 'ccccccccccc', 'modelo'),
(33, 'ddddddddddddddd', 'modelo'),
(34, 'affffffffffff', 'modelo'),
(35, 'cece', 'modelo'),
(36, 'fsdfsdfsdsdsdfdsd', 'modelo'),
(37, 'hhhhhhhhhhhhhhhh', 'modelo'),
(38, 'tatataa', 'modelo'),
(39, 'calcario', 'pedra'),
(40, 'dadada', 'pedra'),
(41, 'dadatete', 'modelo'),
(42, 'zaza', 'pedra'),
(43, 'teste44', 'modelo'),
(44, 'dsds', 'modelo'),
(45, 'ewewew', 'pedra'),
(46, 'ewewew', 'modelo'),
(47, 'tetetete', 'modelo'),
(48, 'modelo Luis', 'modelo'),
(49, 'modelo teste edit', 'modelo'),
(50, 'pedra teste edit', 'pedra'),
(51, 'qqqqqqqqqqqqqqq', 'modelo'),
(52, 'fdssdfdfds', 'modelo'),
(53, 'Bovo', 'modelo'),
(55, 'teste etic', 'modelo'),
(57, 'testepedra sabao', 'pedra'),
(58, 'pedranova', 'pedra');

-- --------------------------------------------------------

--
-- Estrutura para tabela `subgrupo_produtos`
--

CREATE TABLE `subgrupo_produtos` (
  `id` int NOT NULL,
  `nome_subgrupo` varchar(200) NOT NULL,
  `grupo_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `subgrupo_produtos`
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
(126, 'Diamente redondo 3', 31),
(127, 'Ouro 18k', 28),
(128, 'Prata de Bali', 29),
(129, 'Aumente de anel 1pto', 36),
(130, 'Platina Pura 100%', 30),
(131, 'Ouro 24K', 28);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipos`
--

CREATE TABLE `tipos` (
  `id` int NOT NULL,
  `descricao` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `senha` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nivel_acesso` enum('Administrador','Operador','Consulta') NOT NULL,
  `bairro` varchar(150) NOT NULL,
  `numero` varchar(150) NOT NULL,
  `status` tinyint NOT NULL,
  `permissoes` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome_completo`, `email`, `cargo`, `telefone`, `rg`, `emissao_rg`, `cpf`, `data_nascimento`, `cep`, `endereco`, `cidade`, `estado`, `login`, `senha`, `nivel_acesso`, `bairro`, `numero`, `status`, `permissoes`) VALUES
(5, 'Equipe Desenvolvimento', 'teste@teste.com', 1, '12991519678', '32.132.123-1', '2025-01-29', '99999999999', '2025-02-06', '11688-632', 'Rua Paulo Setubal', 'Ubatuba', 'SP', 'bovolato', '$2y$10$Sok2OqR1rCaAfbZwcFuXj.8jGfQou1U7h83JbuVx5MDsqg6cHfqEK', 'Administrador', 'Itaguá', '291', 1, '{\"Cargos\":{\"visualizar\":true,\"manipular\":false},\"Clientes\":{\"visualizar\":true,\"manipular\":false},\"Feriados\":{\"visualizar\":true,\"manipular\":true}}'),
(6, 'Admin', 'admin@admin.com', 1, '999999999', '999999999', '2025-02-24', '09907383880', '2025-02-05', '18017-189', 'Rua Moacyr Razl', 'Sorocaba', 'SP', 'admin', '$2y$10$kZSTakbAd2ci8fuJjRKSf.V1IWOVb8szXre7law4X.BghHGy4Zd6m', 'Administrador', 'Granja Olga I', '145', 1, NULL);

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
-- Índices de tabela `comissao_vendedor`
--
ALTER TABLE `comissao_vendedor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_comissao_vendedor_grupo_produtos1_idx` (`grupo_produtos_id`),
  ADD KEY `fk_comissao_vendedor_usuarios1_idx` (`usuarios_id`);

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
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `entrada_mercadorias`
--
ALTER TABLE `entrada_mercadorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entradafor` (`fornecedor_id`);

--
-- Índices de tabela `estoque`
--
ALTER TABLE `estoque`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_estoque_produtos1_idx` (`produtos_id`),
  ADD KEY `fk_estoque_entrada_mercadorias1_idx` (`entrada_mercadorias_id`);

--
-- Índices de tabela `fabrica`
--
ALTER TABLE `fabrica`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fabped` (`pedido_id`);

--
-- Índices de tabela `fabrica_etapas`
--
ALTER TABLE `fabrica_etapas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_fabrica_etapas_fabrica1_idx` (`fabrica_id`),
  ADD KEY `fk_fabrica_etapas_usuarios1_idx` (`usuarios_id`);

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
  ADD KEY `fincat` (`categoria_id`),
  ADD KEY `cliid_idx` (`cliente_id`);

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
-- Índices de tabela `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip_address` (`ip_address`);

--
-- Índices de tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `ped_prod` (`produto_id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedfor` (`fornecedor_id`),
  ADD KEY `pedgrupo` (`grupo_id`),
  ADD KEY `cotprod2_idx` (`cotacao`),
  ADD KEY `pedsubgrupo_idx` (`subgrupo_id`);

--
-- Índices de tabela `produto_definicoes`
--
ALTER TABLE `produto_definicoes`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de tabela `estoque`
--
ALTER TABLE `estoque`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `fabrica`
--
ALTER TABLE `fabrica`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fabrica_etapas`
--
ALTER TABLE `fabrica_etapas`
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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `grupo_clientes`
--
ALTER TABLE `grupo_clientes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `grupo_produtos`
--
ALTER TABLE `grupo_produtos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de tabela `impressao_etiquetas`
--
ALTER TABLE `impressao_etiquetas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `movimentacao_estoque`
--
ALTER TABLE `movimentacao_estoque`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de tabela `pedidos_itens`
--
ALTER TABLE `pedidos_itens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de tabela `produto_definicoes`
--
ALTER TABLE `produto_definicoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT de tabela `subgrupo_produtos`
--
ALTER TABLE `subgrupo_produtos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT de tabela `tipos`
--
ALTER TABLE `tipos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `grupo` FOREIGN KEY (`grupo`) REFERENCES `grupo_clientes` (`id`);

--
-- Restrições para tabelas `comissao_vendedor`
--
ALTER TABLE `comissao_vendedor`
  ADD CONSTRAINT `fk_comissao_vendedor_grupo_produtos1` FOREIGN KEY (`grupo_produtos_id`) REFERENCES `grupo_produtos` (`id`),
  ADD CONSTRAINT `fk_comissao_vendedor_usuarios1` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`);

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
-- Restrições para tabelas `entrada_mercadorias`
--
ALTER TABLE `entrada_mercadorias`
  ADD CONSTRAINT `entradafor` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Restrições para tabelas `estoque`
--
ALTER TABLE `estoque`
  ADD CONSTRAINT `fk_estoque_entrada_mercadorias1` FOREIGN KEY (`entrada_mercadorias_id`) REFERENCES `entrada_mercadorias` (`id`),
  ADD CONSTRAINT `fk_estoque_produtos1` FOREIGN KEY (`produtos_id`) REFERENCES `produtos` (`id`);

--
-- Restrições para tabelas `fabrica`
--
ALTER TABLE `fabrica`
  ADD CONSTRAINT `fabped` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Restrições para tabelas `fabrica_etapas`
--
ALTER TABLE `fabrica_etapas`
  ADD CONSTRAINT `fk_fabrica_etapas_fabrica1` FOREIGN KEY (`fabrica_id`) REFERENCES `fabrica` (`id`),
  ADD CONSTRAINT `fk_fabrica_etapas_usuarios1` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `financeiro_contas`
--
ALTER TABLE `financeiro_contas`
  ADD CONSTRAINT `cliid` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
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
  ADD CONSTRAINT `ped_id` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `cotprod2` FOREIGN KEY (`cotacao`) REFERENCES `cotacoes` (`id`),
  ADD CONSTRAINT `pedfor` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pedgrupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupo_produtos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pedsubgrupo` FOREIGN KEY (`subgrupo_id`) REFERENCES `subgrupo_produtos` (`id`);

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

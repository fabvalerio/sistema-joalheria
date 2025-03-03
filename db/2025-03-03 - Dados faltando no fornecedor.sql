ALTER TABLE `fornecedores` ADD `numero` VARCHAR(20) NULL AFTER `endereco`, ADD `complemento` VARCHAR(200) NULL AFTER `numero`;

ALTER TABLE `fornecedores` ADD `cep` VARCHAR(20) NOT NULL AFTER `email`;

ALTER TABLE `fornecedores` ADD `whatsapp` VARCHAR(20) NULL AFTER `telefone`;

ALTER TABLE `fornecedores` ADD `bairro` VARCHAR(200) NULL AFTER `numero`;
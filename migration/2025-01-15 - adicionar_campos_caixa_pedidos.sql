-- Adicionar campos para controle de caixa na tabela pedidos
ALTER TABLE `pedidos` 
ADD COLUMN `troco_abertura` DECIMAL(10,2) DEFAULT 0.00 AFTER `orcamento`,
ADD COLUMN `troco_fechamento` DECIMAL(10,2) DEFAULT 0.00 AFTER `troco_abertura`,
ADD COLUMN `data_caixa` DATE NULL AFTER `troco_fechamento`,
ADD COLUMN `observacoes_caixa` TEXT NULL AFTER `data_caixa`;

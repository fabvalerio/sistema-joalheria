-- Adicionar campo desconto_percentual na tabela consignacao
-- Data: 2025-10-24
-- Descrição: Campo para armazenar o percentual de desconto aplicado na consignação

ALTER TABLE `consignacao` 
ADD COLUMN `desconto_percentual` DECIMAL(5,2) DEFAULT 0.00 AFTER `observacao`;

-- Comentário do campo
ALTER TABLE `consignacao` 
MODIFY COLUMN `desconto_percentual` DECIMAL(5,2) DEFAULT 0.00 
COMMENT 'Percentual de desconto aplicado (0.00 a 100.00)';


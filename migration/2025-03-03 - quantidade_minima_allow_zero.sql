-- Permite quantidade_minima = 0 e NULL na tabela estoque
-- Corrige erro: SQLSTATE[22003]: Numeric value out of range

ALTER TABLE `estoque`
  MODIFY COLUMN `quantidade_minima` decimal(10,2) DEFAULT NULL;

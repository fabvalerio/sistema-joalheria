-- Adicionar coluna tempo (dias de confecção) na tabela grupo_produtos
ALTER TABLE grupo_produtos ADD COLUMN tempo INT DEFAULT 0 COMMENT 'Dias de confecção do grupo de produtos';

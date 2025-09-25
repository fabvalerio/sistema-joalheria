-- Inserir materiais padrão na tabela material
INSERT INTO material (nome) VALUES 
    ('Ouro'),
    ('Prata'),
    ('Platina'),
    ('Aço'),
    ('Cobre'),
    ('Bronze'),
    ('Titânio'),
    ('Palladium'),
    ('Rodio'),
    ('Outros')
ON DUPLICATE KEY UPDATE nome = VALUES(nome);

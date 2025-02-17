-- MySQL Workbench Synchronization
-- Generated: 2025-02-11 15:36
-- Model: New Model
-- Version: 1.0
-- Project: Name of the project
-- Author: fabva

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

ALTER TABLE `joalheria`.`fabrica` 
DROP FOREIGN KEY `fabusu`,
DROP FOREIGN KEY `fabcli`;

ALTER TABLE `joalheria`.`fabrica` 
DROP COLUMN `fila_producao`,
DROP COLUMN `posse`,
DROP COLUMN `vendedor_id`,
DROP COLUMN `data_inicio`,
DROP COLUMN `cliente_id`,
DROP INDEX `fabusu` ,
DROP INDEX `fabcli` ;
;

CREATE TABLE IF NOT EXISTS `joalheria`.`fabrica_etapas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `fabrica_id` INT(11) NOT NULL,
  `usuarios_id` INT(11) NOT NULL,
  `data_inicio` DATE NULL DEFAULT NULL,
  `data_fim` DATE NULL DEFAULT NULL,
  `status` INT(11) NULL DEFAULT NULL,
  `observacao` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_fabrica_etapas_fabrica1_idx` (`fabrica_id` ASC),
  INDEX `fk_fabrica_etapas_usuarios1_idx` (`usuarios_id` ASC),
  CONSTRAINT `fk_fabrica_etapas_fabrica1`
    FOREIGN KEY (`fabrica_id`)
    REFERENCES `joalheria`.`fabrica` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_fabrica_etapas_usuarios1`
    FOREIGN KEY (`usuarios_id`)
    REFERENCES `joalheria`.`usuarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

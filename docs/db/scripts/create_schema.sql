SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


-- -----------------------------------------------------
-- Table `types`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `types` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `subject` VARCHAR(255) NULL ,
  `body` TEXT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `emails`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `emails` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `typeId` INT NOT NULL ,
  `email` VARCHAR(455) NOT NULL ,
  `sent` TINYINT(1) NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_emails_types_idx` (`typeId` ASC) ,
  CONSTRAINT `fk_emails_types`
    FOREIGN KEY (`typeId` )
    REFERENCES `types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- SQL Patch to add reset token support to the Login table
ALTER TABLE `Login` 
ADD COLUMN `reset_token` VARCHAR(64) DEFAULT NULL,
ADD COLUMN `reset_token_expires` DATETIME DEFAULT NULL;

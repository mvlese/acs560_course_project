-- MySQL dump 10.13  Distrib 5.5.31, for debian-linux-gnu (armv7l)
--
-- Host: 10.0.0.77    Database: jot
-- ------------------------------------------------------
-- Server version	5.5.31-0+wheezy1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `jot`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `jot` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `jot`;

--
-- Table structure for table `entities`
--

DROP TABLE IF EXISTS `entities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entities` (
  `identities` int(11) NOT NULL AUTO_INCREMENT,
  `iduser` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`identities`),
  KEY `fk_entities_1` (`iduser`),
  CONSTRAINT `fk_entities_1` FOREIGN KEY (`iduser`) REFERENCES `users` (`iduser`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `entity_items`
--

DROP TABLE IF EXISTS `entity_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entity_items` (
  `identity_items` int(11) NOT NULL,
  `identities` int(11) NOT NULL,
  `iditem_types` int(11) NOT NULL,
  `annotation` varchar(1024) DEFAULT NULL,
  `item` blob,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `primary_key` (`identity_items`,`identities`),
  KEY `fk_entity_items_1` (`identities`),
  KEY `fk_entity_items_2` (`iditem_types`),
  CONSTRAINT `fk_entity_items_1` FOREIGN KEY (`identities`) REFERENCES `entities` (`identities`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_entity_items_2` FOREIGN KEY (`iditem_types`) REFERENCES `item_types` (`iditem_types`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `item_types`
--

DROP TABLE IF EXISTS `item_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_types` (
  `iditem_types` int(11) NOT NULL AUTO_INCREMENT,
  `friendly_name` varchar(45) NOT NULL,
  PRIMARY KEY (`iditem_types`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `parameters`
--

DROP TABLE IF EXISTS `parameters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parameters` (
  `default_space_mb` int(11) NOT NULL DEFAULT '100'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shared_entities`
--

DROP TABLE IF EXISTS `shared_entities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shared_entities` (
  `from_userid` int(11) NOT NULL,
  `to_userid` int(11) NOT NULL,
  `identity_items` int(11) NOT NULL,
  KEY `fk_shared_entities_1` (`from_userid`),
  KEY `fk_shared_entities_2` (`to_userid`),
  KEY `fk_shared_entities_3` (`identity_items`),
  CONSTRAINT `fk_shared_entities_1` FOREIGN KEY (`from_userid`) REFERENCES `users` (`iduser`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_shared_entities_2` FOREIGN KEY (`to_userid`) REFERENCES `users` (`iduser`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_shared_entities_3` FOREIGN KEY (`identity_items`) REFERENCES `entity_items` (`identity_items`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `iduser` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(45) NOT NULL,
  `pw` varchar(128) NOT NULL,
  `token` varchar(45) NOT NULL,
  `is_active` int(1) NOT NULL DEFAULT '0',
  `last_modified` timestamp NULL DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `space_remaining_kb` int(11) DEFAULT NULL,
  PRIMARY KEY (`iduser`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'jot'
--
/*!50003 DROP PROCEDURE IF EXISTS `addUser` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`acs560`@`%` PROCEDURE `addUser`(
    i_username varchar(45), 
    i_hashedPassword varchar(128), out o_status int)
BEGIN
    declare v_count int;
    declare v_default_space_mb int;
    declare v_token varchar(45);
    set o_status = 0;
    set v_token = '';
    call getToken(v_token);
    select default_space_mb into v_default_space_mb from parameters;
    select count(*) into v_count from users where username = i_username;
    if (v_count > 0)
    then
        set o_status = -1;
    else
        
        insert into users (username, pw, token, is_active, last_modified, space_remaining_kb) 
            values (i_username, i_hashedPassword, v_token, 1, now(), v_default_space_mb * 1000);
        set o_status = 0;
    end if;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `createTokenForUser` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`acs560`@`%` PROCEDURE `createTokenForUser`(
    i_username varchar(45),
    inout o_token varchar(45)
)
BEGIN
    declare v_token varchar(45);
    set v_token = '';
    call getToken(v_token);
    update users set token = v_token where username = i_username;
    set o_token = v_token;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `getToken` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`acs560`@`%` PROCEDURE `getToken`(out o_token varchar(45))
BEGIN
	declare v_token varchar(45);
	declare v_flag int;
	set o_token = '';
	set v_flag = 1;
	-- Loop until an unused token is found.
	while v_flag > 0
	do
		set v_token = 
        substr(concat(
            trim(convert(floor(rand() * 1000000000), char(24))),
            trim(convert(floor(rand() * 1000000000), char(24)))
        ), 1, 13);
		select count(*) into v_flag from users where token = v_token;
	end while;
	set o_token = v_token;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `storeEntity` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`acs560`@`%` PROCEDURE `storeEntity`(
    i_token varchar(45),
    i_title varchar(45),
    out o_identity int)
BEGIN
    declare v_iduser int;
    declare v_count int;
    select iduser into v_iduser from users where token = i_token;
    select count(*) into v_count from entities where 
        iduser = v_iduser and title = i_title;
    if v_count = 0
    then
        insert into entities (iduser, title, last_modified) values
            (v_iduser, i_title, CURRENT_TIMESTAMP);
        SELECT LAST_INSERT_ID() into o_identity;
    else
        update entities set last_modified = CURRENT_TIMESTAMP
            where iduser = v_iduser and title = i_title;  
        select identities into o_identity from entities where 
            iduser = v_iduser and title = i_title;
    end if;
    
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `storeEntityItem` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`acs560`@`%` PROCEDURE `storeEntityItem`(
    i_token varchar(45),
    i_identity int,
    i_identityItem int,
    i_itemtype varchar(45),
    i_annotation varchar(200),
    i_bdata blob,
    o_return int
)
BEGIN
    declare v_count int;
    declare v_itemtype int;
    
    set v_itemtype = -1;
    set o_return = -1;
    
    select iditem_types into v_itemtype from item_types 
        where friendly_name = lower(i_itemtype);
    if v_itemtype is null
    then
        set o_return = -2;
    else
        -- If this entity item exits, update it else insert it.
        select count(*) into v_count from entity_items 
            where identities = i_identity and identity_items = i_identityItem;
        if v_count = 0
        then
            insert into entity_items (identity_items, identities, iditem_types, annotation)
                values (i_identityItem, i_identity, v_itemtype, i_annotation);
        else
            -- update (with blob?)
            update entity_items set iditem_types = v_itemtype, annotation = i_annotation
                where identities = i_identity and identity_items = i_identityItem; 
        end if;
        -- Store blob if necessary.
        if v_itemtype <> 1
        then
            update entity_items set item = i_bdata 
                where identities = i_identity and identity_items = i_identityItem;
        end if;
        set o_return = 0;
    end if;
    
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-11-12 22:53:56

-- MySQL dump 10.13  Distrib 8.0.19, for Linux (x86_64)
--
-- Host: localhost    Database: dpl
-- ------------------------------------------------------
-- Server version	8.0.19

/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE = @@TIME_ZONE */;
/*!40103 SET TIME_ZONE = '+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS = @@UNIQUE_CHECKS, UNIQUE_CHECKS = 0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0 */;
/*!40101 SET @OLD_SQL_MODE = @@SQL_MODE, SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES = @@SQL_NOTES, SQL_NOTES = 0 */;

--
-- Table structure for table `addresses`
--

DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `addresses`
(
    `id`          int          NOT NULL AUTO_INCREMENT,
    `create_date` datetime     NOT NULL COMMENT 'Дата создания',
    `index`       int                   DEFAULT NULL COMMENT 'Индекс',
    `area`        int                   DEFAULT NULL COMMENT 'Область',
    `region`      varchar(255)          DEFAULT NULL COMMENT 'Регион/район',
    `city`        varchar(255) NOT NULL COMMENT 'Город/н.п.',
    `street`      varchar(255) NOT NULL COMMENT 'Улица',
    `building`    varchar(255)          DEFAULT NULL COMMENT 'Дом',
    `deleted`     tinyint(1)   NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `index` (`index`),
    KEY `region` (`region`),
    KEY `city` (`city`),
    KEY `street` (`street`),
    KEY `building` (`building`),
    KEY `area` (`area`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dealers`
--

DROP TABLE IF EXISTS `dealers`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dealers`
(
    `id`          int          NOT NULL AUTO_INCREMENT,
    `name`        varchar(255) NOT NULL COMMENT 'Название дилера',
    `code`        varchar(4)   NOT NULL COMMENT 'Код дилера',
    `client_code` varchar(9)   NOT NULL COMMENT 'Код клиента',
    `group`       int          NOT NULL COMMENT 'Группа',
    `branch`      int          NOT NULL COMMENT 'Филиал',
    `type`        int                   DEFAULT NULL COMMENT 'Тип',
    `create_date` datetime     NOT NULL COMMENT 'Дата регистрации',
    `daddy`       int                   DEFAULT NULL COMMENT 'ID зарегистрировавшего/проверившего пользователя',
    `deleted`     tinyint(1)   NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `code` (`code`),
    UNIQUE KEY `client_code` (`client_code`),
    KEY `deleted` (`deleted`),
    KEY `daddy` (`daddy`),
    KEY `name` (`name`),
    KEY `branch` (`branch`),
    KEY `group` (`group`),
    KEY `type` (`type`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fraud_checks_steps`
--

DROP TABLE IF EXISTS `fraud_checks_steps`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fraud_checks_steps`
(
    `id`              int NOT NULL AUTO_INCREMENT,
    `entity_id`       int          DEFAULT NULL COMMENT 'ID заказа какой-то сущности',
    `entity_class`    varchar(255) DEFAULT NULL COMMENT 'Класс сущности заказа',
    `fraud_validator` varchar(255) DEFAULT NULL COMMENT 'Класс фрода, который реализует проверку',
    `step_info`       json         DEFAULT NULL COMMENT 'Дополнительная информация',
    `status`          tinyint      DEFAULT NULL COMMENT 'Статус проверки',
    `created_at`      datetime     DEFAULT NULL,
    `updated_at`      datetime     DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `managers`
--

DROP TABLE IF EXISTS `managers`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `managers`
(
    `id`          int          NOT NULL AUTO_INCREMENT,
    `create_date` datetime     NOT NULL COMMENT 'Дата регистрации',
    `update_date` datetime              DEFAULT NULL COMMENT 'Дата обновления',
    `user`        int                   DEFAULT NULL COMMENT 'Пользователь',
    `name`        varchar(128) NOT NULL COMMENT 'Имя',
    `surname`     varchar(128) NOT NULL COMMENT 'Фамилия',
    `patronymic`  varchar(128)          DEFAULT NULL COMMENT 'Отчество',
    `deleted`     tinyint(1)   NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `name` (`name`),
    KEY `surname` (`surname`),
    KEY `patronymic` (`patronymic`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migration`
--

DROP TABLE IF EXISTS `migration`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migration`
(
    `version`    varchar(180) NOT NULL,
    `apply_time` int DEFAULT NULL,
    PRIMARY KEY (`version`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `phones`
--

DROP TABLE IF EXISTS `phones`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `phones`
(
    `id`          int          NOT NULL AUTO_INCREMENT,
    `phone`       varchar(255) NOT NULL COMMENT 'Телефон',
    `create_date` datetime              DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата регистрации',
    `status`      int                   DEFAULT NULL COMMENT 'Статус',
    `deleted`     tinyint(1)   NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `phone` (`phone`),
    KEY `status` (`status`),
    KEY `deleted` (`deleted`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_order`
--

DROP TABLE IF EXISTS `product_order`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_order`
(
    `id`          int        NOT NULL AUTO_INCREMENT,
    `initiator`   int        NOT NULL COMMENT 'Заказчик',
    `store`       int        NOT NULL COMMENT 'Магазин',
    `status`      int        NOT NULL COMMENT 'Статус',
    `create_date` datetime   NOT NULL COMMENT 'Дата регистрации',
    `deleted`     tinyint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `initiator` (`initiator`),
    KEY `store` (`store`),
    KEY `status` (`status`),
    KEY `deleted` (`deleted`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products`
(
    `id`          int        NOT NULL AUTO_INCREMENT,
    `class_id`    int        NOT NULL COMMENT 'Класс продукта',
    `user`        int                 DEFAULT NULL COMMENT 'Пользователь',
    `create_date` datetime            DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата создания',
    `deleted`     tinyint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `class_id` (`class_id`),
    KEY `user` (`user`),
    KEY `deleted` (`deleted`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `products_classes`
--

DROP TABLE IF EXISTS `products_classes`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products_classes`
(
    `id`          int          NOT NULL AUTO_INCREMENT,
    `name`        varchar(255) NOT NULL COMMENT 'Название товара',
    `item_class`  varchar(255) NOT NULL COMMENT 'Класс товара',
    `create_date` datetime     NOT NULL COMMENT 'Дата регистрации',
    `deleted`     tinyint(1)   NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `deleted` (`deleted`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_branches`
--

DROP TABLE IF EXISTS `ref_branches`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ref_branches`
(
    `id`        int          NOT NULL AUTO_INCREMENT,
    `name`      varchar(255) NOT NULL,
    `color`     varchar(255)          DEFAULT NULL,
    `textcolor` varchar(255)          DEFAULT NULL,
    `deleted`   tinyint(1)   NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `deleted` (`deleted`),
    KEY `name` (`name`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_countries`
--

DROP TABLE IF EXISTS `ref_countries`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ref_countries`
(
    `id`          int          NOT NULL AUTO_INCREMENT,
    `name`        varchar(255) NOT NULL,
    `color`       varchar(255)          DEFAULT NULL,
    `textcolor`   varchar(255)          DEFAULT NULL,
    `deleted`     tinyint(1)   NOT NULL DEFAULT '0',
    `is_homeland` tinyint(1)            DEFAULT '0' COMMENT 'Это Россия?',
    PRIMARY KEY (`id`),
    KEY `deleted` (`deleted`),
    KEY `name` (`name`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_dealers_groups`
--

DROP TABLE IF EXISTS `ref_dealers_groups`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ref_dealers_groups`
(
    `id`        int          NOT NULL AUTO_INCREMENT,
    `name`      varchar(255) NOT NULL,
    `color`     varchar(255)          DEFAULT NULL,
    `textcolor` varchar(255)          DEFAULT NULL,
    `deleted`   tinyint(1)   NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `deleted` (`deleted`),
    KEY `name` (`name`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_dealers_types`
--

DROP TABLE IF EXISTS `ref_dealers_types`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ref_dealers_types`
(
    `id`        int          NOT NULL AUTO_INCREMENT,
    `name`      varchar(255) NOT NULL,
    `color`     varchar(255)          DEFAULT NULL,
    `textcolor` varchar(255)          DEFAULT NULL,
    `deleted`   tinyint(1)   NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `deleted` (`deleted`),
    KEY `name` (`name`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_regions`
--

DROP TABLE IF EXISTS `ref_regions`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ref_regions`
(
    `id`        int          NOT NULL AUTO_INCREMENT,
    `name`      varchar(255) NOT NULL,
    `color`     varchar(255)          DEFAULT NULL,
    `textcolor` varchar(255)          DEFAULT NULL,
    `deleted`   tinyint(1)   NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `deleted` (`deleted`),
    KEY `name` (`name`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_selling_channels`
--

DROP TABLE IF EXISTS `ref_selling_channels`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ref_selling_channels`
(
    `id`        int          NOT NULL AUTO_INCREMENT,
    `name`      varchar(255) NOT NULL,
    `color`     varchar(255)          DEFAULT NULL,
    `textcolor` varchar(255)          DEFAULT NULL,
    `deleted`   tinyint(1)   NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `deleted` (`deleted`),
    KEY `name` (`name`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_stores_types`
--

DROP TABLE IF EXISTS `ref_stores_types`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ref_stores_types`
(
    `id`        int          NOT NULL AUTO_INCREMENT,
    `name`      varchar(255) NOT NULL,
    `color`     varchar(255)          DEFAULT NULL,
    `textcolor` varchar(255)          DEFAULT NULL,
    `deleted`   tinyint(1)   NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `deleted` (`deleted`),
    KEY `name` (`name`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `relation_dealers_to_managers`
--

DROP TABLE IF EXISTS `relation_dealers_to_managers`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `relation_dealers_to_managers`
(
    `id`         int NOT NULL AUTO_INCREMENT,
    `dealer_id`  int NOT NULL,
    `manager_id` int NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `dealer_id_manager_id` (`dealer_id`, `manager_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `relation_dealers_to_sellers`
--

DROP TABLE IF EXISTS `relation_dealers_to_sellers`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `relation_dealers_to_sellers`
(
    `id`        int NOT NULL AUTO_INCREMENT,
    `dealer_id` int NOT NULL,
    `seller_id` int NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `dealer_id_seller_id` (`dealer_id`, `seller_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `relation_dealers_to_stores`
--

DROP TABLE IF EXISTS `relation_dealers_to_stores`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `relation_dealers_to_stores`
(
    `id`        int NOT NULL AUTO_INCREMENT,
    `dealer_id` int NOT NULL,
    `store_id`  int NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `dealer_id_store_id` (`dealer_id`, `store_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `relation_managers_to_stores`
--

DROP TABLE IF EXISTS `relation_managers_to_stores`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `relation_managers_to_stores`
(
    `id`         int NOT NULL AUTO_INCREMENT,
    `manager_id` int NOT NULL,
    `store_id`   int NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `manager_id_store_id` (`manager_id`, `store_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `relation_order_to_product`
--

DROP TABLE IF EXISTS `relation_order_to_product`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `relation_order_to_product`
(
    `id`         int NOT NULL AUTO_INCREMENT,
    `order_id`   int NOT NULL,
    `product_id` int NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `order_id_product_id` (`order_id`, `product_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `relation_stores_to_product`
--

DROP TABLE IF EXISTS `relation_stores_to_product`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `relation_stores_to_product`
(
    `id`         int NOT NULL AUTO_INCREMENT,
    `store_id`   int NOT NULL,
    `product_id` int NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `store_id_product_id` (`store_id`, `product_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `relation_stores_to_sellers`
--

DROP TABLE IF EXISTS `relation_stores_to_sellers`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `relation_stores_to_sellers`
(
    `id`        int NOT NULL AUTO_INCREMENT,
    `store_id`  int NOT NULL,
    `seller_id` int NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `store_id_seller_id` (`store_id`, `seller_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `relation_stores_to_users`
--

DROP TABLE IF EXISTS `relation_stores_to_users`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `relation_stores_to_users`
(
    `id`       int NOT NULL AUTO_INCREMENT,
    `store_id` int NOT NULL,
    `user_id`  int NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `store_id_user_id` (`store_id`, `user_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `relation_users_to_phones`
--

DROP TABLE IF EXISTS `relation_users_to_phones`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `relation_users_to_phones`
(
    `id`       int NOT NULL AUTO_INCREMENT,
    `user_id`  int NOT NULL,
    `phone_id` int NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_id_phone_id` (`user_id`, `phone_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rewards`
--

DROP TABLE IF EXISTS `rewards`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rewards`
(
    `id`           int      NOT NULL AUTO_INCREMENT,
    `user`         int      NOT NULL COMMENT 'Аккаунт',
    `operation`    int      NOT NULL COMMENT 'Операция',
    `reason`       int      NOT NULL COMMENT 'Причина начисления',
    `rule`         int          DEFAULT NULL COMMENT 'Правило расчёта',
    `quantity`     int          DEFAULT NULL COMMENT 'Расчётное вознаграждение',
    `waiting`      varchar(255) DEFAULT NULL COMMENT 'Ожидает события',
    `comment`      text COMMENT 'Произвольный комментарий',
    `product_id`   int          DEFAULT NULL,
    `product_type` int          DEFAULT NULL,
    `create_date`  datetime NOT NULL COMMENT 'Дата создания',
    `override`     int          DEFAULT NULL COMMENT 'Переопределено',
    `deleted`      tinyint(1)   DEFAULT '0' COMMENT 'Флаг удаления',
    PRIMARY KEY (`id`),
    UNIQUE KEY `override` (`override`),
    KEY `user` (`user`),
    KEY `operation` (`operation`),
    KEY `rule` (`rule`),
    KEY `deleted` (`deleted`),
    KEY `reason` (`reason`),
    KEY `waiting` (`waiting`),
    KEY `product_id_product_type` (`product_id`, `product_type`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales`
(
    `id`           int        NOT NULL AUTO_INCREMENT,
    `product_id`   int        NOT NULL COMMENT 'Товар',
    `product_type` int        NOT NULL,
    `seller`       int        NOT NULL COMMENT 'Продавец',
    `create_date`  datetime            DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата регистрации',
    `status`       int                 DEFAULT NULL COMMENT 'Статус',
    `deleted`      tinyint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `product_id_product_type` (`product_id`, `product_type`),
    KEY `seller` (`seller`),
    KEY `status` (`status`),
    KEY `deleted` (`deleted`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sellers`
--

DROP TABLE IF EXISTS `sellers`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sellers`
(
    `id`                       int        NOT NULL AUTO_INCREMENT,
    `user`                     int                 DEFAULT NULL COMMENT 'Пользователь',
    `name`                     varchar(255)        DEFAULT NULL,
    `surname`                  varchar(255)        DEFAULT NULL,
    `patronymic`               varchar(128)        DEFAULT NULL COMMENT 'Отчество',
    `gender`                   int                 DEFAULT NULL COMMENT 'Пол',
    `birthday`                 date                DEFAULT NULL,
    `create_date`              datetime   NOT NULL COMMENT 'Дата регистрации',
    `update_date`              datetime            DEFAULT NULL COMMENT 'Дата обновления',
    `citizen`                  int                 DEFAULT NULL COMMENT 'Гражданство',
    `passport_series`          varchar(64)         DEFAULT NULL,
    `passport_number`          varchar(64)         DEFAULT NULL,
    `passport_whom`            varchar(255)        DEFAULT NULL,
    `passport_when`            date                DEFAULT NULL,
    `reg_address`              varchar(255)        DEFAULT NULL,
    `entry_date`               date                DEFAULT NULL COMMENT 'Дата въезда в страну',
    `inn`                      varchar(12)         DEFAULT NULL COMMENT 'ИНН',
    `snils`                    varchar(14)         DEFAULT NULL COMMENT 'СНИЛС',
    `keyword`                  varchar(64)         DEFAULT NULL,
    `is_wireman_shpd`          tinyint(1) NOT NULL COMMENT 'Монтажник ШПД',
    `contract_signing_address` varchar(255)        DEFAULT NULL COMMENT 'Адрес подписания договора',
    `deleted`                  tinyint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `inn` (`inn`),
    UNIQUE KEY `snils` (`snils`),
    UNIQUE KEY `user` (`user`),
    KEY `name` (`name`),
    KEY `surname` (`surname`),
    KEY `patronymic` (`patronymic`),
    KEY `birthday` (`birthday`),
    KEY `gender` (`gender`),
    KEY `entry_date` (`entry_date`),
    KEY `keyword` (`keyword`),
    KEY `create_date` (`create_date`),
    KEY `update_date` (`update_date`),
    KEY `citizen` (`citizen`),
    KEY `reg_address` (`reg_address`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sellers_invite_links`
--

DROP TABLE IF EXISTS `sellers_invite_links`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sellers_invite_links`
(
    `id`           int          NOT NULL AUTO_INCREMENT,
    `store_id`     int          DEFAULT NULL,
    `phone_number` varchar(255) DEFAULT NULL,
    `email`        varchar(255) DEFAULT NULL,
    `token`        varchar(255) NOT NULL,
    `expired_at`   datetime     DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_store_phone_email` (`store_id`, `phone_number`, `email`),
    CONSTRAINT `fk_invite_link_store` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `simcard`
--

DROP TABLE IF EXISTS `simcard`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `simcard`
(
    `id`     int        NOT NULL AUTO_INCREMENT,
    `ICCID`  int        NOT NULL,
    `active` tinyint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `active` (`active`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stores`
--

DROP TABLE IF EXISTS `stores`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stores`
(
    `id`              int          NOT NULL AUTO_INCREMENT,
    `name`            varchar(255) NOT NULL COMMENT 'Название магазина',
    `type`            int          NOT NULL COMMENT 'Тип магазина',
    `region`          int          NOT NULL COMMENT 'Филиал',
    `branch`          int          NOT NULL COMMENT 'Филиал',
    `selling_channel` int          NOT NULL COMMENT 'Канал продаж',
    `create_date`     datetime     NOT NULL COMMENT 'Дата регистрации',
    `deleted`         tinyint(1)   NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `deleted` (`deleted`),
    KEY `selling_channel` (`selling_channel`),
    KEY `branch` (`branch`),
    KEY `region` (`region`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_exceptions`
--

DROP TABLE IF EXISTS `sys_exceptions`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sys_exceptions`
(
    `id`         int        NOT NULL AUTO_INCREMENT,
    `timestamp`  timestamp  NULL     DEFAULT CURRENT_TIMESTAMP,
    `user_id`    int                 DEFAULT NULL,
    `code`       int                 DEFAULT NULL,
    `statusCode` int                 DEFAULT NULL COMMENT 'HTTP status code',
    `file`       varchar(255)        DEFAULT NULL,
    `line`       int                 DEFAULT NULL,
    `message`    text,
    `trace`      text,
    `get`        text COMMENT 'GET',
    `post`       text COMMENT 'POST',
    `known`      tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Known error',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_file_storage`
--

DROP TABLE IF EXISTS `sys_file_storage`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sys_file_storage`
(
    `id`         int          NOT NULL AUTO_INCREMENT,
    `name`       varchar(255) NOT NULL,
    `path`       varchar(255) NOT NULL,
    `model_name` varchar(255)          DEFAULT NULL,
    `model_key`  int                   DEFAULT NULL,
    `at`         timestamp    NULL     DEFAULT CURRENT_TIMESTAMP,
    `daddy`      int                   DEFAULT NULL,
    `delegate`   varchar(255)          DEFAULT NULL,
    `deleted`    tinyint(1)   NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `path` (`path`),
    KEY `model_name_model_key` (`model_name`, `model_key`),
    KEY `daddy` (`daddy`),
    KEY `deleted` (`deleted`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_file_storage_tags`
--

DROP TABLE IF EXISTS `sys_file_storage_tags`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sys_file_storage_tags`
(
    `id`   int          NOT NULL AUTO_INCREMENT,
    `file` int          NOT NULL,
    `tag`  varchar(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `file_tag` (`file`, `tag`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_history`
--

DROP TABLE IF EXISTS `sys_history`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sys_history`
(
    `id`                   int       NOT NULL AUTO_INCREMENT,
    `at`                   timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `user`                 int            DEFAULT NULL,
    `model_class`          varchar(255)   DEFAULT NULL,
    `model_key`            int            DEFAULT NULL,
    `old_attributes`       blob COMMENT 'Old serialized attributes',
    `new_attributes`       blob COMMENT 'New serialized attributes',
    `relation_model`       varchar(255)   DEFAULT NULL,
    `scenario`             varchar(255)   DEFAULT NULL,
    `event`                varchar(255)   DEFAULT NULL,
    `operation_identifier` varchar(255)   DEFAULT NULL,
    `delegate`             int            DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `user` (`user`),
    KEY `model_class` (`model_class`),
    KEY `relation_model` (`relation_model`),
    KEY `model_key` (`model_key`),
    KEY `event` (`event`),
    KEY `operation_identifier` (`operation_identifier`),
    KEY `model_class_model_key` (`model_class`, `model_key`),
    KEY `delegate` (`delegate`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_history_tags`
--

DROP TABLE IF EXISTS `sys_history_tags`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sys_history_tags`
(
    `id`      int          NOT NULL AUTO_INCREMENT,
    `history` int          NOT NULL,
    `tag`     varchar(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `history_tag` (`history`, `tag`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_import`
--

DROP TABLE IF EXISTS `sys_import`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sys_import`
(
    `id`        int          NOT NULL AUTO_INCREMENT,
    `model`     varchar(255) NOT NULL,
    `domain`    int          NOT NULL,
    `data`      blob,
    `processed` tinyint(1) DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `processed` (`processed`),
    KEY `domain` (`domain`),
    KEY `model` (`model`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_notifications`
--

DROP TABLE IF EXISTS `sys_notifications`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sys_notifications`
(
    `id`        int NOT NULL AUTO_INCREMENT,
    `type`      int NOT NULL DEFAULT '0' COMMENT 'Тип уведомления',
    `initiator` int          DEFAULT NULL COMMENT 'автор уведомления, null - система',
    `receiver`  int          DEFAULT NULL COMMENT 'получатель уведомления, null - определяется типом',
    `object_id` int          DEFAULT NULL COMMENT 'идентификатор объекта уведомления, null - определяется типом',
    `comment`   text,
    `timestamp` datetime     DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `type_receiver_object_id` (`type`, `receiver`, `object_id`),
    KEY `type` (`type`),
    KEY `initiator` (`initiator`),
    KEY `receiver` (`receiver`),
    KEY `object_id` (`object_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_options`
--

DROP TABLE IF EXISTS `sys_options`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sys_options`
(
    `id`     int          NOT NULL AUTO_INCREMENT,
    `option` varchar(256) NOT NULL COMMENT 'Option name',
    `value`  blob COMMENT 'Serialized option value',
    PRIMARY KEY (`id`),
    UNIQUE KEY `option` (`option`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_permissions`
--

DROP TABLE IF EXISTS `sys_permissions`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sys_permissions`
(
    `id`         int          NOT NULL AUTO_INCREMENT,
    `name`       varchar(128) NOT NULL COMMENT 'Название доступа',
    `controller` varchar(255)          DEFAULT NULL COMMENT 'Контроллер, к которому устанавливается доступ, null для внутреннего доступа',
    `action`     varchar(255)          DEFAULT NULL COMMENT 'Действие, для которого устанавливается доступ, null для всех действий контроллера',
    `verb`       varchar(255)          DEFAULT NULL COMMENT 'REST-метод, для которого устанавливается доступ',
    `comment`    text COMMENT 'Описание доступа',
    `priority`   int          NOT NULL DEFAULT '0' COMMENT 'Приоритет использования (больше - выше)',
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`),
    KEY `controller_action_verb` (`controller`, `action`, `verb`),
    KEY `priority` (`priority`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_permissions_collections`
--

DROP TABLE IF EXISTS `sys_permissions_collections`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sys_permissions_collections`
(
    `id`      int          NOT NULL AUTO_INCREMENT,
    `name`    varchar(128) NOT NULL COMMENT 'Название группы доступа',
    `comment` text COMMENT 'Описание группы доступа',
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_relation_permissions_collections_to_permissions`
--

DROP TABLE IF EXISTS `sys_relation_permissions_collections_to_permissions`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sys_relation_permissions_collections_to_permissions`
(
    `id`            int NOT NULL AUTO_INCREMENT,
    `collection_id` int NOT NULL COMMENT 'Ключ группы доступа',
    `permission_id` int NOT NULL COMMENT 'Ключ правила доступа',
    PRIMARY KEY (`id`),
    UNIQUE KEY `collection_id_permission_id` (`collection_id`, `permission_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_relation_permissions_collections_to_permissions_collections`
--

DROP TABLE IF EXISTS `sys_relation_permissions_collections_to_permissions_collections`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sys_relation_permissions_collections_to_permissions_collections`
(
    `id`        int NOT NULL AUTO_INCREMENT,
    `master_id` int NOT NULL,
    `slave_id`  int NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `master_id_slave_id` (`master_id`, `slave_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_relation_users_to_permissions`
--

DROP TABLE IF EXISTS `sys_relation_users_to_permissions`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sys_relation_users_to_permissions`
(
    `id`            int NOT NULL AUTO_INCREMENT,
    `user_id`       int NOT NULL COMMENT 'Ключ объекта доступа',
    `permission_id` int NOT NULL COMMENT 'Ключ правила доступа',
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_id_permission_id` (`user_id`, `permission_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_relation_users_to_permissions_collections`
--

DROP TABLE IF EXISTS `sys_relation_users_to_permissions_collections`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sys_relation_users_to_permissions_collections`
(
    `id`            int NOT NULL AUTO_INCREMENT,
    `user_id`       int NOT NULL COMMENT 'Ключ объекта доступа',
    `collection_id` int NOT NULL COMMENT 'Ключ группы доступа',
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_id_collection_id` (`user_id`, `collection_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_status`
--

DROP TABLE IF EXISTS `sys_status`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sys_status`
(
    `id`         int       NOT NULL AUTO_INCREMENT,
    `model_name` varchar(255)   DEFAULT NULL,
    `model_key`  int            DEFAULT NULL,
    `status`     int       NOT NULL,
    `at`         timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `daddy`      int            DEFAULT NULL,
    `delegate`   varchar(255)   DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `model_name_model_key` (`model_name`, `model_key`),
    KEY `daddy` (`daddy`),
    KEY `status` (`status`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_users`
--

DROP TABLE IF EXISTS `sys_users`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sys_users`
(
    `id`              int          NOT NULL AUTO_INCREMENT,
    `username`        varchar(255) NOT NULL COMMENT 'Отображаемое имя пользователя',
    `login`           varchar(64)  NOT NULL COMMENT 'Логин',
    `password`        varchar(255) NOT NULL COMMENT 'Хеш пароля',
    `salt`            varchar(255)          DEFAULT NULL COMMENT 'Unique random salt hash',
    `restore_code`    varchar(40)           DEFAULT NULL COMMENT 'Код восстановления',
    `is_pwd_outdated` tinyint(1)   NOT NULL DEFAULT '0' COMMENT 'Ожидается смена пароля',
    `email`           varchar(255)          DEFAULT NULL,
    `comment`         text COMMENT 'Служебный комментарий пользователя',
    `create_date`     datetime     NOT NULL COMMENT 'Дата регистрации',
    `daddy`           int                   DEFAULT NULL COMMENT 'ID зарегистрировавшего/проверившего пользователя',
    `deleted`         tinyint(1)            DEFAULT '0' COMMENT 'Флаг удаления',
    PRIMARY KEY (`id`),
    UNIQUE KEY `login` (`login`),
    UNIQUE KEY `email` (`email`),
    KEY `username` (`username`),
    KEY `daddy` (`daddy`),
    KEY `deleted` (`deleted`),
    KEY `is_pwd_outdated` (`is_pwd_outdated`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sys_users_tokens`
--

DROP TABLE IF EXISTS `sys_users_tokens`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sys_users_tokens`
(
    `id`         int         NOT NULL AUTO_INCREMENT,
    `user_id`    int         NOT NULL COMMENT 'user id foreign key',
    `auth_token` varchar(40) NOT NULL COMMENT 'Bearer auth token',
    `type_id`    tinyint     NOT NULL COMMENT 'Тип токена',
    `created`    timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Таймстамп создания',
    `valid`      timestamp   NULL     DEFAULT NULL COMMENT 'Действует до',
    `ip`         varchar(255)         DEFAULT NULL COMMENT 'Адрес авторизации',
    `user_agent` varchar(255)         DEFAULT NULL COMMENT 'User-Agent',
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_id_auth_token` (`user_id`, `auth_token`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_options`
--

DROP TABLE IF EXISTS `users_options`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_options`
(
    `id`      int          NOT NULL AUTO_INCREMENT,
    `user_id` int DEFAULT NULL COMMENT 'System user id',
    `option`  varchar(256) NOT NULL COMMENT 'Option name',
    `value`   blob COMMENT 'Serialized option value',
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_id_option` (`user_id`, `option`),
    KEY `user_id` (`user_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE = @OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE = @OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS = @OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES = @OLD_SQL_NOTES */;

-- Dump completed on 2021-07-13 14:13:15

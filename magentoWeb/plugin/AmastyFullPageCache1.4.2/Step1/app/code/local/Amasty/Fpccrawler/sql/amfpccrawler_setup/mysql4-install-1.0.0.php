<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpccrawler
 */
$this->startSetup();

$this->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('amfpccrawler/queue')}` (
    `queue_id`     int(10) unsigned NOT NULL auto_increment,
    `url`          varchar(255) NOT NULL,
    `rate`         int(11) unsigned NOT NULL,

    PRIMARY KEY (`queue_id`),
    UNIQUE KEY (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$this->getTable('amfpccrawler/log')}` (
    `log_id`   int(10) unsigned NOT NULL auto_increment,
    `url`      varchar(255) NOT NULL,
    `store`    varchar(255) NOT NULL,
    `currency` varchar(3)   NOT NULL,
    `customer_group`    smallint(4) NOT NULL,
    `mobile`   varchar(255) NOT NULL,
    `rate`     int(11) unsigned NOT NULL,
    `status`   int(11) unsigned NOT NULL,
    `page_load`     int(11) unsigned NOT NULL,
    `date`     int(10) NOT NULL,
    `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    KEY `date` (`date`),
    KEY `status` (`status`),
    KEY `rate` (`rate`),
    KEY `url` (`url`),

    PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$this->endSetup();

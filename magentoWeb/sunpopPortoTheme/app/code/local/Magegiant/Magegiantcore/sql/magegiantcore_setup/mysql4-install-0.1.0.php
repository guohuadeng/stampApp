<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('magegiantcore')};

CREATE TABLE {$this->getTable('magegiantcore')} (
  `magegiantcore_id` int(11) unsigned NOT NULL auto_increment,
  `notification_id` int(10) unsigned NOT NULL,
  `url` varchar(255) NOT NULL default '',
  `added_date` datetime NOT NULL,
  UNIQUE (`notification_id`, `url`),
  PRIMARY KEY (`magegiantcore_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 
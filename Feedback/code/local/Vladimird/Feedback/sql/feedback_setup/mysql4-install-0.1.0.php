<?php
//die ('setup database');
$installer = $this;
$installer->startSetup();
$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable(feedback)};
CREATE TABLE {$this->getTable(feedback)} (
  `feedback_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reason` varchar(150) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `comment` varchar(150) NOT NULL,
  `telephone` varchar(14) NOT NULL,
  PRIMARY KEY (`feedback_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->endSetup();

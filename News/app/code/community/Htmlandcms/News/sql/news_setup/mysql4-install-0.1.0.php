<?php
$installer = $this;
$installer->startSetup();
$installer->run(
    "
-- DROP TABLE IF EXISTS {$this->getTable(news)};
CREATE TABLE {$this->getTable(news)} (
  `news_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` int(10) unsigned NOT NULL,
  `rewrite_id` int(15) unsigned NOT NULL,
  `is_active` BOOLEAN NOT NULL,
  `date_from` TIMESTAMP NOT NULL,
  `date_until` TIMESTAMP NOT NULL,
  `title` varchar(75) NOT NULL,
  `image` varchar(150) NOT NULL,
  `short` varchar(150) NOT NULL,
  `body` text NOT NULL,
  `meta_title` varchar(69) NOT NULL,
  `description` varchar(156) NOT NULL,
  `keywords` varchar(200) NOT NULL,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`news_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
    "
);
$installer->endSetup();

$installer = $this;
$installer->startSetup();
$installer->run(
    "
-- DROP TABLE IF EXISTS {$this->getTable(news_category)};

CREATE TABLE IF NOT EXISTS {$this->getTable(news_category)} (
  `cat_id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(5) unsigned NULL DEFAULT NULL,
  `is_active` BOOLEAN NOT NULL,
  `rewrite_id` int(15) unsigned NOT NULL,
  `image` varchar(150) NOT NULL,
  `name` varchar(75) NOT NULL,
  `store_id` varchar(75) NOT NULL,
  `body` text NOT NULL,
  `meta_title` varchar(69) NOT NULL,
  `description` varchar(156) NOT NULL,
  `keywords` varchar(200) NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    "
);
$installer->endSetup();
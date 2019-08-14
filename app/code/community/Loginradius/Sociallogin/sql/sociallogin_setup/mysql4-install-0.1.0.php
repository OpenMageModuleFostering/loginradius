<?php

$installer = $this;
 
$installer->startSetup();
 
$installer->run("
 
CREATE TABLE IF NOT EXISTS {$this->getTable('sociallogin')} (
   `sociallogin_id` varchar(50) NULL ,
  `entity_id` int(11) NULL 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 
    ");
 
$installer->endSetup();
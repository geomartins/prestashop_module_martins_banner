<?php
$sqls  = array();
$sqls[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'martinsnewsletter` (
    `id` INT(10) AUTO_INCREMENT,
    `email` TEXT,
    `telephone` TEXT,
    PRIMARY KEY(`id`)
) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8';


foreach($sqls as $sql)
    if(!Db::getInstance()->execute($sql))
        return false;
    



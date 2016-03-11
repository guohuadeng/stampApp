<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */
$this->startSetup();

$connection = $this->getConnection();

$tableName = $this->getTable('core/cache_tag');
$keys = $connection->getForeignKeys($tableName);
if ($keys) {
    foreach ($keys as $keyName => $keyInfo) {
        $connection->dropForeignKey($tableName, $keyName);
    }
}

$this->endSetup();

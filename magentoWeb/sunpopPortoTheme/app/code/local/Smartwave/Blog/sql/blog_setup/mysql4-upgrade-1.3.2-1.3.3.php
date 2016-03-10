<?php

$installer = $this;

$installer->startSetup();

try {
    $installer->run("
        ALTER TABLE {$this->getTable('blog/blog')} ADD `meta_title` TEXT NOT NULL AFTER `update_user`;
    ");
    $installer->run("
        ALTER TABLE {$this->getTable('blog/cat')} ADD `meta_title` TEXT NOT NULL AFTER `sort_order`;
    ");
} catch (Exception $e) {
    
}

$installer->endSetup();


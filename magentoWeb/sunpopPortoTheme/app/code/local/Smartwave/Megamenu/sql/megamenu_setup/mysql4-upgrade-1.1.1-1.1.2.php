<?php
/**
 * @package		Eternal_Megamenu
 * @author		Eternal Friend
 * @copyright	Copyright 2014
 */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_category', 'sw_cat_hide_menu_item', array(
    'group'             => 'Menu',
    'label'             => 'Hide This Menu Item',
    'type'              => 'int',
    'input'             => 'select',
    'source'            => 'megamenu/category_attribute_source_block_yesno',
    'visible'           => true,
    'required'          => false,
    'sort_order'        => 0,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => false,
    'is_html_allowed_on_front'    => false,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
));

$installer->endSetup();
<?php
/**
 * @package		Eternal_Megamenu
 * @author		Eternal Friend
 * @copyright	Copyright 2014
 */
$installer = $this;
$installer->startSetup();

$installer->updateAttribute('catalog_category', 'sw_cat_block_type', 'is_required', 0, '10');
$installer->updateAttribute('catalog_category', 'sw_cat_static_width', 'note', 'The width of the static width megamenu popup. eg: 600px', '11');
$installer->updateAttribute('catalog_category', 'sw_cat_block_columns', 'is_required', 0, '12');
$installer->updateAttribute('catalog_category', 'sw_cat_block_top', 'is_required', 0, '31');
$installer->updateAttribute('catalog_category', 'sw_cat_left_block_width', 'is_required', 0, '40');
$installer->updateAttribute('catalog_category', 'sw_cat_block_left', 'is_required', 0, '41');
$installer->updateAttribute('catalog_category', 'sw_cat_right_block_width', 'is_required', 0, '50');
$installer->updateAttribute('catalog_category', 'sw_cat_block_right', 'is_required', 0, '51');
$installer->updateAttribute('catalog_category', 'sw_cat_block_bottom', 'is_required', 0, '60');
$installer->updateAttribute('catalog_category', 'sw_cat_label', 'is_required', 0, '14');
$installer->updateAttribute('catalog_category', 'sw_cat_float_type', 'is_required', 0, '13');

$installer->addAttribute('catalog_category', 'sw_icon_image', array(
    'type'          => 'varchar',
    'label'         => 'Icon Image',
    'input'         => 'image',
    'backend'       => 'catalog/category_attribute_backend_image',
    'required'      => false,
    'sort_order'    => 15,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'group'             => 'Menu'
));
$installer->addAttribute('catalog_category', 'sw_font_icon', array(
    'group'             => 'Menu',
    'label'             => 'Font Icon Class',
    'note'              => 'If this category has no "Icon Image", font icon will be shown. example to input: icon-dollar',
    'type'              => 'text',
    'input'             => 'text',
    'visible'           => true,
    'required'          => false,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => true,
    'is_html_allowed_on_front'    => true,
    'sort_order'    => 16,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
));

$installer->endSetup();
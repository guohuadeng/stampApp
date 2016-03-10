<?php
/**
 * @package		Eternal_Megamenu
 * @author		Eternal Friend
 * @copyright	Copyright 2014
 */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_category', 'sw_cat_block_type', array(
    'group'             => 'Menu',
    'label'             => 'Menu Type',
    'note'              => "This field is applicable only for top-level categories.",
    'type'              => 'varchar',
    'input'             => 'select',
    'source'            => 'megamenu/category_attribute_source_type_style',
    'visible'           => true,
    'required'          => false,
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
$installer->addAttribute('catalog_category', 'sw_cat_static_width', array(
    'group'             => 'Menu',
    'label'             => 'Static Width',
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
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
));
$installer->addAttribute('catalog_category', 'sw_cat_block_columns', array(
    'group'             => 'Menu',
    'label'             => 'Sub Category Menu Columns',
    'note'              => "The number of displayed subcategories' column. This field is applicable only for top-level categories.",
    'type'              => 'varchar',
    'input'             => 'select',
    'source'            => 'megamenu/category_attribute_source_block_subcolumns',
    'visible'           => true,
    'required'          => false,
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

$installer->addAttribute('catalog_category', 'sw_cat_block_top', array(
    'group'             => 'Menu',
    'label'             => 'Block Top',
    'type'              => 'text',
    'input'             => 'textarea',
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
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
));

$installer->addAttribute('catalog_category', 'sw_cat_left_block_width', array(
    'group'             => 'Menu',
    'label'             => 'Block Left Width (%)',
    'note'              => "Proportions of Block Left. This field is applicable only for top-level categories.",
    'type'              => 'text',
    'input'             => 'select',
    'source'            => 'megamenu/category_attribute_source_block_columns',
    'visible'           => true,
    'required'          => false,
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

$installer->addAttribute('catalog_category', 'sw_cat_block_left', array(
	'group'				=> 'Menu',
	'label'				=> 'Block Left',
	'note'				=> "This field is applicable only for top-level categories.",
	'type'				=> 'text',
	'input'				=> 'textarea',
	'visible'			=> true,
	'required'			=> false,
	'backend'			=> '',
	'frontend'			=> '',
	'searchable'		=> false,
	'filterable'		=> false,
	'comparable'		=> false,
	'user_defined'		=> true,
	'visible_on_front'	=> true,
	'wysiwyg_enabled'	=> true,
	'is_html_allowed_on_front'	=> true,
	'global'			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
));

$installer->addAttribute('catalog_category', 'sw_cat_right_block_width', array(
    'group'             => 'Menu',
    'label'             => 'Block Right Width (%)',
    'note'              => "Proportions Block Right. This field is applicable only for top-level categories.",
    'type'              => 'text',
    'input'             => 'select',
    'source'            => 'megamenu/category_attribute_source_block_columns',    
    'visible'           => true,
    'required'          => false,
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

$installer->addAttribute('catalog_category', 'sw_cat_block_right', array(
    'group'             => 'Menu',
    'label'             => 'Block Right',
    'note'              => "This field is applicable only for top-level categories.",
    'type'              => 'text',
    'input'             => 'textarea',
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
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
));

$installer->addAttribute('catalog_category', 'sw_cat_block_bottom', array(
    'group'             => 'Menu',
    'label'             => 'Block Bottom',
    'type'              => 'text',
    'input'             => 'textarea',
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
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
));

$installer->addAttribute('catalog_category', 'sw_cat_label', array(
    'group'             => 'Menu',
    'label'             => 'Category Label',
    'note'              => "Labels have to be defined in menu settings",
    'type'              => 'varchar',
    'input'             => 'select',
    'source'            => 'megamenu/category_attribute_source_label_categorylabel',                            
    'visible'           => true,
    'required'          => false,
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
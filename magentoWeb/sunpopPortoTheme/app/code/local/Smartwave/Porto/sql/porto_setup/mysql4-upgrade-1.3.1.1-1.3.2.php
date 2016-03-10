<?php

$installer = $this;

$installer->startSetup();

if(!$installer->getAttribute('catalog_category', 'sw_product_staticblock_tab_1', 'attribute_id')){
	$installer->addAttribute('catalog_category', 'sw_product_staticblock_tab_1', array(
		'group'             => 'Product Custom Tabs',
		'label'             => 'CMS Block Tab #1',
		'note'              => "",
		'type'              => 'varchar',
		'input'             => 'select',
		'source'            => 'porto/category_attribute_source_tab_mode',
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
		'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	));
}
if(!$installer->getAttribute('catalog_category', 'sw_product_staticblock_tab_2', 'attribute_id')){
	$installer->addAttribute('catalog_category', 'sw_product_staticblock_tab_2', array(
		'group'             => 'Product Custom Tabs',
		'label'             => 'CMS Block Tab #2',
		'note'              => "",
		'type'              => 'varchar',
		'input'             => 'select',
		'source'            => 'porto/category_attribute_source_tab_mode',
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
		'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	));
}
if(!$installer->getAttribute('catalog_category', 'sw_product_staticblock_tab_3', 'attribute_id')){
	$installer->addAttribute('catalog_category', 'sw_product_staticblock_tab_3', array(
		'group'             => 'Product Custom Tabs',
		'label'             => 'CMS Block Tab #3',
		'note'              => "",
		'type'              => 'varchar',
		'input'             => 'select',
		'source'            => 'porto/category_attribute_source_tab_mode',
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
		'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	));
}
if(!$installer->getAttribute('catalog_category', 'sw_product_staticblock_tab_4', 'attribute_id')){
	$installer->addAttribute('catalog_category', 'sw_product_staticblock_tab_4', array(
		'group'             => 'Product Custom Tabs',
		'label'             => 'CMS Block Tab #4',
		'note'              => "",
		'type'              => 'varchar',
		'input'             => 'select',
		'source'            => 'porto/category_attribute_source_tab_mode',
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
		'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	));
}
if(!$installer->getAttribute('catalog_category', 'sw_product_staticblock_tab_5', 'attribute_id')){
	$installer->addAttribute('catalog_category', 'sw_product_staticblock_tab_5', array(
		'group'             => 'Product Custom Tabs',
		'label'             => 'CMS Block Tab #5',
		'note'              => "",
		'type'              => 'varchar',
		'input'             => 'select',
		'source'            => 'porto/category_attribute_source_tab_mode',
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
		'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	));
}
if(!$installer->getAttribute('catalog_category', 'sw_product_attribute_tab_1', 'attribute_id')){
	$installer->addAttribute('catalog_category', 'sw_product_attribute_tab_1', array(
		'group'             => 'Product Custom Tabs',
		'label'             => 'Attribute Tab #1',
		'note'              => "",
		'type'              => 'varchar',
		'input'             => 'select',
		'source'            => 'porto/category_attribute_source_tab_mode',
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
		'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	));
}
if(!$installer->getAttribute('catalog_category', 'sw_product_attribute_tab_2', 'attribute_id')){
	$installer->addAttribute('catalog_category', 'sw_product_attribute_tab_2', array(
		'group'             => 'Product Custom Tabs',
		'label'             => 'Attribute Tab #2',
		'note'              => "",
		'type'              => 'varchar',
		'input'             => 'select',
		'source'            => 'porto/category_attribute_source_tab_mode',
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
		'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	));
}
if(!$installer->getAttribute('catalog_category', 'sw_product_attribute_tab_3', 'attribute_id')){
	$installer->addAttribute('catalog_category', 'sw_product_attribute_tab_3', array(
		'group'             => 'Product Custom Tabs',
		'label'             => 'Attribute Tab #3',
		'note'              => "",
		'type'              => 'varchar',
		'input'             => 'select',
		'source'            => 'porto/category_attribute_source_tab_mode',
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
		'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	));
}
if(!$installer->getAttribute('catalog_category', 'sw_product_attribute_tab_4', 'attribute_id')){
	$installer->addAttribute('catalog_category', 'sw_product_attribute_tab_4', array(
		'group'             => 'Product Custom Tabs',
		'label'             => 'Attribute Tab #4',
		'note'              => "",
		'type'              => 'varchar',
		'input'             => 'select',
		'source'            => 'porto/category_attribute_source_tab_mode',
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
		'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	));
}
if(!$installer->getAttribute('catalog_category', 'sw_product_attribute_tab_5', 'attribute_id')){
	$installer->addAttribute('catalog_category', 'sw_product_attribute_tab_5', array(
		'group'             => 'Product Custom Tabs',
		'label'             => 'Attribute Tab #5',
		'note'              => "",
		'type'              => 'varchar',
		'input'             => 'select',
		'source'            => 'porto/category_attribute_source_tab_mode',
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
		'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	));
}
$installer->endSetup();


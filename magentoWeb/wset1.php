<?php
//安装2，生成属性
require_once('app/Mage.php');
Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
$installer = new Mage_Sales_Model_Mysql4_Setup; //Mage_Eav_Model_Entity_Setup,Mage_Catalog_Model_Resource_Setup,

//产品的显示图标 iconfont
$a_iconfont  = array(
        'type'              => 'varchar',//varchar,int,decimal,text,datetime
        'backend'           => '',
        'frontend'          => '',
        'label'             => '产品图标',
        'input'             => 'text', //text,textarea,date,boolean,multiselect,select,price,media_image,weee
        'class'             => '',
    	'source'            => '',
        'default'           => '',
	    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	    'visible'           => 1,
	    'required'          => 0,
	    'user_defined'      => 1,
	    'searchable'        => 0,
	    'visible_in_advanced_search' => 0,
	    'filterable'        => 0,
	    'comparable'        => 0,
	    'visible_on_front'  => 0,
      'used_in_product_listing' => 1,
	    'unique'            => 0,
        'apply_to'          => '',
        'is_configurable'   => 0
);
//先清理可能存在的属性
//$installer->removeAttribute('catalog_product', 'a_iconfont');

/*  生成属性并加入default组
$installer->addAttribute('catalog_product', 'a_iconfont',$a_iconfont);
$attributeId = $installer->getAttributeId($entity, $attributeCode);

foreach ($installer->getAllAttributeSetIds($entity) as $setId) {
    $installer->addAttributeToSet(
        $entity,
        $setId,
        $installer->getDefaultAttributeGroupId($entity, $setId),
        $attributeId
    );
}
*/
$installer->endSetup();
echo __file__;
echo '增加产品属性成功';
?>

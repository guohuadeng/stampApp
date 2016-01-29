<?php
/**
 * Productattachments extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    Productattachments
 * @author     Kamran Rafiq Malik <kamran.malik@unitedsol.net>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */

class FME_Productattachments_Block_Adminhtml_Productattachments_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('productattachments_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('productattachments')->__('File Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('productattachments')->__('File Information'),
          'title'     => Mage::helper('productattachments')->__('File Information'),
          'content'   => $this->getLayout()->createBlock('productattachments/adminhtml_productattachments_edit_tab_form')->toHtml(),
      ));
	  
	  $this->addTab('cms_section', array(
          'label'     => Mage::helper('productattachments')->__('Attach With CMS Pages'),
          'title'     => Mage::helper('productattachments')->__('Attach With CMS Pages'),
          'content'   => $this->getLayout()->createBlock('productattachments/adminhtml_productattachments_edit_tab_cmspages')->toHtml(),
      ));
	  
	  $this->addTab('products_section', array(
			'label'     => Mage::helper('productattachments')->__('Attach With Products'),
			'url'       => $this->getUrl('*/*/products', array('_current' => true)),
			'class'     => 'ajax',
	  ));
     
      return parent::_beforeToHtml();
  }
}
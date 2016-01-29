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

class FME_Productattachments_Block_Adminhtml_Producttab extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface {
	public function __construct()
	{
		parent::__construct();
		$this->setId('attachmentsGrid');
		$this->setDefaultSort('productattachments_id');
		$this->setDefaultDir('ASC');
		$this->setUseAjax(true);
		$this->setSaveParametersInSession(false);	
	}
	public function getTabLabel(){
		return Mage::helper('core')->__('Attachments');
	}
	public function getTabTitle(){
		return Mage::helper('core')->__('Attachments');
	}
	public function canShowTab(){
		return true;
	}
	public function isHidden(){
		return false;
	}
	
	
	 protected function _prepareCollection()
  {
      $collection = Mage::getModel('productattachments/productattachments')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
	  
	  $this->addColumn('in_attachments', array(
			'header_css_class'  => 'a-center',
			'type'              => 'checkbox',
			'name'              => 'in_attachments',
			'align'             => 'center',
			'index'             => 'productattachments_id'
		));
	  
      $this->addColumn('productattachments_id', array(
          'header'    => Mage::helper('productattachments')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'productattachments_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('productattachments')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

      $this->addColumn('status', array(
          'header'    => Mage::helper('productattachments')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
      return parent::_prepareColumns();
  }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }
	 
}

?>

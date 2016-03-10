<?php

class Magegiant_Magegiantcore_Block_Adminhtml_Feedback_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('feedbackGrid');
      $this->setDefaultSort('feedback_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('magegiantcore/feedback')->getCollection();
	  $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('feedback_id', array(
          'header'    => Mage::helper('magegiantcore')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'feedback_id',
      ));
	  
      $this->addColumn('code', array(
          'header'    => Mage::helper('magegiantcore')->__('Feedback Code'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'code',
      ));	  

      $this->addColumn('extension', array(
          'header'    => Mage::helper('magegiantcore')->__('Extension'),
          'align'     =>'left',
          'index'     => 'extension',
		  'renderer'  => 'magegiantcore/adminhtml_feedback_renderer_product',
      ));		  
	  
      $this->addColumn('coupon_code', array(
          'header'    => Mage::helper('magegiantcore')->__('Coupon'),
          'align'     =>'left',
          'index'     => 'coupon_code',
      ));			  
	  
      $this->addColumn('created', array(
          'header'    => Mage::helper('magegiantcore')->__('Posted'),
          'align'     =>'left',
		  'width'     => '80px',
		  'type'      => 'date',
          'index'     => 'created',
      )); 	    
	  
      $this->addColumn('status', array(
          'header'    => Mage::helper('magegiantcore')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => Mage::helper('magegiantcore')->getFeedbackStatusList(),
		  'renderer'  => 'magegiantcore/adminhtml_feedback_renderer_status',
      ));
	  
	  
      $this->addColumn('is_sent', array(
          'header'    => Mage::helper('magegiantcore')->__('Is Sent'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'is_sent',
          'type'      => 'options',
          'options'   => Mage::helper('magegiantcore/feedback')->getSentStatusList(),
		  'renderer'  => 'magegiantcore/adminhtml_feedback_renderer_sentstatus',
      ));	  
	  	  
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('magegiantcore')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('magegiantcore')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('magegiantcore')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('magegiantcore')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('feedback_id');
        $this->getMassactionBlock()->setFormFieldName('feedback');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('magegiantcore')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('magegiantcore')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('magegiantcore/status')->getOptionArray();

        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}
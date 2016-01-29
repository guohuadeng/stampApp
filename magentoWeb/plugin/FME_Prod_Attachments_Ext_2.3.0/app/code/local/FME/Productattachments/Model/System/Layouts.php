<?php

class FME_Productattachments_Model_System_Layouts {
	
	public function toOptionArray() {
		return array(
            array(
                'label' => Mage::helper('productattachments')->__('Empty'),
                'value' => 'page/empty'
            ),
            array(
                'label' => Mage::helper('productattachments')->__('1 column'),
                'value' => 'page/1column'
            ),
            array(
                'label' => Mage::helper('productattachments')->__('2 columns with left bar'),
                'value' => '2columns-left'
            ),
            array(
                'label' => Mage::helper('productattachments')->__('2 column with right bar'),
                'value' => 'page/2columns-right'
            ),
            array(
                'label' => Mage::helper('productattachments')->__('3 columns'),
                'value' => 'page/3columns'
            )
        );
	}
}

<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpccrawler
 */


class Amasty_Fpccrawler_Block_Adminhtml_AjaxGenerate extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /*
     *  Set template
     */
    /**
     * Return ajax url for button
     *
     * @return string
     */
    public function getAjaxCheckUrl()
    {
        return Mage::helper('adminhtml')->getUrl('amasty/amfpccrawler/adminhtml_ajax/generate');
    }

    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
                       ->setData(array(
                           'id'      => 'amfpccrawler_button_generate',
                           'label'   => $this->helper('adminhtml')->__('Generate'),
                           'onclick' => 'queueGenerate();'
                       )
                       );

        return $button->toHtml();
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('amasty/amfpccrawler/system/config/buttonGenerate.phtml');
    }

    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

}
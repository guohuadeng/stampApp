<?php
/**
 * MageGiant
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magegiant.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magegiant.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @copyright   Copyright (c) 2014 Magegiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement.html
 */

class Magegiant_Magegiantcore_Adminhtml_Magegiantcore_MagegiantController extends Mage_Adminhtml_Controller_Action{

    public function indexAction()
    {
        $this->_title($this->__('System'))
            ->_title($this->__('Magegiant.com'));
        $this->loadLayout();
        $this->_setActiveMenu('mgtcommerce');
        $this->_addContent($this->getLayout()->createBlock('magegiantcore/adminhtml_shop', 'magegiant.com'));

        $this->renderLayout();
    }

    protected function _addContent(Mage_Core_Block_Abstract $block)
    {
        $this->getLayout()->getBlock('content')->append($block);
    }
}
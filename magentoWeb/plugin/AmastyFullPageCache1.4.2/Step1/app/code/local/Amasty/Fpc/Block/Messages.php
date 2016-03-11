<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */

class Amasty_Fpc_Block_Messages extends Mage_Core_Block_Messages
{
    public function getGroupedHtml()
    {
        $html = parent::getGroupedHtml();

        $_transportObject = new Varien_Object;
        $_transportObject->setHtml($html);
        Mage::dispatchEvent('core_block_abstract_to_html_after',
            array('block' => $this, 'transport' => $_transportObject));
        $html = $_transportObject->getHtml();

        return $html;
    }
}
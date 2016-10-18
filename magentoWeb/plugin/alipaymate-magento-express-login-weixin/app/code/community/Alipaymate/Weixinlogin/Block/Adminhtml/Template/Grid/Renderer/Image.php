<?php
class Alipaymate_Weixinlogin_Block_Adminhtml_Template_Grid_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return $this->_getValue($row);
    }
    protected function _getValue(Varien_Object $row)
    {
        $val = $row->getData($this->getColumn()->getIndex());
        $val = str_replace("no_selection", "", $val);
        //$url = Mage::getBaseUrl('media') . 'catalog/product' . $val;
        $url =  $val;
        $out = "<img src=". $url ." width='60px'/>";
        return $out;
    }
}

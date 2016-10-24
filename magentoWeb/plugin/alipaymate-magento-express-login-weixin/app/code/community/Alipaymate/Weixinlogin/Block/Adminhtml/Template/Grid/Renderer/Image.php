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
        $url =  $val;
        if ($url=="no_selection" || $url =="")  {
            $out = $this->__("No Uploaded File");
          } elseif (filter_var ($url, FILTER_VALIDATE_URL))  {
            $out = "<img src=". $url ." width='60px'/>";
          } else  {
          $out = "<img src=". Mage::getBaseUrl('media') .$url ." width='60px'/>";
          }
        return $out;
    }
}

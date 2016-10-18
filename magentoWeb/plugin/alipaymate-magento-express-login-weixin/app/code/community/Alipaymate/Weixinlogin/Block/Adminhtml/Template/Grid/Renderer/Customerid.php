<?php
class Alipaymate_Weixinlogin_Block_Adminhtml_Template_Grid_Renderer_Customerid extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return $this->_getValue($row);
    }
    protected function _getValue(Varien_Object $row)
    {
        $val = $row->getData($this->getColumn()->getIndex());
        if ($val == 0)  {
          $out = '';
        } else  {
          $url = $this->getUrl("*/customer/edit", array("id" => $val));
          $out = '<a target="_blank" href="' .$url. '">' .$val. '</a>';
        }
        return $out;
    }
}

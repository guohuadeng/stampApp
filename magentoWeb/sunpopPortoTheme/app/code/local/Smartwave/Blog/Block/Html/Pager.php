<?php

class Smartwave_Blog_Block_Html_Pager extends Mage_Page_Block_Html_Pager
{

    public function getPagerUrl($params=array())
    {
        $urlParams = array();
        $urlParams['_escape'] = true;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_query'] = $params;

        if ($this->getParentBlock()->getCurrentOrder()) {
            $urlParams['_query'][$this->getParentBlock()->getOrderVarName()] = $this->getParentBlock()->getCurrentOrder();
        }
        if ($this->getParentBlock()->getCurrentDirection()) {
            $urlParams['_query'][$this->getParentBlock()->getDirectionVarName()] = $this->getParentBlock()->getCurrentDirection();
        }
        if ($this->getParentBlock()->getLimit()) {
            $urlParams['_query'][$this->getParentBlock()->getLimitVarName()] = $this->getParentBlock()->getLimit();
        }

        return $this->getUrl('*/*/*', $urlParams);
    }

    protected function _getUrlModelClass()
    {
        return 'blog/url';
    }

}

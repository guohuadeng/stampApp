<?php

class Smartwave_Blog_Block_Product_Toolbar extends Smartwave_Blog_Block_Product_ToolbarCommon
{
    public function setCollection($collection)
    {
        parent::setCollection($collection);

        if ($this->getCurrentOrder() && $this->getCurrentDirection()) {
            $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
        }

        return $this;
    }

    public function getCurrentOrder()
    {
        $order = $this->getRequest()->getParam($this->getOrderVarName());

        if (!$order) {
            return $this->_orderField;
        }

        if (array_key_exists($order, $this->getAvailableOrders())) {
            return $order;
        }

        return $this->_orderField;
    }

    public function getCurrentMode()
    {
        return null;
    }

    public function getAvailableLimit()
    {
        return $this->getPost()->getAvailLimits();
    }

    public function getCurrentDirection()
    {
        $dir = $this->getRequest()->getParam($this->getDirectionVarName());

        if (in_array($dir, array('asc', 'desc'))) {
            return $dir;
        }

        return strtolower(Mage::helper('blog')->defaultPostSort(Mage::app()->getStore()->getId()));
    }

    public function setDefaultOrder($field)
    {
        $this->_orderField = $field;
    }

    public function getLimit()
    {
        return $this->getRequest()->getParam($this->getLimitVarName());
    }

    public function getPagerUrl($params=array())
    {
        $urlParams = array();
        $urlParams['_escape'] = true;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_query'] = $params;
        if ($this->getLimit() && !isset($params['limit'])) {
            $urlParams['_query'][$this->getLimitVarName()] = $this->getLimit();
        }
        if ($this->getRequest()->getParam($this->getOrderVarName()) && !isset($params['order'])) {
            $urlParams['_query'][$this->getOrderVarName()] = $this->getRequest()->getParam($this->getOrderVarName());
        }
        if ($this->getRequest()->getParam($this->getDirectionVarName()) && !isset($params['dir'])) {
            $urlParams['_query'][$this->getDirectionVarName()] = $this->getRequest()->getParam($this->getDirectionVarName());
        }
        return $this->getUrl('*/*/*', $urlParams);
    }

    protected function _getUrlModelClass()
    {
        return 'blog/url';
    }

}

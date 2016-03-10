<?php
/**
 * SMARTWAVE ALL
 *
 * @category   Smartwave
 * @package    Smartwave_All
 * @author     SW-THEMES
 * @copyright  Copyright (c) 2014 Smartwave Co. (http://www.newsmartwave.net)
 */

/**
 * Layout model
 *
 * @category   Smartwave
 * @package    Smartwave_All
 * @author     SW-THEMES
 */
class Smartwave_All_Model_Core_Layout extends Mage_Core_Model_Layout
{
    public function generateBlocks($parent=null) {

        if (empty($parent)) {
            $parent = $this->getNode();
        }

        if (isset($parent['ifconfig']) && ($configPath = (string)$parent['ifconfig'])) {
            if (!Mage::getStoreConfigFlag($configPath)) {
                return;
            }
        }
        parent::generateBlocks($parent);
    }

    protected function _generateBlock($node, $parent) {
        if (isset($node['ifconfig']) && ($configPath = (string)$node['ifconfig'])) {
            if (!Mage::getStoreConfigFlag($configPath)) {
                return;
            }
        }
        return parent::_generateBlock($node, $parent);
    }
    /**
     * Checks ifconfig and conditions to run action or not
     *
     * @param Varien_Simplexml_Element $node
     * @param Varien_Simplexml_Element $parent
     * @return Smartwave_All_Model_Core_Layout
     */
    protected function _generateAction($node, $parent)
    {
        $compiler = Mage::getModel('all/compiler');
        
        if (isset($node['ifconfig']) && ($configPath = (string)$node['ifconfig'])) {
            $condition = true;
            if (isset($node['condition'])) {
                $condition = $compiler->getXmlCondition($compiler->spaceRemover($node['condition']));
            }
            $config = $compiler->getAdminConfig($compiler->spaceRemover($configPath));
            
            if ($config !== $condition) {
                return $this;
            }
        }
        else if (isset($node['modules']) && isset($node['options'])) {
            $finalResult = false;
            $extracted   = $compiler->extractor($node);
            $operation   = $compiler->spaceRemover((string)$node['operation']);
            $valideOpe   = $operation != '' ? $compiler->validator($node) : true;
            
            if ($valideOpe) {
                $tokens      = $compiler->getToken($operation);
                $finalResult = $compiler->operation($extracted, $tokens);
            }
            if ($finalResult !== true) {
                return $this;
            }
        }

        $this->_runAction($node, $parent);
    }
    
    /**
     * If all ifconfig conditions are ok then action runs
     *
     * @param Varien_Simplexml_Element $node
     * @param Varien_Simplexml_Element $parent
     * @return Smartwave_All_Model_Core_Layout
     */
    private function _runAction($node, $parent)
    {
        $method = (string)$node['method'];
        if (!empty($node['block'])) {
            $parentName = (string)$node['block'];
        } else {
            $parentName = $parent->getBlockName();
        }

        $_profilerKey = 'BLOCK ACTION: '.$parentName.' -> '.$method;
        Varien_Profiler::start($_profilerKey);

        if (!empty($parentName)) {
            $block = $this->getBlock($parentName);
        }
        if (!empty($block)) {

            $args = (array)$node->children();
            unset($args['@attributes']);

            foreach ($args as $key => $arg) {
                if (($arg instanceof Mage_Core_Model_Layout_Element)) {
                    if (isset($arg['helper'])) {
                        $helperName = explode('/', (string)$arg['helper']);
                        $helperMethod = array_pop($helperName);
                        $helperName = implode('/', $helperName);
                        $arg = $arg->asArray();
                        unset($arg['@']);
                        $args[$key] = call_user_func_array(array(Mage::helper($helperName), $helperMethod), $arg);
                    } else {
                        /**
                         * if there is no helper we hope that this is assoc array
                         */
                        $arr = array();
                        foreach($arg as $subkey => $value) {
                            $arr[(string)$subkey] = $value->asArray();
                        }
                        if (!empty($arr)) {
                            $args[$key] = $arr;
                        }
                    }
                }
            }

            if (isset($node['json'])) {
                $json = explode(' ', (string)$node['json']);
                foreach ($json as $arg) {
                    $args[$arg] = Mage::helper('core')->jsonDecode($args[$arg]);
                }
            }

            $this->_translateLayoutNode($node, $args);
            call_user_func_array(array($block, $method), $args);
        }

        Varien_Profiler::stop($_profilerKey);

        return $this;
    }
    
}

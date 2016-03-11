<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */

class Amasty_Fpc_Model_Session
{
    public function __construct()
    {
        if (isset($_SESSION))
        {
            if (!isset($_SESSION['amfpc']))
            {
                $_SESSION['amfpc'] = array('updated_blocks' => array());
            }
        }
    }

    public function getUpdatedBlocks()
    {
        if (isset($_SESSION))
            return $_SESSION['amfpc']['updated_blocks'];
        else
            return array();
    }

    public function updateBlock($name)
    {
        if (!in_array($name, $_SESSION['amfpc']['updated_blocks']))
            $_SESSION['amfpc']['updated_blocks'][] = $name;
    }

    public function isBlockUpdated($name)
    {
        if (isset($_SESSION) && isset($_SESSION['amfpc']['updated_blocks']))
            return in_array($name, $_SESSION['amfpc']['updated_blocks']);
        else
            return false;
    }
}
<?php

class Smartwave_Homeslider_Model_Source_Slidertypes
{
    public function toOptionArray()
    { 
        $result = array('0'=>'Below of the Header', 
            '1'=>'Above of the Header'
        );
		return $result;
    }
}
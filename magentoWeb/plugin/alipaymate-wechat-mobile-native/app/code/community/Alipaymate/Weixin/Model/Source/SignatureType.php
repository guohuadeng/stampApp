<?php

class Alipaymate_Weixin_Model_Source_SignatureType
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'MD5', 'label' => 'MD5'),            
        );
    }
}

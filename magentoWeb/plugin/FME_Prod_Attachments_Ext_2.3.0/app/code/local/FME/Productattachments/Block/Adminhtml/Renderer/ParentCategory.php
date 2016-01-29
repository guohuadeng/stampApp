<?php
 class FME_Productattachments_Block_Adminhtml_Renderer_ParentCategory 
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $m = Mage::getModel ('productattachments/productcats')->load($row['category_id']);
       
        $all = Mage::getModel ('productattachments/productcats')->getCollection(); //echo '<pre>';print_r($all);
        
        $result = "";
        
        if ($row['parent_category_id'] != 0)
        {
            foreach ($all as $c)
            {
                if ($c->getCategoryId() == $row['parent_category_id'])
                {
                    $result = $c->getCategoryName();
                }
            }
        }
        else
        {
            $result = 'Nill';
        }
        
        return $result;
    }
}

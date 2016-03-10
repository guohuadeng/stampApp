<?php

class Smartwave_Megamenu_Block_Navigation extends Mage_Catalog_Block_Navigation
{
	protected static $_model;
	protected static $_helper;
	protected static $_cms_block_model;
	protected static $_processor;
	protected static $_key_current;

	protected function _construct()
    {
		if (!self::$_helper) {
            self::$_helper = Mage::helper('megamenu');
        }
        if (!self::$_model) {
            self::$_model = Mage::getModel('catalog/category');
        }
		if (!self::$_cms_block_model) {
			self::$_cms_block_model = Mage::getModel('cms/block');
		}
		if (!self::$_processor) {
			$proc_helper = Mage::helper('cms');
			self::$_processor = $proc_helper->getPageTemplateProcessor();
		}
		if (!self::$_key_current) {
			self::$_key_current = $this->getCurrentCategory()->getId();
		}
		
    }

    public function drawMegaMenuItem($category,$mode = 'dt', $level = 0, $last = false)
    {
        $_menuHelper = self::$_helper;
        if (!$category->getIsActive()) return '';
        $html = array();
        $id = $category->getId();
        // --- Block Options ---
        $catModel = Mage::getModel('catalog/category')->load($id);
        $blockType = $this->_getBlocks($catModel, 'sw_cat_block_type');
        if (!$blockType || $blockType == 'default')
            $blockType = $_menuHelper->getConfig('general/wide_style');    //Default Format is wide style.
        if ($mode == 'mb')
            $blockType = 'narrow';
        $block_top = $block_left = $block_right = $block_bottom = false;
        if ($blockType == 'wide' || $blockType == 'staticwidth') {
            // ---Get Static Blocks for category, only format is wide style, it is enable.
            if ($level == 0) {
            //  block top of category
                $block_top = $this->_getBlocks($catModel, 'sw_cat_block_top');
            //  block left of category
                $block_left = $this->_getBlocks($catModel, 'sw_cat_block_left');
            //  block left width of category
                $block_left_width = (int)$this->_getBlocks($catModel, 'sw_cat_left_block_width');
                if (!$block_left_width)
                    $block_left_width = 3;
            //  block right of category
                $block_right = $this->_getBlocks($catModel, 'sw_cat_block_right');
            //  block left width of category
                $block_right_width = (int)$this->_getBlocks($catModel, 'sw_cat_right_block_width');
                if (!$block_right_width)
                    $block_right_width = 3;
            //  block bottom of category
                $block_bottom = $this->_getBlocks($catModel, 'sw_cat_block_bottom');
            }
        }
        
        // ---get Category Label---
        $catLabel = $this->_getLabelHtml($catModel, $level);
        
        // --- Sub Categories ---
        $activeChildren = $this->_getActiveChildren($category, $level);
        
        // --- class for active category ---
        $active = ''; if ($this->isCategoryActive($category)) $active = ' act';
        
        $float = $catModel->getData('sw_cat_float_type');
        $hide_item = $catModel->getData('sw_cat_hide_menu_item');
        if($float == "right")
            $float = "fl-right";
        
        $staticWidth = $catModel->getData('sw_cat_static_width');
        if(!$staticWidth)
			$staticWidth = "500px";
            
        // --- category name ---
        $name = $this->escapeHtml($category->getName());
        if (Mage::getStoreConfig('megamenu/general/non_breaking_space')) {
            $name = str_replace(' ', '&nbsp;', $name);
        }
        
        // --- category icon ---
        $cat_icon_img = $catModel->getData('sw_icon_image');
        $cat_font_icon = $catModel->getData('sw_font_icon');
        $cat_icon = "";
        if($cat_icon_img){
            $cat_icon = '<img class="category-icon" src="'.Mage::getBaseUrl('media').'catalog/category/'.$cat_icon_img.'" alt="'.$name.'"/>';
        } else if($cat_font_icon){
            $cat_icon = '<i class="category-icon '.$cat_font_icon.'"></i>';
        }
        
        $drawPopup = ($block_top || $block_left || $block_right || $block_bottom || count($activeChildren));
        if(!$hide_item){
        if ($drawPopup) {
            //Has subcategories or static blocks
            if ($blockType == 'wide') {
                $parentClass = 'menu-full-width';
            } else if ($blockType == 'staticwidth') {
                $parentClass = 'menu-static-width';
            } else {
                $parentClass = 'menu-item menu-item-has-children menu-parent-item';
            }            
            $html[] = '<li class="'.$parentClass.' '.$active.' '.$float.'">';
            $html[] = '<a href="'.$this->getCategoryUrl($category).'">'.$cat_icon.$name.$catLabel.'</a>';
            if ($mode != 'mb') {
                if($blockType == 'staticwidth'){
                    $html[] = '<div class="nav-sublist-dropdown" style="display: none; width:'.$staticWidth.';">';
                    $html[] = '<div class="container">';
                } else {
                    $html[] = '<div class="nav-sublist-dropdown" style="display: none;">';
                    $html[] = '<div class="container">';
                }
            }
            if ($level == 0 && ($blockType == 'wide' || $blockType == 'staticwidth') ) {
                if ($block_top)
                    $html[] = '<div class="top-mega-block">' . $block_top . '</div>';
                $html[] = '<div class="mega-columns row">';
                if ($block_left)
                    $html[] = '<div class="left-mega-block col-sm-'.$block_left_width.'">' . $block_left . '</div>';
                if (count($activeChildren)) {
                    //columns for category
                    $columns = (int)$catModel->getData('sw_cat_block_columns');
                    if (!$columns)
                        $columns = 6;
                    
                    //columns item width    
                    $columnsWidth = 12;
                    if ($block_left)
                        $columnsWidth = $columnsWidth - $block_left_width;
                    if ($block_right)
                        $columnsWidth = $columnsWidth - $block_right_width;
                        
                    //draw category menu items
                    $html[] = '<div class="block1 col-sm-'.$columnsWidth.'">';
                    $html[] = '<div class="row">';                    
                    $html[] = '<ul>';
                    $html[] = $this->drawColumns($activeChildren, $columns, count($activeChildren),'', 'wide');
                    $html[] = '</ul>';
                    $html[] = '</div>';
                    $html[] = '</div>';
                }
                if ($block_right)
                    $html[] = '<div class="right-mega-block col-sm-'.$block_right_width.'">' . $block_right . '</div>';
                $html[] = '</div>';
				/* Fixed from version 1.0.1 */
				//verion 1.0.1 start
                if ($block_bottom)
                    $html[] = '<div class="bottom-mega-block">' . $block_bottom . '</div>';
				//version 1.0.1 end
            } else if ($level == 0 && $blockType == 'narrow') {                
                $html[] = '<ul>';
                $html[] = $this->drawColumns($activeChildren, '', count($activeChildren),'','narrow', $mode);
                $html[] = '</ul>';
            }
            if ($mode != 'mb') {
                $html[] = '</div>';
                $html[] = '</div>';   
            }
            $html[] = '</li>';
        } else {
            //Has no subcategories and static blocks
            $html[] = '<li class="'.$active.' '.$float.'">';
            $html[] = '<a href="'.$this->getCategoryUrl($category).'">'.$cat_icon.$name.$catLabel.'</a>';
            $html[] = '</li>';
        }
        }
        $html = implode("\n", $html);
        return $html;
    }
    
//    custom block 
    public function drawCustomBlock() 
    {
        $_menuHelper = self::$_helper;
        $blockIds = $_menuHelper->getConfig('custom/custom_block');        
        if (!$blockIds) return;
        
        $html = array();
        $blockIds = preg_replace('/\s/', '', $blockIds);
        $IDs = explode(',', $blockIds);
        foreach ($IDs as $blockId) {
            $block = self::$_cms_block_model->setStoreId(Mage::app()->getStore()->getId())->load($blockId);            
            if (!$block) continue;
                        
            $blockTitle = $block->getTitle();            
            $blockContent = $block->getContent(); 
            $blockContent = self::$_processor->filter($blockContent);
            if (!$blockContent) continue;
            $html[] = '<li class="menu-full-width">';
            $html[] = '<a href="javascript:void();" rel="#">';
            if ($_menuHelper->getConfig('general/non_breaking_space')) {
                $blockTitle = str_replace(' ', '&nbsp;', $blockTitle);
            }
            $html[] = $blockTitle;
            $html[] = '</a>';
            $html[] = '<div class="nav-sublist-dropdown">';
            $html[] = '<div class="container">';
            $html[] = $blockContent;
            $html[] = '</div>';
            $html[] = '</div>';            
            $html[] = '</li>';
        }
        if (!$html) return;
        $html = implode("\n", $html);
        return $html;
    }
    
    public function drawCustomMobileLinks()
    {
        $_menuHelper = self::$_helper;
        $blockIds = $_menuHelper->getConfig('custom/custom_mobile_links');        
        if (!$blockIds) return;
        
        $html = array();
        $blockIds = preg_replace('/\s/', '', $blockIds);
        $IDs = explode(',', $blockIds);
        foreach ($IDs as $blockId) {
            $block = self::$_cms_block_model->setStoreId(Mage::app()->getStore()->getId())->load($blockId);
            if (!$block) continue;
            $menuItemContent = $block->getContent();
            $menuItemContent = self::$_processor->filter($menuItemContent);
            if(substr($menuItemContent, 0, 4) == '<ul>') {
                $menuItemContent = substr($menuItemContent, 4);                
            }
            if(substr($menuItemContent, strlen($menuItemContent) - 5) == '</ul>') {
                $menuItemContent = substr($menuItemContent, 0, - 5);                
            }
            $html[] = $menuItemContent; 
        }
        if (!$html) return;
        $html = implode("\n", $html);
        return $html;
    }
    
    public function drawCustomLinks()
    {
        $_menuHelper = self::$_helper;
        $blockIds = $_menuHelper->getConfig('custom/custom_links');
        if (!$blockIds) return;        
        
        $html = array();
        $blockIds = preg_replace('/\s/', '', $blockIds);
        $IDs = explode(',', $blockIds);
        foreach($IDs as $blockId) {
            $block = self::$_cms_block_model->setStoreId(Mage::app()->getStore()->getId())->load($blockId);
            if (!$block) continue;
            $menuItemContent = $block->getContent();
            $menuItemContent = self::$_processor->filter($menuItemContent);  
            if(substr($menuItemContent, 0, 4) == '<ul>') {
                $menuItemContent = substr($menuItemContent, 4);                
            }
            if(substr($menuItemContent, strlen($menuItemContent) - 5) == '</ul>') {
                $menuItemContent = substr($menuItemContent, 0, - 5);                
            }
            $html[] = $menuItemContent;   
        }
        if (!$html) return;
        
        $html = implode("\n", $html);
        return $html;
    }
    public function drawMenuItem($children, $level = 1, $type, $width, $mode = 'dt')
    {
        $keyCurrent = self::$_key_current;
        $html = '';        
        foreach ($children as $child)
        {
            if (is_object($child) && $child->getIsActive())
            {
                $activeChildren = $this->_getActiveChildren($child, $level);
                // --- class for active category ---
                $id = $child->getId();
                // --- Static Block ---
                $catModel = Mage::getModel('catalog/category')->load($id);
                $active = '';
                if ($this->isCategoryActive($child))
                {
                    $active = ' actParent';
                    if ($child->getId() == $keyCurrent) $active = ' act';
                }
                // ---category label
                $label = $this->_getLabelHtml($catModel, $level);
                // --- format category name ---
                $name = $this->escapeHtml($child->getName());
                if (Mage::getStoreConfig('megamenu/general/non_breaking_space'))
                    $name = str_replace(' ', '&nbsp;', $name);
                $class = 'menu-item';
                if (count($activeChildren) > 0) {
                    $class .= ' menu-item-has-children menu-parent-item';
                }
                $hide_item = $catModel->getData('sw_cat_hide_menu_item');
                if(!$hide_item){
                if ($level == 1) {
                    if ($type == 'wide') {
                        //$class .= ' col-sm-'.$width;  ---version 1.0.0---
						$class .= ' col-sw-'.$width; // --- version 1.0.2
                    }                    
                    $html .= '<li class="'.$class.' '.$active.' ">';
					//added from version 1.0.2
					//version 1.0.2 start
					if ($type == 'wide' && $catModel->getThumbnail()) {
						$html .= '<div class="menu_thumb_img">';
						$html .= '<a class="menu_thumb_link" href="'. $this->getCategoryUrl($child) .'">';
						$html .= '<img src="' . Mage::getBaseUrl('media').'catalog/category/' . $catModel->getThumbnail() . '" alt="' . Mage::helper('catalog/output')->__("Thumbnail Image").'" />';
						$html .= '</a>';
						$html .= '</div>';
					}
					//version 1.0.2 end
                    $html.= '<a class="level' . $level . '" href="' . $this->getCategoryUrl($child) . '"><span>' . $name .$label. '</span></a>';    
                } else {
                    $html .= '<li class="'.$class.' '.$active.'">';
                    $html .= '<a class="level' . $level . '" href="' . $this->getCategoryUrl($child) . '"><span>' . $name .$label. '</span></a>';
                }
                if (count($activeChildren) > 0)
                {
                    if ($mode != 'mb') {
                        $html.= '<div class="nav-sublist level' . $level . '">';   
                    }
                    $html.= '<ul>';
                    $html.= $this->drawMenuItem($activeChildren, $level + 1, $type, $width, $mode);
                    $html.= '</ul>';                    
                    if ($mode != 'mb') {
                        $html.= '</div>';   
                    }
                }
                $html .= '</li>';
                }
            }
        }        
        return $html;
    }

    public function drawColumns($children, $columns = 1, $catNum = 1, $catLabel = '', $type, $mode = 'dt')
    {
        $html = '';        
        // --- explode by columns ---
		//---- updated from version 1.0.2 ----
		//version 1.0.0 
        //if ($columns < 1) $columns = 1;
        //$colWidth = 12 / $columns;  
		
		//version 1.0.2 start
		if ($columns < 1) $colWidth = 4;
		$colWidth = $columns;
		//version 1.0.2 end

        $chunks = $this->_explodeByColumns($children, $columns, $catNum);        

        // --- draw columns ---
        $lastColumnNumber = count($chunks);        
        $i = 1;
        foreach ($chunks as $key => $value)
        {
            if ($type == 'wide') {
                //$class = 'col-sm-'.$colWidth; ---version 1.0.0---
				$class = 'col-sw-'.$colWidth; //---version 1.0.2---
                if (!count($value)) continue;                
                $html.= $this->drawMenuItem($value, 1, $type, $colWidth);                
//                if ($i == $colWidth)
//                    $html .= '<div class="clearfix"></div>';
            } else {
                $html .= $this->drawMenuItem($value, 1,'','',$mode);
            }
            $i++;
        }
        
        return $html;
    }

    protected function _getActiveChildren($parent, $level)
    {
        $activeChildren = array();
        // --- check level ---
        $maxLevel = (int)Mage::getStoreConfig('megamenu/general/max_level');
        if ($maxLevel > 0)
        {
            if ($level >= ($maxLevel - 1)) return $activeChildren;
        }
        // --- / check level ---
        if (Mage::helper('catalog/category_flat')->isEnabled())
        {
            $children = $parent->getChildrenNodes();
            $childrenCount = count($children);
        }
        else
        {
            $children = Mage::getModel('catalog/category')->getCategories($parent->getId());
            $childrenCount = $children->count();
        }
        $hasChildren = $children && $childrenCount;
        if ($hasChildren)
        {
            foreach ($children as $child)
            {
                if ($this->_isCategoryDisplayed($child))
                {
                    array_push($activeChildren, $child);
                }
            }
        }
        return $activeChildren;
    }

	private function _isCategoryDisplayed(&$child)
    {
        if (!$child->getIsActive()) return false;
        // === check products count ===
        // --- get collection info ---
        if (!Mage::getStoreConfig('megamenu/general/display_empty_categories'))
        {
            $data = $this->_getProductsCountData();
            // --- check by id ---
            $id = $child->getId();
            #Mage::log($id); Mage::log($data);
            if (!isset($data[$id]) || !$data[$id]['product_count']) return false;
        }
        // === / check products count ===
        return true;
    }

	private function _getProductsCountData()
    {
        if (is_null($this->_productsCount))
        {
            $collection = Mage::getModel('catalog/category')->getCollection();
            $storeId = Mage::app()->getStore()->getId();
            /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
            $collection->addAttributeToSelect('name')
                ->addAttributeToSelect('is_active')
                ->setStoreId($storeId);
            if(!Mage::helper('catalog/category_flat')->isEnabled()){
                $collection->setProductStoreId($storeId)
                    ->setLoadProductCount(true);
            }
            $productsCount = array();
            foreach($collection as $cat)
            {
                $productsCount[$cat->getId()] = array(
                    'name' => $cat->getName(),
                    'product_count' => $cat->getProductCount(),
                );
            }
            #Mage::log($productsCount);
            $this->_productsCount = $productsCount;
        }
        return $this->_productsCount;
    }

	private function _explodeByColumns($target, $num, $catNum)
    {
        $target = self::_explodeArrayByColumnsHorisontal($target, $num, $catNum);
        
        #return $target;
//        if ((int)Mage::getStoreConfig('megamenu/columns/integrate') && count($target))
        if (count($target))
        {
            // --- combine consistently numerically small column ---
            // --- 1. calc length of each column ---
            $max = 0; $columnsLength = array();
            foreach ($target as $key => $child)
            {
                $count = 0;
                $this->_countChild($child, 1, $count);
                if ($max < $count) $max = $count;
                $columnsLength[$key] = $count;
            }
            // --- 2. merge small columns with next ---
            $xColumns = array(); $column = array(); $cnt = 0;
            $xColumnsLength = array(); $k = 0;
            foreach ($columnsLength as $key => $count)
            {
                $cnt+= $count;
                if ($cnt > $max && count($column))
                {
                    $xColumns[$k] = $column;
                    $xColumnsLength[$k] = $cnt - $count;
                    $k++; $column = array(); $cnt = $count;
                }
                $column = array_merge($column, $target[$key]);
            }
            $xColumns[$k] = $column;
            $xColumnsLength[$k] = $cnt - $count;
            // --- 3. integrate columns of one element ---
            $target = $xColumns; $xColumns = array(); $nextKey = -1;
            if ($max > 1 && count($target) > 1)
            {
                foreach($target as $key => $column)
                {
                    if ($key == $nextKey) continue;
                    if ($xColumnsLength[$key] == 1)
                    {
                        // --- merge with next column ---
                        $nextKey = $key + 1;
                        if (isset($target[$nextKey]) && count($target[$nextKey]))
                        {
                            $xColumns[] = array_merge($column, $target[$nextKey]);
                            continue;
                        }
                    }
                    $xColumns[] = $column;
                }
                $target = $xColumns;
            }
        }
        $_rtl = Mage::getStoreConfigFlag('megamenu/general/rtl');
        if ($_rtl) {
            $target = array_reverse($target);
        }
        return $target;
    }

    private function _countChild($children, $level, &$count)
    {
        foreach ($children as $child)
        {
            if ($child->getIsActive())
            {
                $count++; $activeChildren = $this->_getActiveChildren($child, $level);
                if (count($activeChildren) > 0) $this->_countChild($activeChildren, $level + 1, $count);
            }
        }
    }
    
    //get static blocks in menu
    private function _getBlocks($model, $block_signal)
    {
        if (!$this->_tplProcessor)
        { 
            $this->_tplProcessor = Mage::helper('cms')->getBlockTemplateProcessor();            
        }
        return $this->_tplProcessor->filter( trim($model->getData($block_signal)) ); 
    }

    private static function _explodeArrayByColumnsHorisontal($list, $num, $catNum)
    {
        if ($num <= 0) return array($list);
        $partition = array();        
        $partition = array_pad($partition, $catNum, array());  
              
        $i = 0;
        foreach ($list as $key => $value) {
            $partition[$i][$key] = $value;
            if (++$i == $catNum) $i = 0;
        }
        return $partition;
    }
    
    //get Label for menu
    private function _getLabelHtml($catModel, $level)
    {
        $label = $catModel->getData('sw_cat_label');
        if ($label) {
            $labelContent = self::$_helper->getConfig('category_labels/'.$label);
            if ($labelContent) {
                if ($level = 0) {
                    return ' <span class="cat-label cat-label-'. $label .' pin-bottom">' . $labelContent . '</span>';
                } else {
                    return ' <span class="cat-label cat-label-'. $label .'">' . $labelContent . '</span>';
                }
            }
        }
        return '';
    }
    
    /**
     * Check if current url is url for home page
     *
     * @return true
     */
    public function getIsHomePage()
    {
        if(Mage::app()->getFrontController()->getRequest()->getActionName()=='index' && Mage::app()->getFrontController()->getRequest()->getRouteName()=='cms' && Mage::app()->getFrontController()->getRequest()->getControllerName()=='index')
            return true;
        return false;
    }

    public function getLogoSrc()
    {
        if (empty($this->_data['logo_src'])) {
            $this->_data['logo_src'] = Mage::getStoreConfig('design/header/logo_src');
        }
        return $this->getSkinUrl($this->_data['logo_src']);
    }

    public function getLogoAlt()
    {
        if (empty($this->_data['logo_alt'])) {
            $this->_data['logo_alt'] = Mage::getStoreConfig('design/header/logo_alt');
        }
        return $this->_data['logo_alt'];
    }
}

<?php

/**
 * Productattachments extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    Productattachments
 * @author     Kamran Rafiq Malik <kamran.malik@unitedsol.net>
 * @copyright  Copyright 2010 � free-magentoextensions.com All right reserved
 */
class FME_Productattachments_Helper_Data extends Mage_Core_Helper_Abstract {

    const XML_PATH_LIST_PRODUCT_PAGE_ATTACHMENT_HEADING = 'productattachments/productattachments/product_attachment_heading';
    const XML_PATH_LIST_CMS_PAGE_ATTACHMENT_HEADING = 'productattachments/cmspagesattachments/cms_page_attachment_heading';
    const XML_PATH_LIST_LAYOUT = 'productattachments/general/list_layout';
    //const XML_PATH_LIST_ALLOWED_FILE_EXTENSIONS	= 'productattachments/productattachments/allowed_file_extensions';
    CONST XML_PATH_PRODUCTATTACHMENTS_PRODUCTATTACHMENTS_ENABLED = 'productattachments/productattachments/enabled';
    CONST XML_PATH_PRODUCTATTACHMENTS_CMSPAGEATTACHMENTS_ENABLED = 'productattachments/cmspagesattachments/enabled';

    /*
     * @var array Characters to be URL-encoded
     */

    protected static $_URL_ENCODED_CHARS = array(
        ' ', '+', '(', ')', ';', ':', '@', '&', '`', '\'',
        '=', '!', '$', ',', '/', '?', '#', '[', ']', '%',
    );

    public function isEnabledProductPageAttachments() {

        return Mage::getStoreConfig(self::XML_PATH_PRODUCTATTACHMENTS_PRODUCTATTACHMENTS_ENABLED, Mage::app()->getStore()->getId());
    }

    public function isEnabledCmsPageAttachments() {

        return Mage::getStoreConfig(self::XML_PATH_PRODUCTATTACHMENTS_CMSPAGEATTACHMENTS_ENABLED, Mage::app()->getStore()->getId());
    }

    public function getProductPageAttachmentHeading() {
        return Mage::getStoreConfig(self::XML_PATH_LIST_PRODUCT_PAGE_ATTACHMENT_HEADING);
    }

    public function getCMSPageAttachmentHeading() {
        return Mage::getStoreConfig(self::XML_PATH_LIST_CMS_PAGE_ATTACHMENT_HEADING);
    }

    /* public function getAllowedFileExtensions()
      {
      return Mage::getStoreConfig(self::XML_PATH_LIST_ALLOWED_FILE_EXTENSIONS);
      } */

    public static function nameToUrlKey($name) {
        $name = trim($name);

        $name = str_replace(self::$_URL_ENCODED_CHARS, '_', $name);

        do {
            $name = $newStr = str_replace('__', '_', $name, $count);
        } while ($count);

        return $name;
    }

    public function getCatData($pid = 0) {
        $out = array();
        $collection = Mage::getModel('productattachments/productcats')->getCollection()
                ->addOrder('category_name', 'ASC');

        foreach ($collection as $item) {
            $out[] = $item->getData();
        }

        return $out;
    }

/*    public function getSelectcat() {
        //Set 0 For Parent Cat!
        $this->drawSelect(0);
        foreach ($this->outtree['value'] as $k => $v) {
            $out[] = array('value' => $v, 'label' => $this->outtree['label'][$k]);
        }
        return $out;
    }

    public function drawSelect($pid = 0) {
        $items = $this->getCatData($pid);
        if (count($items) > 0) {
            $this->outtree['value'][] = $item[0];
            $this->outtree['label'][] = 'Select Category';
            foreach ($items as $item) {
                $this->outtree['value'][] = $item['category_id'];
                $this->outtree['label'][] = $item['category_name'];
            }
        }
        return;
    }
*/
    public function getAllCategories($parentCatId = null, $isAdmin = false) {

        $collection = Mage::getModel('productattachments/productcats')
                ->getCollection();

        if ($parentCatId != null) {
            $collection->addRootCategoryFilter($parentCatId);
        }
        if (!$isAdmin) {
            $collection->addStoreFilter(Mage::app()->getStore()->getId());
            $collection->addStatusFilter();
        } //echo (string) $collection->getSelect();exit;
        //echo '<pre>';print_r($collection->getData());exit;
        return $collection;
    }

    public function getAllParentCategories() {

        $collection = Mage::getModel('productattachments/productcats')
                ->getCollection()
                ->addFieldToFilter('main_table.parent_category_id', 0)
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addStatusFilter(true);

        return $collection;
    }

    public function getLayout() {
        return Mage::getStoreConfig(self::XML_PATH_LIST_LAYOUT, Mage::app()->getStore()->getId()) . '.phtml';
    }

    public function getProductAttachments($categoryId = null) {

        $collection = Mage::getModel('productattachments/productattachments')
                ->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId());

        if ($categoryId != null) {
            $collection->addFieldToFilter('main_table.cat_id', $categoryId);
        }

        $collection->addEnableFilter(true);

        return $collection;
    }

    /*
     * generate a list differently for
     * edit/add respectively. this is to prevent
     * duplicate records and also to restrict 2 level
     * inheritence.
     * @param <int> $id
     * @return <array> $val
     */

    public function getCategoryList($id = 0) {
        $val = array();

        $val[] = array(
            'value' => 0,
            'label' => Mage::helper('productattachments')->__('Parent Category')
        );

        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $table = $resource->getTableName('productattachments/productcats');
        /* if update mode else add */
        if ($id != 0) {
            $q = "SELECT category_id,category_name
				  FROM " . $table . "
				  WHERE category_id !=" . $id . "
				  AND parent_category_id =0";

            $result = $read->fetchAll($q);

            foreach ($result as $r) {
                $val[] = array(
                    'value' => $r['category_id'],
                    'label' => Mage::helper('productattachments')->__($r['category_name'])
                );
            }
        } else {
            $q = "SELECT category_id,category_name
				  FROM " . $table . "
				  WHERE parent_category_id = 0";

            $result = $read->fetchAll($q);

            foreach ($result as $r) {
                $val[] = array(
                    'value' => $r['category_id'],
                    'label' => Mage::helper('productattachments')->__($r['category_name'])
                );
            }
        }

        return $val;
    }

    /*
     * the method generate array as an outcome
     * for dropdown with only parent categories title
     * @return <array> $categoryInfo
     */

    public function parentTitleOptions() {
        $collection = Mage::getModel('productattachments/productcats')->getCollection();
        $collection->addFieldToFilter('parent_category_id', 0);

        $options = array();
        $categoryInfo = array();
        $i = 0;

        foreach ($collection as $i) {
            $categoryInfo[$i->getCategoryId()] = $i->getCategoryName();
        }

        if (!empty($categoryInfo)) {
            return $categoryInfo;
        }

        return false;
    }

    public function getSubCategories($parentCatId) {

        $collection = Mage::getModel('productattachments/productcats')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getStoreId())
                ->addFieldToFilter('parent_category_id', $parentCatId)
                ->addStatusFilter(true);

        return $collection;
    }

    public function checkParent($obj) {

        $model = Mage::getModel('productattachments/productcats');
        $data = false;

        if (is_object($obj) && $obj->getParentCategoryId() > 0) {

            $data = $model->load($obj->getParentCategoryId());
        }

        return $data;
    }

    /* for breadcrumbs only */

    public function getAllLevels($obj) {

        $model = Mage::getModel('productattachments/productcats');
        $info = array();
        if (is_object($obj) && $obj->getId() > 0) {

            $curr_cat = $model->load($obj->getCatId());
            $info['curr_cat'] = $curr_cat;
            if ($curr_cat->getParentCategoryId() > 0) {
                $curr_par_cat = Mage::getModel('productattachments/productcats')->load($curr_cat->getParentCategoryId());
                $info['curr_par_cat'] = $curr_par_cat;
            }
        }

        return $info;
    }

    public function linkTitleHeader() {

        return Mage::helper('productattachments')->__('Products Attachments');
    }

    public function clientUrl() {

        return Mage::getUrl('productattachments/index/list');
    }

    public function doesExistsByBlock($store, $cateogryId, $block, $productId) {

        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $_categoryTable = $resource->getTableName('productattachments_cats');
        $_categoryTableStore = $resource->getTableName('productattachments_category_store');
        $_mainTable = $resource->getTableName('productattachments');
        $_mainTableStore = $resource->getTableName('productattachments_store');
        $_mainTableProducts = $resource->getTableName('productattachments_products');

        $exists = false;

        $sql = $read->select()
                ->from(array('main_table' => $_mainTable))
                ->columns('main_table.cat_id')
                ->join(array('main_table_store' => $_mainTableStore), 'main_table_store.productattachments_id = main_table.productattachments_id')
                ->join(array('main_table_prods' => $_mainTableProducts), 'main_table_prods.productattachments_id = main_table.productattachments_id')
                /*
                  ->join(array('category_table' => $_categoryTable), 'category_table.category_id = main_table.cat_id')
                  ->join(array('category_table_store' => $_categoryTableStore), 'category_table_store.category_id = category_table.category_id')
                  ->where('')
                 */
                ->where('main_table.block_position LIKE ?', "%{$block}%")
                ->where('main_table_store.store_id = (?)', $store)
                ->where('main_table_prods.product_id = (?)', $productId)
                ->where('main_table.status = (?)', 1);

        $fetch = $read->fetchAll($sql);
        echo '<pre>';
        print_r($fetch);
        exit;
        /*
          ->join(array('category_table' => $_categoryTable))
          ->join(array('category_table_store' => $_mainTableStore));
         */
    }

	/**
     * Resize Image proportionally and return the resized image url
     *
     * @param string $imageName         name of the image file
     * @param integer|null $width       resize width
     * @param integer|null $height      resize height
     * @param string|null $imagePath    directory path of the image present inside media directory
     * @return string               full url path of the image
     */
    public function resizeImage($imageName, $width = NULL, $height = NULL, $imagePath = NULL) {

        $imagePath = str_replace("/", DS, $imagePath);
        $imagePathFull = Mage::getBaseDir('media') . DS . $imagePath . DS . $imageName;

        if ($width == NULL && $height == NULL) {
            $width = 100;
            $height = 100;
        }
        $resizePath = $width . 'x' . $height;
        $resizePathFull = Mage::getBaseDir('media') . DS . $imagePath . DS . $resizePath . DS . $imageName;

        if (file_exists($imagePathFull) && !file_exists($resizePathFull)) {
            $imageObj = new Varien_Image($imagePathFull);
            $imageObj->constrainOnly(TRUE);
            $imageObj->keepAspectRatio(TRUE);
            $imageObj->resize($width, $height);
            $imageObj->save($resizePathFull);
        }

        $imagePath = str_replace(DS, "/", $imagePath);
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $imagePath . "/" . $resizePath . "/" . $imageName;
    }
}

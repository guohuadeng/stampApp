<?php 
class Smartwave_Porto_Model_Cssconfig_Generator extends Mage_Core_Model_Abstract{ 
    public function __construct(){
        parent::__construct(); 
    } 
    public function generateCss($type, $websiteCode, $storeCode){
        if ($websiteCode){ 
            if ($storeCode) {
                $this->generateStoreCss($type, $storeCode);
            } 
            else {
                $this->generateWebsiteCss($type, $websiteCode); 
            }
        }else{
            $websites = Mage::app()->getWebsites(false, true);
            foreach ($websites as $code => $value) {
                $this->generateWebsiteCss($type, $code); 
            }
        } 
    } 
    protected function generateWebsiteCss($type, $websiteCode) {
        $website = Mage::app()->getWebsite($websiteCode);
        foreach ($website->getStoreCodes() as $code){ 
            $this->generateStoreCss($type, $code);
        } 
    }
    protected function generateStoreCss($type, $storeCode){
        if (!Mage::app()->getStore($storeCode)->getIsActive()) 
            return;
        $str1 = '_' . $storeCode;
        $str2 = $type . $str1 . '.css';
        $str3 = Mage::helper('porto/cssconfig')->getCssConfigDir() . $str2;
        $str4 = 'porto/css/' . $type . '.phtml';
        Mage::register('cssgen_store', $storeCode);
        try{ 
            $block = Mage::app()->getLayout()->createBlock("core/template")->setData('area', 'frontend')->setTemplate($str4)->toHtml();
            if (empty($block)) {
                throw new Exception( Mage::helper('porto')->__("Template file is empty or doesn't exist: %s", $str4) );
            }
            $file = new Varien_Io_File(); 
            $file->setAllowCreateFolders(true); 
            $file->open(array( 'path' => Mage::helper('porto/cssconfig')->getCssConfigDir() )); 
            $file->streamOpen($str3, 'w+'); 
            $file->streamLock(true); 
            $file->streamWrite($block); 
            $file->streamUnlock(); 
            $file->streamClose(); 
        }catch (Exception $e){ 
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('porto')->__('Failed generating CSS file: %s in %s', $str2, Mage::helper('porto/cssconfig')->getCssConfigDir()). '<br/>Message: ' . $e->getMessage());
            Mage::logException($e);
        }
        Mage::unregister('cssgen_store'); 
    } 
}
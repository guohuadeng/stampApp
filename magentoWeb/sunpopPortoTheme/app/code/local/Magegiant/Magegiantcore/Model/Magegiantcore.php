<?php

class Magegiant_Magegiantcore_Model_Magegiantcore extends Mage_Core_Model_Abstract
{
    const XML_FREQUENCY_PATH    = 'magegiantcore/general/frequency';
    const XML_LAST_UPDATE_PATH  = 'magegiantcore/general/last_update';
    const XML_MAGEGIANT_URL_PATH  = 'magegiantcore/general/magegiant_url';

    public function _construct()
    {
        parent::_construct();
        $this->_init('magegiantcore/magegiantcore');
    }	
	
    public function checkUpdate()
    {
		$timestamp = Mage::getModel('core/date')->timestamp(time());
		
		if (($this->getFrequency() + $this->getLastUpdate()) > $timestamp) 
		{
			return $this;
        }

        $notificationXml = $this->getNotificationData();
        
		$noticeData = array();

        if ($notificationXml && $notificationXml->item) 
		{
            foreach ($notificationXml->item as $item) {
                $item_data = array(
                    'severity'      => (int)$item->severity,
                    'date_added'    => $this->getDate((string)$item->date_added),
                    'title'         => (string)$item->title,
                    'description'   => (string)$item->description,
                    'url'           => (string)$item->url,
					'related_extensions' => (string)$item->related_extensions
                );
				if($this->allowGetFeed($item_data)){
					$noticeData[] = $item_data;
				}
            }
			
			if($noticeData) 
			{
				$this->parse(array_reverse($noticeData));
				
				$this->setLastUpdate();
			}
        }
        return $this;
    }	
	
	public function allowGetFeed($item)
	{
		if($item['related_extensions'] == null || $item['related_extensions'] == '0'){
			return true;
		}
		
        $modules = Mage::getConfig()->getNode('modules')->children();
        foreach ($modules as $moduleName => $moduleInfo) {
			if ($moduleName==='Mage_Adminhtml') {
                continue;
            }
            if ($moduleName==='Magegiant_Magegiantcore') {
                continue;
            }			
			if(strpos('a'.$moduleName,'Magegiant') == 0){
				continue;
			}
			$extension_code = str_replace('Magegiant_','',$moduleName);
			$related_extensions  = explode(',',$item['related_extensions']);
			if(count($related_extensions)){
				foreach($related_extensions as $related_extension){
					if($related_extension == $extension_code){
						return true;
					}
				}
			}
        }	
		return false;
	}
	
	public function getLastUpdate()
	{
		$timestring = Mage::getStoreConfig(self::XML_LAST_UPDATE_PATH);

		return strtotime($timestring);
	}
	
	public function setLastUpdate()
	{
		$config = Mage::getModel('core/config');
		$timestamp = Mage::getModel('core/date')->timestamp(time()); 
		
		$config ->saveConfig(self::XML_LAST_UPDATE_PATH,$timestamp);
		$config->cleanCache();
	}	
	
	public function getFrequency()
	{
		return Mage::getStoreConfig(self::XML_FREQUENCY_PATH) * 3600;
	}
	
	public function getMagegiantUrl()
	{
		$lastTimeNotice = strtotime($this->getLastNotice()->getAddedDate());
		
		return Mage::getStoreConfig(self::XML_MAGEGIANT_URL_PATH) .'/magegiantcore/service/getfeed2/lastupdatetime/'. $lastTimeNotice;
	}
	
	public function getNotificationData()
    {	
		$curl = new Varien_Http_Adapter_Curl();
        $curl->setConfig(array(
            'timeout'   => 2
        ));
        $curl->write(Zend_Http_Client::GET, $this->getMagegiantUrl(), '1.0');
        $data = $curl->read();
	
        if ($data === false) {
            return false;
        }
        $data = preg_split('/^\r?$/m', $data, 2);
        $data = trim($data[1]);
        $curl->close();

        try {
            $xml  = new SimpleXMLElement($data);
        }
        catch (Exception $e) {
            return false;
        }

        return $xml;
    }

    public function getNotificationXml()
    {
        try {
            $data = $this->getNotificationData();
            $xml  = new SimpleXMLElement($data);
        }
        catch (Exception $e) {
            $xml  = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?>');
        }

        return $xml;
    }

    public function getDate($rssDate)
    {
        return gmdate('Y-m-d H:i:s', strtotime($rssDate));
    }	
	
	public function parse($data)
	{
		if(count($data))
		{
			$inbox = Mage::getModel('adminnotification/inbox');
			
			try{
			foreach($data as $item)
			{
				if(! $this->is_existedUrl($item['url']) )
				{
					$inbox->setData($item)
							->save();
							
					$this->setUrl($inbox->getUrl());
					$this->setNotificationId($inbox->getId());
					$this->setAddedDate($inbox->getDateAdded());
					$this->save();
					
						//unset Id
					$this->setId(null);
					$inbox->setId(null);
				}
			}
			} catch(Exception $e) {
			
				Mage::getSingleton('core/session')->addError($e->getMessage());
			}
		}
	}
	
	public function is_existedUrl($url)
	{
		$collection = $this->getCollection()
						->addFieldToFilter('url',$url);
		if($collection->getSize())
			return true;
		
		return false;
	}
	
	public function getLastNotice()
	{
		$item = $this->getCollection()
						->setOrder('added_date','DESC')
						->getFirstItem();
		return $item;
	}
}
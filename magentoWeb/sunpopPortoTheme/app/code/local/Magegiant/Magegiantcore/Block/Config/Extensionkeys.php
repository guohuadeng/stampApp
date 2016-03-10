<?php
class Magegiant_Magegiantcore_Block_Config_Extensionkeys
	extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    protected $_dummyElement;
    protected $_fieldRenderer;
    protected $_values;

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);

        $modules = Mage::getConfig()->getNode('modules')->children();

       // sort($modules);

        foreach ($modules as $moduleName => $moduleInfo) {
		
			if ($moduleName==='Mage_Adminhtml') {
                continue;
            }
            if ($moduleName==='Magegiant_Magegiantcore') {
                continue;
            }			
			if(strpos('a'.$moduleName,'Magegiantcore') == 0){
				continue;
			}
			if((string)$moduleInfo->codePool != 'local'){
				continue;
			}
			
			if(isset($moduleInfo->nonecheckkey) && (string)$moduleInfo->nonecheckkey == 'true'){
				continue;
			}			
			
			$module_alias = (string)$moduleInfo->aliasName ? (string)$moduleInfo->aliasName : $moduleName;
			
            $html .= $this->_getFieldHtml($element, $moduleName, $module_alias);
            $html .= $this->_getInfoHtml($element, $moduleName);
            $html .= $this->_getDividerHtml($element, $moduleName);
        }
        $html .= $this->_getFooterHtml($element);
        return $html;
    }

    protected function _getDummyElement()
    {
        if (empty($this->_dummyElement)) {
            $this->_dummyElement = new Varien_Object(array('show_in_default'=>1, 'show_in_website'=>1));
        }
        return $this->_dummyElement;
    }

    protected function _getFieldRenderer()
    {
        if (empty($this->_fieldRenderer)) {
            $this->_fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
        }
        return $this->_fieldRenderer;
    }

    protected function _getValues()
    {
        if (empty($this->_values)) {
            $this->_values = array(
                array('label'=>Mage::helper('adminhtml')->__('Enable'), 'value'=>0),
                array('label'=>Mage::helper('adminhtml')->__('Disable'), 'value'=>1),
            );
        }
        return $this->_values;
    }	
	
    protected function _getDividerHtml($fieldset,$moduleName)
    {
        $field = $fieldset->addField($moduleName.'_divider', 'label',
            array(
                'name'          => 'groups[extension_keys][fields]['.$moduleName.'_divider][value]',
                'label'         => '',
                'value'         => '<div style="margin:5px 0 20px 0;border-top: 1px dashed rgb(221, 108, 15);"></div>',
                'inherit'       => false,
                'can_use_default_value' => 0,
                'can_use_website_value' => 0,
            ))->setRenderer(Mage::getBlockSingleton('magegiantcore/config_field'));

        return $field->toHtml();
    }	

    protected function _getFieldHtml($fieldset, $moduleName, $module_alias)
    {
        $configData = $this->getConfigData();
        $path = 'magegiantcoresecure/extension_keys/'.$moduleName; //TODO: move as property of form
        $data = isset($configData[$path]) ? $configData[$path] : '';
		
        $e = $this->_getDummyElement();

        $field = $fieldset->addField($moduleName, 'text',
            array(
                'name'          => 'groups[extension_keys][fields]['.$moduleName.'][value]',
                'label'         => $module_alias,
                'value'         => $data,
				'style'         => 'width:688px;',
                'inherit'       => isset($configData[$path]) ? false : true,
                'can_use_default_value' => $this->getForm()->canUseDefaultValue($e),
                'can_use_website_value' => $this->getForm()->canUseWebsiteValue($e),
            ))->setRenderer($this->_getFieldRenderer());

        return $field->toHtml();
    }
	
    protected function _getInfoHtml($fieldset, $moduleName)
    {
        $configData = $this->getConfigData();
        $path = 'magegiantcoresecure/extension_keys/'.$moduleName; //TODO: move as property of form
        $data = isset($configData[$path]) ? $configData[$path] : array();		
        if($data){
			$helper = Mage::helper('magegiantcore');
			//prepare license info
			$licenseinfo = $helper->getLicenseInfo($data,$moduleName);
			//prepare additional license form
			$licenseType = $helper->getLicenseType($moduleName);
			if( in_array($licenseType,array(-1,
											0,
											Magegiant_Magegiantcore_Model_Keygen::TRIAL_VERSION,
											//Magegiant_Magegiantcore_Model_Keygen::DEVELOPMENT,
				)) || $helper->getDBResponseCode() < Magegiant_Magegiantcore_Model_Keygen::NEW_DOMAIN_SUCCESS ){
				$additionalLicenseForm = Mage::getBlockSingleton('magegiantcore/adminhtml_license_purchaseform')
										->setExtensionName($helper->getExtensionName($moduleName))
										->toHtml();				
			}
			//license info field
			$e = $this->_getDummyElement();
			$field = $fieldset->addField($moduleName.'_license_info', 'label',
				array(
					'name'          => 'groups[extension_keys][fields]['.$moduleName.'_license_info][value]',
					'label'         => $helper->__('License Info'),
					'value'         => $licenseinfo . $additionalLicenseForm,
					'can_use_default_value' => $this->getForm()->canUseDefaultValue($e),
					'can_use_website_value' => $this->getForm()->canUseWebsiteValue($e),
				))->setRenderer(Mage::getBlockSingleton('magegiantcore/config_field'));

			return $field->toHtml();
		}
    }	
}
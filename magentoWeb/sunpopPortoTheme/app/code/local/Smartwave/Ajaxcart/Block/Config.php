<?php
class Smartwave_Ajaxcart_Block_Config extends Mage_Core_Block_Abstract {

	public function _toHtml() {
		$content = "";
		
			$configs = array(
				'selector'	=> array(
					'paginator'			=> 'ajaxcart/selector/paginator',
					'limiter'			=> 'ajaxcart/selector/limiter',
					'mode'				=> 'ajaxcart/selector/mode',
					'sortby'			=> 'ajaxcart/selector/sortby',
					'sortdir'			=> 'ajaxcart/selector/sortdir',
					'navfilter'			=> 'ajaxcart/selector/navfilter',
					'navclear'			=> 'ajaxcart/selector/navclear',
					'navremove'			=> 'ajaxcart/selector/navremove'
				),
				'content'	=> array(
					'name'				=> 'ajaxcart/blockcontent/name',
					'selector'			=> 'ajaxcart/blockcontent/selector',
					'replace'			=> 'ajaxcart/blockcontent/replace'
				),
				'layer'		=> array(
					'name'				=> 'ajaxcart/blocklayer/name',
					'selector'			=> 'ajaxcart/blocklayer/selector',
					'replace'			=> 'ajaxcart/blocklayer/replace'
				)
			);
			
			$newConfig = array();
			foreach ($configs as $confKey => $_data) {
				foreach ($_data as $dataKey => $dataVal) {
					$tempVal = Mage::getStoreConfig($dataVal);
					if (!empty($tempVal)) {
						if (!isset($newConfig[$confKey]))
							$newConfig[$confKey] = array();
						$newConfig[$confKey][$dataKey] = $tempVal;
					}
				}
			}
			
			if (count($newConfig)) {
				foreach ($newConfig as $key => $data) {
					switch ($key) {
						case 'selector' :
							foreach ($data as $_key => $_val) {
								$content.= "TR.catalogajax.config.".$_key."='".$_val."';\n";
							}
							break;
						case 'content' :
							foreach ($data as $_key => $_val) {
								$content.= "TR.catalogajax.config.blocks.content.".$_key."='".$_val."';\n";
							}
							break;
						case 'layer' :
							foreach ($data as $_key => $_val) {
								$content.= "TR.catalogajax.config.blocks.layer.".$_key."='".$_val."';\n";
							}
							break;
						default:
							continue;
					} 
				}
			}
			if (Mage::app()->getFrontController()->getAction()->getFullActionName() == 'catalogsearch_result_index') {
				$searchBlockName = Mage::getStoreConfig('ajaxcart/blocksearchlayer/name');
				$content.= 'TR.catalogajax.config.blocks.layer.name = \''.(($searchBlockName)?$searchBlockName:'catalogsearch.leftnav').'\';'."\n";
				$temp = Mage::getStoreConfig('ajaxcart/blocksearchlayer/selector');
				if ($temp) {
					$content.= 'TR.catalogajax.config.blocks.layer.selector = \''.$temp.'\''."\n";
				}
				$temp = Mage::getStoreConfig('ajaxcart/blocksearchlayer/replace');
				if ($temp) {
					$content.= 'TR.catalogajax.config.blocks.layer.replace = \''.$temp.'\''."\n";
				}
			}
			if ($content) {
				$content = "<script type=\"text/javascript\">\n".$content."\n</script>\n";
			}
		return $content;
	}
		
}
?>
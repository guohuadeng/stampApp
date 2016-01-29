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
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('productattachments_cats')};
CREATE TABLE {$this->getTable('productattachments_cats')} (                               
  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(254) NOT NULL DEFAULT '',
  `category_image` mediumtext,
  `category_status` tinyint(1) NOT NULL DEFAULT '1',
  `category_url_key` varchar(254) DEFAULT NULL,
  `parent_category_id` mediumint(9) NOT NULL DEFAULT '0',
  `category_order` int(10) NOT NULL DEFAULT '1',
  `meta_keywords` text,
  `meta_description` text,
  `left_node` int(11) DEFAULT NULL,
  `right_node` int(11) DEFAULT NULL,
  PRIMARY KEY (`category_id`),
  KEY `productattachments_category_index_name` (`category_name`),
  KEY `productattachments_category_index_url_key` (`category_url_key`)  
 ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
 
DROP TABLE IF EXISTS {$this->getTable('productattachments')};
CREATE TABLE {$this->getTable('productattachments')} (
  `productattachments_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `filename` varchar(255) NOT NULL DEFAULT '',
  `file_icon` text,
  `file_type` varchar(255) DEFAULT '',
  `file_size` varchar(255) DEFAULT '',
  `download_link` text,
  `block_position` text,
  `link_url` text,
  `link_title` varchar(255) DEFAULT NULL,
  `embed_video` text,
  `video_title` varchar(255) DEFAULT NULL,
  `downloads` int(11) DEFAULT '0',
  `content` text NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '0',
  `cmspage_id` text,
  `created_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `customer_group_id` tinyint(4) DEFAULT '0',
  `limit_downloads` int(11) DEFAULT NULL,
  `cat_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`productattachments_id`)                        
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('productattachments_store')};
CREATE TABLE {$this->getTable('productattachments_store')} (
  `productattachments_id` int(11) unsigned NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`productattachments_id`,`store_id`),
  KEY `FK_PRODUCTATTACHMENTS_STORE_STORE` (`store_id`)                    
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Product Attachments Stores';

DROP TABLE IF EXISTS {$this->getTable('productattachments_products')};
CREATE TABLE {$this->getTable('productattachments_products')} (
  `product_related_id` int(11) NOT NULL AUTO_INCREMENT,
  `productattachments_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `block_name_product` varchar(250) DEFAULT NULL,
  UNIQUE KEY `product_related_id` (`product_related_id`) 
 ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
 
DROP TABLE IF EXISTS {$this->getTable('productattachments_category_store')};
CREATE TABLE {$this->getTable('productattachments_category_store')} (                      
 `category_id` int(10) unsigned NOT NULL,                             
 `store_id` smallint(5) unsigned NOT NULL,                            
 PRIMARY KEY  (`category_id`,`store_id`),                             
 KEY `productattachments_category_store_index_store_id` (`store_id`)  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
						 
");

$installer->setConfigData('productattachments/general/show_counter','1');
$installer->setConfigData('productattachments/general/login_before_download','1');

$installer->setConfigData('productattachments/productattachments/enabled','1');
$installer->setConfigData('productattachments/productattachments/product_attachment_heading','Downloads');
$installer->setConfigData('productattachments/productattachments/showcontent','0');

$installer->setConfigData('productattachments/cmspagesattachments/enabled','1');
$installer->setConfigData('productattachments/cmspagesattachments/cms_page_attachment_heading','Downloads');
$installer->setConfigData('productattachments/cmspagesattachments/showcontent','0');

$installer->setConfigData('productattachments/general/list_layout','page/1column');
$installer->endSetup(); 

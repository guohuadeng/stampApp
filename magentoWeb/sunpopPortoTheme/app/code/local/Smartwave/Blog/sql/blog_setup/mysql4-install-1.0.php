<?php

$installer = $this;

$installer->startSetup();
try {
    $installer->run("

CREATE TABLE IF NOT EXISTS {$this->getTable('blog/blog')} (
  `post_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `post_content` text NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `identifier` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `update_user` varchar(255) NOT NULL DEFAULT '',
  `meta_keywords` text NOT NULL,
  `meta_description` text NOT NULL,
  `comments` tinyint(11) NOT NULL,
  `tags` text NOT NULL,
  `short_content` text NOT NULL,
  `banner_content` text NOT NULL,
  PRIMARY KEY (`post_id`),
  UNIQUE KEY `identifier` (`identifier`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('blog/blog')} (`post_id`,`title`,`post_content`,`status`,`image`,`created_time`,`update_time`,`identifier`,`user`,`update_user`,`meta_keywords`,`meta_description`,`comments`,`tags`,`short_content`,`banner_content`) values 

(3,'Hello world!','Welcome to Porto Magento. This is your first post. Edit or delete it, then start blogging!',1,'wysiwyg/smartwave/blog/blog-1.jpg','2014-01-15 17:45:00','2014-11-12 14:00:26','hello_world','Dmitry','Super Admin','','',0,'Clothing,Blog,Photography,Women','Welcome to Porto Magento. This is your first post. Edit or delete it, then start blogging!',''),(4,'Post Format – Single Image','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras non placerat mi. Etiam non tellus sem. Aenean pretium convallis lorem, sit amet dapibus ante mollis a. Integer bibendum interdum sem, eget volutpat purus pulvinar in. Sed tristique augue vitae sagittis porta. Phasellus ullamcorper, dolor suscipit mattis viverra, sapien elit condimentum odio, ut imperdiet nisi risus sit amet ante. Sed sem lorem, laoreet et facilisis quis, tincidunt non lorem. Etiam tempus, dolor in sollicitudin faucibus, sem massa accumsan erat, sit amet laoreet eros metus eu mauris. Donec in suscipit nisl. Nullam eu sollicitudin tellus.\r\n\r\nNulla aliquet turpis eget sodales scelerisque. Ut accumsan rhoncus sapien a dignissim. Sed vel ipsum nunc. Aliquam erat volutpat. Donec et dignissim elit. Etiam condimentum, ante sed rutrum auctor, quam arcu consequat massa, at gravida enim velit id nisl. Nullam non felis odio. Praesent aliquam magna est, nec volutpat quam aliquet non. Cras ut lobortis massa, a fringilla dolor. Quisque ornare est at felis consectetur mollis. Aliquam vitae metus et enim posuere ornare.\r\n\r\nPraesent sapien erat, pellentesque quis sollicitudin eget, imperdiet bibendum magna. Aenean sit amet odio est. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Mauris quis est lobortis odio dignissim rutrum. Pellentesque blandit lacinia diam, a tincidunt felis tempus eget. Donec egestas metus non vehicula accumsan. Pellentesque sit amet tempor nibh. Mauris in risus lorem. Cras malesuada gravida massa eget viverra. Suspendisse vitae dolor erat. Morbi id rhoncus enim.\r\n\r\nIn hac habitasse platea dictumst. Aenean lorem diam, venenatis nec venenatis id, adipiscing ac massa. Nam vel dui eget justo dictum pretium a rhoncus ipsum. Donec venenatis erat tincidunt nunc suscipit, sit amet bibendum lacus posuere. Sed scelerisque, dolor a pharetra sodales, mi augue consequat sapien, et interdum tellus leo et nunc. Nunc imperdiet eu libero ut imperdiet. Nunc varius ornare tortor. In dignissim quam eget quam sodales egestas.\r\n\r\nNullam imperdiet velit feugiat, egestas risus nec, rhoncus felis. Suspendisse sagittis enim aliquet augue consequat facilisis. Nunc sit amet eleifend tellus. Etiam rhoncus turpis quam. Vestibulum eu lacus mattis, dignissim justo vel, fermentum nulla. Donec pharetra augue eget diam dictum, eu ullamcorper arcu feugiat. Proin ut ante vitae magna cursus porta. Aenean rutrum faucibus augue eu convallis. Phasellus condimentum elit id cursus sodales. Vivamus nec est consectetur, tincidunt augue at, tempor libero.',1,'wysiwyg/smartwave/blog/blog-2.jpg','2014-06-25 01:53:00','2014-11-12 14:00:16','post-format-single-image','Martin','Super Admin','','',0,'Fashion,Bags','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras non placerat mi…','<img src=\"{{media url=\"wysiwyg/porto/blog/01/01.jpg\"}}\" alt=\"\" />'),(6,'Post Format – Image Gallery','Euismod atras vulputate iltricies etri elit. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nulla nunc dui, tristique in semper vel, congue sed ligula. Nam dolor ligula, faucibus id sodales in, auctor fringilla libero. Pellentesque pellentesque tempor tellus eget hendrerit. Morbi id aliquam ligula. Aliquam id dui sem. Proin rhoncus consequat nisl, eu ornare mauris tincidunt vitae.\r\n\r\nNulla aliquet turpis eget sodales scelerisque. Ut accumsan rhoncus sapien a dignissim. Sed vel ipsum nunc. Aliquam erat volutpat. Donec et dignissim elit. Etiam condimentum, ante sed rutrum auctor, quam arcu consequat massa, at gravida enim velit id nisl. Nullam non felis odio. Praesent aliquam magna est, nec volutpat quam aliquet non. Cras ut lobortis massa, a fringilla dolor. Quisque ornare est at felis consectetur mollis. Aliquam vitae metus et enim posuere ornare.\r\n\r\nPraesent sapien erat, pellentesque quis sollicitudin eget, imperdiet bibendum magna. Aenean sit amet odio est. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Mauris quis est lobortis odio dignissim rutrum. Pellentesque blandit lacinia diam, a tincidunt felis tempus eget. Donec egestas metus non vehicula accumsan. Pellentesque sit amet tempor nibh. Mauris in risus lorem. Cras malesuada gravida massa eget viverra. Suspendisse vitae dolor erat. Morbi id rhoncus enim.\r\n\r\nIn hac habitasse platea dictumst. Aenean lorem diam, venenatis nec venenatis id, adipiscing ac massa. Nam vel dui eget justo dictum pretium a rhoncus ipsum. Donec venenatis erat tincidunt nunc suscipit, sit amet bibendum lacus posuere. Sed scelerisque, dolor a pharetra sodales, mi augue consequat sapien, et interdum tellus leo et nunc. Nunc imperdiet eu libero ut imperdiet. Nunc varius ornare tortor. In dignissim quam eget quam sodales egestas.\r\n\r\nNullam imperdiet velit feugiat, egestas risus nec, rhoncus felis. Suspendisse sagittis enim aliquet augue consequat facilisis. Nunc sit amet eleifend tellus. Etiam rhoncus turpis quam. Vestibulum eu lacus mattis, dignissim justo vel, fermentum nulla. Donec pharetra augue eget diam dictum, eu ullamcorper arcu feugiat. Proin ut ante vitae magna cursus porta. Aenean rutrum faucibus augue eu convallis. Phasellus condimentum elit id cursus sodales. Vivamus nec est consectetur, tincidunt augue at, tempor libero.',1,'wysiwyg/smartwave/blog/blog-3.jpg','2014-07-17 02:21:00','2014-11-12 14:00:05','post-format-image-gallery','Admin','Super Admin','','',0,'Fashion,Shoes','Euismod atras vulputate iltricies etri elit. Class aptent taciti sociosqu ad litora torquent…','<div id=\"owl-banner-1\" class=\"owl-carousel owl-theme\">\r\n \r\n  <div class=\"item\"><img src=\"{{media url=\"wysiwyg/porto/blog/02/01.jpg\"}}\" alt=\"\" /></div>\r\n  <div class=\"item\"><img src=\"{{media url=\"wysiwyg/porto/blog/02/02.jpg\"}}\" alt=\"\" /></div>\r\n  <div class=\"item\"><img src=\"{{media url=\"wysiwyg/porto/blog/02/03.jpg\"}}\" alt=\"\" /></div>\r\n \r\n</div>\r\n<script type=\"text/javascript\">\r\njQuery(function($) {\r\n \r\n  $(\"#owl-banner-1\").owlCarousel({\r\n \r\n      navigation : false,\r\n      slideSpeed : 300,\r\n      paginationSpeed : 400,\r\n      singleItem:true\r\n\r\n  });\r\n \r\n});\r\n\r\n</script>'),(7,'Post Format – Video','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras non placerat mi. Etiam non tellus sem. Aenean pretium convallis lorem, sit amet dapibus ante mollis a. Integer bibendum interdum sem, eget volutpat purus pulvinar in. Sed tristique augue vitae sagittis porta. Phasellus ullamcorper, dolor suscipit mattis viverra, sapien elit condimentum odio, ut imperdiet nisi risus sit amet ante. Sed sem lorem, laoreet et facilisis quis, tincidunt non lorem. Etiam tempus, dolor in sollicitudin faucibus, sem massa accumsan erat, sit amet laoreet eros metus eu mauris. Donec in suscipit nisl. Nullam eu sollicitudin tellus. Nulla aliquet turpis eget sodales scelerisque. Ut accumsan rhoncus sapien a dignissim. Sed vel ipsum nunc. Aliquam erat volutpat. Donec et dignissim elit. Etiam condimentum, ante sed rutrum auctor, quam arcu consequat massa, at gravida enim velit id nisl. Nullam non felis odio. Praesent aliquam magna est, nec volutpat quam aliquet non. Cras ut lobortis massa, a fringilla dolor. Quisque ornare est at felis consectetur mollis. Aliquam vitae metus et enim posuere ornare. Praesent sapien erat, pellentesque quis sollicitudin eget, imperdiet bibendum magna. Aenean sit amet odio est. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Mauris quis est lobortis odio dignissim rutrum. Pellentesque blandit lacinia diam, a tincidunt felis tempus eget. Donec egestas metus non vehicula accumsan. Pellentesque sit amet tempor nibh. Mauris in risus lorem. Cras malesuada gravida massa eget viverra. Suspendisse vitae dolor erat. Morbi id rhoncus enim. In hac habitasse platea dictumst. Aenean lorem diam, venenatis nec venenatis id, adipiscing ac massa. Nam vel dui eget justo dictum pretium a rhoncus ipsum. Donec venenatis erat tincidunt nunc suscipit, sit amet bibendum lacus posuere. Sed scelerisque, dolor a pharetra sodales, mi augue consequat sapien, et interdum tellus leo et nunc. Nunc imperdiet eu libero ut imperdiet. Nunc varius ornare tortor. In dignissim quam eget quam sodales egestas. Nullam imperdiet velit feugiat, egestas risus nec, rhoncus felis. Suspendisse sagittis enim aliquet augue consequat facilisis. Nunc sit amet eleifend tellus. Etiam rhoncus turpis quam. Vestibulum eu lacus mattis, dignissim justo vel, fermentum nulla. Donec pharetra augue eget diam dictum, eu ullamcorper arcu feugiat. Proin ut ante vitae magna cursus porta. Aenean rutrum faucibus augue eu convallis. Phasellus condimentum elit id cursus sodales. Vivamus nec est consectetur, tincidunt augue at, tempor libero.',1,'wysiwyg/smartwave/blog/blog-5.jpg','2014-08-06 02:23:00','2014-11-12 13:59:55','post-format-video','Roberto','Super Admin','','',0,'Fashio,Dresses','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras non placerat mi…','<iframe width=\"100%\" height=\"100%\" src=\"http://player.vimeo.com/video/28614006?wmode=transparent&amp;autoplay=0\" frameborder=\"0\" wmode=\"transparent\"></iframe>');

CREATE TABLE IF NOT EXISTS {$this->getTable('blog/cat')} (
  `cat_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `identifier` varchar(255) NOT NULL DEFAULT '',
  `sort_order` tinyint(6) NOT NULL,
  `meta_keywords` text NOT NULL,
  `meta_description` text NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('blog/cat')} (`cat_id`,`title`,`identifier`,`sort_order`,`meta_keywords`,`meta_description`) values (2,'All about clothing','all-about-clothing',0,'',''),(3,'Make-up & beauty','make-up-beauty',1,'',''),(4,'Accessories','accessories',2,'',''),(5,'Fashion trends','fashion-trends',3,'',''),(6,'Haircuts & hairstyles','haircuts-hairstyles',4,'','');

CREATE TABLE IF NOT EXISTS {$this->getTable('blog/cat_store')} (
  `cat_id` smallint(6) unsigned DEFAULT NULL,
  `store_id` smallint(6) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('blog/cat_store')} (`cat_id`,`store_id`) values (2,0),(3,0),(4,0),(5,0),(6,0);

CREATE TABLE IF NOT EXISTS {$this->getTable('blog/comment')} (
  `comment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` smallint(11) NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '0',
  `created_time` datetime DEFAULT NULL,
  `user` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('blog/comment')} (`comment_id`,`post_id`,`comment`,`status`,`created_time`,`user`,`email`) values (2,3,'Cgestas metus id nunc vestibulum dictum. Etiam dapibus nunc nec risus egestas vel bibendum eros vehicula. Suspendisse facilisisneque in augue feugiat tempor donec velit diam pharetra.',2,'2013-10-16 13:28:09','Elen Aliquam','elen@gmail.com'),(3,3,'Aliquam eu augue dolor, eget commodo lacus. Nullam diam lorem, pellentesque dignissim tempor id, interdum quis nisi. Duis tempor, mauris nec interdum molestie, elit erat porta dui, quis sagittis sapien ante nec nibh.',2,'2013-10-16 13:31:16','Martin Doe','martin@gmail.com'),(4,6,'test',2,'2014-09-12 03:48:31','Test','test1@gmail.com'),(5,6,'test',2,'2014-09-12 03:48:46','Martin','test@gmail.com');

CREATE TABLE IF NOT EXISTS {$this->getTable('blog/post_cat')} (
  `cat_id` smallint(6) unsigned DEFAULT NULL,
  `post_id` smallint(6) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('blog/post_cat')} (`cat_id`,`post_id`) values (5,5),(2,7),(4,7),(6,7),(3,6),(4,6),(5,6),(4,4),(5,4),(6,4),(2,3),(3,3),(4,3);

CREATE TABLE IF NOT EXISTS {$this->getTable('blog/store')} (
  `post_id` smallint(6) unsigned DEFAULT NULL,
  `store_id` smallint(6) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('blog/store')} (`post_id`,`store_id`) values (5,1),(7,1),(6,1),(4,1),(3,1);

CREATE TABLE IF NOT EXISTS  {$this->getTable('blog/tag')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) NOT NULL,
  `tag_count` int(11) NOT NULL DEFAULT '0',
  `store_id` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tag` (`tag`,`tag_count`,`store_id`)
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('blog/tag')} (`id`,`tag`,`tag_count`,`store_id`) values (12,'Accessories',0,1),(63,'Bags',1,1),(22,'Blog',1,1),(73,'Clother',1,1),(2,'Clothing',1,1),(103,'Dresses',1,1),(83,'Fashio',1,1),(53,'Fashion',3,1),(32,'Photography',1,1),(93,'Shoes',1,1),(43,'Women',1,1);
");
} catch (Exception $e) {
    
}

$installer->endSetup();
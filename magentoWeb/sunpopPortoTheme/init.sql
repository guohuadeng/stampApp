#1.9.2.2 要对block有权限
 insert  into `permission_block`(`block_name`,`is_allowed`) values
 ('filterproducts/featured_home_list',1),
 ('filterproducts/latest_home_list',1),
 ('filterproducts/newproduct_home_list',1),
 ('filterproducts/sale_home_list',1),
 ('filterproducts/mostviewed_home_list',1),
 ('filterproducts/bestsellers_home_list',1),
 ('blog/last',1),
 ('newsletter/subscribe',1),
 ('tag/popular',1),
 ('zeon_manufacturer/home',1);


<?php
$installer = $this;
$data = array(
array('CN','GD','广东', 'Guangdong'),
array('CN','BJ','北京', 'Beijing'),
array('CN','SH','上海', 'Shanghai'),
array('CN','TJ','天津', 'Tianjin'),
array('CN','JS','江苏', 'Jiangsu'),
array('CN','ZJ','浙江', 'Zhejiang'),
array('CN','SD','山东', 'Shandong'),
array('CN','SN','陕西', 'Shaanxi'),
array('CN','HE','河北', 'Hebei'),
array('CN','SX','山西', 'Shanxi'),
array('CN','NM','内蒙古', 'Inner Mongolia'),
array('CN','LN','辽宁', 'Liaoning'),
array('CN','JL','吉林', 'Jilin'),
array('CN','HL','黑龙江', 'Heilongjiang'),
array('CN','AH','安徽', 'Anhui'),
array('CN','FJ','福建', 'Fujian'),
array('CN','JX','江西', 'Jiangxi'),
array('CN','HA','河南', 'Henan'),
array('CN','HB','湖北', 'Hubei'),
array('CN','HN','湖南', 'Hunan'),
array('CN','GX','广西', 'Guangxi'),
array('CN','HI','海南', 'Hainan'),
array('CN','CQ','重庆', 'Chongqing'),
array('CN','SC','四川', 'Sichuan'),
array('CN','GZ','贵州', 'Guizhou'),
array('CN','YN','云南', 'Yunnan'),
array('CN','XZ','西藏', 'Tibet'),
array('CN','GS','甘肃', 'Gansu'),
array('CN','QH','青海', 'Qinghai'),
array('CN','NX','宁夏', 'Ningxia'),
array('CN','XJ','新疆', 'Xinjiang'),
array('CN','HK','香港', 'Hong Kong'),
array('CN','AM','澳门', 'Macau'),
array('CN','TW','台湾', 'Taiwan')
);
foreach ($data as $row) {
    $bind = array(
        'country_id'    => $row[0],
        'code'          => $row[1],
        'default_name'  => $row[3],
    );
    $installer->getConnection()->insert($installer->getTable('directory/country_region'), $bind);
    $regionId = $installer->getConnection()->lastInsertId($installer->getTable('directory/country_region'));

    $bind = array(
        'locale'    => 'en_US',
        'region_id' => $regionId,
        'name'      => $row[3]
    );
    $installer->getConnection()->insert($installer->getTable('directory/country_region_name'), $bind);
    $bind = array(
        'locale'    => 'zh_CN',
        'region_id' => $regionId,
        'name'      => $row[2]
    );
    $installer->getConnection()->insert($installer->getTable('directory/country_region_name'), $bind);
}


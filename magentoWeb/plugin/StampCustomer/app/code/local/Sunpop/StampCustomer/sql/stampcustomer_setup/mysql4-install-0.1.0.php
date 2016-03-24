<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE IF NOT EXISTS `stampcustomerlist` (
  `a_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号， 自增长id字段，主键',
  `a_state` varchar(30) NOT NULL COMMENT '注册区域',
  `a_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_estonian_ci NOT NULL COMMENT '姓名',
  `a_company` varchar(100) NOT NULL COMMENT '公司',
  `a_certtype` varchar(30) NOT NULL COMMENT '专业类型',
  `a_certspec` varchar(30) NOT NULL COMMENT '注册专业',
  `a_certsn` varchar(30) NOT NULL COMMENT '注册号',
  `a_stampsn` varchar(30) NOT NULL COMMENT '印章编号',
  `a_validatesn` varchar(5) NOT NULL COMMENT '执业印章校验码',
  `a_expdate` date NOT NULL COMMENT '有效期至',
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`a_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

INSERT INTO `stampcustomerlist` (`a_id`, `a_state`, `a_name`, `a_company`, `a_certtype`,`a_certspec`, `a_certsn`, `a_stampsn`, `a_expdate`) VALUES
(1, '北京', '李建飞', '北京紫博蓝', '一级建筑师', '机电', '99103145', '52221-212', '2015-12-31'),
(2, '北京', '王小二', '王小二网络科技有限公司', '一级建筑师', '机电', '1598224', '52221-154', '2016-01-28'),
(3, '苏州', '李四', '李四网络科技有限公司', '二级建筑师', '机电', '1598654', '52221-151', '2016-01-29'),
(4, '苏州', '王五', '王五网络科技有限公司', '二级建筑师', '机电', '1598894', '52221-141', '2016-01-29'),
(5, '苏州', '赵六', '赵六网络科技有限公司', '一级建筑师', '机电', '1598454', '52221-251', '2016-01-29');

SQLTEXT;

$installer->run($sql);
//demo
//Mage::getModel('core/url_rewrite')->setId(null);
//demo
$installer->endSetup();

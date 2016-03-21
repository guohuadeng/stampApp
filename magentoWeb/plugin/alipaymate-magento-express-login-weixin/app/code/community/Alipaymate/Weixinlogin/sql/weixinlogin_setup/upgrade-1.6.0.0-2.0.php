<?php
$installer = $this;
$installer->startSetup();

$installer->run("
    create table if not exists {$this->getTable('social_login_weixin')} (
      id            int not null auto_increment,
      customer_id   int null,
      inside_weixin tinyint      not null default 0,
      openid        varchar(64)  not null default '',
      nickname      varchar(64)  not null default '',
      sex           tinyint      not null default 1,
      city          varchar(64)  not null default '',
      province      varchar(64)  not null default '',
      country       char(2)      not null default '',
      avatar         varchar(255) not null default '',
      unionid       varchar(64)  not null default '',
      refresh_token varchar(255) not null default '',
      primary key (id), unique(unionid)
    ) engine=innodb default charset=utf8;
  alter table  {$this->getTable('social_login_weixin')} add COLUMN headimgurl VARCHAR(255) not null after country; //增加头像地址
");

$installer->run("
");

$installer->endSetup();
<?php
/**
 * This file is a part of MyWebSQL package
 * defining more that one server here will give user the option to select a server at login time
 * Notes:
 *   Server list is used only when authentication type is LOGIN
 *
 * @file:      config/servers.php
 * @author     Samnan ur Rehman
 * @copyright  (c) 2008-2014 Samnan ur Rehman
 * @web        http://mywebsql.net
 * @license    http://mywebsql.net/license
 */

	// add or remove list of servers below

	// please make sure you have the proper extensions enabled in your php config
	// to successfully connect to servers other than MySQL

	// valid drivers types are:
	// mysql4, mysql5, mysqli, sqlite, sqlite3, pgsql

	// for sqlite driver:
	//   'host' should be the folder name where sqlite databases are saved,
	//   'user' and 'password' options should be set for additional security

	// if true, a free form server name will be allowed to be entered instead of selecting
	// existing one from the list
	$ALLOW_CUSTOM_SERVERS = FALSE;

	// if the above is true, only the following server types will be allowed
	// sqlite is not recommended here, in order to avoid possible file system attacks
	$ALLOW_CUSTOM_SERVER_TYPES = "mysql,pgsql";

	$SERVER_LIST = array(
		'Localhost MySQL'           => array(
		                             'host'     => 'rdskvx3pv037n13rv6yn.mysql.rds.aliyuncs.com',
		                             'driver'   => extension_loaded('mysqli') ? 'mysqli' : 'mysql5'
		                         )
	);
?>

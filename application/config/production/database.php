<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Academic Free License version 3.0
 *
 * This source file is subject to the Academic Free License (AFL 3.0) that is
 * bundled with this package in the files license_afl.txt / license_afl.rst.
 * It is also available through the world wide web at this URL:
 * http://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2012, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/AFL-3.0 Academic Free License (AFL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['dsn']      The full DSN string describe a connection to the database.
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. e.g.: mysql.  Currently supported:
				 mysql, mysqli, pdo, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|	['failover'] array - A array with 0 or more data for connections if the main should fail.
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = 'default';
$active_record = TRUE;

$db['default']['dsn']      = '';
$db['default']['hostname'] = '195.100.100.85';
$db['default']['username'] = 'root';
$db['default']['password'] = 'rootpass';
$db['default']['database'] = 'mmpi';
$db['default']['dbdriver'] = 'mysql';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = FALSE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_unicode_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;
$db['default']['failover'] = array();

$db['spare_parts']['hostname'] = '195.100.100.85';
$db['spare_parts']['username'] = 'root';
$db['spare_parts']['password'] = 'rootpass';
$db['spare_parts']['database'] = 'spare_parts';
$db['spare_parts']['dbdriver'] = 'mysql';
$db['spare_parts']['dbprefix'] = '';
$db['spare_parts']['pconnect'] = FALSE;
$db['spare_parts']['db_debug'] = TRUE;
$db['spare_parts']['cache_on'] = FALSE;
$db['spare_parts']['cachedir'] = '';
$db['spare_parts']['char_set'] = 'utf8';
$db['spare_parts']['dbcollat'] = 'utf8_unicode_ci';
$db['spare_parts']['swap_pre'] = '';
$db['spare_parts']['autoinit'] = TRUE;
$db['spare_parts']['stricton'] = FALSE;
$db['spare_parts']['failover'] = array();

$db['hrdatabase']['hostname'] = '195.100.100.85';
$db['hrdatabase']['username'] = 'root';
$db['hrdatabase']['password'] = 'rootpass';
$db['hrdatabase']['database'] = 'HRDataBase';
$db['hrdatabase']['dbdriver'] = 'mysql';
$db['hrdatabase']['dbprefix'] = '';
$db['hrdatabase']['pconnect'] = FALSE;
$db['hrdatabase']['db_debug'] = TRUE;
$db['hrdatabase']['cache_on'] = FALSE;
$db['hrdatabase']['cachedir'] = '';
$db['hrdatabase']['char_set'] = 'utf8';
$db['hrdatabase']['dbcollat'] = 'utf8_unicode_ci';
$db['hrdatabase']['swap_pre'] = '';
$db['hrdatabase']['autoinit'] = TRUE;
$db['hrdatabase']['stricton'] = FALSE;
$db['hrdatabase']['failover'] = array();

$db['human_relations']['hostname'] = '195.100.100.85';
$db['human_relations']['username'] = 'root';
$db['human_relations']['password'] = 'rootpass';
$db['human_relations']['database'] = 'human_relations';
$db['human_relations']['dbdriver'] = 'mysql';
$db['human_relations']['dbprefix'] = '';
$db['human_relations']['pconnect'] = FALSE;
$db['human_relations']['db_debug'] = TRUE;
$db['human_relations']['cache_on'] = FALSE;
$db['human_relations']['cachedir'] = '';
$db['human_relations']['char_set'] = 'utf8';
$db['human_relations']['dbcollat'] = 'utf8_unicode_ci';
$db['human_relations']['swap_pre'] = '';
$db['human_relations']['autoinit'] = TRUE;
$db['human_relations']['stricton'] = FALSE;
$db['human_relations']['failover'] = array();

$db['dpr']['hostname'] = '195.100.100.85';
$db['dpr']['username'] = 'root';
$db['dpr']['password'] = 'rootpass';
$db['dpr']['database'] = 'dpr';
$db['dpr']['dbdriver'] = 'mysql';
$db['dpr']['dbprefix'] = '';
$db['dpr']['pconnect'] = FALSE;
$db['dpr']['db_debug'] = TRUE;
$db['dpr']['cache_on'] = FALSE;
$db['dpr']['cachedir'] = '';
$db['dpr']['char_set'] = 'utf8';
$db['dpr']['dbcollat'] = 'utf8_unicode_ci';
$db['dpr']['swap_pre'] = '';
$db['dpr']['autoinit'] = TRUE;
$db['dpr']['stricton'] = FALSE;
$db['dpr']['failover'] = array();

$db['warehouse']['hostname'] = '195.100.100.85';
$db['warehouse']['username'] = 'root';
$db['warehouse']['password'] = 'rootpass';
$db['warehouse']['database'] = 'warehouse';
$db['warehouse']['dbdriver'] = 'mysql';
$db['warehouse']['dbprefix'] = '';
$db['warehouse']['pconnect'] = FALSE;
$db['warehouse']['db_debug'] = TRUE;
$db['warehouse']['cache_on'] = FALSE;
$db['warehouse']['cachedir'] = '';
$db['warehouse']['char_set'] = 'utf8';
$db['warehouse']['dbcollat'] = 'utf8_unicode_ci';
$db['warehouse']['swap_pre'] = '';
$db['warehouse']['autoinit'] = TRUE;
$db['warehouse']['stricton'] = FALSE;
$db['warehouse']['failover'] = array();

$db['operations']['hostname'] = '195.100.100.85';
$db['operations']['username'] = 'root';
$db['operations']['password'] = 'rootpass';
$db['operations']['database'] = 'operations';
$db['operations']['dbdriver'] = 'mysql';
$db['operations']['dbprefix'] = '';
$db['operations']['pconnect'] = FALSE;
$db['operations']['db_debug'] = TRUE;
$db['operations']['cache_on'] = FALSE;
$db['operations']['cachedir'] = '';
$db['operations']['char_set'] = 'utf8';
$db['operations']['dbcollat'] = 'utf8_unicode_ci';
$db['operations']['swap_pre'] = '';
$db['operations']['autoinit'] = TRUE;
$db['operations']['stricton'] = FALSE;
$db['operations']['failover'] = array();

$db['information_technology']['hostname'] = '195.100.100.85';
$db['information_technology']['username'] = 'root';
$db['information_technology']['password'] = 'rootpass';
$db['information_technology']['database'] = 'operations';
$db['information_technology']['dbdriver'] = 'mysql';
$db['information_technology']['dbprefix'] = '';
$db['information_technology']['pconnect'] = FALSE;
$db['information_technology']['db_debug'] = TRUE;
$db['information_technology']['cache_on'] = FALSE;
$db['information_technology']['cachedir'] = '';
$db['information_technology']['char_set'] = 'utf8';
$db['information_technology']['dbcollat'] = 'utf8_unicode_ci';
$db['information_technology']['swap_pre'] = '';
$db['information_technology']['autoinit'] = TRUE;
$db['information_technology']['stricton'] = FALSE;
$db['information_technology']['failover'] = array();

/* End of file database.php */
/* Location: ./application/config/database.php */
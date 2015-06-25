<?php

if (!defined('APP_VERSION'))
{
	define('APP_VERSION', '1.7.0.210');
	define('APP_INDEX_ROOT_FILE', __FILE__);
	define('APP_INDEX_ROOT_PATH', str_replace('\\', '/', rtrim(dirname(__FILE__), '\\/').'/'));
}

if (file_exists(APP_INDEX_ROOT_PATH.'rainloop/v/'.APP_VERSION.'/index.php'))
{
	include APP_INDEX_ROOT_PATH.'rainloop/v/'.APP_VERSION.'/index.php';
}
else
{
	echo '[105] Missing version directory';
	exit(105);
}

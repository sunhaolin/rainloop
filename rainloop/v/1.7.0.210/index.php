<?php

	if (defined('APP_VERSION'))
	{
		if (!defined('APP_START'))
		{
			define('APP_START', microtime(true));

			@ini_set('register_globals', 0);
			@ini_set('zend.ze1_compatibility_mode', 0);
			@ini_set('magic_quotes_gpc', 0);
			@ini_set('magic_quotes_runtime', 0);

			define('APP_DEFAULT_DENY_ALL_HTACCESS', 'Deny from all
<IfModule mod_autoindex.c>
Options -Indexes
</ifModule>');

			define('APP_START_TIME', time());
			define('APP_REQUEST_RND', md5(APP_START.rand(10000, 99999).APP_START));
			define('APP_VERSION_ROOT_PATH', APP_INDEX_ROOT_PATH.'rainloop/v/'.APP_VERSION.'/');

			define('APP_USE_APC_CACHE', true);

			$sCustomDataPath = '';
			if (file_exists(APP_INDEX_ROOT_PATH.'include.php'))
			{
				include_once APP_INDEX_ROOT_PATH.'include.php';
				$sCustomDataPath = function_exists('__get_custom_data_full_path') ? rtrim(trim(__get_custom_data_full_path()), '\\/') : '';
			}

			define('APP_DATA_FOLDER_PATH', 0 === strlen($sCustomDataPath) ? APP_INDEX_ROOT_PATH.'data/' : $sCustomDataPath.'/');
			unset($sCustomDataPath);

			if (function_exists('date_default_timezone_set'))
			{
				date_default_timezone_set('UTC');
			}

			$sSite = strtolower(trim(empty($_SERVER['HTTP_HOST']) ? (empty($_SERVER['SERVER_NAME']) ? '' : $_SERVER['SERVER_NAME']) : $_SERVER['HTTP_HOST']));
			$sSite = 'www.' === substr($sSite, 0, 4) ? substr($sSite, 4) : $sSite;
			$sSite = preg_replace('/^.+@/', '', preg_replace('/:[\d]+$/', '', $sSite));
			$sSite = in_array($sSite, array('localhost', '127.0.0.1', '::1', '::1/128', '0:0:0:0:0:0:0:1')) ? 'localhost' : $sSite;

			define('APP_SITE', $sSite);
			define('APP_SITE_CLEAR', 0 < strlen(APP_SITE) ? trim(preg_replace('/[^a-zA-Z0-9_.\-]+/', '_', trim(strtolower(APP_SITE))), ' _') : '');

			define('APP_DEFAULT_PRIVATE_DATA_NAME', '_default_');

			$sPrivateDataFolderInternalName = @file_exists(APP_INDEX_ROOT_PATH.'MULTIPLY') ? APP_SITE : '';
			define('APP_PRIVATE_DATA_NAME', 0 === strlen($sPrivateDataFolderInternalName) ? APP_DEFAULT_PRIVATE_DATA_NAME : $sPrivateDataFolderInternalName);
			define('APP_MULTIPLY', 0 < strlen($sPrivateDataFolderInternalName) && APP_DEFAULT_PRIVATE_DATA_NAME !== APP_PRIVATE_DATA_NAME);

			define('APP_DUMMY', '********');
			define('APP_DEV_VERSION', '0.0.0');
			define('APP_API_PATH', 'http://api.rainloop.net/');
			define('APP_REP_PATH', 'http://repository.rainloop.net/v1/');
			define('APP_REPO_CORE_FILE', 'http://repository.rainloop.net/v2/core.{{channel}}.json');
			define('APP_WEB_PATH', 'rainloop/v/'.APP_VERSION.'/');
			define('APP_WEB_STATIC_PATH', APP_WEB_PATH.'static/');
			define('APP_DATA_FOLDER_PATH_UNIX', str_replace('\\', '/', APP_DATA_FOLDER_PATH));

			$sSalt = @file_get_contents(APP_DATA_FOLDER_PATH.'SALT.php');
			$sData = @file_get_contents(APP_DATA_FOLDER_PATH.'DATA.php');
			$sInstalled = @file_get_contents(APP_DATA_FOLDER_PATH.'INSTALLED');

			// installation checking data folder
			if (APP_VERSION !== $sInstalled)
			{
				include APP_VERSION_ROOT_PATH.'check.php';

				$sCheckName = 'delete_if_you_see_it_after_install';
				$sCheckFolder = APP_DATA_FOLDER_PATH.$sCheckName;
				$sCheckFilePath = APP_DATA_FOLDER_PATH.$sCheckName.'/'.$sCheckName.'.file';

				@unlink($sCheckFilePath);
				@rmdir($sCheckFolder);

				if (!@is_dir(APP_DATA_FOLDER_PATH))
				{
					@mkdir(APP_DATA_FOLDER_PATH, 0755);
				}
				else
				{
					@chmod(APP_DATA_FOLDER_PATH, 0755);
				}

				$sTest = '';
				switch (true)
				{
					case !@is_dir(APP_DATA_FOLDER_PATH):
						$sTest = 'is_dir';
						break;
					case !@is_readable(APP_DATA_FOLDER_PATH):
						$sTest = 'is_readable';
						break;
					case !@is_writable(APP_DATA_FOLDER_PATH):
						$sTest = 'is_writable';
						break;
					case !@mkdir($sCheckFolder, 0755):
						$sTest = 'mkdir';
						break;
					case false === @file_put_contents($sCheckFilePath, time()):
						$sTest = 'file_put_contents';
						break;
					case !@unlink($sCheckFilePath):
						$sTest = 'unlink';
						break;
					case !@rmdir($sCheckFolder):
						$sTest = 'rmdir';
						break;
				}

				if (!empty($sTest))
				{
					echo '[202] Data folder permisions error ['.$sTest.']';
					exit(202);
				}

				unset($sCheckName, $sCheckFilePath, $sCheckFolder, $sTest);
			}

			if (false === $sSalt || false === $sData)
			{
				if (false === $sSalt)
				{
					// random salt
					$sSalt = '<'.'?php //'
						.md5(microtime(true).rand(1000, 5000))
						.md5(microtime(true).rand(5000, 9999))
						.md5(microtime(true).rand(1000, 5000));

					@file_put_contents(APP_DATA_FOLDER_PATH.'SALT.php', $sSalt);
				}

				if (false === $sData)
				{
					// random data folder name
					$sData = '<'.'?php //'.md5(microtime(true).rand(1000, 9999));
					@file_put_contents(APP_DATA_FOLDER_PATH.'DATA.php', $sData);
				}
			}

			define('APP_SALT', md5($sSalt.APP_PRIVATE_DATA_NAME.$sSalt));
			define('APP_PRIVATE_DATA', APP_DATA_FOLDER_PATH.'_data_'.md5($sData).'/'.APP_PRIVATE_DATA_NAME.'/');
			define('APP_PLUGINS_PATH', APP_PRIVATE_DATA.'plugins/');

			if (APP_VERSION !== $sInstalled || (APP_MULTIPLY && !@is_dir(APP_PRIVATE_DATA)))
			{
				define('APP_INSTALLED_START', true);
				define('APP_INSTALLED_VERSION', $sInstalled);

				@file_put_contents(APP_DATA_FOLDER_PATH.'INSTALLED', APP_VERSION);
				@file_put_contents(APP_DATA_FOLDER_PATH.'index.html', 'Forbidden');
				@file_put_contents(APP_DATA_FOLDER_PATH.'index.php', 'Forbidden');
				@file_put_contents(APP_DATA_FOLDER_PATH.'.htaccess', APP_DEFAULT_DENY_ALL_HTACCESS);

				if (!@is_dir(APP_PRIVATE_DATA))
				{
					@mkdir(APP_PRIVATE_DATA, 0755, true);
				}

				foreach (array('logs', 'cache', 'configs', 'plugins', 'storage') as $sName)
				{
					if (!@is_dir(APP_PRIVATE_DATA.$sName))
					{
						@mkdir(APP_PRIVATE_DATA.$sName, 0755, true);
					}
				}

				if (!@file_exists(APP_PRIVATE_DATA.'domains/default.ini'))
				{
					if (!@is_dir(APP_PRIVATE_DATA.'domains'))
					{
						@mkdir(APP_PRIVATE_DATA.'domains', 0755);

						@copy(APP_VERSION_ROOT_PATH.'app/domains/disabled', APP_PRIVATE_DATA.'domains/disabled');
						@copy(APP_VERSION_ROOT_PATH.'app/domains/gmail.com.ini', APP_PRIVATE_DATA.'domains/gmail.com.ini');
						@copy(APP_VERSION_ROOT_PATH.'app/domains/yahoo.com.ini', APP_PRIVATE_DATA.'domains/yahoo.com.ini');
						@copy(APP_VERSION_ROOT_PATH.'app/domains/outlook.com.ini', APP_PRIVATE_DATA.'domains/outlook.com.ini');
						@copy(APP_VERSION_ROOT_PATH.'app/domains/qq.com.ini', APP_PRIVATE_DATA.'domains/qq.com.ini');
					}

					@copy(APP_VERSION_ROOT_PATH.'app/domains/default.ini.dist', APP_PRIVATE_DATA.'domains/default.ini');
				}
			}

			unset($sSite, $sSalt, $sData, $sInstalled, $sPrivateDataFolderInternalName);
		}

		include APP_VERSION_ROOT_PATH.'app/handle.php';

		if (defined('RAINLOOP_EXIT_ON_END') && RAINLOOP_EXIT_ON_END)
		{
			exit(0);
		}
	}

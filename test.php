<?php

define('APP_DATA_FOLDER_PATH', './data/');

$sCheckName = 'delete_if_you_see_it_after_install';
$sCheckFolder = APP_DATA_FOLDER_PATH.$sCheckName;
$sCheckFilePath = APP_DATA_FOLDER_PATH.$sCheckName.'/'.$sCheckName.'.file';

@unlink($sCheckFilePath);
@rmdir($sCheckFolder);

$aResult = array();
if (is_dir(APP_DATA_FOLDER_PATH))
{
    $aResult = array(
        '1) is_dir='.(@is_dir(APP_DATA_FOLDER_PATH) ? '1' : '0'),
        '2) is_readable='.(@is_readable(APP_DATA_FOLDER_PATH) ? '1' : '0'),
        '3) is_writable='.(@is_writable(APP_DATA_FOLDER_PATH) ? '1' : '0'),
        '4) mkdir='.(@mkdir($sCheckFolder, 0755) ? '1' : '0'),
        '5) file_put_contents='.(false !== @file_put_contents($sCheckFilePath, time()) ? '1' : '0'),
        '6) unlink='.(false !== @unlink($sCheckFilePath) ? '1' : '0'),
        '7) rmdir='.(false !== @rmdir($sCheckFolder) ? '1' : '0')
    );
}
else
{
    $aResult[] = 'is_dir=0';
}

echo implode("</br>\r\n", $aResult);
?>

<?php
/**
 * Script to build the installable zip file for the extension
 *
 * We create a tmp folder and copy the files into it and zip it up.
 */
$extensionName = 'fjrelated';
$adminPath = dirname(__FILE__);
$basePath = dirname(dirname(dirname(dirname(__FILE__))));
$sitePath = $basePath . '/components/com_' . $extensionName . '/';
$tempPath = $basePath . '/tmp/' . date('YmdHis');
define('JPATH_BASE',$basePath);
define('_JEXEC', 1);

require_once $basePath . '/includes/defines.php';
require_once $basePath . '/libraries/import.php';
require_once $basePath . '/libraries/joomla/filesystem/folder.php';
require_once $basePath . '/libraries/joomla/filesystem/file.php';


$result = JFolder::create($tempPath);
$result = JFolder::copy($adminPath, $tempPath . '/admin');
$result = JFolder::copy($sitePath, $tempPath . '/site');
$result = JFile::copy($tempPath . '/admin/' . $extensionName . '.xml', $tempPath . '/' . $extensionName . '.xml');
$result = JFile::delete($tempPath . '/admin/' . $extensionName . '.xml');
$result = JFile::delete($tempPath . '/admin/build.php');
$result = JFolder::delete($tempPath . '/admin/build');

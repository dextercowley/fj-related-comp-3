<?php
/**
 * Script to build the installable zip file for the extension
 *
 * We create a tmp folder and copy the files into it and zip it up.
 */
echo "Starting build of FJ Related Component ...\n";
$extensionName = 'fjrelated';
$adminPath = dirname(__FILE__);
$basePath = dirname(dirname(dirname(dirname(__FILE__))));
$sitePath = $basePath . '/components/com_' . $extensionName . '/';
$tempPath = $basePath . '/tmp/' . date('Y-m-d-His');
define('JPATH_BASE',$basePath);
define('_JEXEC', 1);

require_once $basePath . '/includes/defines.php';
require_once $basePath . '/libraries/import.php';
require_once $basePath . '/libraries/joomla/filesystem/folder.php';
require_once $basePath . '/libraries/joomla/filesystem/file.php';
require_once $basePath . '/libraries/import.legacy.php';
require_once $basePath . '/libraries/vendor/joomla/registry/src/Registry.php';


$result = JFolder::create($tempPath);
$result = JFolder::copy($adminPath, $tempPath . '/admin');
$result = JFolder::copy($sitePath, $tempPath . '/site');
$result = JFile::copy($tempPath . '/admin/' . $extensionName . '.xml', $tempPath . '/' . $extensionName . '.xml');
$result = JFile::delete($tempPath . '/admin/' . $extensionName . '.xml');
$result = JFile::delete($tempPath . '/admin/build.php');
$result = JFolder::delete($tempPath . '/admin/build');
echo "Build completed. Folder name is $tempPath.";

<?php
/**
 * @package     com_fjrelated
 *
 * @copyright   Copyright (C) 2013 - 2014 Mark Dexter. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JHtml::_('behavior.tabstate');

// No access check.

$controller	= JControllerLegacy::getInstance('FJRelated');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
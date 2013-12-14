<?php
/**
 * @package		com_fjrelated
 * @copyright	Copyright (C) 2008 - 2014 Mark Dexter. Portions Copyright Open Source Matters. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
$data = JFactory::getApplication()->getUserState('com_fjrelated.createtags.data', array());

?>
<form action="<?php echo JRoute::_('index.php?option=com_fjrelated'); ?>" method="post" name="adminForm" id="adminForm">

<h2><?php echo JText::_('COM_FJRELATED_CREATE_TAGS_HEADING');?></h2>

<ul>
	<li><?php echo JText::sprintf('COM_FJRELATED_CREATE_TAGS_UNIQUE_ARTICLES', $data['uniqueArticles']); ?></li>
	<li><?php echo JText::sprintf('COM_FJRELATED_CREATE_TAGS_KEYWORDS_READ', $data['keywordsRead']); ?></li>
	<li><?php echo JText::sprintf('COM_FJRELATED_CREATE_TAGS_TAGS_CREATED', $data['tagsCreated']); ?></li>
	<li><?php echo JText::sprintf('COM_FJRELATED_CREATE_TAGS_MAP_ROWS', $data['mapRows']); ?></li>
</ul>
<input type="hidden" name="task" value="" />
</form>

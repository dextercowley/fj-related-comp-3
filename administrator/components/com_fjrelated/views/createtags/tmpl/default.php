<?php
/**
 * @package		com_fjrelated
 * @copyright	Copyright (C) 2008 - 2014 Mark Dexter. Portions Copyright Open Source Matters. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
$data = $this->data;

?>
<form action="<?php echo JRoute::_('index.php?option=com_fjrelated'); ?>" method="post" name="adminForm" id="adminForm">

<h2><?php echo JText::_('COM_FJRELATED_SHOW_STATS_HEADING');?></h2>

<ul>
	<li><?php echo JText::sprintf('COM_FJRELATED_CREATE_TAGS_UNIQUE_ARTICLES', $data['uniqueArticles']); ?></li>
	<li><?php echo JText::sprintf('COM_FJRELATED_CREATE_TAGS_EXISTING_KEYWORDS', $data['keywordsRead']); ?></li>
	<li><?php echo JText::sprintf('COM_FJRELATED_CREATE_TAGS_EXISTING_TAGS', $data['existingTags']); ?></li>
	<li><?php echo JText::sprintf('COM_FJRELATED_CREATE_TAGS_EXISTING_MAP_ROWS', $data['existingMapRows']); ?></li>
</ul>
<input type="hidden" name="task" value="" />
</form>

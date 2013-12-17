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
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('createtags.createtags', 'createTags');

?>
<form action="<?php echo JRoute::_('index.php?option=com_fjrelated'); ?>" method="post" name="adminForm" id="adminForm">

<h2><?php echo JText::_('COM_FJRELATED_SHOW_STATS_HEADING');?></h2>

<ul>
	<li><?php echo JText::sprintf('COM_FJRELATED_CREATE_TAGS_UNIQUE_ARTICLES', $data['uniqueArticles']); ?></li>
	<li><?php echo JText::sprintf('COM_FJRELATED_CREATE_TAGS_EXISTING_KEYWORDS', $data['keywordsRead']); ?></li>
	<li><?php echo JText::sprintf('COM_FJRELATED_CREATE_TAGS_EXISTING_TAGS', $data['existingTags']); ?></li>
	<li><?php echo JText::sprintf('COM_FJRELATED_CREATE_TAGS_EXISTING_MAP_ROWS', $data['existingMapRows']); ?></li>
</ul>
<div id="createtags-progress-container"></div>
<div id="createtags-success-container"></div>
<input id="createtags-token" type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1" />

</form>

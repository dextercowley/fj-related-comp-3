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
	<li><?php echo JText::sprintf('COM_FJRELATED_CREATE_TAGS_UNIQUE_ARTICLES', $data['totalArticles']); ?></li>
	<li><?php echo JText::sprintf('COM_FJRELATED_CREATE_TAGS_EXISTING_KEYWORDS', $data['totalKeywords']); ?></li>
	<li><?php echo JText::sprintf('COM_FJRELATED_CREATE_TAGS_EXISTING_TAGS', $data['totalTags']); ?></li>
	<li><?php echo JText::sprintf('COM_FJRELATED_CREATE_TAGS_EXISTING_MAP_ROWS', $data['totalMapRows']); ?></li>
</ul>

<di	v id="createtags-progress-container" style="display: none">
<h2><?php echo JText::_('COM_FJRELATED_CREATETAGS_PROGRESS_HEADING');?>
	<?php echo JHtml::_('image', 'media/modal/spinner.gif', JText::_('COM_FJRELATED_CREATETAGS_SPINNER'),
		array('class' => 'spinner', 'id' => 'spinner'), true);?>
</h2>

<div class="createtags-progress-bar">
	<?php echo JHtml::_('image', 'media/bar.gif', JText::_('COM_FJRELATED_CREATETAGS_PROGRESS'),
		array('class' => 'progress', 'id' => 'progress'), true);?>
</div>
</div>
<div id="createtags-success-container" style="display: none">
<h2><?php echo JText::_('COM_FJRELATED_CREATETAGS_SUCCESS_HEADING');?></h2>
</div>


<input id="createtags-token" type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1" />

</form>


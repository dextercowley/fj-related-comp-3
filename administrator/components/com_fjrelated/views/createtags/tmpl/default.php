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
JHtml::_('jquery.ui', array('core'));

?>
<form action="<?php echo JRoute::_('index.php?option=com_fjrelated'); ?>" method="post" name="adminForm" id="adminForm">
<h2><?php echo JText::_('COM_FJRELATED_SHOW_STATS_HEADING');?></h2>

<ul>
	<li><?php echo JText::sprintf('COM_FJRELATED_CREATE_TAGS_UNIQUE_ARTICLES', $data['totalArticles']); ?></li>
	<li><?php echo JText::sprintf('COM_FJRELATED_CREATE_TAGS_EXISTING_KEYWORDS', $data['totalKeywords']); ?></li>
	<li><?php echo JText::sprintf('COM_FJRELATED_CREATE_TAGS_EXISTING_TAGS', $data['totalTags']); ?></li>
	<li><?php echo JText::sprintf('COM_FJRELATED_CREATE_TAGS_EXISTING_MAP_ROWS', $data['totalMapRows']); ?></li>
</ul>

<div id="createtags-progress-container" style="display: none">
<h2><?php echo JText::sprintf('COM_FJRELATED_CREATETAGS_PROGRESS_HEADING', $data['batchSize']);?>
	<?php echo JHtml::_('image', 'media/modal/spinner.gif', JText::_('COM_FJRELATED_CREATETAGS_SPINNER'),
		array('class' => 'spinner', 'id' => 'spinner'), true);?>
</h2>
</div>

<div id="createtags-progress-bar" style="width: 50%"></div>


<ul id="createtags-progress-values" style="display: none">
		<li class="extprogrow">
			<span class="extlabel"><?php echo JText::_('COM_FJRELATED_CREATE_TAGS_ARTICLES_PROCESSED'); ?></span>
			<span class="extvalue" id="articlesProcessed"></span>
		</li>
		<li class="extprogrow">
			<span class="extlabel"><?php echo JText::_('COM_FJRELATED_CREATE_TAGS_KEYWORDS_PROCESSED'); ?></span>
			<span class="extvalue" id="keywordsProcessed"></span>
		</li>
		<li class="extprogrow">
			<span class="extlabel"><?php echo JText::_('COM_FJRELATED_CREATE_TAGS_TAGS_CREATED'); ?></span>
			<span class="extvalue" id="tagsCreated"></span>
		</li>
		<li class="extprogrow">
			<span class="extlabel"><?php echo JText::_('COM_FJRELATED_CREATE_TAGS_TAG_MAPS_CREATED'); ?></span>
			<span class="extvalue" id="tagMapsCreated"></span>
		</li>
</ul>

<div id="createtags-success-container" style="display: none">
<h2><?php echo JText::_('COM_FJRELATED_CREATETAGS_SUCCESS_HEADING');?></h2>
</div>

<div id="createtags-error-container" style="display: none">
<h2><?php echo JText::_('COM_FJRELATED_CREATETAGS_ERROR_HEADING');?></h2>
<h3 id="error-message"></h3>
</div>

<input id="createtags-token" type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1" />

</form>


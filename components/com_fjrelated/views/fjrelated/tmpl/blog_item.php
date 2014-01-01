<?php
/**
 * @package		Site
 * @subpackage	com_fjrelated
 * @copyright	Copyright (C) 2009 - 2010 Mark Dexter. Portions Copyright(C) Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
// Create a shortcut for params.
$params = &$this->item->params;
$canEdit = $this->item->access_edit;
$showCount = $params->get('showMatchCount', 0);
$showMatchList = $params->get('showMatchList', 0);
$info = $this->item->params->get('info_block_position', 0);
JHtml::_('behavior.framework');
?>

<?php if ($this->item->state == 0) : ?>
	<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
<?php endif; ?>

<?php echo JLayoutHelper::render('joomla.content.blog_style_default_item_title', $this->item); ?>

<?php echo JLayoutHelper::render('joomla.content.icons', array('params' => $params, 'item' => $this->item, 'print' => false)); ?>


<?php // Todo Not that elegant would be nice to group the params ?>
<?php $useDefList = ($params->get('show_modify_date') || $params->get('show_publish_date') || $params->get('show_create_date')
	|| $params->get('show_hits') || $params->get('show_category') || $params->get('show_parent_category') || $params->get('show_author') ); ?>

<?php if ($useDefList && ($info == 0 ||  $info == 2)) : ?>
	<dl class="article-info  muted">
		<dt class="article-info-term">
		<?php echo JText::_('COM_FJRELATED_ARTICLE_INFO'); ?>
		</dt>

		<?php if ($params->get('show_author') && !empty($this->item->author )) : ?>
			<dd class="createdby">
				<?php $author = $this->item->author; ?>
				<?php $author = ($this->item->created_by_alias ? $this->item->created_by_alias : $author); ?>
				<?php if (!empty($this->item->contactid ) && $params->get('link_author') == true) : ?>
					<?php
					echo JText::sprintf('COM_FJRELATED_WRITTEN_BY',
						JHtml::_('link', JRoute::_('index.php?option=com_contact&view=contact&id='.$this->item->contactid), $author)
					); ?>
				<?php else :?>
					<?php echo JText::sprintf('COM_FJRELATED_WRITTEN_BY', $author); ?>
				<?php endif; ?>
			</dd>
		<?php endif; ?>

		<?php if ($params->get('show_parent_category') && !empty($this->item->parent_slug)) : ?>
			<dd class="parent-category-name">
				<?php $title = $this->escape($this->item->parent_title);
				$url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->parent_slug)).'">'.$title.'</a>';?>
				<?php if ($params->get('link_parent_category') && !empty($this->item->parent_slug)) : ?>
					<?php echo JText::sprintf('COM_FJRELATED_PARENT', $url); ?>
				<?php else : ?>
					<?php echo JText::sprintf('COM_FJRELATED_PARENT', $title); ?>
				<?php endif; ?>
			</dd>
		<?php endif; ?>

		<?php if ($params->get('show_category')) : ?>
			<dd class="category-name">
				<?php $title = $this->escape($this->item->category);
				$url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug)).'">'.$title.'</a>';?>
				<?php if ($params->get('link_category') && $this->item->catslug) : ?>
					<?php echo JText::sprintf('COM_FJRELATED_CATEGORY', $url); ?>
				<?php else : ?>
					<?php echo JText::sprintf('COM_FJRELATED_CATEGORY', $title); ?>
				<?php endif; ?>
			</dd>
		<?php endif; ?>

		<?php if ($params->get('show_publish_date')) : ?>
			<dd class="published">
				<span class="icon-calendar"></span> <?php echo JText::sprintf('COM_FJRELATED_PUBLISHED_DATE', JHtml::_('date', $this->item->publish_up, JText::_('DATE_FORMAT_LC3'))); ?>
			</dd>
		<?php endif; ?>

		<?php if ($info == 0) : ?>
			<?php if ($params->get('show_modify_date')) : ?>
				<dd class="modified">
				<span class="icon-calendar"></span>
				<?php echo JText::sprintf('COM_FJRELATED_LAST_UPDATED', JHtml::_('date', $this->item->modified, JText::_('DATE_FORMAT_LC3'))); ?>
				</dd>
			<?php endif; ?>

			<?php if ($params->get('show_create_date')) : ?>
				<dd class="create">
					<span class="icon-calendar"></span>
					<?php echo JText::sprintf('COM_FJRELATED_CREATED_DATE_ON', JHtml::_('date', $this->item->created, JText::_('DATE_FORMAT_LC3'))); ?>
				</dd>
			<?php endif; ?>

			<?php if ($params->get('show_hits')) : ?>
				<dd class="hits">
					<span class="icon-eye-open"></span>
					<?php echo JText::sprintf('COM_FJRELATED_ARTICLE_HITS', $this->item->hits); ?>
				</dd>
			<?php endif; ?>

		<?php endif; ?>

<?php endif; ?>

<?php if (isset($images->image_intro) && !empty($images->image_intro)) : ?>
	<?php $imgfloat = (empty($images->float_intro)) ? $params->get('float_intro') : $images->float_intro; ?>
	<div class="pull-<?php echo htmlspecialchars($imgfloat); ?> item-image"> <img
	<?php if ($images->image_intro_caption):
		echo 'class="caption"'.' title="' .htmlspecialchars($images->image_intro_caption) .'"';
	endif; ?>
	src="<?php echo htmlspecialchars($images->image_intro); ?>" alt="<?php echo htmlspecialchars($images->image_intro_alt); ?>"/> </div>
<?php endif; ?>

<?php if (!$params->get('show_intro')) : ?>
	<?php echo $this->item->event->afterDisplayTitle; ?>
<?php endif; ?>

<?php echo $this->item->event->beforeDisplayContent; ?>

<?php if ($showCount) : ?>
	<dd class="match_count">
	<?php echo JText::sprintf('COM_FJRELATED_ARTICLE_MATCH_COUNT', $this->item->match_count)?>
	</dd>
<?php endif; ?>
<?php if ($showMatchList) : ?>
	<dd class="match_list">
	<?php $matchingTagNames = array(); ?>
	<?php $tagArray = explode(',', $this->item->match_list); ?>
	<?php foreach ($tagArray as $tagId) : ?>
	<?php 	$matchingTagNames[] = $this->article->tagNames[$tagId];?>
	<?php endforeach; ?>
	<p>
	<?php echo JText::sprintf('COM_FJRELATED_ARTICLE_MATCH_LIST', implode(', ', $matchingTagNames)); ?>
	</p>
	</dd>
<?php endif; ?>
 </dl>


<?php echo $this->item->introtext; ?>

<?php if ($params->get('show_readmore') && $this->item->readmore) :
	if ($this->item->access_allowed) :
		$link = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid));
	else :
		$menu = JFactory::getApplication()->getMenu();
		$active = $menu->getActive();
		$itemId = $active->id;
		$link1 = JRoute::_('index.php?option=com_users&view=login&&Itemid=' . $itemId);
		$returnURL = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid));
		$link = new JURI($link1);
		$link->setVar('return', base64_encode($returnURL));
	endif; ?>

	<p class="readmore"><a class="btn" href="<?php echo $link; ?>"> <span class="icon-chevron-right"></span>
	<?php if (!$this->item->access_allowed) :
		echo JText::_('COM_FJRELATED_REGISTER_TO_READ_MORE');
	elseif ($readmore = $this->item->params->get('alternative_readmore', 0)) :
		echo $readmore;
	else :
		echo JText::sprintf('COM_FJRELATED_READ_MORE', $this->escape($this->item->title));
	endif; ?></a>
	</p>
<?php endif; ?>

<?php echo $this->item->event->afterDisplayContent; ?>
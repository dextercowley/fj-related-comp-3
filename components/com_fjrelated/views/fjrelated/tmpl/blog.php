<?php
/**
 * @package		com_fj_related
 * @copyright	Copyright (C) 2008-2013 Mark Dexter. Portions Copyright Open Source Matters. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

JHtml::_('behavior.caption');

// If the page class is defined, add to class as suffix.
// It will be a separate class if the user starts it with a space
$pageClass = $this->params->get('pageclass_sfx');
?>
<div class="blog<?php echo $pageClass;?>">
<?php if ( $this->params->get('show_page_heading')!=0) : ?>
	<div class="page-header">
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
<?php endif; ?>
<?php if ($this->params->get('showTitle', 1) || $this->params->get('page_subheading')) : ?>
	<h2>
		<?php echo $this->escape($this->params->get('page_subheading')); ?>
		<?php if ($this->params->get('showTitle')) : ?>
			<span class="subheading-fjrelated"><?php echo $this->article->title;?></span>
		<?php endif; ?>
	</h2>
<?php endif; ?>

<?php if ($this->params->get('show_tags', 1) && !empty($this->category->tags->itemTags)) : ?>
	<?php $this->category->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
	<?php echo $this->category->tagLayout->render($this->category->tags->itemTags); ?>
<?php endif; ?>

<?php if ($this->params->get('showText', 0)) : ?>
	<div class="fjrelated-article-text">
	<?php echo JHtml::_('content.prepare', $this->article->text); ?>
	<div class="clr"></div>
	</div>
<?php endif; ?>
<?php if (!$this->total && $this->params->get('noRelatedText')) :?>
	<div class="no_related"><p>
	<?php echo $this->escape($this->params->get('noRelatedText'));?>
	</p></div>
<?php endif; ?>
<?php $leadingcount=0 ; ?>
<?php if (!empty($this->lead_items)) : ?>
<div class="items-leading clearfix">
	<?php foreach ($this->lead_items as &$item) : ?>
		<div class="leading-<?php echo $leadingcount; ?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?>">
			<?php
				$this->item = &$item;
				echo $this->loadTemplate('item');
			?>
		</div>
		<?php
			$leadingcount++;
		?>
	<?php endforeach; ?>
</div><!-- end items-leading -->
<?php endif; ?>
<?php
	$introcount = (count($this->intro_items));
	$counter = 0;
?>
<?php if (!empty($this->intro_items)) : ?>
	<?php foreach ($this->intro_items as $key => &$item) : ?>

	<?php
		$key= ($key - $leadingcount) + 1;
		$rowcount=(((int)$key - 1) % (int) $this->columns) + 1;
		$row = $counter / $this->columns ;

		if ($rowcount == 1) : ?>

			<div class="items-row cols-<?php echo (int) $this->columns;?> <?php echo 'row-' . $row ; ?> row-fluid clearfix">
		<?php endif; ?>
			<div class="span<?php echo round((12 / $this->columns));?>">
				<div class="item column-<?php echo $rowcount;?><?php echo $item->state == 0 ? ' system-unpublished"' : null; ?>">
				<?php
					$this->item = &$item;
					echo $this->loadTemplate('item');
				?>
		</div><!-- end item -->
		<?php $counter++; ?>
		</div><!-- end span -->
		<?php if (($rowcount == $this->columns) or ($counter == $introcount)): ?>
	</div><!-- end row -->
			<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($this->link_items)) : ?>
	<div class="items-more">
	<?php echo $this->loadTemplate('links'); ?>
	</div>
<?php endif; ?>

<?php if ($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2 && $this->pagination->get('pages.total') > 1)) : ?>
	<div class="pagination">

		<?php if ($this->params->def('show_pagination_results', 1)) : ?>
			<p class="counter pull-right">
				<?php echo $this->pagination->getPagesCounter(); ?>
			</p>
		<?php  endif; ?>
				<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
<?php endif; ?>

</div>
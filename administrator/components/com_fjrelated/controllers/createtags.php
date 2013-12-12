<?php
/**
 * @package     com_fjrelated
 *
 * @copyright   Copyright (C) 2013 - 2014 Mark Dexter. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * FJ Related controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_fjrelated
 * @since       3.1
 */
class FJRelatedControllerCreatetags extends JControllerAdmin
{
	/**
	 * Creates tags from existing article keywords
	 *
	 * return  std object with statistics: keywordsRead, tagsCreated, uniqueArticles, mapRows
	 */
	public function createTags()
	{
		$model = $this->getModel('createtags');
		if (JFactory::getUser()->authorise('core.admin'))
		{
			$model->createTags();
			$this->setRedirect(JRoute::_('index.php?option=com_fjrelated&layout=createtags', false));
		}
		else
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

	}

	/**
	 * Shows statistics for current articles, keywords, and tags
	 *
	 */
	public function showStats()
	{
		$model = $this->getModel('createtags');
		$model->showstats();
		$this->setRedirect(JRoute::_('index.php?option=com_fjrelated&layout=showstats', false));

	}
}

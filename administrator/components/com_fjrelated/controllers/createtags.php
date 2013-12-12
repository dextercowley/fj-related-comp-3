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
class AdminControllerCreateTags extends JControllerLegacy
{
	/**
	 * Creates tags from existing article keywords
	 *
	 * return  std object with statistics: keywordsRead, tagsCreated, uniqueArticles, mapRows
	 */
	public function createTags()
	{
		$model = $this->getModel('createtags');
		$results = $model->createTags();
		$this->setRedirect(JRoute::_('index.php?option=com_fjrelated', false));
	}
}

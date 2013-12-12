<?php
/**
 * @package     com_fjrelated
 *
 * @copyright   Copyright (C) 2013 - 2014 Mark Dexter. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


/**
 *
 * @package     Joomla.Administrator
 * @subpackage  com_fjrelated
 * @since       3.1
 */
class FJRelatedModelCreatetags extends JModelAdmin
{
	protected $_context = 'com_fjrelated.createtags';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

	}

	public function createtags()
	{
		$result = new stdClass();
		$result->keywordsRead = 1;
		$result->tagsCreated = 2;
		$result->uniqueArticles = 3;
		$result->mapRows = 4;
		return $result;
	}
}

<?php
/**
 * @package     com_fjrelated
 *
 * @copyright   Copyright (C) 2013 - 2014 Mark Dexter. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Sysinfo View class for the Admin component
 *
 * @package     com_fjrelated
 * @since       3.1
 */
class FJRelatedViewFJRelated extends JViewLegacy
{

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->addToolbar();

		if ($this->_layout == 'createtags')
		{
			$this->state = $this->get('state');
		}

		parent::display($tpl);
	}


	/**
	 * Setup the Toolbar
	 *
	 * @since   3.1
	 */
	protected function addToolbar()
	{
		JToolbarHelper::title(JText::_('COM_FJRELATED_ARTICLES_COMPONENT_HELP'));
		if (JFactory::getUser()->authorise('core.admin') && $this->_layout == 'default')
		{
			JToolbarHelper::custom('createtags.createtags', 'tags', 'tags', 'COM_FJRELATED_CREATE_TAGS', false, false);
		}
	}
}

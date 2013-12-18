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
class FJRelatedViewCreatetags extends JViewLegacy
{

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->data = $this->get('Stats');
		$this->addToolbar();
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
		if (JFactory::getUser()->authorise('core.admin'))
		{
			$toolbar = JToolbar::getInstance('toolbar');
			$toolbar->appendButton('Link', 'tags', 'COM_FJRELATED_OVERVIEW', 'index.php?option=com_fjrelated');
			$toolbar->appendButton('Link', 'tags', 'COM_FJRELATED_SHOW_STATS', 'index.php?option=com_fjrelated&view=createtags');
			$html = '<div class="btn-wrapper" id=toolbar-tags"><button id = "createTags" class="btn btn-small" ><span class="icon-tags"></span> Create Tags</button></div>';
			$toolbar->appendButton('Custom', $html, 'createtags');
		}
	}
}

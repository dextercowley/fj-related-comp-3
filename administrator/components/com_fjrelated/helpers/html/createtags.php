<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2013 Mark Dexter. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * HTML utility class for creating bar charts using jQuery and jqplot JavaScript libraries.
 *
 * @package     com_trackerstats
 * @subpackage  HTML
 * @since       2.5
 */
abstract class JHtmlCreatetags
{
	/**
	 * @var    array  Array containing information for loaded files
	 * @since  3.0
	 */
	protected static $loaded = array();

	/**
	 * Method to load the Barchart script to display a bar chart using jQuery and jqPlot
	 *
	 * @param   string   $containerID             DOM id of the element where the chart will be rendered
	 * @param   string   $urlId                   DOM id of the element whose href attribute has the URL to the JSON data
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	public static function createtags()
	{
		// Only load once
		if (isset(self::$loaded[__METHOD__]))
		{
			return;
		}

		// Depends on jQuery UI
		$document = JFactory::getDocument();
		JHtml::_('bootstrap.framework');


		JFactory::getDocument()->addScriptDeclaration("
			(function ($){
				$(document).ready(function (){
    			$('#createTags').click(function() {
					alert('button clicked!');
				});
				});
			})(jQuery);
			"
		);

		// Set static array
		self::$loaded[__METHOD__] = true;
		return;
	}


}

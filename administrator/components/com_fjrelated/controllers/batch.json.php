<?php
/**
 * @package     com_trackerstats
 *
 * @copyright   Copyright (C) 2013 Mark Dexter. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * JSON controller for Trackerstats -- Returns data array for rendering total activity bar chart
 *
 * @since       2.5
 */
class FJRelatedControllerBatch extends JControllerLegacy
{
	/**
	 * Method to display bar chart data
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	public function processbatch()
	{
		$model = $this->getModel('Createtags', 'FJRelatedModel');
		$input = JFactory::getApplication()->input;
		$totalArticles = $input->getInt('totalarticles', 0);
		$totalKeywords = $input->getInt('totalkeywords', 0);
		$processedArticles = $input->getInt('processedarticles', 0);
		$processedKeywords = $input->getInt('processedkeyword', 0);
		if ($totalArticles && ($totalArticles > $processedArticles))
		{


			// Use the correct json mime-type
			header('Content-Type: application/json');

			// Send the response.
			echo json_encode($return);
			JFactory::getApplication()->close();
		}


	}
}

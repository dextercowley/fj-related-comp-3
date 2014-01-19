<?php
/**
 * @package     com_trackerstats
 *
 * @copyright   Copyright (C) 2013 - 2014 Mark Dexter. All rights reserved.
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
		// Check for a valid token. If invalid, send a 403 with the error message.
		JSession::checkToken('request') or $this->sendResponse(new Exception(JText::_('JINVALID_TOKEN'), 403));

		$model = $this->getModel('Createtags', 'FJRelatedModel');

		$data = $model->createTags();
		if ($data['articlesProcessed'] >= $data['totalArticles'])
		{
			$data['endingTagCount'] = (int) $model->getTagTotal();
			$data['endingTagMapCount'] = (int) $model->getTagMapTotal();
			$data['tagsCreated'] = $data['endingTagCount'] - $data['startingTagCount'];
			$data['tagMapsCreated'] = $data['endingTagMapCount'] - $data['startingTagMapCount'];
		}

		// Use the correct json mime-type
		header('Content-Type: application/json');

		// Send the response.
		echo json_encode($data);
		JFactory::getApplication()->close();

	}
}

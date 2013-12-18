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
class FJRelatedModelCreatetags extends JModelList
{
	protected $_context = 'com_fjrelated.createtags';

	public $tagsHelper = null;

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

	/**
	 * Creates new tags and tag-article mapping rows from existing article keywords.
	 */
	public function createtags()
	{
		$data = JFactory::getApplication()->getUserState('com_fjrelated.createtags.data');
		if (!isset($data['totalArticles']))
		{
			$rawArray = $this->getArticleCounts();
			$data['totalArticles'] = $rawArray[0];
			$data['totalKeywords'] = $rawArray[1];
			$data['batchSize'] = JComponentHelper::getParams('com_fjrelated')->get('batch_size', 100);
		}
		$data['startingTagCount'] = isset($data['startingTagCount']) ? $data['startingTagCount'] : $this->getTagTotal();
		$data['startingTagMapCount'] = isset($data['startingTagMapCount']) ? $data['startingTagMapCount'] : $this->getTagMapTotal();

		$data['articlesProcessed'] = isset($data['articlesProcessed']) ? $data['articlesProcessed'] : 0;
		$data['keywordsProcessed'] = isset($data['keywordsProcessed']) ? $data['keywordsProcessed'] : 0;
		$data['tagsCreated'] = isset($data['tagsCreated']) ? $data['tagsCreated'] : 0;

		$contentTable = JTable::getInstance('Content');
		$contentTypeTable = JTable::getInstance('Contenttype');
		$this->tagsHelper = new JHelperTags();
		$this->tagsHelper->typeAlias = 'com_content.article';
		$ucmId = $contentTypeTable-> getTypeId('com_content.article');

		// Loop through articles in batches (so we can do AJAX calls for each batch later)
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('id')
			->from('#__content')
			->where($db->quoteName('metakey') . ' > ""');
		$db->setQuery($query, $data['articlesProcessed'], $data['batchSize']);
		$rows = $db->loadObjectList();

		foreach ($rows as $row)
		{
			$data['articlesProcessed'] ++;

			$contentTable->load($row->id);
			$tagArray = array_map(array($this,'prepareTags'), explode(',', $contentTable->metakey));

			$data['keywordsProcessed'] += count($tagArray);
			$tagIds = $this->tagsHelper->createTagsFromField($tagArray);

			if (is_array($tagIds))
			{
				$contentTable->newTags = $tagIds;
				$taggedResults = $contentTable->store();
			}
		}

		JFactory::getApplication()->setUserState('com_fjrelated.createtags.data', $data);
		return $data;

	}

	/**
	 * Gets current counts or articles, keywords, tags, and tagged articles.
	 * Stores them in the session for the layout to use.
	 */
	public function getStats()
	{
		JFactory::getApplication()->setUserState('com_fjrelated.createtags.data', array());
		$articleCounts = $this->getArticleCounts();
		$tagTotal = $this->getTagTotal();
		$tagMapTotal = $this->getTagMapTotal();
		$result = array();
		$result['totalKeywords'] = $articleCounts[1];
		$result['totalArticles'] = $articleCounts[0];
		$result['totalTags'] = $tagTotal;
		$result['totalMapRows'] = $tagMapTotal;
		return $result;
	}

	/**
	 * Gets current count of articles and total keyword phrases in all articles
	 *
	 * @return  array  array(total article count, total keyword count)
	 */
	public function getArticleCounts()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('count(id)')
			->from('#__content')
			->where("metakey > ''");

		$query->select("sum(1 + LENGTH(metakey) - LENGTH(REPLACE(metakey, ',', ''))) AS " . $db->quoteName('keywords'));
		$db->setQuery($query);
		return $db->loadRow();
	}

	/** Gets current count of all Tags
	 *
	 * @return  integer  total number of existing tag rows.
	 */
	public function getTagTotal()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
		->select('count(id)')
		->from('#__tags')
		->where('parent_id != 0');
		$db->setQuery($query);
		return $db->loadResult();
	}

	/** Gets current count of all tag mappings for articles
	 *
	 * @return  integer  total number of existing tag mapping rows.
	 */
	public function getTagMapTotal()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
		->select('count(*)')
		->from('#__contentitem_tag_map')
		->where('type_alias = ' . $db->quote('com_content.article'));
		$db->setQuery($query);
		return $db->loadResult();
	}

	protected function prepareTags($tag)
	{
		return '#new#' . trim($tag);
	}

}

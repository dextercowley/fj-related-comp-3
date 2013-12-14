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
		$contentTable = JTable::getInstance('Content');
		$contentTypeTable = JTable::getInstance('Contenttype');
		$this->tagsHelper = new JHelperTags();
		$this->tagsHelper->typeAlias = 'com_content.article';
		$ucmId = $contentTypeTable-> getTypeId('com_content.article');
		// Loop through articles in batches (so we can do AJAX calls for each batch later)
		$batchSize = 5;
		$batchPointer = 0;
		$result['keywordsRead'] = 0;
		$result['uniqueArticles'] = 0;
		$result['mapRows'] = 0;
		$result['tagsCreated'] = 0;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('id')
			->from('#__content')
			->where($db->quoteName('metakey') . ' > ""');
		$db->setQuery($query, $batchPointer, $batchSize);
		$rows = $db->loadObjectList();
		while ($rows)
		{
			$batchPointer += $batchSize;
			foreach ($rows as $row)
			{
				$result['uniqueArticles']++;

				$contentTable->load($row->id);
				$tagArray = array_map(array($this, 'prepareTags'), explode(',', $contentTable->metakey));
				$result['keywordsRead'] += count($tagArray);
				$tagIds = $this->tagsHelper->createTagsFromField($tagArray);

				if (is_array($tagIds))
				{
					$taggedResults = $this->tagsHelper->tagItem($ucmId, $contentTable, $tagIds, true);
					if ($taggedResults)
					{
						$result['mapRows'] += count($tagIds);
					}
				}
			}
			$db->setQuery($query, $batchPointer, $batchSize);
			$rows = $db->loadObjectList();
		}

		JFactory::getApplication()->setUserState('com_fjrelated.createtags.data', $result);

	}

	/**
	 * Gets current counts or articles, keywords, tags, and tagged articles.
	 * Stores them in the session for the layout to use.
	 */
	public function showstats()
	{
		$articleCounts = $this->getArticleCounts();
		$tagTotal = $this->getTagTotal();
		$tagMapTotal = $this->getTagMapTotal();
		$result = array();
		$result['keywordsRead'] = $articleCounts[1];
		$result['uniqueArticles'] = $articleCounts[0];
		$result['existingTags'] = $tagTotal;
		$result['existingMapRows'] = $tagMapTotal;
		JFactory::getApplication()->setUserState('com_fjrelated.showstats.data', $result);

	}

	/**
	 * Gets current count of articles and total keyword phrases in all articles
	 *
	 * @return  array  array(total article count, total keyword count)
	 */
	protected function getArticleCounts()
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
	protected function getTagTotal()
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
	protected function getTagMapTotal()
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

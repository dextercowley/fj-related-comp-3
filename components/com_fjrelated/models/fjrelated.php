<?php
/**
 * @package		com_fjrelated_plus
 * @copyright	Copyright (C) 2008 Mark Dexter. Portions Copyright Open Source Matters. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.modellist');
jimport('joomla.html.parameter');
require_once (JPATH_SITE . '/components/com_content/helpers/route.php');
require_once (JPATH_SITE . '/components/com_content/helpers/query.php');


/**
 * Related Articles Component Model
 *
 * @package		Related Articles
 *
 */
class FJRelatedModelFJRelated extends JModelList
{
	/**
	 * id of "guide" Article
	 *
	 * @var int
	 */
	var $_id = null;

	/**
	 * Related items list
	 *
	 * @var array
	 */

	/**
	 * Total number of related items
	 *
	 * @var integer
	 */
	var $_total = null;
	var $_data = null;

	/**
	 * Article content for Guide Article
	 *
	 * @var object
	 */
	var $_article = null;

	/**
	 * Array of FJ Related menu item links
	 *
	 * @var object
	 */
	var $_fjrelatedLinks = null;

	/**
	 * Constructor
	 *
	 */
	function __construct()
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'alias', 'a.alias',
				'author', 'a.author',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'catid', 'a.catid', 'category_title',
				'state', 'a.state',
				'access', 'a.access', 'access_level',
				'created', 'a.created',
				'modified', 'a.modified',
				'publish_up', 'a.publish_up',
				'created_by', 'a.created_by',
				'ordering', 'a.ordering',
				'featured', 'a.featured',
				'language', 'a.language',
				'hits', 'a.hits',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
				'match_count', 'a.match_count',
			);
		}
		parent::__construct($config);

		$app = JFactory::getApplication();
		$params = $app->getParams();

		$id = $params->get('id', '0');
		$this->setId((int)$id);
		$this->_loadArticle();

		// here we initialize defaults for related items model

	}
	/**
	 * Method to set the related items id
	 *
	 * @access	public
	 * @param	int	Related items ID number
	 */
	function setId($id)
	{
		// Set related ID and wipe data
		$this->_id			= $id;
		$this->_data		= array();
		$this->_total		= null;
	}
	/**
	 * Method to get content item data for the current related items list
	 *
	 * @param	int	$state	The content state to pull from for the current
	 * related list
	 */
	function getList($state = 1)
	{
		// Load the Category data
		$this->_loadData($state);
		return $this->_data[$state];
	}


	function getArticle()
	{
		// Load the lead article data
		if ($this->_loadArticle())
		{
			$user	=  JFactory::getUser();
			$groups = $user->getAuthorisedViewLevels();

			// Is the category published?
			if (!$this->_article->cat_pub && $this->_article->catid) {
				JError::raiseError( 404, JText::_("Article category not published") );
			}

			// Do we have access to the category?
			if (!in_array($this->_article->cat_access, $groups)) {
				JError::raiseError( 403, JText::_("ALERTNOTAUTH") );
			}

			$this->_loadArticleParams();
			// Process the content preparation plugins
			$dispatcher	= JDispatcher::getInstance();
			JPluginHelper::importPlugin('content');
			$results = $dispatcher->trigger('onPrepareContent', array ( $this->_article,  $this->_article->parameters, 0));
			$this->_article->event = new stdClass();
			$results = $dispatcher->trigger('onAfterDisplayTitle', array ( $this->_article,  $this->_article->parameters,0));
			$this->_article->event->afterDisplayTitle = trim(implode("\n", $results));

			$results = $dispatcher->trigger('onBeforeDisplayContent', array ( $this->_article,  $this->_article->parameters, 0));
			$this->_article->event->beforeDisplayContent = trim(implode("\n", $results));

			$results = $dispatcher->trigger('onAfterDisplayContent', array ( $this->_article,  $this->_article->parameters, 0));
			$this->_article->event->afterDisplayContent = trim(implode("\n", $results));

		}
		else
		{
			$user = JFactory::getUser();
			$article = JTable::getInstance('content');
			$article->state			= 1;
			$article->cat_pub		= null;
			$article->cat_access	= null;
			$article->author		= null;
			$article->created_by	= $user->get('id');
			$article->parameters	= new JRegistry();
			$article->text			= '';
			$app = JFactory::getApplication();
			$params = $app->getParams();
			$article->tags = $params->get('tags');
			$this->_article	= $article;
		}

		return $this->_article;
	}
	/**
	 * Method to get content item data for the current keywords
	 *
	 * @param	int	$state	The content state to pull from for the current
	 * category
	 * @since 1.5
	 */
	function getData($state = 1)
	{
		// Load the Related Items data
		$this->_loadData($state);


		return $this->_data[$state];
	}
	/**
	 * Method to get the total number of content items for the frontpage
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal($state = 1)
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery($state);
			$this->_total[$state] = $this->_getListCount($query);
		}

		return $this->_total[$state];
	}

	/**
	 * Method to load content article data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	protected function _loadArticle()
	{
		$app = JFactory::getApplication();

		// Load the content if it doesn't already exist
		if (empty($this->_article))
		{
			// Get the page/component configuration
			$params = $app->getParams();

			// If voting is turned on, get voting data as well for the article
			$voting	= ContentHelperQuery::buildVotingQuery($params);

			// Get the WHERE clause
			$where	= $this->_buildContentWhere();

			$query = 'SELECT a.*, u.name AS author, cc.title AS category, ' .
					' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
					' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug,'.
					' cc.published AS cat_pub, ' .
					' cc.access AS cat_access ' . $voting['select'].
					' FROM #__content AS a' .
					' LEFT JOIN #__categories AS cc ON cc.id = a.catid' .
					' LEFT JOIN #__users AS u ON u.id = a.created_by' .
			$voting['join'] .
			$where;
			$this->_db->setQuery($query);
			$this->_article = $this->_db->loadObject();

			if ( ! $this->_article )
			{
				return false;
			}

			if ($this->_article->publish_down == $this->_db->getNullDate())
			{
				$this->_article->publish_down = JText::_('COM_FJRELATED_NEVER');
			}

			// These attributes need to be defined in order for the voting plugin to work
			if ( count($voting) && ! isset($this->_article->rating_count) ) {
				$this->_article->rating_count	= 0;
				$this->_article->rating			= 0;
			}

			// Load the tags from the mapping table
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('t.id, t.title')
				->from('#__tags AS t')
				->innerJoin('#__contentitem_tag_map AS m ON t.id = m.tag_id')
				->where("m.type_alias = 'com_content.article'")
				->where('content_item_id = ' . $this->_article->id)
				->order('t.title ASC');
			$db->setQuery($query);
			$this->_article->tags = $db->loadObjectList();

			return true;
		}
		return true;
	}

	/**
	 * Method to load content article parameters
	 *
	 * @access	private
	 * @return	void
	 * @since	1.5
	 */
	protected function _loadArticleParams()
	{
		$app = JFactory::getApplication();

		// Get the page/component configuration
		$params = clone($app->getParams('com_content'));

		// Merge article parameters into the page configuration
		$aparams = new JRegistry($this->_article->attribs);
		$params->merge($aparams);

		// Set the popup configuration option based on the request
		$pop = $app->input->get('pop', 0, 'int');
		$params->set('popup', $pop);

		// Are we showing introtext with the article
		if (!$params->get('show_intro') && !empty($this->_article->fulltext)) {
			$this->_article->text = $this->_article->fulltext;
		} else {
			$this->_article->text = $this->_article->introtext . chr(13).chr(13) . $this->_article->fulltext;
		}

		// Set the article object's parameters
		$this->_article->parameters =  $params;
	}

	/**
	 * Method to build the WHERE clause of the query to select a content article
	 *
	 * @access	private
	 * @return	string	WHERE clause
	 * @since	1.5
	 */
	protected function _buildContentWhere()
	{
		$app 		= JFactory::getApplication();

		$user		= JFactory::getUser();
		$aid		= (int) $user->get('aid', 0);
		$id 		= $app->input->getUInt('id', 0);

		$jnow		= JFactory::getDate();
		$now		= $jnow->toSql();
		$nullDate	= $this->_db->getNullDate();

		/*
		 * First thing we need to do is assert that the content article is the one
		* we are looking for and we have access to it.
		*/
		$where = ' WHERE a.id = '. (int) $this->_id;

		if (!$user->authorise('com_content', 'edit', 'content', 'all'))
		{
			$where .= ' AND ( ';
			$where .= ' ( a.created_by = ' . (int) $user->id . ' ) ';
			$where .= '   OR ';
			$where .= ' ( a.state = 1' .
				' AND ( a.publish_up = '.$this->_db->Quote($nullDate).' OR a.publish_up <= '.$this->_db->Quote($now).' )' .
				' AND ( a.publish_down = '.$this->_db->Quote($nullDate).' OR a.publish_down >= '.$this->_db->Quote($now).' )';
			$where .= '   ) ';
			$where .= '   OR ';
			$where .= ' ( a.state = -1 ) ';
			$where .= ' ) ';
		}

		return $where;
	}

	protected function _buildQuery($state = 1)
	{
		$app = JFactory::getApplication();
		$params = $app->getParams();
		$user = JFactory::getUser();
		$userGroups = implode(',', $user->getAuthorisedViewLevels());
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		if (!$user->authorise('core.edit.state', 'com_content') || !$user->authorise('core.edit', 'com_content'))
		{
			$nullDate = $db->quote($db->getNullDate());
			$date = JFactory::getDate();

			$nowDate = $db->quote($date->toSql());

			$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')')
			->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');
		}

		// If voting is turned on, get voting data as well for the content items
		$voting = $params->get('show_vote');

		if ($voting)
		{
			$query->select('ROUND(v.rating_sum / v.rating_count, 0) AS rating, v.rating_count as rating_count')
				->join('LEFT', '#__content_rating AS v ON a.id = v.content_id');
		}

		$tags = array();
		$tagNames = array();
		foreach ($this->_article->tags as $object)
		{
			$tags[] = $object->id;
			$tagNames[$object->id] = $object->title;
		}
		$this->_article->tagIds = $tags;
		$this->_article->tagNames = $tagNames;

		$thisAlias = trim($this->_article->created_by_alias);
		$thisAuthor = $this->_article->created_by;
		$matchAuthor = trim($params->get('matchAuthor', 0));
		$matchAuthorAlias = trim($params->get('matchAuthorAlias', 0));
		$noauth	= !$params->get('show_noauth');
		$anyOrAll = $params->get('anyOrAll', 'any');
		$publishedState = $params->get('fjArticleState', 1);
		if (is_array($publishedState))
		{
			if (count($publishedState == 1))
			{
				$publishedState = $publishedState[0];
			}
			else
			{
				JArrayHelper::toInteger($publishedState, 'int');
			}
		}

		if (($tags) || 	// do the query if there are tags
		($matchAuthor) || 	// or if the author match is on
		(($matchAuthorAlias) && ($thisAlias)))	// of if the alias match is on and an alias
		{
			$ordering 	= $params->get('ordering', 'alpha');
			$query	= $this->_buildContentOrderBy($ordering, $query);

			// If tags, we need to do the subquery for the tag table join
			if ($tags)
			{
				$tagQuery = $db->getQuery(true);
				$tagQuery->from('#__contentitem_tag_map')
					->select('content_item_id')
					->select('COUNT(*) AS total_tag_count')
					->select('SUM(CASE WHEN tag_id IN (' . implode(',', $tags) . ') THEN 1 ELSE 0 END) AS matching_tag_count')
					->select('GROUP_CONCAT(CASE WHEN tag_id IN (' . implode(',', $tags) . ') THEN tag_id ELSE null END) AS matching_tags')
					->where('type_alias = \'com_content.article\'')
					->group('content_item_id');
				$tagQueryString = '(' . (string) $tagQuery . ')';
				$query->leftJoin($tagQueryString . ' AS m ON m.content_item_id = a.id');
			}

			// get published state select
			if (is_array($publishedState)) {
				$query->where('a.state IN(' . implode(',', $publishedState) . ')');
			}
			else {
				$query->where('a.state = ' . $publishedState);
			}

			// get category selections
			// process either as comma-delimited list or as array (for backward compatibility)
			$catid = (is_array($params->get('catid'))) ? implode(',', $params->get('catid') ) : trim($params->get('catid'));
			$catCondition = '';

			if ($catid || $catid == '0')
			{
				$ids = str_replace('C', $this->_article->catid, strtoupper($catid));
				$ids = explode( ',', $ids);
				JArrayHelper::toInteger( $ids );
				$query->where('a.catid IN (' . implode(',', $ids ) . ')');
			}

			if ($matchAuthor)
			{
				$query->where('a.created_by = ' . $db->quote($thisAuthor));
			}

			if (($matchAuthorAlias) && ($thisAlias))
			{
				$query->where('UPPER(a.created_by_alias) = ' . $db->Quote(strtoupper($thisAlias)));
			}

			if ($noauth)
			{
				$query->where('a.access IN (' . $userGroups . ')')
					->where('cc.access IN (' . $userGroups . ')')
					->where('cc.published = 1');
			}

			if ($params->get('filter_type') && $params->get('filter_type') != 'none')
			{
				$query->where($this->_getFilterWhere($params->get('filter_type')));
			}

			$query->select('a.id, a.title, a.alias, a.introtext, a.fulltext, DATE_FORMAT(a.created, "%Y-%m-%d") AS created, a.state, a.catid, a.hits')
				->select('a.created, a.created_by, a.created_by_alias, a.modified, a.modified_by')
				->select('a.checked_out, a.checked_out_time, a.publish_up, a.publish_down, a.attribs, a.hits, a.images, a.urls, a.ordering, a.metakey, a.metadesc, a.access')
				->select('cc.access AS cat_access, cc.published AS cat_state')
				->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug')
				->select('CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug')
				->select('CHAR_LENGTH( a.`fulltext` ) AS readmore, u.name AS author')
				->select('a.metakey, "" as main_article_keywords')
				->select('cc.title as category, "article" as link_type, cc.alias as category_alias, parent.id as parent_id, parent.alias as parent_alias')
				->select('m.total_tag_count, m.matching_tag_count AS match_count, m.matching_tags as match_list');

			$query->from('#__content AS a');
			$query->leftJoin('#__content_frontpage AS f ON f.content_id = a.id');
			$query->leftJoin('#__categories AS cc ON cc.id = a.catid');
			$query->leftJoin('#__users AS u ON u.id = a.created_by');
			$query->leftJoin('#__categories AS parent on parent.id = cc.parent_id');

			$query->where('a.id != '.(int) $this->_id);

		}

		return $query;
	}

	protected function _reverseSort ($row1, $row2) // comp
	{
		if ($row1->match_count == $row2->match_count) // sort by title within match_count (if same # matches)
		{
			$result = strcmp ($row1->title, $row2->title);
		}
		else
		{
			$result = - strcmp ($row1->match_count, $row2->match_count); // otherwise, sort by reverse match_count
		}
		return $result;
	}

	protected function _normalSort ($row1, $row2) // comp
	{
		if ($row1->match_count == $row2->match_count) // sort by title within match_count (if same # matches)
		{
			$result = strcmp ($row1->title, $row2->title);
		}
		else
		{
			$result = strcmp ($row1->match_count, $row2->match_count); // otherwise, sort by reverse match_count
		}
		return $result;
	}
	/**
	 * Creates the ORDER BY clause of the query
	 * There are two cases: (1) The initial query and (2) sorting by a column header
	 * @param $orderby
	 * @return unknown_type
	 */
	protected function _buildContentOrderBy($defaultOrder, $query)
	{
		$app = JFactory::getApplication();
		$itemid = $app->input->getUInt('Itemid',0);
		$filter_order  = $app->getUserStateFromRequest('com_fjrelated.list.:' . $itemid . '.filter_order', 'filter_order', '', 'string');
		$filter_order_Dir = $app->getUserStateFromRequest('com_fjrelated.list.:' . $itemid . '.filter_order_Dir', 'filter_order_Dir', '', 'cmd');

		if ($filter_order && $filter_order_Dir)
		{
			$query->order($filter_order . ' ' . $filter_order_Dir);
		}

		if (($filter_order == 'author') && $filter_order_Dir)
		{
			$query->order('CASE WHEN CHAR_LENGTH(a.created_by_alias) THEN a.created_by_alias ELSE u.name END '. $filter_order_Dir);
		}

		if (!($filter_order && $filter_order_Dir)) {
			switch ($defaultOrder)
			{
				case 'alpha' :
					$query->order('a.title');
					break;

				case 'rdate' :
					$query->order('a.created desc, a.title');
					break;

				case 'date' :
					$query->order('a.created, a.title');
					break;

				case 'article_order' :
					$query->order('cc.title, a.ordering');
					break;

				default:
					$query->order('a.title');
			}
		}

		return $query;
	}

	/**
	 * Method to load content item data for related items if they don't
	 * exist.
	 *
	 * @access	private
	 * @return	boolean	True on success
	 */
	protected function _loadData($state = 1)
	{
		$app = JFactory::getApplication();
		if (empty($this->_article)) {
			return false;
		}
		// Lets load the siblings if they don't already exist
		if (empty($this->_content[$state]))
		{
			// Get the pagination request variables
			$limit		= $app->input->getUInt('limit', 0);
			$limitstart	= $app->input->getUInt('limitstart', 0);

			$query = $this->_buildQuery();
			$rows = $this->_getFJRelatedList($query, $limitstart, $limit);

			$this->_data[$state] = $rows;
		}
		return true;
	}

	protected function _getFJRelatedList($query, $limitstart, $limit)
	{
		// Override because we have to allow
		// for the possibility of processing after the query to count
		// the number of matches
		$related = array();
		if (!$query)
		{
			return $related;
		}
		$app = JFactory::getApplication();
		$db = $this->getDBO();
		$params = $app->getParams();
		$orderBy = $params->get('ordering', 'alpha');
		$showMatchList = $params->get('showMatchList', '0');
		$showCount = $params->get('showMatchCount', '0');
		$matchAuthor = $params->get('matchAuthor', 0);
		$matchAuthorAlias = $params->get('matchAuthorAlias', 0);
		$linkToFJRelated = $params->get('link_to_fjrelated', 0);
		$layoutType = $params->get('layout_type', 'default');
		$user = JFactory::getUser();
		$groups = $user->getAuthorisedViewLevels();

		// Variables to check to see if we are responding to a user column sort
		$itemid = $app->input->getUInt('Itemid');
		$filter_order  = $app->getUserStateFromRequest('com_fjrelated.list.:' . $itemid . '.filter_order', 'filter_order', '', 'string');
		$filter_order_Dir = $app->getUserStateFromRequest('com_fjrelated.list.:' . $itemid . '.filter_order_Dir', 'filter_order_Dir', '', 'cmd');


		$metakey = trim($this->_article->metakey);
		$tags = explode(',', $metakey);

		$db->setQuery($query, $limitstart, $limit);
		$temp = $db->loadObjectList();
		$related = array();
		if (count($temp)) // we have at least one related article
		{

			foreach ($temp as $row) // loop through each related article
			{
				// First, let's set the access parameters
				$row->access_allowed = in_array($row->access, $groups) && in_array($row->cat_access, $groups);
				$row->access_edit = false;

				// Compute the asset access permissions.
				// Technically guest could edit an article, but lets not check that to improve performance a little.
				if (!$user->get('guest')) {
					$asset	= 'com_content.article.'.$row->id;

					// Check general edit permission first.
					if ($user->authorise('core.edit', $asset)) {
						$row->access_edit = true;
					}
					// Now check if edit.own is available.
					elseif (!empty($user->id) && $user->authorise('core.edit.own', $asset)) {
						// Check for a valid user and that they are the owner.
						if ($user->id == $row->created_by) {
							$row->access_edit = true;
						}
					}
				}

				// get display date
				switch ($params->get('list_show_date'))
				{
					case 'modified':
						$row->displayDate = $row->modified;
						break;

					case 'published':
						$row->displayDate = ($row->publish_up == 0) ? $row->created : $row->publish_up;
						break;

					default:
					case 'created':
						$row->displayDate = $row->created;
						break;
				}

				// Next, let's process the link_to_fjrelated
				$row->fjrelated_link = '';
				if (($linkToFJRelated) && ($relatedLink = $this->getRelatedLayoutLink($row->id, $layoutType))) {
					$row->fjrelated_link = JRoute::_($relatedLink); // link to layout
				}

				// Next process any use_article values in the options (only if we are in a blog)
				$articleParams = new JRegistry($row->attribs);
				$row->params = clone $params;
				$row->params->set('access-view', true);
				if ($params->get('layout_type') == 'blog') {
					$menuParamsArray = $params->toArray();
					$articleArray = array();
					foreach ($menuParamsArray as $key => $value)
					{
						if ($value === 'use_article') {
							// if the article has a value, use it
							if ($articleParams->get($key) != '') {
								// get the value from the article
								$articleArray[$key] = $articleParams->get($key);
							}
						}
					}

					// merge the selected article params
					if (count($articleArray) > 0) {
						$articleParams = new JRegistry;
						$articleParams->loadArray($articleArray);
						$row->params->merge($articleParams);
					}
				}
				else {
					// For non-blog layouts, merge all of the article params
					$row->params->merge($articleParams);
				}

				// count the number of keyword matches (skip if not required based on parameter settings)
				if (($showMatchList) || ($showCount) || ($orderBy == 'bestmatch'))
				{
					$row->main_article_keywords = $this->_article->tagNames; // save main article keywords in each row
				}
			}
			if ($orderBy == 'bestmatch') {
				$ii = $limitstart;
			}
			else {
				$ii = 0;
			}
			if (($orderBy == 'bestmatch' && (!$filter_order)) || (($filter_order=='match_count') && ($filter_order_Dir=='desc'))) // need to sort now that we have the count of keyword matches
			{
				usort($temp, array('FJRelatedModelFJRelated', '_reverseSort'));
				$ii = $limitstart; // we have retrieved all related articles and need to select desired range manually
			}
			else if (($filter_order=='match_count') && ($filter_order_Dir=='asc'))
			{
				usort($temp, array('FJRelatedModelFJRelated', '_normalSort'));
				$ii = $limitstart; // we have retrieved all related articles and need to select desired range manually
			}
			if (!$limit) $limit = count($temp); // loop through all if All selected
			for ($i=$ii; $i < min($limit + $limitstart, count($temp)); $i++)
			{
				$row = $temp[$i];
				if (true || ($row->cat_state == 1 || $row->cat_state == '') && ($row->cat_access <= $user->get('aid', 0) || $row->cat_access == ''))
				{
					$row->route = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug));
					$related[] = $row;
				}
			}
		}
		unset ($temp);
		return $related;
	}

	/**
	 * Method to add filter to SQL Where clause
	 * @access private
	 * @return string value of where clause for filter
	 */
	protected function _getFilterWhere($filterType)
	{
		$filter = JFactory::getApplication()->input->getString('filter-search', '');

		$filterWhere = '';
		if ($filter)
		{
			// clean filter variable
			$filter = JString::strtoupper($filter);
			$hitsFilter = intval($filter);
			$filter	= $this->_db->quote( '%'.$this->_db->escape( $filter, true ).'%', false );

			switch ($filterType)
			{
				case 'title' :
					$filterWhere = ' AND UPPER( a.title ) LIKE '.$filter;
					break;

				case 'author' :
					$filterWhere = ' AND ((CASE WHEN a.created_by_alias > \' \' THEN a.created_by_alias ELSE u.name END) LIKE '.$filter.' ) ';
					break;

				case 'hits' :
					$filterWhere = ' AND a.hits >= '.$hitsFilter. ' ';
					break;
			}
		}
		return $filterWhere;
	}
	/**
	 * Check to see if this is an FJ Related Intro Article
	 * If it is, return the link to the FJ Related Layout
	 * @param $id Id of article to check
	 * @param $layout layout (blog or default)
	 * @return string - link if menu item exists
	 */
	function getRelatedLayoutLink($id, $layout) {
		if ($this->_fjrelatedLinks == null) { // do query only the first time
			$query_string = "'%option=com_fjrelated&view=fjrelated%'";
			$levels		= JFactory::getUser()->getAuthorisedViewLevels();
			$query = "SELECT link, CONCAT('index.php?Itemid=', id) as itemid, params " .
				 ' FROM #__menu WHERE link LIKE ' . $query_string .
				 ' AND access IN (' . implode(',', $levels) .')' .
				 ' AND published = 1';
			$db = $this->_db;
			$db->setQuery($query);
			$this->_fjrelatedLinks = $db->loadObjectList();
		}
		$matchingBlog = '';
		$matchingList = '';
		$result = '';
		foreach ($this->_fjrelatedLinks as $item) { // read through all fjrelated menu items
			$menuParams = new JRegistry($item->params);
			if (($menuParams->get('id') == $id) && $menuParams->get('layout_type') == 'blog') {
				$matchingBlog = $item->itemid; // save the matching blog
			}
			elseif ($menuParams->get('id') == $id) {
				$matchingList = $item->itemid; // save the matching list item
			}
		}
		// return the best match
		if (($matchingBlog) && ($matchingList)) { // if id has a list and blog, get same layout
			if ($layout == 'default') {
				$result = $matchingList;
			}
			else {
				$result = $matchingBlog;
			}
		}
		else { // if we only have one match, take the one with a value
			$result = ($matchingList) ? $matchingList : $matchingBlog;
		}
		return $result;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = 'ordering', $direction = 'ASC')
	{
		$app = JFactory::getApplication('site');

		// List state information
		$value = $app->input->getUInt('limit', $app->getCfg('list_limit', 0));
		$this->setState('list.limit', $value);

		$value = $app->input->getUInt('limitstart', 0);
		$this->setState('list.start', $value);

		$itemid = $app->input->getUInt('Itemid',0);
		$orderCol = $app->getUserStateFromRequest('com_fjrelated.list.:' . $itemid . '.filter_order', 'filter_order', '', 'string');
		if (!in_array($orderCol, $this->filter_fields)) {
			$orderCol = 'a.ordering';
		}
		$this->setState('list.ordering', $orderCol);

		$listOrder = $app->getUserStateFromRequest('com_fjrelated.list.:' . $itemid . '.filter_order_Dir',
			'filter_order_Dir', '', 'cmd');
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', ''))) {
			$listOrder = 'ASC';
		}
		$this->setState('list.direction', $listOrder);

		$params = $app->getParams();
		$this->setState('params', $params);
		$user = JFactory::getUser();

		if ((!$user->authorise('core.edit.state', 'com_content')) &&  (!$user->authorise('core.edit', 'com_content'))){
			// filter on published for those who do not have edit or edit.state rights.
			$this->setState('filter.published', 1);
		}

		$this->setState('filter.language',$app->getLanguageFilter());

		// process show_noauth parameter
		if (!$params->get('show_noauth')) {
			$this->setState('filter.access', true);
		}
		else {
			$this->setState('filter.access', false);
		}

		$this->setState('layout', $app->input->getCmd('layout'));

		// Optional filter text
		$this->setState('list.filter', $app->input->getString('filter-search'));

		$this->setState('list.start', $app->input->getUInt('limitstart', 0));

	}

}

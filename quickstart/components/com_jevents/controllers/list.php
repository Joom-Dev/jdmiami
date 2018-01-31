<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: range.php 3549 2012-04-20 09:26:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2017 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.application.component.controller');

class ListController extends JControllerLegacy   {

	function __construct($config = array())
	{
		parent::__construct($config);
		// TODO get this from config
		$this->registerDefaultTask( 'events' );

		// Load abstract "view" class
		$cfg = JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();
		JLoader::register('JEvents'.ucfirst($theme).'View',JEV_VIEWS."/$theme/abstract/abstract.php");
		if (!isset($this->_basePath)){
			$this->_basePath = $this->basePath;
			$this->_task = $this->task;
		}
	}

	function events() {

		list($year,$month,$day) = JEVHelper::getYMD();
		$jinput = JFactory::getApplication()->input;

		// Joomla unhelpfully switched limitstart to start when sef is enabled!  includes/router.php line 390
		$limitstart = intval( $jinput->getInt('start', $jinput->getInt('limitstart', 0)));
		
		$params = JComponentHelper::getParams( JEV_COM_COMPONENT );
		$limit = intval(JFactory::getApplication()->getUserStateFromRequest( 'jevlistlimit.list','limit', $params->get("com_calEventListRowsPpg",15)));

		$Itemid	= JEVHelper::getItemid();

		// get the view

		$document = JFactory::getDocument();
		$viewType	= $document->getType();
		
		$cfg = JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();

		if (!JFolder::exists($this->_basePath.'/'."views".'/'.$theme."/list")){
			$theme = "default";
		}
		$view = "list";
		$this->addViewPath($this->_basePath.'/'."views".'/'.$theme);
		$this->view = $this->getView($view,$viewType, $theme."View", 
			array( 'base_path'=>$this->_basePath, 
				"template_path"=>$this->_basePath.'/'."views".'/'.$theme.'/'.$view.'/'.'tmpl',
				"name"=>$theme.'/'.$view));

		// Set the layout
		$this->view->setLayout('events');

		$this->view->assign("Itemid",$Itemid);
		$this->view->assign("limitstart",$limitstart);
		$this->view->assign("limit",$limit);
		$this->view->assign("month",$month);
		$this->view->assign("day",$day);
		$this->view->assign("year",$year);
		$this->view->assign("task",$this->_task);
		
		// View caching logic -- simple... are we logged in?
		$cfg	 = JEVConfig::getInstance();
		$joomlaconf = JFactory::getConfig();
		$useCache = intval($cfg->get('com_cache', 0)) && $joomlaconf->get('caching', 1);
		$user = JFactory::getUser();
		if ($user->get('id') || !$useCache) {
			$this->view->display();
		} else {
			$cache = JFactory::getCache(JEV_COM_COMPONENT, 'view');
			$cache->get($this->view, 'display');
		}
	}
	
}


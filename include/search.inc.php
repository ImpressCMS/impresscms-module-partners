<?php
/**
 * partners version infomation
 *
 * This file holds the configuration information of this module
 *
 * @copyright	Copyright Madfish (Simon Wilkinson) 2012
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
 * @package		partners
 * @version		$Id$
 */

defined("ICMS_ROOT_PATH") or die("ICMS root path not defined");

function partners_search($queryarray, $andor, $limit, $offset, $userid)
{
	$partners_partner_handler = icms_getModuleHandler("partner", basename(dirname(dirname(__FILE__))), "partners");
	$partnerArray = $partners_partner_handler->getPartnersForSearch($queryarray, $andor, $limit, $offset, $userid);
	$ret = array();

	foreach ($partnerArray as $partner) 
	{
		$item['image'] = "images/partner.png";
		$item['link'] = $partner->getItemLink(TRUE);
		$item['title'] = $partner->getVar("title");
		$item['time'] = $partner->getVar("date", "e");
		$item['uid'] = $partner->getVar("creator");
		$ret[] = $item;
		unset($item);
	}

	return $ret;
}
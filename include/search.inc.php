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

function partners_search($queryarray, $andor, $limit, $offset = 0, $userid = 0)
{
	global $icmsConfigSearch;
	
	$partnersArray = $ret = array();
	$count = $number_to_process = $partners_left = '';
	
	$partners_partner_handler = icms_getModuleHandler("partner", basename(dirname(dirname(__FILE__))), "partners");
	$partnersArray = $partners_partner_handler->getPartnersForSearch($queryarray, $andor, $limit, $offset, $userid);
	
	// Count the number of records
	$count = count($partnersArray);
	
	// The number of records actually containing partner objects is <= $limit, the rest are padding
	$partners_left = ($count - ($offset + $icmsConfigSearch['search_per_page']));
	if ($partners_left < 0) {
		$number_to_process = $icmsConfigSearch['search_per_page'] + $partners_left; // $partners_left is negative
	} else {
		$number_to_process = $icmsConfigSearch['search_per_page'];
	}

	// Process the actual partners (not the padding)
	for ($i = 0; $i < $number_to_process; $i++)
	{
		if (is_object($partnersArray[$i])) { // Required to prevent crashing on profile view
			$item['image'] = "images/partner.png";
			$item['link'] = $partnersArray[$i]->getItemLink(TRUE);
			$item['title'] = $partnersArray[$i]->getVar("title");
			$item['time'] = $partnersArray[$i]->getVar("date", "e");
			$item['uid'] = $partnersArray[$i]->getVar("creator");
			$ret[] = $item;
			unset($item);
		}
	}

	if ($limit == 0) {
		// Restore the padding (required for 'hits' information and pagination controls). The offset
		// must be padded to the left of the results, and the remainder to the right or else the search
		// pagination controls will display the wrong results (which will all be empty).
		// Left padding = -($limit + $offset)
		$ret = array_pad($ret, -($offset + $number_to_process), 1);

		// Right padding = $count
		$ret = array_pad($ret, $count, 1);
	}
	
	return $ret;
}
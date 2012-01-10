<?php
/**
 * Classes responsible for managing partners partner objects
 *
 * @copyright	Copyright Madfish (Simon Wilkinson) 2012
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
 * @package		partners
 * @version		$Id$
 */

defined("ICMS_ROOT_PATH") or die("ICMS root path not defined");

class mod_partners_PartnerHandler extends icms_ipf_Handler
{
	/**
	 * Constructor
	 *
	 * @param icms_db_legacy_Database $db database connection object
	 */
	public function __construct(&$db)
	{
		parent::__construct($db, "partner", "partner_id", "title", "description", "partners");
		$this->enableUpload(array("image/gif", "image/jpeg", "image/pjpeg", "image/png"), 512000, 800, 600);
	}

	/**
	 * Switches an items status from online to offline or vice versa
	 *
	 * @return null
	 */
	public function changeStatus($id)
	{
		$visibility = '';
		$partnerObj = $this->get($id);
		if ($partnerObj->getVar('online_status', 'e') == true) {
			$partnerObj->setVar('online_status', 0);
			$visibility = 0;
		} else {
			$partnerObj->setVar('online_status', 1);
			$visibility = 1;
		}
		$this->insert($partnerObj, true);
		
		return $visibility;
	}

	/**
	 * Converts status value to human readable text
	 *
	 * @return array
	 */
	public function online_status_filter()
	{
		return array(0 => _AM_PARTNERS_PARTNER_OFFLINE, 1 => _AM_PARTNERS_PARTNER_ONLINE);
	}
}
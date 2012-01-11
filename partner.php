<?php
/**
* Partner page
*
* @copyright	Copyright Madfish (Simon Wilkinson) 2012
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @since		1.0
* @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
* @package		partners
* @version		$Id$
*/

include_once "header.php";

$xoopsOption["template_main"] = "partners_partner.html";
include_once ICMS_ROOT_PATH . "/header.php";

// Sanitise input parameters
$clean_partner_id = isset($_GET["partner_id"]) ? (int)$_GET["partner_id"] : 0 ;
$clean_start = isset($_GET['start']) ? intval($_GET['start']) : 0;

// Get the requested partner, or retrieve the index page
$partners_partner_handler = icms_getModuleHandler("partner", basename(dirname(__FILE__)), "partners");
$criteria = icms_buildCriteria(array('online_status' => '1'));
$partnerObj = $partners_partner_handler->get($clean_partner_id, TRUE, FALSE, $criteria);

////////// VIEW SINGLE PARTNER //////////

if($partnerObj && !$partnerObj->isNew()) 
{
	$icmsTpl->assign("partners_partner", $partnerObj->toArray());

	$icms_metagen = new icms_ipf_Metagen($partnerObj->getVar("title"), $partnerObj->getVar("meta_keywords", "n"), $partnerObj->getVar("meta_description", "n"));
	$icms_metagen->createMetaTags();
}

////////// VIEW PARTNER INDEX //////////
else
{
	$icmsTpl->assign("partners_title", _MD_PARTNERS_ALL_PARTNERS);
	
	if (icms::$module->config['index_display_mode'] == TRUE)
	{
		// View partners as list of summary descriptions
		$criteria = new icms_db_criteria_Compo();
		$criteria->add(new icms_db_criteria_Item('online_status', TRUE));

		// Count the number of online partners for the pagination control
		$partner_count = $partners_partner_handler->getCount($criteria);
		
		// Continue to retrieve partners for this page view
		$criteria->setStart($clean_start);
		$criteria->setLimit(icms::$module->config['number_of_partners_per_page']);
		$criteria->setSort('title');
		$criteria->setOrder('ASC');
		$partner_summaries = $partners_partner_handler->getObjects($criteria, TRUE, FALSE);
		$icmsTpl->assign('partner_summaries', $partner_summaries);
		
		$pagenav = new icms_view_PageNav($partner_count, icms::$module->config['number_of_partners_per_page'],
			$clean_start, 'start');
		
		$icmsTpl->assign('partners_navbar', $pagenav->renderNav());
	}
	else 
	{
		// View partners in compact table
		$objectTable = new icms_ipf_view_Table($partners_partner_handler, FALSE, array());
		$objectTable->isForUserSide();
		$objectTable->addColumn(new icms_ipf_view_Column("title"));
		$icmsTpl->assign("partners_partner_table", $objectTable->fetch());
	}
}

$icmsTpl->assign("partners_module_home", '<a href="' . ICMS_URL . "/modules/" . icms::$module->getVar("dirname") . '/">' . icms::$module->getVar("name") . "</a>");

include_once "footer.php";
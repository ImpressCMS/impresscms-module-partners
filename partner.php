<?php
/**
* Partner index page - displays details of a single partner, a list of partner summary descriptions or a compact table of partners
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

// Get relative path to document root for this ICMS install. This is required to call the logos correctly if ICMS is installed in a subdirectory
$directory_name = basename(dirname(__FILE__));
$script_name = getenv("SCRIPT_NAME");
$document_root = str_replace('modules/' . $directory_name . '/partner.php', '', $script_name);

// Assign common logo preferences to template
$icmsTpl->assign('display_partner_logos', icms::$module->config['display_partner_logos']);
$icmsTpl->assign('freestyle_logo_dimensions', icms::$module->config['freestyle_logo_dimensions']);
$icmsTpl->assign('logo_display_width', icms::$module->config['logo_index_display_width']);
if (icms::$module->config['partner_logo_position'] == 1) // Align right
{
	$icmsTpl->assign('partner_logo_position', 'partners_float_right');
}
else // Align left
{
	$icmsTpl->assign('partner_logo_position', 'partners_float_left');
}

////////// VIEW SINGLE PARTNER //////////

if($partnerObj && !$partnerObj->isNew())
{
	$partner = $partnerObj->toArray();
	$partner['logo'] = $document_root . 'uploads/' . $directory_name . '/partner/' . $partner['logo'];
	
	$icmsTpl->assign("partners_partner", $partner);

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
		
		// Adjust the partner logo paths to allow dynamic resizing as per the resized_image Smarty plugin
		foreach ($partner_summaries as &$partner)
		{
			$partner['logo'] = $document_root . 'uploads/' . $directory_name . '/partner/'
				. $partner['logo'];
		}
		$icmsTpl->assign('partner_summaries', $partner_summaries);
		
		// Pagination control
		$pagenav = new icms_view_PageNav($partner_count, icms::$module->config['number_of_partners_per_page'],
			$clean_start, 'start');
		$icmsTpl->assign('partners_navbar', $pagenav->renderNav());
	}
	else 
	{
		// View partners in compact table
		$criteria = new icms_db_criteria_Compo();
		$criteria->add(new icms_db_criteria_Item('online_status', TRUE));
		$objectTable = new icms_ipf_view_Table($partners_partner_handler, $criteria, array());
		$objectTable->isForUserSide();
		$objectTable->addColumn(new icms_ipf_view_Column("title"));
		$icmsTpl->assign("partners_partner_table", $objectTable->fetch());
	}
}

$icmsTpl->assign("partners_module_home", '<a href="' . ICMS_URL . "/modules/" . icms::$module->getVar("dirname") . '/">' . icms::$module->getVar("name") . "</a>");

include_once "footer.php";
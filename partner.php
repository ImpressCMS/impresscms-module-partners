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

$partners_partner_handler = icms_getModuleHandler("partner", basename(dirname(__FILE__)), "partners");

/** Use a naming convention that indicates the source of the content of the variable */
$clean_partner_id = isset($_GET["partner_id"]) ? (int)$_GET["partner_id"] : 0 ;
$partnerObj = $partners_partner_handler->get($clean_partner_id);

if($partnerObj && !$partnerObj->isNew()) 
{
	$icmsTpl->assign("partners_partner", $partnerObj->toArray());

	$icms_metagen = new icms_ipf_Metagen($partnerObj->getVar("title"), $partnerObj->getVar("meta_keywords", "n"), $partnerObj->getVar("meta_description", "n"));
	$icms_metagen->createMetaTags();
}
else
{
	$icmsTpl->assign("partners_title", _MD_PARTNERS_ALL_PARTNERS);

	$objectTable = new icms_ipf_view_Table($partners_partner_handler, FALSE, array());
	$objectTable->isForUserSide();
	$objectTable->addColumn(new icms_ipf_view_Column("title"));
	$icmsTpl->assign("partners_partner_table", $objectTable->fetch());
}

$icmsTpl->assign("partners_module_home", '<a href="' . ICMS_URL . "/modules/" . icms::$module->getVar("dirname") . '/">' . icms::$module->getVar("name") . "</a>");

include_once "footer.php";
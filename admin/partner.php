<?php
/**
 * Admin page to manage partners
 *
 * List, add, edit and delete partner objects
 *
 * @copyright	Copyright Madfish (Simon Wilkinson) 2012
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
 * @package		partners
 * @version		$Id$
 */

/**
 * Edit a Partner
 *
 * @param int $partner_id Partnerid to be edited
*/
function editpartner($partner_id = 0)
{
	global $partners_partner_handler, $icmsAdminTpl;

	$partnerObj = $partners_partner_handler->get($partner_id);

	if (!$partnerObj->isNew()){
		$partnerObj->loadTags();
		icms::$module->displayAdminMenu(0, _AM_PARTNERS_PARTNERS . " > " . _CO_ICMS_EDITING);
		$sform = $partnerObj->getForm(_AM_PARTNERS_PARTNER_EDIT, "addpartner");
		$sform->assign($icmsAdminTpl);
	} else {
		icms::$module->displayAdminMenu(0, _AM_PARTNERS_PARTNERS . " > " . _CO_ICMS_CREATINGNEW);
		$sform = $partnerObj->getForm(_AM_PARTNERS_PARTNER_CREATE, "addpartner");
		$sform->assign($icmsAdminTpl);

	}
	$icmsAdminTpl->display("db:partners_admin_partner.html");
}

include_once "admin_header.php";

$partners_partner_handler = icms_getModuleHandler("partner", basename(dirname(dirname(__FILE__))), "partners");
/** Use a naming convention that indicates the source of the content of the variable */
$clean_op = "";
/** Create a whitelist of valid values, be sure to use appropriate types for each value
 * Be sure to include a value for no parameter, if you have a default condition
 */
$valid_op = array ("mod", "changedField", "addpartner", "del", "view", "changeWeight", "visible", "");

if (isset($_GET["op"])) $clean_op = htmlentities($_GET["op"]);
if (isset($_POST["op"])) $clean_op = htmlentities($_POST["op"]);

$clean_partner_id = isset($_GET["partner_id"]) ? (int)$_GET["partner_id"] : 0 ;
$clean_tag_id = isset($_GET['tag_id']) ? (int)$_GET['tag_id'] : 0 ;

/**
 * in_array() is a native PHP function that will determine if the value of the
 * first argument is found in the array listed in the second argument. Strings
 * are case sensitive and the 3rd argument determines whether type matching is
 * required
*/
if (in_array($clean_op, $valid_op, TRUE))
{
	switch ($clean_op)
	{
		case "mod":
		case "changedField":
			icms_cp_header();
			editpartner($clean_partner_id);
			break;

		case "addpartner":
			$controller = new icms_ipf_Controller($partners_partner_handler);
			$controller->storeFromDefaultForm(_AM_PARTNERS_PARTNER_CREATED, _AM_PARTNERS_PARTNER_MODIFIED);
			break;

		case "del":
			$controller = new icms_ipf_Controller($partners_partner_handler);
			$controller->handleObjectDeletion();
			break;

		case "view":
			$partnerObj = $partners_partner_handler->get($clean_partner_id);
			icms_cp_header();
			$partnerObj->displaySingleObject();
			break;
		
		case "changeWeight":
			foreach ($_POST['mod_partners_Partner_objects'] as $key => $value)
			{
				$changed = false;
				$itemObj = $partners_partner_handler->get($value);

				if ($itemObj->getVar('weight', 'e') != $_POST['weight'][$key])
				{
					$itemObj->setVar('weight', intval($_POST['weight'][$key]));
					$changed = TRUE;
				}
				if ($changed)
				{
					$partners_partner_handler->insert($itemObj);
				}
			}
			$ret = '/modules/' . basename(dirname(dirname(__FILE__))) . '/admin/partner.php';
			redirect_header(ICMS_URL . $ret, 2, _AM_PARTNERS_PARTNER_WEIGHTS_UPDATED);
			break;
			
		case "visible":
			$visibility = $partners_partner_handler->changeStatus($clean_partner_id);
			$ret = '/modules/' . basename(dirname(dirname(__FILE__))) . '/admin/partner.php';
			if ($visibility == 0) {
				redirect_header(ICMS_URL . $ret, 2, _AM_PARTNERS_PARTNER_INVISIBLE);
			} else {
				redirect_header(ICMS_URL . $ret, 2, _AM_PARTNERS_PARTNER_VISIBLE);
			}
			break;

		default:
			icms_cp_header();
			icms::$module->displayAdminMenu(0, _AM_PARTNERS_PARTNERS);
			
			// Display a single project, if a project_id is set
			if ($clean_project_id)
			{
				$projectObj = $projects_project_handler->get($clean_project_id);
				$projectObj->displaySingleObject();
			}
			
			$criteria = '';
			
			// Display a tag select filter (if the Sprockets module is installed)
			if (icms_get_module_status("sprockets")) {

				$tag_select_box = '';
				$taglink_array = $tagged_partner_list = array();
				$sprockets_tag_handler = icms_getModuleHandler('tag', 'sprockets', 'sprockets');
				$sprockets_taglink_handler = icms_getModuleHandler('taglink', 'sprockets', 'sprockets');

				$tag_select_box = $sprockets_tag_handler->getTagSelectBox('partner.php', $clean_tag_id,
					_AM_PARTNERS_PARTNER_ALL_PARTNERS, FALSE, icms::$module->getVar('mid'));
				
				if (!empty($tag_select_box)) {
					echo '<h3>' . _AM_PARTNERS_PARTNER_FILTER_BY_TAG . '</h3>';
					echo $tag_select_box;
				}

				if ($clean_tag_id) {

					// get a list of project IDs belonging to this tag
					$criteria = new icms_db_criteria_Compo();
					$criteria->add(new icms_db_criteria_Item('tid', $clean_tag_id));
					$criteria->add(new icms_db_criteria_Item('mid', icms::$module->getVar('mid')));
					$criteria->add(new icms_db_criteria_Item('item', 'partner'));
					$taglink_array = $sprockets_taglink_handler->getObjects($criteria);
					foreach ($taglink_array as $taglink) {
						$tagged_partner_list[] = $taglink->getVar('iid');
					}
					$tagged_partner_list = "('" . implode("','", $tagged_partner_list) . "')";

					// Use the list to filter the persistable table
					$criteria = new icms_db_criteria_Compo();
					$criteria->add(new icms_db_criteria_Item('partner_id', $tagged_partner_list, 'IN'));
				}
			}
			
			if (empty($criteria)) {
				$criteria = null;
			}			
			
			$objectTable = new icms_ipf_view_Table($partners_partner_handler, $criteria);
			$objectTable->addColumn(new icms_ipf_view_Column("online_status"));
			$objectTable->addColumn(new icms_ipf_view_Column("title"));
			$objectTable->addColumn(new icms_ipf_view_Column('weight', 'center', TRUE, 'getWeightControl'));
			$objectTable->addIntroButton("addpartner", "partner.php?op=mod", _AM_PARTNERS_PARTNER_CREATE);
			$objectTable->addActionButton("changeWeight", FALSE, _SUBMIT);
			$objectTable->addFilter('online_status', 'online_status_filter');
			$icmsAdminTpl->assign("partners_partner_table", $objectTable->fetch());
			$icmsAdminTpl->display("db:partners_admin_partner.html");
			break;
	}
	icms_cp_footer();
}
/**
 * If you want to have a specific action taken because the user input was invalid,
 * place it at this point. Otherwise, a blank page will be displayed
 */
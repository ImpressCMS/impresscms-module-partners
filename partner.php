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
$clean_tag_id = isset($_GET["tag_id"]) ? (int)$_GET["tag_id"] : 0 ;
$clean_start = isset($_GET["start"]) ? intval($_GET["start"]) : 0;

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

/////////////////////////////////////////
////////// VIEW SINGLE PARTNER //////////
/////////////////////////////////////////

if($partnerObj && !$partnerObj->isNew())
{
	$partner = $partnerObj->toArray();
	if (!empty($partner['logo']))
	{
		$partner['logo'] = $document_root . 'uploads/' . $directory_name . '/partner/' . $partner['logo'];
	}
	
	$icmsTpl->assign("partners_partner", $partner);

	$icms_metagen = new icms_ipf_Metagen($partnerObj->getVar("title"), $partnerObj->getVar("meta_keywords", "n"), $partnerObj->getVar("meta_description", "n"));
	$icms_metagen->createMetaTags();
}

////////////////////////////////////////
////////// VIEW PARTNER INDEX //////////
////////////////////////////////////////
else
{
	$icmsTpl->assign("partners_title", _MD_PARTNERS_ALL_PARTNERS);
	
	if (icms::$module->config['index_display_mode'] == TRUE)
	{
		// View partners as list of summary descriptions
		
		$sprocketsModule = icms_getModuleInfo('sprockets');
		
		// Get a select box (if preferences allow, and only if Sprockets module installed)
		if ($sprocketsModule && icms::$module->config['show_tag_select_box'] == TRUE)
		{
			// Initialise
			$partners_tag_name = '';
			$tag_buffer = $tagList = array();
			$sprockets_tag_handler = icms_getModuleHandler('tag', $sprocketsModule->getVar('dirname'),
					'sprockets');
			$sprockets_taglink_handler = icms_getModuleHandler('taglink', 
					$sprocketsModule->getVar('dirname'), 'sprockets');

			// Prepare buffer to reduce queries
			$tag_buffer = $sprockets_tag_handler->getObjects(null, true, false);

			// Append the tag to the breadcrumb title
			if (array_key_exists($clean_tag_id, $tag_buffer) && ($clean_tag_id !== 0)) {
				$partners_tag_name = $tag_buffer[$clean_tag_id]['title'];
				$icmsTpl->assign('partners_tag_name', $partners_tag_name);
				$icmsTpl->assign('partners_category_path', $tag_buffer[$clean_tag_id]['title']);
			}

			// Load the tag navigation select box
			// $action, $selected = null, $zero_option_message = '---', $navigation_elements_only = true, $module_id = null, $item = null
			$tag_select_box = $sprockets_tag_handler->getTagSelectBox('partner.php', $clean_tag_id,
					_CO_PARTNERS_PARTNER_ALL_TAGS, TRUE, icms::$module->getVar('mid'));
			$icmsTpl->assign('partners_tag_select_box', $tag_select_box);
		}
		
		// Retrieve partners for a given tag
		if ($clean_tag_id && $sprocketsModule)
		{
			/**
			 * Retrieve a list of partners JOINED to taglinks by partner_id/tag_id/module_id/item
			 */

			$query = $rows = $partner_count = '';
			$linked_partner_ids = array();
			
			// First, count the number of articles for the pagination control
			$group_query = "SELECT count(*) FROM " . $partners_partner_handler->table . ", "
					. $sprockets_taglink_handler->table
					. " WHERE `partner_id` = `iid`"
					. " AND `online_status` = '1'"
					. " AND `tid` = '" . $clean_tag_id . "'"
					. " AND `mid` = '" . icms::$module->getVar('mid') . "'"
					. " AND `item` = 'partner'";
			
			$result = icms::$xoopsDB->query($group_query);

			if (!$result)
			{
				echo 'Error';
				exit;
				
			}
			else
			{
				while ($row = icms::$xoopsDB->fetchArray($result))
				{
					foreach ($row as $key => $count) 
					{
						$partner_count = $count;
					}
					
				}
			}

			// Secondly, get the partners
			$query = "SELECT * FROM " . $partners_partner_handler->table . ", "
					. $sprockets_taglink_handler->table
					. " WHERE `partner_id` = `iid`"
					. " AND `online_status` = '1'"
					. " AND `tid` = '" . $clean_tag_id . "'"
					. " AND `mid` = '" . icms::$module->getVar('mid') . "'"
					. " AND `item` = 'partner'"
					. " ORDER BY `date` DESC"
					. " LIMIT " . $clean_start . ", " . icms::$module->config['number_of_partners_per_page'];

			$result = icms::$xoopsDB->query($query);

			if (!$result)
			{
				echo 'Error';
				exit;
				
			}
			else
			{

				$rows = $partners_partner_handler->convertResultSet($result, TRUE, FALSE);
				foreach ($rows as $key => $row) 
				{
					$partner_summaries[$row['partner_id']] = $row;
				}
			}
		}
				
		// Retrieve partners without filtering by tag
		else
		{
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
		}
		
		// Adjust the partner logo paths to allow dynamic resizing as per the resized_image Smarty plugin
		foreach ($partner_summaries as &$partner)
		{
			if (!empty($partner['logo']))
			$partner['logo'] = $document_root . 'uploads/' . $directory_name . '/partner/'
				. $partner['logo'];
		}
		$icmsTpl->assign('partner_summaries', $partner_summaries);
		
		// Adjust pagination for tag, if present
		if (!empty($clean_tag_id))
		{
			$extra_arg = 'tag_id=' . $clean_tag_id;
		}
		else
		{
			$extra_arg = false;
		}
		
		// Pagination control
		$pagenav = new icms_view_PageNav($partner_count, icms::$module->config['number_of_partners_per_page'],
				$clean_start, 'start', $extra_arg);
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

$icmsTpl->assign("show_breadcrumb", icms::$module->config['show_breadcrumb']);
$icmsTpl->assign("partners_module_home", '<a href="' . ICMS_URL . "/modules/" . icms::$module->getVar("dirname") . '/">' . icms::$module->getVar("name") . "</a>");
include_once "footer.php";
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
$untagged_content = FALSE;
$clean_partner_id = isset($_GET["partner_id"]) ? (int)$_GET["partner_id"] : 0 ;
if (isset($_GET['tag_id'])) {
	if ($_GET['tag_id'] == 'untagged') {
		$untagged_content = TRUE;
	}
}
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

// Optional tagging support (only if Sprockets module installed)
$sprocketsModule = icms::handler("icms_module")->getByDirname("sprockets");

if (icms_get_module_status("sprockets"))
{
	// Prepare common Sprockets handlers and buffers
	icms_loadLanguageFile("sprockets", "common");
	$sprockets_tag_handler = icms_getModuleHandler('tag', $sprocketsModule->getVar('dirname'), 'sprockets');
	$sprockets_taglink_handler = icms_getModuleHandler('taglink', $sprocketsModule->getVar('dirname'), 'sprockets');
	$criteria = icms_buildCriteria(array('label_type' => '0'));
	$sprockets_tag_buffer = $sprockets_tag_handler->getList($criteria, TRUE, TRUE);
	
	// Append the tag to the breadcrumb title
	if (array_key_exists($clean_tag_id, $sprockets_tag_buffer) && ($clean_tag_id != 0))
	{
		$partners_tag_name = $sprockets_tag_buffer[$clean_tag_id];
		$icmsTpl->assign('partners_tag_name', $partners_tag_name);
		$icmsTpl->assign('partners_category_path', $sprockets_tag_buffer[$clean_tag_id]);
	} elseif ($untagged_content) {
		$icmsTpl->assign('partners_category_path', _CO_PARTNERS_UNTAGGED);
	}
}

// Assign common logo preferences to template
$icmsTpl->assign('display_partner_logos', icms::$module->config['display_partner_logos']);
$icmsTpl->assign('partners_freestyle_logo_dimensions', icms::$module->config['partners_freestyle_logo_dimensions']);
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
	// Update hit counter
	if (!icms_userIsAdmin(icms::$module->getVar('dirname')))
	{
		$partners_partner_handler->updateCounter($partnerObj);
	}
	
	// Convert partner to array for easy insertion to template
	$partner = $partnerObj->toArray();
	
	// Add SEO friendly string to URL
	if (!empty($partner['short_url']))
	{
		$partner['itemUrl'] .= "&amp;title=" . $partner['short_url'];
	}
	
	if (!empty($partner['logo']))
	{
		$partner['logo'] = $document_root . 'uploads/' . $directory_name . '/partner/' . $partner['logo'];
	}
	
	// Set some preferences
	$icmsTpl->assign('partners_logo_display_width', icms::$module->config['partners_logo_single_display_width']);
	if (icms::$module->config['partners_show_counter'] == FALSE)
	{
		unset($partner['counter']);
	}
	
	// Prepare tags for display
	if (icms_get_module_status("sprockets"))
	{
		$partner['tags'] = array();
		$partner_tag_array = $sprockets_taglink_handler->getTagsForObject($partnerObj->getVar('partner_id'), $partners_partner_handler, 0);
		foreach ($partner_tag_array as $key => $value)
		{
			$partner['tags'][$value] = '<a href="' . PARTNERS_URL . 'partner.php?tag_id=' . $value 
					. '">' . $sprockets_tag_buffer[$value] . '</a>';
		}
		$partner['tags'] = implode(', ', $partner['tags']);
	}
	
	// Set page title
	$icmsTpl->assign("partners_page_title", _CO_PARTNERS_PARTNERS);
	
	// Assign partner to template
	$icmsTpl->assign("partners_partner", $partner);

	$icms_metagen = new icms_ipf_Metagen($partnerObj->getVar("title"), $partnerObj->getVar("meta_keywords", "n"), $partnerObj->getVar("meta_description", "n"));
	$icms_metagen->createMetaTags();
}

////////////////////////////////////////
////////// VIEW PARTNER INDEX //////////
////////////////////////////////////////
else
{
	// Get a select box (if preferences allow, and only if Sprockets module installed)
	if (icms_get_module_status("sprockets") && icms::$module->config['partners_show_tag_select_box'] == TRUE)
	{
		// Initialise
		$partners_tag_name = '';
		$tagList = array();

		// Load the tag navigation select box
		// $action, $selected = null, $zero_option_message = '---', 
		// $navigation_elements_only = TRUE, $module_id = null, $item = null,
		if ($untagged_content) {
			$tag_select_box = $sprockets_tag_handler->getTagSelectBox('partner.php', 'untagged', 
				_CO_PARTNERS_PARTNER_ALL_TAGS, TRUE, icms::$module->getVar('mid'), 'partner', TRUE);
		} else {
			$tag_select_box = $sprockets_tag_handler->getTagSelectBox('partner.php', $clean_tag_id, 
				_CO_PARTNERS_PARTNER_ALL_TAGS, TRUE, icms::$module->getVar('mid'), 'partner', TRUE);
		}
		$icmsTpl->assign('partners_show_tag_select_box', $tag_select_box);
	}
	
	// Set the page title
	$icmsTpl->assign("partners_page_title", _CO_PARTNERS_PARTNERS);
	
	///////////////////////////////////////////////////////////////////
	////////// View partners as list of summary descriptions //////////
	///////////////////////////////////////////////////////////////////
	
	if (icms::$module->config['partners_index_display_mode'] == TRUE)
	{		
		// Retrieve partners for a given tag
		if (($clean_tag_id || $untagged_content) && icms_get_module_status("sprockets"))
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
					. " ORDER BY `weight` ASC"
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
		else
		{
			// Retrieve partners without filtering by tag
			$criteria = new icms_db_criteria_Compo();
			$criteria->add(new icms_db_criteria_Item('online_status', TRUE));
			
			// Count the number of online partners for the pagination control
			$partner_count = $partners_partner_handler->getCount($criteria);

			// Continue to retrieve partners for this page view
			$criteria->setStart($clean_start);
			$criteria->setLimit(icms::$module->config['number_of_partners_per_page']);
			$criteria->setSort('weight');
			$criteria->setOrder('ASC');
			$partner_summaries = $partners_partner_handler->getObjects($criteria, TRUE, FALSE);
		}
		
		// Prepare partners for display
		foreach ($partner_summaries as &$partner)
		{
			// Adjust the partner logo paths to allow dynamic resizing as per the resized_image Smarty plugin.
			if (!empty($partner['logo']))
			$partner['logo'] = $document_root . 'uploads/' . $directory_name . '/partner/'
				. $partner['logo'];
			
			// Add SEO friendly string to URL
			if (!empty($partner['short_url']))
			{
				$partner['itemUrl'] .= "&amp;title=" . $partner['short_url'];
			}
		}
		
		
		// Set logo display width
		$icmsTpl->assign('partners_logo_display_width', icms::$module->config['partners_logo_index_display_width']);
		
		// Assign partners to template
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
		//////////////////////////////////////////////////////////////////////////////
		////////// View partners in compact table, optionally filter by tag //////////
		//////////////////////////////////////////////////////////////////////////////
		
		$tagged_partner_list = '';
		
		if (($clean_tag_id || $untagged_content) && icms_get_module_status("sprockets")) 
		{
			// Get a list of partner IDs belonging to this tag
			$criteria = new icms_db_criteria_Compo();
			$criteria->add(new icms_db_criteria_Item('tid', $clean_tag_id));
			$criteria->add(new icms_db_criteria_Item('mid', icms::$module->getVar('mid')));
			$criteria->add(new icms_db_criteria_Item('item', 'partner'));
			$taglink_array = $sprockets_taglink_handler->getObjects($criteria);
			foreach ($taglink_array as $taglink) {
				$tagged_partner_list[] = $taglink->getVar('iid');
			}
			$tagged_partner_list = "('" . implode("','", $tagged_partner_list) . "')";
			unset($criteria);			
		}
		$criteria = new icms_db_criteria_Compo();
		if (!empty($tagged_partner_list))
		{
			$criteria->add(new icms_db_criteria_Item('partner_id', $tagged_partner_list, 'IN'));
		}

		$criteria->setSort('weight');
		$criteria->setOrder('ASC');
		
		$objectTable = new icms_ipf_view_Table($partners_partner_handler, $criteria, array());
		$objectTable->isForUserSide();
		$objectTable->addQuickSearch('title');
		$objectTable->addColumn(new icms_ipf_view_Column("title", _GLOBAL_LEFT, FALSE, addSEOStringToItemUrl));
		$icmsTpl->assign("partners_partner_table", $objectTable->fetch());
	}
}

$icmsTpl->assign("partners_show_breadcrumb", icms::$module->config['partners_show_breadcrumb']);
$icmsTpl->assign("partners_module_home", '<a href="' . ICMS_URL . "/modules/" . icms::$module->getVar("dirname") . '/">' . icms::$module->getVar("name") . "</a>");
include_once "footer.php";
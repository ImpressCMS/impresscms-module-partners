<?php
/**
 * Random partners block file
 *
 * This file holds the functions needed for the random partners block
 *
 * @copyright	http://smartfactory.ca The SmartFactory
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		marcan aka Marc-André Lanciault <marcan@smartfactory.ca>
 * Modified for use in the Podcast module by Madfish
 * @version		$Id$
 */

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

/**
 * Prepare random partners block for display
 *
 * @param array $options
 * @return array 
 */
function show_random_partners($options)
{
	$partnersModule = icms_getModuleInfo('partners');
	$sprocketsModule = icms_getModuleInfo('sprockets');
	include_once(ICMS_ROOT_PATH . '/modules/' . $partnersModule->getVar('dirname') . '/include/common.php');
	$partners_partner_handler = icms_getModuleHandler('partner', $partnersModule->getVar('dirname'), 'partners');
	// Check for dynamic tag filtering, including by untagged content
	$untagged_content = FALSE;
	if ($options[4] == 1 && isset($_GET['tag_id'])) {
		$untagged_content = ($_GET['tag_id'] == 'untagged') ? TRUE : FALSE;
		$options[3] = (int)trim($_GET['tag_id']);
	}
	if (icms_get_module_status("sprockets"))
	{
		$sprockets_taglink_handler = icms_getModuleHandler('taglink', $sprocketsModule->getVar('dirname'), 'sprockets');
	}
	
	$criteria = new icms_db_criteria_Compo();
	$partner_list = $partners = array();

	// Get a list of partners filtered by tag
	if (icms_get_module_status("sprockets") && ($options[3] != 0 || $untagged_content))
	{
		$query = "SELECT `partner_id` FROM " . $partners_partner_handler->table . ", "
			. $sprockets_taglink_handler->table
			. " WHERE `partner_id` = `iid`"
			. " AND `tid` = '" . $options[3] . "'"
			. " AND `mid` = '" . $partnersModule->getVar('mid') . "'"
			. " AND `item` = 'partner'"
			. " AND `online_status` = '1'"
			. " ORDER BY `weight` ASC";

		$result = icms::$xoopsDB->query($query);

		if (!$result)
		{
			echo 'Error: Random partners block';
			exit;

		}
		else
		{
			$rows = $partners_partner_handler->convertResultSet($result, TRUE, FALSE);
			foreach ($rows as $key => $row) 
			{
				$partner_list[$key] = $row['partner_id'];
			}
		}
	}
	// Otherwise just get a list of all partners
	else 
	{
		$criteria->add(new icms_db_criteria_Item('online_status', '1'));
		$criteria->setSort('weight');
		$criteria->setOrder('ASC');
		$partner_list = $partners_partner_handler->getList($criteria);
		$partner_list = array_flip($partner_list);
	}
	
	// Pick random partners from the list, if the block preference is so set
	if ($options[1] == TRUE && !empty($partner_list)) 
	{
		shuffle($partner_list);
	}
		
	// Retrieve the partners and assign them to the block - need to shuffle a second time
	if (!empty($partner_list)) {
		// Cut the partner list down to the number of required entries and set the IDs as criteria
		$partner_list = array_slice($partner_list, 0, $options[0], TRUE);
		$criteria->add(new icms_db_criteria_Item('partner_id', '(' . implode(',', $partner_list) . ')', 'IN'));
		$partners = $partners_partner_handler->getObjects($criteria, TRUE, FALSE);
		if ($options[1] == TRUE)
		{
			shuffle($partners);
		}
		
		// Adjust the logo paths and append SEO string to URLs
		foreach ($partners as $key => &$object)
		{
			if (!empty($object['logo'])) {
				$object['logo'] = ICMS_URL . '/uploads/' . $partnersModule->getVar('dirname') . '/partner/' . $object['logo'];
			}		
			if (!empty($object['short_url']))
			{
				$object['itemUrl'] .= "&amp;title=" . $object['short_url'];
			}
		
		// Assign to template
		$block['random_partners'] = $partners;
		$block['show_logos'] = $options[2];
		$block['partners_logo_block_display_width'] = icms_getConfig('partners_logo_block_display_width', $partnersModule->getVar('dirname'));
		}
	} else {
		$block = array();
	}
	
	return $block;
}

/**
 * Edit recent partners block options
 *
 * @param array $options
 * @return string 
 */
function edit_random_partners($options) 
{
	$partnersModule = icms_getModuleInfo('partners');
	include_once(ICMS_ROOT_PATH . '/modules/' . $partnersModule->getVar('dirname') . '/include/common.php');
	$partners_partner_handler = icms_getModuleHandler('partner', $partnersModule->getVar('dirname'), 'partners');
	
	// Select number of random partners to display in the block
	$form = '<table><tr>';
	$form .= '<tr><td>' . _MB_PARTNERS_RANDOM_LIMIT . '</td>';
	$form .= '<td>' . '<input type="text" name="options[0]" value="' . $options[0] . '"/></td>';
	
	// Randomise the partners? NB: Only works if you do not cache the block
	$form .= '<tr><td>' . _MB_PARTNERS_RANDOM_OR_FIXED . '</td>';
	$form .= '<td><input type="radio" name="options[1]" value="1"';
	if ($options[1] == 1) 
	{
		$form .= ' checked="checked"';
	}
	$form .= '/>' . _MB_PARTNERS_RANDOM_YES;
	$form .= '<input type="radio" name="options[1]" value="0"';
	if ($options[1] == 0) 
	{
		$form .= 'checked="checked"';
	}
	$form .= '/>' . _MB_PARTNERS_RANDOM_NO . '</td></tr>';	
	
	// Show partner logos, or just a simple list?
	$form .= '<tr><td>' . _MB_PARTNERS_LOGOS_OR_LIST . '</td>';
	$form .= '<td><input type="radio" name="options[2]" value="1"';
	if ($options[2] == 1) 
	{
		$form .= ' checked="checked"';
	}
	$form .= '/>' . _MB_PARTNERS_RANDOM_YES;
	$form .= '<input type="radio" name="options[2]" value="0"';
	if ($options[2] == 0) 
	{
		$form .= 'checked="checked"';
	}
	$form .= '/>' . _MB_PARTNERS_RANDOM_NO . '</td></tr>';
	
	// Optionally display results from a single tag - but only if sprockets module is installed
	$sprocketsModule = icms::handler("icms_module")->getByDirname("sprockets");

	if (icms_get_module_status("sprockets"))
	{
		$sprockets_tag_handler = icms_getModuleHandler('tag', $sprocketsModule->getVar('dirname'), 'sprockets');
		$sprockets_taglink_handler = icms_getModuleHandler('taglink', $sprocketsModule->getVar('dirname'), 'sprockets');
		
		// Get only those tags that contain content from this module
		$criteria = '';
		$relevant_tag_ids = array();
		$criteria = icms_buildCriteria(array('mid' => $partnersModule->getVar('mid')));
		$partners_module_taglinks = $sprockets_taglink_handler->getObjects($criteria, TRUE, TRUE);
		foreach ($partners_module_taglinks as $key => $value)
		{
			$relevant_tag_ids[] = $value->getVar('tid');
		}
		$relevant_tag_ids = array_unique($relevant_tag_ids);
		$relevant_tag_ids = '(' . implode(',', $relevant_tag_ids) . ')';
		unset($criteria);

		$criteria = new icms_db_criteria_Compo();
		$criteria->add(new icms_db_criteria_Item('tag_id', $relevant_tag_ids, 'IN'));
		$criteria->add(new icms_db_criteria_Item('label_type', '0'));
		$tagList = $sprockets_tag_handler->getList($criteria);

		$tagList = array(0 => _MB_PARTNERS_RANDOM_ALL) + $tagList;
		$form .= '<tr><td>' . _MB_PARTNERS_RANDOM_TAG . '</td>';
		// Parameters icms_form_elements_Select: ($caption, $name, $value = null, $size = 1, $multiple = TRUE)
		$form_select = new icms_form_elements_Select('', 'options[3]', $options[3], '1', FALSE);
		$form_select->addOptionArray($tagList);
		$form .= '<td>' . $form_select->render() . '</td></tr>';
		
		// Dynamic tagging (overrides static tag filter)
		$form .= '<tr><td>' . _MB_PARTNERS_DYNAMIC_TAG . '</td>';			
		$form .= '<td><input type="radio" name="options[4]" value="1"';
		if ($options[4] == 1) {
			$form .= ' checked="checked"';
		}
		$form .= '/>' . _MB_PARTNERS_PROJECT_YES;
		$form .= '<input type="radio" name="options[4]" value="0"';
		if ($options[4] == 0) {
			$form .= 'checked="checked"';
		}
		$form .= '/>' . _MB_PARTNERS_PROJECT_NO . '</td></tr>';
	}
	
	$form .= '</table>';
	
	return $form;
}
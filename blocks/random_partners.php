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
	if (icms_get_module_status("sprockets"))
	{
		$sprockets_taglink_handler = icms_getModuleHandler('taglink', $sprocketsModule->getVar('dirname'), 'sprockets');
	}
	
	$criteria = new icms_db_criteria_Compo();
	$partnerList = $partners = array();

	// Get a list of partners filtered by tag
	if (icms_get_module_status("sprockets") && $options[1] != 0)
	{
		$query = "SELECT `partner_id` FROM " . $partners_partner_handler->table . ", "
			. $sprockets_taglink_handler->table
			. " WHERE `partner_id` = `iid`"
			. " AND `tid` = '" . $options[1] . "'"
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
	if ($options[2] == TRUE) 
	{
		shuffle($partner_list);
	}
	
	// Cut the partner list down to the number of required entries and set the IDs as criteria
	$partner_list = array_slice($partner_list, 0, $options[0], TRUE);
	$criteria->add(new icms_db_criteria_Item('partner_id', '(' . implode(',', $partner_list) . ')', 'IN'));
			
	// Retrieve the partners and assign them to the block - need to shuffle a second time
	$partners = $partners_partner_handler->getObjects($criteria, TRUE, FALSE);
	shuffle($partners);
	
	// Adjust the logo paths
	foreach ($partners as $key => &$object)
	{
		$object['logo'] = ICMS_URL . '/uploads/' . $partnersModule->getVar('dirname') . '/partner/' . $object['logo'];
	}
	
	// Assign to template
	$block['random_partners'] = $partners;
	$block['show_logos'] = $options[3];
	$block['logo_block_display_width'] = icms_getConfig('logo_block_display_width', $partnersModule->getVar('dirname'));
	
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
	include_once(ICMS_ROOT_PATH . '/class/xoopsform/formselect.php');
	$partners_partner_handler = icms_getModuleHandler('partner', $partnersModule->getVar('dirname'), 'partners');
	
	// Select number of random partners to display in the block
	$form = '<table><tr>';
	$form .= '<tr><td>' . _MB_PARTNERS_RANDOM_LIMIT . '</td>';
	$form .= '<td>' . '<input type="text" name="options[0]" value="' . $options[0] . '"/></td>';
	
	// Optionally display results from a single tag - but only if sprockets module is installed
	$sprocketsModule = icms_getModuleInfo('sprockets');
	if (icms_get_module_status("sprockets"))
	{
		$sprockets_tag_handler = icms_getModuleHandler('tag', $sprocketsModule->getVar('dirname'), 'sprockets');
		$form .= '<tr><td>' . _MB_PARTNERS_RANDOM_TAG . '</td>';
		// Parameters icms_form_elements_Select: ($caption, $name, $value = null, $size = 1, $multiple = false)
		$form_select = new icms_form_elements_Select('', 'options[1]', $options[1], '1', FALSE);
		$tagList = $sprockets_tag_handler->getList();
		$tagList = array(0 => _MB_PARTNERS_RANDOM_ALL) + $tagList;
		$form_select->addOptionArray($tagList);
		$form .= '<td>' . $form_select->render() . '</td></tr>';
	}
	
	// Randomise the partners? NB: Only works if you do not cache the block
	$form .= '<tr><td>' . _MB_PARTNERS_RANDOM_OR_FIXED . '</td>';
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
	
	// Show partner logos, or just a simple list?
	$form .= '<tr><td>' . _MB_PARTNERS_LOGOS_OR_LIST . '</td>';
	$form .= '<td><input type="radio" name="options[3]" value="1"';
	if ($options[3] == 1) 
	{
		$form .= ' checked="checked"';
	}
	$form .= '/>' . _MB_PARTNERS_RANDOM_YES;
	$form .= '<input type="radio" name="options[3]" value="0"';
	if ($options[3] == 0) 
	{
		$form .= 'checked="checked"';
	}
	$form .= '/>' . _MB_PARTNERS_RANDOM_NO . '</td></tr>';
	$form .= '</table>';
	
	return $form;
}
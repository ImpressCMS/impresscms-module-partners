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
	$sprockets_taglink_handler = icms_getModuleHandler('taglink', $sprocketsModule->getVar('dirname'), 'sprockets');
	
	if ($sprocketsModule && $options[1] != 0)
	{
		// Optionally prepare an array of random partner ids
		if ($options[2] == TRUE) // Randomised and filtered by tag
		{
			

		}
		else // Not randomised, but filtered by tag
		{

		}
	}
	else // No Sprockets module / no tag filter
	{
		// Get a list of online partner_ids and count them
		$criteria = icms_buildCriteria(array('online_status' => '1'));
		$partner_list = $partners_partner_handler->getList($criteria);
		$partner_count = count($partner_list);
		
		// Select random partners for display
		$random_ids = array();
		for ($i = 0; $i < $options[0]; $i++)
		{
			$random_ids[] = mt_rand(1, $partner_count);
		}
		$criteria->add(new icms_db_criteria_Item('partner_id', '(' . implode(',', $random_ids) . ')', 'IN'));
		$block['random_partners'] = $partners_partner_handler->getObjects($criteria, TRUE, FALSE);
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
	include_once(ICMS_ROOT_PATH . '/class/xoopsform/formselect.php');
	$partners_partner_handler = icms_getModuleHandler('partner', $partnersModule->getVar('dirname'), 'partners');
	
	// Select number of random partners to display in the block
	$form = '<table><tr>';
	$form .= '<tr><td>' . _MB_PARTNERS_RANDOM_LIMIT . '</td>';
	$form .= '<td>' . '<input type="text" name="options[]" value="' . $options[0] . '"/></td>';
	
	// Optionally display results from a single tag - but only if sprockets module is installed
	$sprocketsModule = icms_getModuleInfo('sprockets');
	if ($sprocketsModule)
	{
		$sprockets_tag_handler = icms_getModuleHandler('tag', $sprocketsModule->getVar('dirname'), 'sprockets');
		$form .= '<tr><td>' . _MB_PARTNERS_RANDOM_TAG . '</td>';
		// Parameters XoopsFormSelect: ($caption, $name, $value = null, $size = 1, $multiple = false)
		$form_select = new XoopsFormSelect('', 'options[]', $options[1], '1', FALSE);
		$tagList = $sprockets_tag_handler->getList();
		$tagList = array(0 => _MB_PARTNERS_RANDOM_ALL) + $tagList;
		$form_select->addOptionArray($tagList);
		$form .= '<td>' . $form_select->render() . '</td></tr>';
	}
	
	// Randomise the partners? NB: Only works if you do not cache the block
	$form .= '<tr><td>' . _MB_PARTNERS_RANDOM_OR_FIXED . '</td>';
	$form .= '<td><input type="radio" name="options[1]" value="1"';
	if ($options[1] == 1) 
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
	$form .= '</table>';
	
	return $form;
}
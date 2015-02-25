<?php
/**
 * partners version infomation
 *
 * This file holds the configuration information of this module
 *
 * @copyright	Copyright Madfish (Simon Wilkinson) 2012
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
 * @package		partners
 * @version		$Id$
 */

defined("ICMS_ROOT_PATH") or die("ICMS root path not defined");

/**  General Information  */
$modversion = array(
	"name"						=> _MI_PARTNERS_MD_NAME,
	"version"					=> 1.06,
	"description"				=> _MI_PARTNERS_MD_DESC,
	"author"					=> "Madfish (Simon Wilkinson)",
	"credits"					=> "Module icon by Corey Marion.",
	"help"						=> "",
	"license"					=> "GNU General Public License (GPL)",
	"official"					=> 0,
	"dirname"					=> basename(dirname(__FILE__)),
	"modname"					=> "partners",

/**  Images information  */
	"iconsmall"					=> "images/icon_small.png",
	"iconbig"					=> "images/icon_big.png",
	"image"						=> "images/icon_big.png", /* for backward compatibility */

/**  Development information */
	"status_version"			=> "1.06",
	"status"					=> "Beta",
	"date"						=> "24/2/2015",
	"author_word"				=> "For ICMS 1.3+ only.",

/** Contributors */
	"developer_website_url"		=> "https://www.isengard.biz",
	"developer_website_name"	=> "Isengard.biz",
	"developer_email"			=> "simon@isengard.biz",

/** Administrative information */
	"hasAdmin"					=> 1,
	"adminindex"				=> "admin/index.php",
	"adminmenu"					=> "admin/menu.php",

/** Install and update informations */
	"onInstall"					=> "include/onupdate.inc.php",
	"onUpdate"					=> "include/onupdate.inc.php",

/** Search information */
	"hasSearch"					=> 1,
	"search"					=> array("file" => "include/search.inc.php", "func" => "partners_search"),

/** Menu information */
	"hasMain"					=> 1,

/** Comments information */
	"hasComments"				=> 0
	);

/** other possible types: testers, translators, documenters and other */
$modversion['people']['developers'][] = "Madfish (Simon Wilkinson)";

/** Manual */
$modversion['manual']['wiki'][] = "<a href='http://wiki.impresscms.org/index.php?title=partners' target='_blank'>English</a>";

/** Database information */
$modversion['object_items'][1] = 'partner';

$modversion["tables"] = icms_getTablesArray($modversion['dirname'], $modversion['object_items']);

/** Templates information */
$modversion['templates'] = array(
	array("file" => "partners_admin_partner.html", "description" => "Partner admin index."),
	array("file" => "partners_partner.html", "description" => "Partner index."),
	array('file' => 'partners_header.html', 'description' => 'Module header.'),
	array('file' => 'partners_footer.html', 'description' => 'Module footer.'),
	array('file' => 'partners_requirements.html', 'description' => 'Alert if module requirements not met.'));

/** Blocks */

$modversion['blocks'][1] = array(
	'file' => 'random_partners.php',
	'name' => _MI_PARTNERS_RANDOM,
	'description' => _MI_PARTNERS_RANDOMDSC,
	'show_func' => 'show_random_partners',
	'edit_func' => 'edit_random_partners',
	'options' => '5|0|1|0|0',
	'template' => 'partners_block_random.html'
);

/** Preferences */
$modversion['config'][1] = array(
  'name' => 'partners_index_display_mode',
  'title' => '_MI_PARTNERS_INDEX_DISPLAY_MODE',
  'description' => '_MI_PARTNERS_INDEX_DISPLAY_MODE_DSC',
  'formtype' => 'yesno',
  'valuetype' => 'int',
  'default' =>  '1');

$modversion['config'][] = array(
  'name' => 'number_of_partners_per_page',
  'title' => '_MI_PARTNERS_NUMBER_PARTNERS_PER_PAGE',
  'description' => '_MI_PARTNERS_NUMBER_PARTNERS_PER_PAGE_DSC',
  'formtype' => 'textbox',
  'valuetype' => 'int',
  'default' =>  '5');

$modversion['config'][] = array(
	'name' => 'partners_show_breadcrumb',
	'title' => '_MI_PARTNERS_SHOW_BREADCRUMB',
	'description' => '_MI_PARTNERS_SHOW_BREADCRUMB_DSC',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => '1');

$modversion['config'][] = array(
	'name' => 'partners_show_tag_select_box',
	'title' => '_MI_PARTNERS_SHOW_TAG_SELECT_BOX',
	'description' => '_MI_PARTNERS_SHOW_TAG_SELECT_BOX_DSC',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => '1');

$modversion['config'][] = array(
	'name' => 'partners_show_counter',
	'title' => '_MI_PARTNERS_SHOW_COUNTER',
	'description' => '_MI_PARTNERS_SHOW_COUNTER_DSC',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => '1');

$modversion['config'][] = array(
	'name' => 'display_partner_logos',
	'title' => '_MI_PARTNERS_DISPLAY_PARTNER_LOGOS',
	'description' => '_MI_PARTNERS_DISPLAY_PARTNER_LOGOS_DSC',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => '1');

$modversion['config'][] = array(
	'name' => 'partner_logo_position',
	'title' => '_MI_PARTNERS_PARTNER_LOGO_POSITION',
	'description' => '_MI_PARTNERS_PARTNER_LOGO_POSITION_DSC',
	'formtype' => 'select',
	'valuetype' => 'int',
	'options' => array('_MI_PARTNERS_LEFT' => 0, '_MI_PARTNERS_RIGHT' => 1),
	'default' => '1');

$modversion['config'][] = array(
	'name' => 'partners_freestyle_logo_dimensions',
	'title' => '_MI_PARTNERS_FREESTYLE_LOGO_DIMENSIONS',
	'description' => '_MI_PARTNERS_FREESTYLE_LOGO_DIMENSIONS_DSC',
	'formtype' => 'yesno',
	'valuetype' => 'int',
	'default' => '0');

$modversion['config'][] = array(
  'name' => 'partners_logo_index_display_width',
  'title' => '_MI_PARTNERS_LOGO_INDEX_DISPLAY_WIDTH',
  'description' => '_MI_PARTNERS_LOGO_INDEX_DISPLAY_WIDTH_DSC',
  'formtype' => 'text',
  'valuetype' => 'int',
  'default' =>  '150');

$modversion['config'][] = array(
  'name' => 'partners_logo_single_display_width',
  'title' => '_MI_PARTNERS_LOGO_SINGLE_DISPLAY_WIDTH',
  'description' => '_MI_PARTNERS_LOGO_SINGLE_DISPLAY_WIDTH_DSC',
  'formtype' => 'text',
  'valuetype' => 'int',
  'default' =>  '300');

$modversion['config'][] = array(
  'name' => 'partners_logo_block_display_width',
  'title' => '_MI_PARTNERS_LOGO_BLOCK_DISPLAY_WIDTH',
  'description' => '_MI_PARTNERS_LOGO_BLOCK_DISPLAY_WIDTH_DSC',
  'formtype' => 'text',
  'valuetype' => 'int',
  'default' =>  '150');

$modversion['config'][] = array(
	'name' => 'partners_logo_upload_height',
	'title' => '_MI_PARTNERS_LOGO_UPLOAD_HEIGHT',
	'description' => '_MI_PARTNERS_LOGO_UPLOAD_HEIGHT_DSC',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' =>  '500');

$modversion['config'][] = array(
	'name' => 'partners_logo_upload_width',
	'title' => '_MI_PARTNERS_LOGO_UPLOAD_WIDTH',
	'description' => '_MI_PARTNERS_LOGO_UPLOAD_WIDTH_DSC',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' =>  '500');

$modversion['config'][] = array(
	'name' => 'partners_logo_file_size',
	'title' => '_MI_PARTNERS_LOGO_FILE_SIZE',
	'description' => '_MI_PARTNERS_LOGO_FILE_SIZE_DSC',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' =>  '2097152'); // 2MB default max upload size
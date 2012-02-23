<?php
/**
 * English language constants related to module information
 *
 * @copyright	Copyright Madfish (Simon Wilkinson) 2012
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
 * @package		partners
 * @version		$Id$
 */

defined("ICMS_ROOT_PATH") or die("ICMS root path not defined");

define("_MI_PARTNERS_MD_NAME", "partners");
define("_MI_PARTNERS_MD_DESC", "ImpressCMS Simple partners");
define("_MI_PARTNERS_PARTNERS", "Partners");
define("_MI_PARTNERS_TEMPLATES", "Templates");

// Blocks
define("_MI_PARTNERS_RANDOM", "Random partners");
define("_MI_PARTNERS_RANDOMDSC", "Display random partners");

// Preferences
define("_MI_PARTNERS_INDEX_DISPLAY_MODE", "Display index as list of summaries");
define("_MI_PARTNERS_INDEX_DISPLAY_MODE_DSC", "Toggles the display of the index page between a list of summaries (yes) and a compact list of tables (no).");
define("_MI_PARTNERS_NUMBER_PARTNERS_PER_PAGE", "Number of partners per page");
define("_MI_PARTNERS_NUMBER_PARTNERS_PER_PAGE_DSC", "Controls how many partners are shown on the index page, sane value is 5-10.");
define("_MI_PARTNERS_SHOW_TAG_SELECT_BOX", "Show tag select box");
define("_MI_PARTNERS_SHOW_TAG_SELECT_BOX_DSC", "Toggles the tag select box on/off for the partners index page (only if Sprockets module installed).");
define("_MI_PARTNERS_SHOW_BREADCRUMB", "Show breadcrumb");
define("_MI_PARTNERS_SHOW_BREADCRUMB_DSC", "Toggles the module breadcrumb on/off");
define("_MI_PARTNERS_SHOW_COUNTER", "Show counter?");
define("_MI_PARTNERS_SHOW_COUNTER_DSC", "Toggles the counter field on or off");
define("_MI_PARTNERS_DISPLAY_PARTNER_LOGOS", "Display partner logos");
define("_MI_PARTNERS_DISPLAY_PARTNER_LOGOS_DSC", "Toggles logos on or off.");
define("_MI_PARTNERS_PARTNER_LOGO_POSITION", "Logo position");
define("_MI_PARTNERS_PARTNER_LOGO_POSITION_DSC", "Display partner logos on the left or right side of the page.");
define("_MI_PARTNERS_FREESTYLE_LOGO_DIMENSIONS", "Freestyle logo dimensions");
define("_MI_PARTNERS_FREESTYLE_LOGO_DIMENSIONS_DSC", "If enabled, logos will NOT be automatically resized. This setting is useful if your partner logos vary in shape and want to manually resize your logos yourself.");
define("_MI_PARTNERS_LOGO_INDEX_DISPLAY_WIDTH", "Logo display width on the INDEX page (pixels)");
define("_MI_PARTNERS_LOGO_INDEX_DISPLAY_WIDTH_DSC", "Partner logos will be dynamically resized according to this value. You can change the value any time you like. However, you should upload logos that are slightly LARGER than the maximum desired display size to avoid pixelation due to upscaling.");
define("_MI_PARTNERS_LOGO_SINGLE_DISPLAY_WIDTH", "Logo display width in SINGLE view (pixels)");
define("_MI_PARTNERS_LOGO_SINGLE_DISPLAY_WIDTH_DSC", "Partner logos will be dynamically resized according to this value. You can change the value any time you like. However, you should upload logos that are slightly LARGER than the maximum desired display size to avoid pixelation due to upscaling.");
define("_MI_PARTNERS_LOGO_BLOCK_DISPLAY_WIDTH", "Logo display width in the Random Partners block (pixels)");
define("_MI_PARTNERS_LOGO_BLOCK_DISPLAY_WIDTH_DSC", "Partner logos will be dynamically resized according to this value. You can change the value any time you like. However, you should upload logos that are slightly LARGER than the maximum desired display size to avoid pixelation due to upscaling.");
define("_MI_PARTNERS_LOGO_UPLOAD_HEIGHT", "Maximum height of logo files (pixels)");
define("_MI_PARTNERS_LOGO_UPLOAD_HEIGHT_DSC", "Logo files may not exceed this value.");
define("_MI_PARTNERS_LOGO_UPLOAD_WIDTH", "Maximum width of logo files (pixels)");
define("_MI_PARTNERS_LOGO_UPLOAD_WIDTH_DSC", "Logo files may not exceed this value.");
define("_MI_PARTNERS_LOGO_FILE_SIZE", "Maximum file size of logo files (bytes)");
define("_MI_PARTNERS_LOGO_FILE_SIZE_DSC", "Logo files may not exceed this value.");
define("_MI_PARTNERS_DATE_FORMAT", "Date format");
define("_MI_PARTNERS_DATE_FORMAT_DSC", "Controls the format of the date returned in search results, see the <a href='http://php.net/manual/en/function.date.php'>PHP manual</a> for formatting options.");

// Preference options
define("_MI_PARTNERS_LEFT", "Left");
define("_MI_PARTNERS_RIGHT", "Right");

// Manual
define("_MI_PARTNERS_MANUAL", "Manual");
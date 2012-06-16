<?php
/**
 * Common file of the module included on all pages of the module
 *
 * @copyright	Copyright Madfish (Simon Wilkinson) 2012
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
 * @package		partners
 * @version		$Id$
 */

defined("ICMS_ROOT_PATH") or die("ICMS root path not defined");

if (!defined("PARTNERS_DIRNAME")) define("PARTNERS_DIRNAME", $modversion["dirname"] = basename(dirname(dirname(__FILE__))));
if (!defined("PARTNERS_URL")) define("PARTNERS_URL", ICMS_URL."/modules/".PARTNERS_DIRNAME."/");
if (!defined("PARTNERS_ROOT_PATH")) define("PARTNERS_ROOT_PATH", ICMS_ROOT_PATH."/modules/".PARTNERS_DIRNAME."/");
if (!defined("PARTNERS_IMAGES_URL")) define("PARTNERS_IMAGES_URL", PARTNERS_URL."images/");
if (!defined("PARTNERS_ADMIN_URL")) define("PARTNERS_ADMIN_URL", PARTNERS_URL."admin/");

// Include the common language file of the module
icms_loadLanguageFile("partners", "common");
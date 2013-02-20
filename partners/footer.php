<?php
/**
 * Footer page included at the end of each page on user side of the mdoule
 *
 * @copyright	Copyright Madfish (Simon Wilkinson) 2012
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
 * @package		partners
 * @version		$Id$
 */

defined("ICMS_ROOT_PATH") or die("ICMS root path not defined");

$icmsTpl->assign("partners_adminpage", "<a href='" . ICMS_URL . "/modules/" . icms::$module->getVar("dirname") . "/admin/index.php'>" ._MD_PARTNERS_ADMIN_PAGE . "</a>");
$icmsTpl->assign("partners_is_admin", icms_userIsAdmin(PARTNERS_DIRNAME));
$icmsTpl->assign('partners_url', PARTNERS_URL);
$icmsTpl->assign('partners_images_url', PARTNERS_IMAGES_URL);

$xoTheme->addStylesheet(PARTNERS_URL . 'module' . ((defined("_ADM_USE_RTL") && _ADM_USE_RTL) ? '_rtl' : '') . '.css');

include_once ICMS_ROOT_PATH . '/footer.php';
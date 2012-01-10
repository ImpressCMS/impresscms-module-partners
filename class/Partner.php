<?php
/**
 * Class representing partners partner objects
 *
 * @copyright	Copyright Madfish (Simon Wilkinson) 2012
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		Madfish (Simon Wilkinson) <simon@isengard.biz>
 * @package		partners
 * @version		$Id$
 */

defined("ICMS_ROOT_PATH") or die("ICMS root path not defined");

class mod_partners_Partner extends icms_ipf_seo_Object
{
	/**
	 * Constructor
	 *
	 * @param mod_partners_Partner $handler Object handler
	 */
	public function __construct(&$handler)
	{
		icms_ipf_object::__construct($handler);

		$this->quickInitVar("partner_id", XOBJ_DTYPE_INT, TRUE);
		$this->quickInitVar("title", XOBJ_DTYPE_TXTBOX, TRUE);
		$this->quickInitVar("logo", XOBJ_DTYPE_IMAGE, FALSE);
		$this->quickInitVar("website", XOBJ_DTYPE_URL, FALSE);
		$this->quickInitVar("description", XOBJ_DTYPE_TXTAREA, TRUE);
		$this->quickInitVar("extended_text", XOBJ_DTYPE_TXTAREA, FALSE);
		$this->quickInitVar("contact_name", XOBJ_DTYPE_TXTBOX, FALSE);
		$this->quickInitVar("contact_position", XOBJ_DTYPE_TXTBOX, FALSE);
		$this->quickInitVar("contact_email", XOBJ_DTYPE_EMAIL, FALSE);
		$this->quickInitVar("contact_phone", XOBJ_DTYPE_TXTBOX, FALSE);
		$this->quickInitVar("contact_fax", XOBJ_DTYPE_TXTBOX, FALSE);
		$this->quickInitVar("address", XOBJ_DTYPE_TXTAREA, FALSE);
		$this->quickInitVar("creator", XOBJ_DTYPE_INT, TRUE);
		$this->quickInitVar("date", XOBJ_DTYPE_LTIME, TRUE);
		$this->quickInitVar("weight", XOBJ_DTYPE_INT, TRUE, FALSE, FALSE, 0);
		$this->quickInitVar("online_status", XOBJ_DTYPE_INT, TRUE, FALSE, FALSE, 1);
		$this->initCommonVar("counter");
		$this->initCommonVar("dohtml");
		$this->initCommonVar("dobr");
		$this->initCommonVar("doimage");
		$this->initCommonVar("dosmiley");
		$this->setControl("logo", "image");
		$this->setControl("creator", "user");
		$this->setControl("online_status", "yesno");
		
		// Set controls: Allow WYSIWYG editor support in text areas
		$this->setControl("description", "dhtmltextarea");
		$this->setControl("extended_text", "dhtmltextarea");
		$this->setControl("address", "dhtmltextarea");

		// Intialise SEO functionality
		$this->initiateSEO();
	}

	/**
	 * Overriding the icms_ipf_Object::getVar method to assign a custom method on some
	 * specific fields to handle the value before returning it
	 *
	 * @param str $key key of the field
	 * @param str $format format that is requested
	 * @return mixed value of the field that is requested
	 */
	public function getVar($key, $format = "s")
	{
		if ($format == "s" && in_array($key, array("online_status")))
		{
			return call_user_func(array ($this,	$key));
		}
		return parent::getVar($key, $format);
	}
	
	/**
	 * Returns a weight control for the partner admin table view
	 * @return mixed
	 */
	public function getWeightControl()
	{
		$control = new icms_form_elements_Text('','weight[]',5,7,$this->getVar( 'weight', 'e'));
		$control->setExtra('style="text-align:center;"');
		return $control->render();
	}
	
	/**
	 * Converts online_status to human readable icon with toggle link
	 * 
	 * @return string
	 */
	public function online_status()
	{
		$online_status = $this->getVar('online_status', 'e');
		if ($online_status == false) 
		{
			return '<a href="' . ICMS_URL . '/modules/' . basename(dirname(dirname(__FILE__)))
				. '/admin/partner.php?partner_id=' . $this->getVar('partner_id') . '&amp;op=visible">
				<img src="../images/button_cancel.png" alt="Offline" /></a>';
		}
		else
		{
			return '<a href="' . ICMS_URL . '/modules/' . basename(dirname(dirname(__FILE__)))
				. '/admin/partner.php?partner_id=' . $this->getVar('partner_id') . '&amp;op=visible">
				<img src="../images/button_ok.png" alt="Online" /></a>';
		}
	}
}
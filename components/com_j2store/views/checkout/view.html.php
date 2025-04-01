<?php
/**
 * @package J2Store
 * @copyright Copyright (c)2014-17 Ramesh Elamathi / J2Store.org
 * @license GNU GPL v3 or later
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class J2StoreViewCheckout extends F0FViewHtml
{

	protected function onDisplay($tpl = null)
	{
	
		$app = Factory::getApplication();
		$session = $app->getSession();
		$user = $app->getIdentity();
		$view = $this->input->getCmd('view', 'checkout');
		
		$this->params = J2Store::config();
		$this->currency = J2Store::currency();
		$this->storeProfile = J2Store::storeProfile();
		$this->user = $user; 
		
		return true; 
	}
	
}
	
	
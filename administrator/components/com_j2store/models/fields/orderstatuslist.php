<?php
/**
 * @package     Joomla.Component
 * @subpackage  J2Store
 *
 * @copyright Copyright (C) 2014-24 Ramesh Elamathi / J2Store.org
 * @copyright Copyright (C) 2025 J2Commerce, LLC. All rights reserved.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or later
 * @website https://www.j2commerce.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

require_once JPATH_ADMINISTRATOR.'/components/com_j2store/helpers/j2html.php';

class JFormFieldOrderstatusList extends ListField
{
	protected $type = 'OrderstatusList';

	public function getRepeatable()
	{
		$html ='';
		if($this->item->orderstatus_id != '*'){
			$orderstatus = J2Store::fof()->loadTable('Orderstatus','J2StoreTable');
			$orderstatus->load($this->item->orderstatus_id);
			$html ='<label class="label">'.Text::_($orderstatus->orderstatus_name);
			if(isset($orderstatus->orderstatus_cssclass) && $orderstatus->orderstatus_cssclass){
				$html ='<label class="label  '.$orderstatus->orderstatus_cssclass.'">'.Text::_($orderstatus->orderstatus_name);
			}

		}else{
			$html ='<label class="label label-success">'.Text::_('JALL');
		}
		$html .='</label>';
		return $html;
	}

	public function getOptions()
    {
		$model = J2Store::fof()->getModel('Orderstatuses','J2StoreModel');
		$orderlist = $model->getItemList();

		//generate order status list
		$options = [];
		$options[] =  HTMLHelper::_('select.option', '*', Text::_('JALL'));
		foreach($orderlist as $row) {
			$options[] = HTMLHelper::_('select.option', $row->j2store_orderstatus_id, Text::_($row->orderstatus_name));
		}

        $options = array_merge(parent::getOptions(), $options);

        return $options;
	}
}

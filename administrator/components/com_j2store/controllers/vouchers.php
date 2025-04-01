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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

jimport('joomla.application.component.controllerform');
require_once JPATH_ADMINISTRATOR.'/components/com_j2store/controllers/traits/list_view.php';

class J2StoreControllerVouchers extends F0FController
{
    use list_view;

    public function execute($task)
    {
        if(in_array($task, array('edit', 'add'))) {
            $task = 'add';
        }
        return parent::execute($task);
    }

    public function onBeforeApplySave(&$data)
    {
        if(is_array($data)){
            $data['valid_from'] = isset($data['valid_from']) && !empty($data['valid_from']) ? $data['valid_from']:null;
            $data['valid_to'] = isset($data['valid_to']) && !empty($data['valid_to']) ? $data['valid_to']:null;
        }
        return true;
    }

    function add()
    {
        $platform = J2Store::platform();
        $app = $platform->application();
        $id = $app->input->getString('id','');
        $vars = $this->getBaseVars();
        $this->noToolbar();
        if(J2Store::isPro()) {
            $this->editToolBar();
            if(!empty($id)) {
                ToolbarHelper::save2copy('copy');
            }
        }
        $vars->primary_key = 'j2store_voucher_id';
        $vars->id = $this->getPageId();
        $voucher_table = J2Store::fof()->loadTable('Voucher', 'J2StoreTable')->getClone();
        $voucher_table->load($vars->id);
        $vars->item = $voucher_table;
        $vars->field_sets = array();
        $col_class = 'col-md-';
        $vars->field_sets[] = array(
            'name' => 'basic_information',
            'label' => 'J2STORE_VOUCHER_BASIC_INFORMATION',
            'fields' => array(
                'voucher_code' => array(
                    'label' => 'J2STORE_VOUCHER_CODE_LABEL',
                    'type' => 'text',
                    'name' => 'voucher_code',
                    'value' => $voucher_table->voucher_code,
                    'options' => array('required' => 'true','class' => 'form-control')
                ),
                'email_to' => array(
                    'label' => 'J2STORE_VOUCHER_TO_EMAIL_LABEL',
                    'type' => 'text',
                    'name' => 'email_to',
                    'value' => $voucher_table->email_to,
                    'options' => array('required' => 'true','class' => 'form-control')
                ),
                'voucher_value' => array(
                    'label' => 'J2STORE_VOUCHER_VALUE_LABEL',
                    'type' => 'text',
                    'name' => 'voucher_value',
                    'value' => $voucher_table->voucher_value,
                    'options' => array('required' => 'true','class' => 'form-control')
                ),
                'type' => array(
                    'label' => 'J2STORE_VOUCHER_TYPE_LABEL',
                    'type' => 'list',
                    'name' => 'voucher_type',
                    'value' => $voucher_table->voucher_type,
                    'options' => array('class'=> 'form-select','options' => array('giftcard'=> Text::_('J2STORE_VOUCHER_TYPE_GIFT_CARD')))

                ),
                'valid_from' => array(
                    'label' => 'J2STORE_PR_VALIDFROM',
                    'type' => 'calendar',
                    'name' => 'valid_from',
                    'value' => $voucher_table->valid_from,
                    'options' => array('required' => 'true','class'=> 'form-control','format' => '%Y-%m-%d %H:%M:%S')
                ),
                'valid_to' => array(
                    'label' => 'J2STORE_PR_VALIDTO',
                    'type' => 'calendar',
                    'name' => 'valid_to',
                    'value' => $voucher_table->valid_to,
                    'options' => array('required' => 'true','class'=> 'form-control','format' => '%Y-%m-%d %H:%M:%S')
                ),
                'enabled' => array(
                    'label' => 'J2STORE_ENABLED',
                    'type' => 'enabled',
                    'name' => 'enabled',
                    'value' => $voucher_table->enabled,
                    'options' => array('class' => 'form-control')
                ),
            ),
        );
        $vars->field_sets[] = array(
            'id' => 'advanced_information',

            'label' => 'J2STORE_VOUCHER_ADVANCED_INFORMATION',
            'fields' => array(
                'subject' => array(
                    'label' => 'J2STORE_VOUCHER_MESSAGE_LABEL',
                    'type' => 'text',
                    'name' => 'subject',
                    'value' => $voucher_table->subject,
                    'options' => array('required' => 'true','class' => 'form-control')
                ),
                'email_body' => array(
                    'label' => 'J2STORE_VOUCHER_BODY_LABEL',
                    'type' => 'editor',
                    'name' => 'email_body',
                    'value' => $voucher_table->email_body,
                    'options' => array('required'=>'true')
                ),
            )
        );
        echo $this->_getLayout('form', $vars,'edit');
    }

    public function browse()
    {
        $app = Factory::getApplication();
        $model = $this->getThisModel();
        $state = array();
        $state['voucher_code'] = $app->input->getString('voucher_code','');
        $state['voucher_value'] = $app->input->getString('voucher_value','');
        $state['email_to'] = $app->input->getString('email_to','');
        $state['filter_order']= $app->input->getString('filter_order','j2store_voucher_id');
        $state['filter_order_Dir']= $app->input->getString('filter_order_Dir','ASC');
        foreach($state as $key => $value){
            $model->setState($key,$value);
        }
        $items = $model->getList();
        $vars = $this->getBaseVars();
        $vars->model = $model;
        $vars->items = $items;
        $vars->state = $model->getState();
        if(J2Store::isPro()) {
            $this->addBrowseToolBar();
            ToolbarHelper::custom('history','list','',Text::_('J2STORE_VOUCHER_HISTORY'));
            ToolbarHelper::custom('send','mail','',Text::_('J2STORE_VOUCHER_SEND'));
        }else {
            $this->noToolbar();
        }

        $header = array(
            'j2store_voucher_id' => array(
                'type' => 'rowselect',
                'tdwidth' => '20',
                'label' => 'J2STORE_VOUCHER_ID_LABEL'
            ),
            'voucher_code' => array(
                'type' => 'fieldsearchable',
                'sortable' => 'true',
                'show_link' => 'true',
                'url' => "index.php?option=com_j2store&amp;view=voucher&amp;id=[ITEM:ID]",
                'url_id' => 'j2store_voucher_id',
                'label' => 'J2STORE_VOUCHER_CODE_LABEL'
            ),
            'voucher_value' => array(
                 'type' => 'fieldsearchable',
                 'sortable' => 'true',
                 'label' => 'J2STORE_VOUCHER_VALUE_LABEL',
                 'show_id'=> 'false',
                 'show_username' => 'false',
                 'show_email'=> 'true',
                 'show_name'=> 'false',
  		         'show_avatar'=> 'false'
            ),
            'email_to' => array(
                'type' => 'fieldsearchable',
                'sortable' => 'true',
                'label' => 'J2STORE_VOUCHER_TO_EMAIL_LABEL',
                'show_id'=> 'false',
                'show_username' => 'false',
                'show_email'=> 'true',
                'show_name'=> 'false',
                'show_avatar'=> 'false'
            ),
            'enabled' => array(
                'type' => 'published',
                'sortable' => 'true',
                'label' => 'J2STORE_ENABLED'
            )
        );
        $this->setHeader($header,$vars);
        $vars->pagination = $model->getPagination();
        echo $this->_getLayout('default',$vars);
    }

	/**
	 * Method to send Voucher Email
	 */
	public function send()
    {
		$app = Factory::getApplication();
		$cids = $app->input->get('cid',array(),'');
		if(count($cids)) {
			$model = $this->getModel('Vouchers');
			if($model->sendVouchers($cids) === false) {
				$msg = Text::_('J2STORE_VOUCHERS_SENDING_FAILED');
				$msgType = 'warning';
			}else {
				$msg = Text::_('J2STORE_VOUCHERS_SENDING_SUCCESSFUL');
				$msgType = 'message';
			}
		}
		$this->setRedirect('index.php?option=com_j2store&view=vouchers' ,$msg, $msgType);
	}

	public function history()
    {
		$app = Factory::getApplication();
		$cid = $app->input->get('cid', array(), 'array');
		//take the first one
		$id = isset($cid[0]) ? $cid[0] : 0;
		if($id > 0) {

			$view = $this->getThisView();

			if ($model = $this->getThisModel())
			{
				// Push the model into the view (as default)
				$view->setModel($model, true);
			}
			$voucher = J2Store::fof()->loadTable('Voucher', 'J2StoreTable');
			$voucher->load($id);
			$view->set('voucher', $voucher);
			$voucher_history = $model->getVoucherHistory($id);
			$view->set('vouchers', $voucher_history);
			$view->set('params', J2Store::config());
		}
		$view->setLayout('history');
		$view->display();
	}
}

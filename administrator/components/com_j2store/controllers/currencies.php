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

require_once JPATH_ADMINISTRATOR.'/components/com_j2store/controllers/traits/list_view.php';

class J2StoreControllerCurrencies extends F0FController
{
    use list_view;

    public function execute($task)
    {
        if (in_array($task, array('edit', 'add'))) {
          $task = 'add';
        }
        return parent::execute($task);
    }

    function add()
    {
        $platform = J2Store::platform();
        $app = $platform->application();
        $vars = $this->getBaseVars();
        $this->editToolBar();
        $vars->primary_key = 'j2store_currency_id';
        $vars->id = $this->getPageId();
        $currency_table = J2Store::fof()->loadTable('Currency', 'J2StoreTable')->getClone ();
        $currency_table->load($vars->id);
        $vars->item = $currency_table;
        $vars->field_sets = array();
        $col_class = 'col-md-';
        $currency_list = J2Store::currency()->getNumericCode();
        $currency_code = array();
        foreach($currency_list as $key => $value){
            $currency_code[$key] = $key;
        }
        $vars->field_sets[] = array(
            'id' => 'basic_information',
            'class' => array(
                $col_class.'6'
            ),
            'fields' => array(
                'currency_title' => array(
                    'label' => 'J2STORE_CURRENCY_TITLE_LABEL',
                    'type' => 'text',
                    'desc' => 'J2STORE_CURRENCY_TITLE_DESC',
                    'name' => 'currency_title',
                    'value' => $currency_table->currency_title,
                    'options' => array('class' => 'form-control')
                ),
                'currency_code' => array(
                    'label' => 'J2STORE_CURRENCY_CODE_LABEL',
                    'type' => 'list',
                    'name' => 'currency_code',
                    'desc' => 'J2STORE_CURRENCY_CODE_DESC',
                    'source_class'=> 'J2Currency',
                    'source_file'=>'admin://components/com_j2store/helpers/currency.php',
                    'value' => $currency_table->currency_code,
                    'options' => array('options' => $currency_code,'id' => 'currency_code_selector','class' => 'form-select')
                ),
                'currency_symbol' => array(
                    'label' => 'J2STORE_CURRENCY_SYMBOL_LABEL',
                    'type' => 'text',
                    'desc' => 'J2STORE_CURRENCY_SYMBOL_DESC',
                    'name' => 'currency_symbol',
                    'value' => $currency_table->currency_symbol,
                    'options' => array('id' => 'j2store_currency_symbol','class' => 'form-control')
                ),
                'currency_position' => array(
                    'label' => 'J2STORE_CURRENCY_POSITION_LABEL',
                    'type' => 'list',
                    'desc' => 'J2STORE_CURRENCY_POSITION_DESC',
                    'name' => 'currency_position',
                    'value' => $currency_table->currency_position,
                    'options' => array( 'options' => array('pre'=>Text::_('J2STORE_CURRENCY_FRONT'),'post'=>Text::_('J2STORE_CURRENCY_END')),'class' => 'form-select' )
                ),

            ),
        );
        $vars->field_sets[] = array(
            'id' => 'advanced_information',
            'class' => array(
                $col_class.'6'
            ),
            'fields' => array(
                'currency_num_decimals' => array(
                    'label' => 'J2STORE_CURRENCY_NUM_DECIMALS_LABEL',
                    'type' => 'text',
                    'desc' => 'J2STORE_CURRENCY_NUM_DECIMALS_DESC',
                    'name' => 'currency_num_decimals',
                    'value' => $currency_table->currency_num_decimals,
                    'options' => array('class' => 'form-control')
                ),
                'currency_decimal' => array(
                    'label' => 'J2STORE_CURRENCY_DECIMAL_SEPARATOR_LABEL',
                    'type' => 'text',
                    'desc' => 'J2STORE_CURRENCY_DECIMAL_SEPARATOR_DESC',
                    'name' => 'currency_decimal',
                    'value' => $currency_table->currency_decimal,
                    'options' => array('class' => 'form-control')
                ),
                'currency_thousands' => array(
                    'label' => 'J2STORE_CURRENCY_THOUSANDS_LABEL',
                    'type' => 'text',
                    'desc' => 'J2STORE_CURRENCY_THOUSANDS_DESC',
                    'name' => 'currency_thousands',
                    'value' => $currency_table->currency_thousands,
                    'options' => array('class' => 'form-control')
                ),
                'currency_value' => array(
                    'label' => 'J2STORE_CURRENCY_VALUE_LABEL',
                    'type' => 'text',
                    'desc' => 'J2STORE_CURRENCY_VALUE_DESC',
                    'name' => 'currency_value',
                    'value' =>$currency_table->currency_value,
                    'options' => array('class' => 'form-control')
                ),
                'enabled' => array(
                    'label' => 'J2STORE_ENABLED',
                    'type' => 'enabled',
                    'name' => 'enabled',
                    'value' => $currency_table->enabled,
                    'options' => array('class' => '')
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
        $state['currency_code'] = $app->input->getString('currency_code', '');
        $state['filter_order'] = $app->input->getString('filter_order', 'j2store_currency_id');
        $state['filter_order_Dir'] = $app->input->getString('filter_order_Dir', 'ASC');
        foreach ($state as $key => $value) {
            $model->setState($key, $value);
        }
        $items = $model->getList();
        $vars = $this->getBaseVars();
        $vars->model = $model;
        $vars->items = $items;
        $vars->state = $model->getState();
        $this->addBrowseToolBar();
        $header = array(
            'j2store_currency_id' => array(
                'type' => 'rowselect',
                'tdwidth' => '20',
                'label' => 'J2STORE_CURRENCY_ID'
            ),
            'currency_title' => array(
                'sortable' => 'true',
                'show_link' => 'true',
                'url' => "index.php?option=com_j2store&amp;view=currency&amp;id=[ITEM:ID]",
                'url_id' => 'j2store_currency_id',
                'label' => 'J2STORE_CURRENCY_TITLE_LABEL'
            ),
            'currency_code' => array(
                'type' => 'fieldsearchable',
                'sortable' => 'true',
                'label' => 'J2STORE_CURRENCY_CODE_LABEL'
            ),
            'currency_symbol' => array(
                'sortable' => 'true',
                'label' => 'J2STORE_CURRENCY_SYMBOL_LABEL'
            ),
            'currency_value' => array(
                'sortable' => 'true',
                'label' => 'J2STORE_CURRENCY_VALUE_LABEL'
            ),
            'enabled' => array(
                'type' => 'published',
                'sortable' => 'true',
                'label' => 'J2STORE_ENABLED'
            )
        );
        $this->setHeader($header,$vars);
        $vars->pagination = $model->getPagination();
        echo $this->_getLayout('default', $vars);
    }

    protected function onBeforeBrowse()
    {
        $model = J2Store::fof()->getModel('Currencies', 'J2StoreModel');
        $model->updateCurrencies(false);

        return parent::onBeforeBrowse();
    }
}

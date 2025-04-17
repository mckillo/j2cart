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
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Mail\MailerFactoryInterface;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;

require_once JPATH_ADMINISTRATOR . '/components/com_j2store/controllers/traits/list_view.php';

class J2StoreControllerConfigurations extends F0FController
{
    use list_view;

    protected $cacheableTasks = array();

    public function __construct($config)
    {
        parent::__construct($config);
        $this->registerTask('apply', 'save');
        $this->registerTask('saveNew', 'save');
        $this->registerTask('populatedata', 'save');
    }

    public function execute($task)
    {
        if (in_array($task, array('browse', 'read', 'edit', 'add'))) {
            $task = 'add';
        }
        return parent::execute($task);
    }

    function add()
    {
        $platform = J2Store::platform();

        //$platform->loadExtra('behavior.multiselect');
        $vars = $this->getBaseVars();
        $app = $platform->application();
        $option = $app->input->getCmd('option', 'com_foobar');
        $componentName = str_replace('com_', '', $option);

        // Set toolbar title
        $subtitle_key = strtoupper($option . '_TITLE_' . F0FInflector::pluralize($this->input->getCmd('view', 'cpanel'))) . '_EDIT';
	    ToolBarHelper::title(Text::_(strtoupper($option)) . ': ' . Text::_($subtitle_key), $componentName);
	    ToolBarHelper::apply();
	    ToolBarHelper::save();
	    ToolBarHelper::cancel();
        $model = J2Store::fof()->getModel('Configurations', 'J2StoreModel');
        $configurations = $model->getItemList();
        $vars->item = new stdClass();
        foreach ($configurations as $key => $configuration) {
            if ($key === 'limit_orderstatuses') {
                $vars->item->$key = explode(',', $configuration->config_meta_value);
            } else {
                $vars->item->$key = $configuration->config_meta_value;
            }
        }
        $vars->field_sets = array();

        $vars->field_sets[] = array(
            'id' => 'basic_options',
            'label' => 'J2STORE_BASIC_OPTIONS',
            'class' => 'options-form',
            'fields' => array(
                'j2store_enable_css' => array(
                    'label' => 'J2STORE_CONF_J2STORE_ENABLE_CSS_LABEL',
                    'type' => 'radiolist',
                    'name' => 'j2store_enable_css',
                    'value' => isset($vars->item->j2store_enable_css) && !is_null($vars->item->j2store_enable_css) ? $vars->item->j2store_enable_css : 1,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_J2STORE_ENABLE_CSS_DESC'
                ),
                'load_fontawesome_ui' => array(
                    'label' => 'J2STORE_CONF_LOAD_FONTAWESOME_UI_LABEL',
                    'type' => 'radiolist',
                    'name' => 'load_fontawesome_ui',
                    'value' => isset($vars->item->load_fontawesome_ui) && !is_null($vars->item->load_fontawesome_ui) ? $vars->item->load_fontawesome_ui : 1,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_LOAD_FONTAWESOME_UI_DESC'
                ),
                'load_fancybox' => array(
                    'label' => 'J2STORE_CONF_LOAD_FANCYBOX_LABEL',
                    'type' => 'radiolist',
                    'name' => 'load_fancybox',
                    'value' => isset($vars->item->load_fancybox) && !is_null($vars->item->load_fancybox) ? $vars->item->load_fancybox : 1,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_LOAD_FANCYBOX_DESC'
                ),
                'load_jquery_ui' => array(
                    'label' => 'J2STORE_CONF_LOAD_JQUERYUI_LABEL',
                    'type' => 'list',
                    'name' => 'load_jquery_ui',
                    'value' => isset($vars->item->load_jquery_ui) && !is_null($vars->item->load_jquery_ui) ? $vars->item->load_jquery_ui : 3,
                    'options' => array(
                        'options' => array(0 => Text::_('J2STORE_NO'), 1 => Text::_('J2STORE_ONLY_FRONTEND'), 2 => Text::_('J2STORE_ONLY_BACKEND'), 3 => Text::_('J2STORE_BOTH_FRONTEND_AND_BACKEND')),'class' => 'form-select'
                    ),
                    'desc' => 'J2STORE_CONF_LOAD_JQUERYUI_DESC'
                ),
                'load_timepicker' => array(
                    'label' => 'J2STORE_CONF_TIMEPICKER_LABEL',
                    'type' => 'list',
                    'name' => 'load_timepicker',
                    'value' => isset($vars->item->load_timepicker) && !is_null($vars->item->load_timepicker) ? $vars->item->load_timepicker : 0,
                    'options' => array(
                        'options' => array(0 => Text::_('J2STORE_NO'), 1 => Text::_('J2STORE_ONLY_FRONTEND'), 2 => Text::_('J2STORE_ONLY_BACKEND'), 3 => Text::_('J2STORE_BOTH_FRONTEND_AND_BACKEND'))
                    ),
                    'desc' => 'J2STORE_CONF_JQUERY_TIMEPICKER_DESC'
                ),
                'jquery_ui_localisation' => array(
                    'label' => 'J2STORE_CONF_JQUERY_UI_LOCALISATION_LABEL',
                    'type' => 'radiolist',
                    'name' => 'jquery_ui_localisation',
                    'value' => isset($vars->item->jquery_ui_localisation) && !is_null($vars->item->jquery_ui_localisation) ? $vars->item->jquery_ui_localisation : 0,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_JQUERY_UI_LOCALISATION_DESC'
                ),
                'load_bootstrap' => array(
                    'label' => 'J2STORE_CONF_LOAD_BOOTSTRAP_LABEL',
                    'type' => 'radiolist',
                    'name' => 'load_bootstrap',
                    'value' => isset($vars->item->load_bootstrap) && !is_null($vars->item->load_bootstrap) ? $vars->item->load_bootstrap : 1,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_LOAD_BOOTSTRAP_DESC'
                ),
                'load_minimal_bootstrap' => array(
                    'label' => 'J2STORE_CONF_LOAD_MINIMAL_BOOTSTRAP_SUPPORT',
                    'type' => 'radiolist',
                    'name' => 'load_minimal_bootstrap',
                    'value' => isset($vars->item->load_minimal_bootstrap) && !is_null($vars->item->load_minimal_bootstrap) ? $vars->item->load_minimal_bootstrap : 0,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_LOAD_MINIMAL_BOOTSTRAP_SUPPORT_DESC'
                ),
                'bootstrap_version' => array(
                    'label' => 'J2STORE_CONF_BOOTSTRAP_VERSION',
                    'type' => 'list',
                    'name' => 'bootstrap_version',
                    'value' => isset($vars->item->bootstrap_version) && !is_null($vars->item->bootstrap_version) ? $vars->item->bootstrap_version : 5,
                    'options' => array(
                        'options' => array(2 => Text::_('J2STORE_BOOTSTRAP2'), 3 => Text::_('J2STORE_BOOTSTRAP3'),4 => Text::_('J2STORE_BOOTSTRAP4'),5 => Text::_('J2STORE_BOOTSTRAP5'))
                    ),
                    'desc' => 'J2STORE_CONF_BOOTSTRAP_VERSION_DESC'
                ),
                'isregister' => array(
                    'label' => 'J2STORE_CONF_ISREGISTER_LABEL',
                    'type' => 'list',
                    'name' => 'isregister',
                    'value' => isset($vars->item->isregister) && !is_null($vars->item->isregister) ? $vars->item->isregister : 0,
                    'options' => array('class'=>'form-select','options' => array(0 => Text::_('J2STORE_EVERYONE'), 1 => Text::_('J2STORE_ONLY_REGISTERED_USERS')))
                ),
                'show_product_price_for_register_user' => array(
                    'label' => 'j2store_conf_show_product_price_label',
                    'type' => 'list',
                    'name' => 'show_product_price_for_register_user',
                    'value' => isset($vars->item->show_product_price_for_register_user) && !is_null($vars->item->show_product_price_for_register_user) ? $vars->item->show_product_price_for_register_user : 0,
                    'options' => array('class'=>'form-select','options' => array(0 => Text::_('J2STORE_EVERYONE'), 1 => Text::_('J2STORE_ONLY_REGISTERED_USERS')))
                ),
                'show_product_sku_for_register_user' => array(
                    'label' => 'j2store_conf_show_product_sku_label',
                    'type' => 'list',
                    'name' => 'show_product_sku_for_register_user',
                    'value' => isset($vars->item->show_product_sku_for_register_user) && !is_null($vars->item->show_product_sku_for_register_user) ? $vars->item->show_product_sku_for_register_user : 0,
                    'options' => array('class'=>'form-select','options' => array(0 => Text::_('J2STORE_EVERYONE'), 1 => Text::_('J2STORE_ONLY_REGISTERED_USERS')))
                ),
                'date_format' => array(
                    'label' => 'J2STORE_CONF_DATE_FORMAT_LABEL',
                    'type' => 'text',
                    'name' => 'date_format',
                    'value' => isset($vars->item->date_format) && !is_null($vars->item->date_format) ? $vars->item->date_format : 'Y-m-d H:i:s',
                    'options' => array('class' => 'form-control'),
                    'desc' => 'J2STORE_CONF_DATE_FORMAT_DESC'
                ),
                'attachmentfolderpath' => array(
                    'label' => 'J2STORE_CONF_ATTACHMENTFOLDERPATH_LABEL',
                    'type' => 'text',
                    'name' => 'attachmentfolderpath',
                    'value' => isset($vars->item->attachmentfolderpath) && !is_null($vars->item->attachmentfolderpath) ? $vars->item->attachmentfolderpath : '',
                    'options' => array('class' => 'form-control'),
                    'desc' => 'J2STORE_CONF_ATTACHMENTFOLDERPATH_DESC'
                ),
            )
        );
        $vars->field_sets[] = array(
            'id' => 'store_settings',
            'label' => 'J2STORE_STORE_SETTING',
            'class' => 'options-form',
            'fields' => array(
                'queue_key' => array(
                    'label' => 'J2STORE_STORE_QUEUEKEY',
                    'type' => 'queuekey',
                    'name' => 'queue_key',
                    'value' => '',
                    'options' => array(),
                    'desc' => 'J2STORE_STORE_QUEUEKEY_DESC'
                ),
                'cron_last_trigger' => array(
                    'label' => 'J2STORE_STORE_CRONLASTHIT',
                    'type' => 'cronlasthit',
                    'name' => 'cron_last_trigger',
                    'value' => '',
                    'options' => array(),
                    'desc' => 'J2STORE_STORE_CRONLASTHIT_DESC'
                ),
                'queue_repeat_count' => array(
                    'label' => 'J2STORE_STORE_QUEUE_REPEAT_COUNT',
                    'type' => 'number',
                    'name' => 'queue_repeat_count',
                    'value' => isset($vars->item->queue_repeat_count) && !is_null($vars->item->queue_repeat_count) ? $vars->item->queue_repeat_count : 10,
                    'options' => array('class' => 'form-control'),
                    'desc' => 'J2STORE_STORE_QUEUE_REPEAT_COUNT_DESC'
                ),
                'admin_email' => array(
                    'label' => 'J2STORE_ADMIN_EMAIL',
                    'type' => 'text',
                    'name' => 'admin_email',
                    'value' => isset($vars->item->admin_email) && !is_null($vars->item->admin_email) ? $vars->item->admin_email : '',
                    'options' => array('id' => 'admin_email','class' => 'form-control'),
                    'desc' => 'J2STORE_ADMIN_EMAIL_DESC'
                ),
                'customlink_admin' => array(
                    'type' => 'customlink',
                    'name' => 'customlink_admin',
                    'value' => '',
                    'options' => array('id' => 'j2store_testemail', 'text' => 'J2STORE_TEST_ADMIN_EMAIL')
                ),
                'store_name' => array(
                    'label' => 'J2STORE_STORE_NAME',
                    'type' => 'text',
                    'name' => 'store_name',
                    'value' => isset($vars->item->store_name) && !is_null($vars->item->store_name) ? $vars->item->store_name : '',
                    'options' => array('required' => 'true','class' => 'form-control'),
                ),
                'store_address_1' => array(
                    'label' => 'J2STORE_ADDRESS_LINE1',
                    'type' => 'text',
                    'name' => 'store_address_1',
                    'value' => isset($vars->item->store_address_1) && !is_null($vars->item->store_address_1) ? $vars->item->store_address_1 : '',
                    'options' => array('class' => 'form-control'),
                ),
                'store_address_2' => array(
                    'label' => 'J2STORE_ADDRESS_LINE2',
                    'type' => 'text',
                    'name' => 'store_address_2',
                    'value' => isset($vars->item->store_address_2) && !is_null($vars->item->store_address_2) ? $vars->item->store_address_2 : '',
                    'options' => array('class' => 'form-control'),
                ),
                'store_city' => array(
                    'label' => 'J2STORE_ADDRESS_CITY',
                    'type' => 'text',
                    'name' => 'store_city',
                    'value' => isset($vars->item->store_city) && !is_null($vars->item->store_city) ? $vars->item->store_city : '',
                    'options' => array('class' => 'form-control'),
                ),
                'store_zip' => array(
                    'label' => 'J2STORE_ADDRESS_ZIP',
                    'type' => 'text',
                    'name' => 'store_zip',
                    'value' => isset($vars->item->store_zip) && !is_null($vars->item->store_zip) ? $vars->item->store_zip : '',
                    'options' => array('required' => 'true','class' => 'form-control'),
                ),
                'country_id' => array(
                    'label' => 'J2STORE_COUNTRY_NAME',
                    'type' => 'country',
                    'name' => 'country_id',
                    'value' => isset($vars->item->country_id) && !is_null($vars->item->country_id) ? $vars->item->country_id : 223,
                    'options' => array('class' => 'form-select', 'id' => 'j2store_country_id', 'zone_id' => 'j2store_zone_id', 'zone_value' => isset($vars->item->zone_id) && !is_null($vars->item->zone_id) ? $vars->item->zone_id : 0)
                ),
                'zone_id' => array(
                    'label' => 'J2STORE_ZONE_NAME',
                    'type' => 'zone',
                    'name' => 'zone_id',
                    'value' => isset($vars->item->zone_id) && !is_null($vars->item->zone_id) ? $vars->item->zone_id : 0,
                    'options' => array('class' => 'form-select', 'id' => 'j2store_zone_id')
                ),
                'config_currency' => array(
                    'label' => 'J2STORE_STORE_DEFAULT_CURRENCY',
                    'type' => 'fieldsql',
                    'name' => 'config_currency',
                    'value' => isset($vars->item->config_currency) && !is_null($vars->item->config_currency) ? $vars->item->config_currency : '',
                    'options' => array('required' => 'true', 'id' => 'j2store_currency_id', 'key_field' => 'currency_code', 'value_field' => 'currency_code', 'has_one' => 'Currencies','class' => 'form-select')
                ),
                'config_currency_auto' => array(
                    'label' => 'J2STORE_STORE_CURRENCY_AUTO_UPDATE_CURRENCY',
                    'type' => 'radiolist',
                    'name' => 'config_currency_auto',
                    'value' => isset($vars->item->config_currency_auto) && !is_null($vars->item->config_currency_auto) ? $vars->item->config_currency_auto : 1,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES')))
                ),
                'config_weight_class_id' => array(
                    'label' => 'J2STORE_STORE_CONFIG_WEIGHT',
                    'type' => 'fieldsql',
                    'name' => 'config_weight_class_id',
                    'value' => isset($vars->item->config_weight_class_id) && !is_null($vars->item->config_weight_class_id) ? $vars->item->config_weight_class_id : 1,
                    'options' => array('class' => 'form-select', 'key_field' => 'j2store_weight_id', 'value_field' => 'weight_title', 'has_one' => 'Weights')
                ),
                'config_length_class_id' => array(
                    'label' => 'J2STORE_STORE_CONFIG_LENGTH',
                    'type' => 'fieldsql',
                    'name' => 'config_length_class_id',
                    'value' => isset($vars->item->config_length_class_id) && !is_null($vars->item->config_length_class_id) ? $vars->item->config_length_class_id : 1,
                    'options' => array('class' => 'form-select', 'key_field' => 'j2store_length_id', 'value_field' => 'length_title', 'has_one' => 'Lengths')
                ),
            )
        );
        $vars->field_sets[] = array(
            'id' => 'product_settings',
            'class' => 'options-form',
            'label' => 'J2STORE_PRODUCT_DISPLAY_SETTINGS',
            'fields' => array(
                'catalog_mode' => array(
                    'label' => 'J2STORE_CONF_CATALOG_MODE_LABEL',
                    'type' => 'radiolist',
                    'name' => 'catalog_mode',
                    'value' => isset($vars->item->catalog_mode) && !is_null($vars->item->catalog_mode) ? $vars->item->catalog_mode : 0,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_CATALOG_MODE_DESC'
                ),
                'show_sku' => array(
                    'label' => 'J2STORE_CONF_SHOW_SKU_FIELD_LABEL',
                    'type' => 'radiolist',
                    'name' => 'show_sku',
                    'value' => isset($vars->item->show_sku) && !is_null($vars->item->show_sku) ? $vars->item->show_sku : 0,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_SHOW_SKU_FIELD_DESC'
                ),
                'show_manufacturer' => array(
                    'label' => 'J2STORE_CONF_SHOW_SHOW_MANUFACTURER_LABEL',
                    'type' => 'radiolist',
                    'name' => 'show_manufacturer',
                    'value' => isset($vars->item->show_manufacturer) && !is_null($vars->item->show_manufacturer) ? $vars->item->show_manufacturer : 0,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_SHOW_SHOW_MANUFACTURER_DESC'
                ),
                'show_qty_field' => array(
                    'label' => 'J2STORE_CONF_SHOW_QTY_FIELD_LABEL',
                    'type' => 'radiolist',
                    'name' => 'show_qty_field',
                    'value' => isset($vars->item->show_qty_field) && !is_null($vars->item->show_qty_field) ? $vars->item->show_qty_field : 1,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_SHOW_QTY_FIELD_DESC'
                ),
                'show_price_field' => array(
                    'label' => 'J2STORE_CONF_SHOW_PRICE_FIELD_LABEL',
                    'type' => 'radiolist',
                    'name' => 'show_price_field',
                    'value' => isset($vars->item->show_price_field) && !is_null($vars->item->show_price_field) ? $vars->item->show_price_field : 1,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_SHOW_PRICE_FIELD_DESC'
                ),
                'show_base_price' => array(
                    'label' => 'J2STORE_CONF_SHOW_BASE_PRICE_LABEL',
                    'type' => 'radiolist',
                    'name' => 'show_base_price',
                    'value' => isset($vars->item->show_base_price) && !is_null($vars->item->show_base_price) ? $vars->item->show_base_price : 1,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_SHOW_BASE_PRICE_DESC'
                ),
                'product_option_price' => array(
                    'label' => 'J2STORE_CONF_PRODUCT_OPTIONS_PRICE_LABEL',
                    'type' => 'radiolist',
                    'name' => 'product_option_price',
                    'value' => isset($vars->item->product_option_price) && !is_null($vars->item->product_option_price) ? $vars->item->product_option_price : 1,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JHIDE'), 1 => Text::_('JSHOW'))),
                    'desc' => 'J2STORE_CONF_PRODUCT_OPTIONS_PRICE_DESC'
                ),
                'product_option_price_prefix' => array(
                    'label' => 'J2STORE_CONF_PRODUCT_OPTIONS_PRICE_PREFIX_LABEL',
                    'type' => 'radiolist',
                    'name' => 'product_option_price_prefix',
                    'value' => isset($vars->item->product_option_price_prefix) && !is_null($vars->item->product_option_price_prefix) ? $vars->item->product_option_price_prefix : 1,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JHIDE'), 1 => Text::_('JSHOW'))),
                    'desc' => 'J2STORE_CONF_PRODUCT_OPTIONS_PRICE_PREFIX_DESC'
                ),
                'image_for_product_options' => array(
                    'label' => 'J2STORE_CONF_SHOW_IMAGE_FOR_PRODUCT_OPTIONS_LABEL',
                    'type' => 'radiolist',
                    'name' => 'image_for_product_options',
                    'value' => isset($vars->item->image_for_product_options) && !is_null($vars->item->image_for_product_options) ? $vars->item->image_for_product_options : 0,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JHIDE'), 1 => Text::_('JSHOW'))),
                    'desc' => 'J2STORE_CONF_SHOW_IMAGE_FOR_PRODUCT_OPTIONS_DESC'
                ),
                'related_product_columns' => array(
                    'label' => 'J2STORE_CONF_RELATED_PRODUCT_COLUMNS_LABEL',
                    'type' => 'number',
                    'name' => 'related_product_columns',
                    'value' => isset($vars->item->related_product_columns) && !is_null($vars->item->related_product_columns) ? $vars->item->related_product_columns : 3,
                    'options' => array('min' => '1','class' => 'form-control'),
                    'desc' => 'J2STORE_CONF_RELATED_PRODUCT_COLUMNS_DESC'
                ),
            )
        );
        $vars->field_sets[] = array(
            'id' => 'inventory_settings',
            'label' => 'J2STORE_INVENTORY_FIELDS',
            'class' => 'options-form',
            'is_pro' => true,
            'fields' => array(
                'enable_inventory' => array(
                    'label' => 'J2STORE_CONF_ENABLE_INVENTORY_LABEL',
                    'type' => 'radiolist',
                    'name' => 'enable_inventory',
                    'value' => isset($vars->item->enable_inventory) && !is_null($vars->item->enable_inventory) ? $vars->item->enable_inventory : 0,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_ENABLE_INVENTORY_DESC'
                ),
                'cancel_order' => array(
                    'label' => 'J2STORE_CONF_INVENTORY_CANCEL_ORDER_LABEL',
                    'type' => 'radiolist',
                    'name' => 'cancel_order',
                    'value' => isset($vars->item->cancel_order) && !is_null($vars->item->cancel_order) ? $vars->item->cancel_order : 0,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_INVENTORY_CANCEL_ORDER_DESC'
                ),
                'hold_stock' => array(
                    'label' => 'J2STORE_CONF_HOLD_STOCK_LABEL',
                    'type' => 'text',
                    'name' => 'hold_stock',
                    'value' => isset($vars->item->hold_stock) && !is_null($vars->item->hold_stock) ? $vars->item->hold_stock : 60,
                    'options' => array('class' => 'form-control'),
                    'desc' => 'J2STORE_CONF_HOLD_STOCK_LABEL'
                ),
                'stock_display_format' => array(
                    'label' => 'J2STORE_CONF_STOCK_DISPLAY_FORMAT_LABEL',
                    'type' => 'list',
                    'name' => 'stock_display_format',
                    'value' => isset($vars->item->stock_display_format) && !is_null($vars->item->stock_display_format) ? $vars->item->stock_display_format : 'always_show',
                    'options' => array('options' => array('always_show' => Text::_('J2STORE_STOCK_SELECTED_DISPLAY'),
                        'low_stock' => Text::_('J2STORE_STOCK_SHOW_LOW_STOCK'), 'no_display' => Text::_('J2STORE_STOCK_DO_NOT_DISPLAY'))),
                    'desc' => 'J2STORE_CONF_STOCK_DISPLAY_FORMAT_DESC'
                ),
                'store_min_sale_qty' => array(
                    'label' => 'J2STORE_PRODUCT_MIN_SALE_QUANTITY',
                    'type' => 'text',
                    'name' => 'store_min_sale_qty',
                    'value' => isset($vars->item->store_min_sale_qty) && !is_null($vars->item->store_min_sale_qty) ? $vars->item->store_min_sale_qty : 1,
                    'options' => array('class' => 'form-control'),
                    'desc' => 'J2STORE_PRODUCT_MIN_SALE_QUANTITY_DESC'
                ),
                'store_max_sale_qty' => array(
                    'label' => 'J2STORE_PRODUCT_MAX_SALE_QUANTITY',
                    'type' => 'text',
                    'name' => 'store_max_sale_qty',
                    'value' => isset($vars->item->store_max_sale_qty) && !is_null($vars->item->store_max_sale_qty) ? $vars->item->store_max_sale_qty : '',
                    'options' => array('class' => 'form-control'),
                    'desc' => 'J2STORE_PRODUCT_MAX_SALE_QUANTITY_DESC'
                ),
                'store_notify_qty' => array(
                    'label' => 'J2STORE_PRODUCT_NOTIFY_QUANTITY',
                    'type' => 'text',
                    'name' => 'store_notify_qty',
                    'value' => isset($vars->item->store_notify_qty) && !is_null($vars->item->store_notify_qty) ? $vars->item->store_notify_qty : '',
                    'options' => array('class' => 'form-control'),
                    'desc' => 'J2STORE_PRODUCT_NOTIFY_QUANTITY_DESC'
                ),
            )
        );
        $vars->field_sets[] = array(
            'id' => 'tax_settings',
            'label' => 'J2STORE_TAX_FIELDS',
            'class' => 'options-form',
            'fields' => array(
                'config_including_tax' => array(
                    'label' => 'J2STORE_CONF_INCLUDING_TAX_LABEL',
                    'type' => 'list',
                    'name' => 'config_including_tax',
                    'value' => isset($vars->item->config_including_tax) && !is_null($vars->item->config_including_tax) ? $vars->item->config_including_tax : 0,
                    'options' => array('class' => 'form-select', 'options' => array(0 => Text::_('J2STORE_PRICES_EXCLUDING_TAXES'), 1 => Text::_('J2STORE_PRICES_INCLUDING_TAXES'))),
                    'desc' => 'J2STORE_CONF_INCLUDING_TAX_DESC'
                ),
                'config_tax_default' => array(
                    'label' => 'J2STORE_CONF_CALCULATE_TAX_DEFAULT_LABEL',
                    'type' => 'list',
                    'name' => 'config_tax_default',
                    'value' => isset($vars->item->config_tax_default) && !is_null($vars->item->config_tax_default) ? $vars->item->config_tax_default : 'billing',
                    'options' => array('options' => array('billing' => Text::_('J2STORE_BILLING_ADDRESS'), 'shipping' => Text::_('J2STORE_SHIPPING_ADDRESS'))),
                    'desc' => 'J2STORE_CONF_CALCULATE_TAX_DEFAULT_DESC'
                ),
                'config_tax_default_address' => array(
                    'label' => 'J2STORE_CONF_TAX_DEFAULT_ADDRESS_LABEL',
                    'type' => 'list',
                    'name' => 'config_tax_default_address',
                    'value' => isset($vars->item->config_tax_default_address) && !is_null($vars->item->config_tax_default_address) ? $vars->item->config_tax_default_address : 'store',
                    'options' => array('options' => array('noaddress' => Text::_('J2STORE_NO_ADDRESS'), 'store' => Text::_('J2STORE_STORE_ADDRESS'))),
                    'desc' => 'J2STORE_CONF_TAX_DEFAULT_ADDRESS_DESC'
                ),
                'price_display_options' => array(
                    'label' => 'J2STORE_CONF_PRICE_DISPLAY_OPTIONS_LABEL',
                    'type' => 'list',
                    'name' => 'price_display_options',
                    'value' => isset($vars->item->price_display_options) && !is_null($vars->item->price_display_options) ? $vars->item->price_display_options : 1,
                    'options' => array('options' => array(1 => Text::_('J2STORE_CONFIG_PRICE_ONLY'), 2 => Text::_('J2STORE_CONFIG_PRICE_PLUS_TAX'))),
                    'desc' => 'J2STORE_CONF_PRICE_DISPLAY_OPTIONS_DESC'
                ),
                'display_price_with_tax_info' => array(
                    'label' => 'J2STORE_CONF_DISPLAY_PRICE_WITH_TAX_INFO_LABEL',
                    'type' => 'radiolist',
                    'name' => 'display_price_with_tax_info',
                    'value' => isset($vars->item->display_price_with_tax_info) && !is_null($vars->item->display_price_with_tax_info) ? $vars->item->display_price_with_tax_info : 0,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_DISPLAY_PRICE_WITH_TAX_INFO_DESC'
                ),
                'checkout_price_display_options' => array(
                    'label' => 'J2STORE_CONF_CHECKOUT_PRICE_DISPLAY_OPTIONS_LABEL',
                    'type' => 'list',
                    'name' => 'checkout_price_display_options',
                    'value' => isset($vars->item->checkout_price_display_options) && !is_null($vars->item->checkout_price_display_options) ? $vars->item->checkout_price_display_options : 0,
                    'options' => array('options' => array(0 => Text::_('J2STORE_CONFIG_EXCLUDING_TAX'), 1 => Text::_('J2STORE_CONFIG_INCLUDING_TAX'))),
                    'desc' => 'J2STORE_CONF_CHECKOUT_PRICE_DISPLAY_OPTIONS_DESC'
                ),
            )
        );
        $vars->field_sets[] = array(
            'id' => 'discount_settings',
            'label' => 'J2STORE_DISCOUNT_SETTINGS',
            'class' => 'options-form',
            'is_pro' => true,
            'fields' => array(
                'enable_coupon' => array(
                    'label' => 'J2STORE_CONF_ENABLE_COUPON_LABEL',
                    'type' => 'radiolist',
                    'name' => 'enable_coupon',
                    'value' => isset($vars->item->enable_coupon) && !is_null($vars->item->enable_coupon) ? $vars->item->enable_coupon : 0,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_ENABLE_COUPON_DESC'
                ),
                'enable_voucher' => array(
                    'label' => 'J2STORE_CONF_ENABLE_VOUCHER_LABEL',
                    'type' => 'radiolist',
                    'name' => 'enable_voucher',
                    'value' => isset($vars->item->enable_voucher) && !is_null($vars->item->enable_voucher) ? $vars->item->enable_voucher : 0,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_ENABLE_VOUCHER_DESC'
                ),
            )
        );
        $payment_model = J2Store::fof()->getModel('Payments', 'J2StoreModel');
        $default_payment_list = $payment_model->enabled(1)->getList();
        $payment_list = array();
        foreach ($default_payment_list as $payment) {
            $payment_list[$payment->element] = Text::_(strtoupper($payment->element));
        }
        $continue_shopping_page = isset($vars->item->config_continue_shopping_page) && !is_null($vars->item->config_continue_shopping_page) ? $vars->item->config_continue_shopping_page : 'previous';
        $vars->field_sets[] = array(
            'id' => 'cart_settings',
            'label' => 'J2STORE_CART_SETTINGS',
            'class' => 'options-form',
            'fields' => array(
                'addtocart_placement' => array(
                    'label' => 'J2STORE_CONF_ADDTOCART_PLACEMENT_LABEL',
                    'type' => 'list',
                    'name' => 'addtocart_placement',
                    'value' => isset($vars->item->addtocart_placement) && !is_null($vars->item->addtocart_placement) ? $vars->item->addtocart_placement : 'default',
                    'options' => array('class' => 'form-select', 'options' => array('default' => Text::_('J2STORE_CONF_OPTION_ADDTOCART_DEFAULT'), 'tag' => Text::_('J2STORE_CONF_OPTION_ADDTOCART_TAG'), 'both' => Text::_('J2STORE_CONF_OPTION_ADDTOCART_BOTH'))),
                    'desc' => 'J2STORE_CONF_ADDTOCART_PLACEMENT_LABEL'
                ),
                'addtocart_action' => array(
                    'label' => 'J2STORE_CONF_ADDTOCART_ACTION_LABEL',
                    'type' => 'list',
                    'name' => 'addtocart_action',
                    'value' => isset($vars->item->addtocart_action) && !is_null($vars->item->addtocart_action) ? $vars->item->addtocart_action : 1,
                    'options' => array('class' => 'form-select', 'options' => array(1 => Text::_('J2STORE_CONF_OPTION_INLINE'), 3 => Text::_('J2STORE_CONF_OPTION_REDIRECT'))),
                    'desc' => 'J2STORE_CONF_ADDTOCART_ACTION_DESC'
                ),
                'config_continue_shopping_page' => array(
                    'label' => 'J2STORE_CONF_CATALOG_CONTINUE_SHOPPING_LABEL',
                    'type' => 'list',
                    'name' => 'config_continue_shopping_page',
                    'value' => $continue_shopping_page,
                    'options' => array('id' => 'continue_shopping_page', 'options' => array('previous' => Text::_('J2STORE_TO_PREVIOUS_PAGE'), 'menu' => Text::_('J2STORE_TO_MENU'), 'url' => Text::_('J2STORE_TO_URL'))),
                    'desc' => 'J2STORE_CONF_CATALOG_CONTINUE_SHOPPING_DESC'
                ),
                'config_continue_shopping_page_url' => array(
                    'label' => 'J2STORE_CONF_CART_CONTINUE_SHOPPING_URL_LABEL',
                    'type' => 'text',
                    'name' => 'config_continue_shopping_page_url',
                    'value' => isset($vars->item->config_continue_shopping_page_url) && !is_null($vars->item->config_continue_shopping_page_url) ? $vars->item->config_continue_shopping_page_url : '',
                    'options' => array('id' => 'continue_shopping_url','class' => 'form-control'),
                    'desc' => 'J2STORE_CONF_CART_CONTINUE_SHOPPING_URL_DESC'
                ),
                'continue_shopping_page_menu' => array(
                    'label' => 'J2STORE_CONF_CART_CONTINUE_SHOPPING_MENU_LABEL',
                    'type' => 'menuitem',
                    'name' => 'continue_shopping_page_menu',
                    'value' => isset($vars->item->continue_shopping_page_menu) && !is_null($vars->item->continue_shopping_page_menu) ? $vars->item->continue_shopping_page_menu : 0,
                    'options' => array('id' => 'continue_shopping_menu'),
                    'desc' => 'J2STORE_CONF_CART_CONTINUE_SHOPPING_MENU_DESC'
                ),
                'addtocart_button_class' => array(
                    'label' => 'J2STORE_CONF_ADDTOCART_BUTTON_CLASS_LABEL',
                    'type' => 'text',
                    'name' => 'addtocart_button_class',
                    'value' => isset($vars->item->addtocart_button_class) && !is_null($vars->item->addtocart_button_class) ? $vars->item->addtocart_button_class : 'btn btn-primary',
                    'options' => array('class' => 'form-control'),
                    'desc' => 'J2STORE_CONF_ADDTOCART_BUTTON_CLASS_DESC'
                ),
                'config_cart_empty_redirect' => array(
                    'label' => 'J2STORE_CONF_CART_EMPTY_REDIRECT_LABEL',
                    'type' => 'list',
                    'name' => 'config_cart_empty_redirect',
                    'value' => isset($vars->item->config_cart_empty_redirect) && !is_null($vars->item->config_cart_empty_redirect) ? $vars->item->config_cart_empty_redirect : 'cart',
                    'options' => array('id' => 'cart_empty_redirect', 'options' => array('cart' => Text::_('J2STORE_TO_CART_VIEW'), 'menu' => Text::_('J2STORE_TO_MENU'), 'url' => Text::_('J2STORE_TO_URL'))),
                    'desc' => 'J2STORE_CONF_CART_EMPTY_REDIRECT_DESC'
                ),
                'continue_cart_redirect_menu' => array(
                    'label' => 'J2STORE_CONF_CONTINUE_CART_REDIRECT_MENU_LABEL',
                    'type' => 'menuitem',
                    'name' => 'continue_cart_redirect_menu',
                    'value' => isset($vars->item->continue_cart_redirect_menu) && !is_null($vars->item->continue_cart_redirect_menu) ? $vars->item->continue_cart_redirect_menu : 0,
                    'options' => array('id' => 'continue_cart_redirect_menu'),
                    'desc' => 'J2STORE_CONF_CONTINUE_CART_REDIRECT_MENU_DESC'
                ),
                'config_cart_redirect_page_url' => array(
                    'label' => 'J2STORE_CONF_CART_REDIRECT_PAGE_URL_LABEL',
                    'type' => 'text',
                    'name' => 'config_cart_redirect_page_url',
                    'value' => isset($vars->item->config_cart_redirect_page_url) && !is_null($vars->item->config_cart_redirect_page_url) ? $vars->item->config_cart_redirect_page_url : '',
                    'options' => array('id' => 'cart_redirect_page_url','class' => 'form-control'),
                    'desc' => 'J2STORE_CONF_CART_REDIRECT_PAGE_URL_DESC'
                ),
                'show_thumb_cart' => array(
                    'label' => 'J2STORE_CONF_SHOW_THUMB_CART_LABEL',
                    'type' => 'radiolist',
                    'name' => 'show_thumb_cart',
                    'value' => isset($vars->item->show_thumb_cart) && !is_null($vars->item->show_thumb_cart) ? $vars->item->show_thumb_cart : 0,
                    'options' => array('class' => 'radio-list', 'options' => array(0 => Text::_('JHIDE'), 3 => Text::_('JSHOW'))),
                    'desc' => 'J2STORE_CONF_SHOW_THUMB_CART_DESC'
                ),
                'show_item_tax' => array(
                    'label' => 'J2STORE_CONF_SHOW_ITEM_TAX_LABEL',
                    'type' => 'radiolist',
                    'name' => 'show_item_tax',
                    'value' => isset($vars->item->show_item_tax) && !is_null($vars->item->show_item_tax) ? $vars->item->show_item_tax : 0,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_SHOW_ITEM_TAX_DESC'
                ),
                'show_shipping_address' => array(
                    'label' => 'J2STORE_CONF_SHOW_SHIPPING_ADDRESS_LABEL',
                    'type' => 'radiolist',
                    'name' => 'show_shipping_address',
                    'value' => isset($vars->item->show_shipping_address) && !is_null($vars->item->show_shipping_address) ? $vars->item->show_shipping_address : 1,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_SHOW_SHIPPING_ADDRESS_DESC'
                ),
                'show_login_form' => array(
                    'label' => 'J2STORE_CONF_SHOW_LOGIN_FORM_LABEL',
                    'type' => 'radiolist',
                    'name' => 'show_login_form',
                    'value' => isset($vars->item->show_login_form) && !is_null($vars->item->show_login_form) ? $vars->item->show_login_form : 1,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_SHOW_LOGIN_FORM_DESC'
                ),
                'allow_registration' => array(
                    'label' => 'J2STORE_CONF_ALLOW_REGISTRATION_LABEL',
                    'type' => 'radiolist',
                    'name' => 'allow_registration',
                    'value' => isset($vars->item->allow_registration) && !is_null($vars->item->allow_registration) ? $vars->item->allow_registration : 1,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_ALLOW_REGISTRATION_DESC'
                ),
                'allow_password_validation' => array(
                    'label' => 'J2STORE_CONF_ALLOW_PASSWORD_VALIDATION_LABEL',
                    'type' => 'radiolist',
                    'name' => 'allow_password_validation',
                    'value' => isset($vars->item->allow_password_validation) && !is_null($vars->item->allow_password_validation) ? $vars->item->allow_password_validation : 1,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_ALLOW_PASSWORD_VALIDATION_DESC'
                ),
                'allow_guest_checkout' => array(
                    'label' => 'J2STORE_CONF_ALLOW_GUEST_CHECKOUT_LABEL',
                    'type' => 'radiolist',
                    'name' => 'allow_guest_checkout',
                    'value' => isset($vars->item->allow_guest_checkout) && !is_null($vars->item->allow_guest_checkout) ? $vars->item->allow_guest_checkout : 0,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_ALLOW_GUEST_CHECKOUT_DESC'
                ),
                'show_customer_note' => array(
                    'label' => 'J2STORE_CONF_SHOW_CUSTOMER_NOTE_LABEL',
                    'type' => 'radiolist',
                    'name' => 'show_customer_note',
                    'value' => isset($vars->item->show_customer_note) && !is_null($vars->item->show_customer_note) ? $vars->item->show_customer_note : 1,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_SHOW_CUSTOMER_NOTE_DESC'
                ),
                'show_tax_calculator' => array(
                    'label' => 'J2STORE_CONF_SHOW_TAX_CALCULATOR_LABEL',
                    'type' => 'radiolist',
                    'name' => 'show_tax_calculator',
                    'value' => isset($vars->item->show_tax_calculator) && !is_null($vars->item->show_tax_calculator) ? $vars->item->show_tax_calculator : 1,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_SHOW_TAX_CALCULATOR_DESC'
                ),
                'show_clear_cart_button' => array(
                    'label' => 'J2STORE_CONF_SHOW_CLEAR_CART_BUTTON_LABEL',
                    'type' => 'radiolist',
                    'name' => 'show_clear_cart_button',
                    'value' => isset($vars->item->show_clear_cart_button) && !is_null($vars->item->show_clear_cart_button) ? $vars->item->show_clear_cart_button : 0,
                    'options' => array('class' => 'radio-list', 'options' => array(0 => Text::_('JHIDE'), 3 => Text::_('JSHOW'))),
                    'desc' => 'J2STORE_CONF_SHOW_CLEAR_CART_BUTTON_DESC'
                ),
                'postalcode_required' => array(
                    'label' => 'J2STORE_CONF_MAKE_POSTALCODE_REQUIRED_LABEL',
                    'type' => 'radiolist',
                    'name' => 'postalcode_required',
                    'value' => isset($vars->item->postalcode_required) && !is_null($vars->item->postalcode_required) ? $vars->item->postalcode_required : 1,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_MAKE_POSTALCODE_REQUIRED_DESC'
                ),
                'clear_cart' => array(
                    'label' => 'J2STORE_CONF_CLEAR_CART_LABEL',
                    'type' => 'list',
                    'name' => 'clear_cart',
                    'value' => isset($vars->item->clear_cart) && !is_null($vars->item->clear_cart) ? $vars->item->clear_cart : 'order_placed',
                    'options' => array('options' => array('order_placed' => Text::_('J2STORE_ON_PLACEMENT_OF_ORDER'), 'order_confirmed' => Text::_('J2STORE_ON_PAYMENT_CONFIRMATION'))),
                    'desc' => 'J2STORE_CONF_CLEAR_CART_DESC',
                    'class'=>'form-select'
                ),
                'default_payment_method' => array(
                    'label' => 'J2STORE_CONF_DEFAULT_PAYMENT_METHOD_LABEL',
                    'type' => 'list',
                    'name' => 'default_payment_method',
                    'value' => isset($vars->item->default_payment_method) && !is_null($vars->item->default_payment_method) ? $vars->item->default_payment_method : '',
                    'options' => array('options' => $payment_list),
                    'desc' => 'J2STORE_CONF_DEFAULT_PAYMENT_METHOD_DESC'
                ),
                'shipping_mandatory' => array(
                    'label' => 'J2STORE_CONF_SHIPPING_MANDATORY_LABEL',
                    'type' => 'radiolist',
                    'name' => 'shipping_mandatory',
                    'value' => isset($vars->item->shipping_mandatory) && !is_null($vars->item->shipping_mandatory) ? $vars->item->shipping_mandatory : 0,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_SHIPPING_MANDATORY_DESC'
                ),
                'auto_apply_shipping_rate' => array(
                    'label' => 'J2STORE_CONF_J2STORE_AUTO_APPLY_SHIPPING',
                    'type' => 'radiolist',
                    'name' => 'auto_apply_shipping_rate',
                    'value' => isset($vars->item->auto_apply_shipping_rate) && !is_null($vars->item->auto_apply_shipping_rate) ? $vars->item->auto_apply_shipping_rate : 0,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_J2STORE_AUTO_APPLY_SHIPPING_DESC'
                ),
                'hide_shipping_until_address_selection' => array(
                    'label' => 'J2STORE_CONF_AUTO_CALCULATE_SHIPPING_LABEL',
                    'type' => 'radiolist',
                    'name' => 'hide_shipping_until_address_selection',
                    'value' => isset($vars->item->hide_shipping_until_address_selection) && !is_null($vars->item->hide_shipping_until_address_selection) ? $vars->item->hide_shipping_until_address_selection : 1,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_AUTO_CALCULATE_SHIPPING_DESC'
                ),
                'clear_outdated_cart_data_term' => array(
                    'label' => 'J2STORE_CONF_CLEAR_OUTDATED_CART_DATA_TERM_LABEL',
                    'type' => 'list',
                    'name' => 'clear_outdated_cart_data_term',
                    'value' => isset($vars->item->clear_outdated_cart_data_term) && !is_null($vars->item->clear_outdated_cart_data_term) ? $vars->item->clear_outdated_cart_data_term : 90,
                    'options' => array('options' => array(7 => Text::_('J2STORE_CONF_CLEAR_CART_BEFORE_ONE_WEEK'), 14 => Text::_('J2STORE_CONF_CLEAR_CART_BEFORE_FOURTEEN'), 30 => Text::_('J2STORE_CONF_CLEAR_CART_BEFORE_THIRTY'),
                        60 => Text::_('J2STORE_CONF_CLEAR_CART_BEFORE_SIXTY'), 90 => Text::_('J2STORE_CONF_CLEAR_CART_BEFORE_NINETY'))),
                    'desc' => 'J2STORE_CONF_CLEAR_OUTDATED_CART_DATA_TERM_DESC'
                ),
            )
        );
        $vars->field_sets[] = array(
            'id' => 'checkout_layout',
            'label' => 'J2STORE_STORE_CHECKOUT_LAYOUT',
            'class' => 'options-form',
            'fields' => array(
                'populate_button' => array(
                    'type' => 'button',
                    'name' => 'populate_button',
                    'value' => Text::_('J2STORE_PREPOPULATE_CHECKOUT_LAYOUT'),
                    'options' => array('hiddenLabel'=>true,'class' => 'btn btn-primary', 'onclick' => 'Joomla.submitbutton(\'populatedata\');'),
                ),
                'store_billing_layout' => array(
                    'label' => 'J2STORE_STORE_BILLING_LAYOUT_LABEL',
                    'type' => 'textarea',
                    'name' => 'store_billing_layout',
                    'value' => isset($vars->item->store_billing_layout) && !is_null($vars->item->store_billing_layout) ? htmlspecialchars($vars->item->store_billing_layout) : '',
                    'options' => array('class' => 'form-control', 'rows' => '10'),
                ),
                'store_shipping_layout' => array(
                    'label' => 'J2STORE_STORE_SHIPPING_LAYOUT_LABEL',
                    'type' => 'textarea',
                    'name' => 'store_shipping_layout',
                    'value' => isset($vars->item->store_shipping_layout) && !is_null($vars->item->store_shipping_layout) ? htmlspecialchars($vars->item->store_shipping_layout) : '',
                    'options' => array('class' => 'form-control', 'rows' => '10'),
                ),
                'store_payment_layout' => array(
                    'label' => 'J2STORE_STORE_PAYMENT_LAYOUT_LABEL',
                    'type' => 'textarea',
                    'name' => 'store_payment_layout',
                    'value' => isset($vars->item->store_payment_layout) && !is_null($vars->item->store_payment_layout) ? htmlspecialchars($vars->item->store_payment_layout) : '',
                    'options' => array('class' => 'form-control', 'rows' => '10'),
                ),
            )
        );
        $order_status_model = J2Store::fof()->getModel('Orderstatuses', 'J2StoreModel');
        $default_order_status_list = $order_status_model->enabled(1)->getList();
        $order_status = array();
        $order_status['*'] = Text::_('JALL');
        foreach ($default_order_status_list as $status) {
            $order_status[$status->j2store_orderstatus_id] = Text::_(strtoupper($status->orderstatus_name));
        }

        $vars->field_sets[] = array(
            'id' => 'order_settings',
            'label' => 'J2STORE_ORDER_SETTINGS',
            'class' => 'options-form',
            'fields' => array(
                'invoice_prefix' => array(
                    'label' => 'J2STORE_INVOICE_PREFIX',
                    'type' => 'text',
                    'name' => 'invoice_prefix',
                    'value' => isset($vars->item->invoice_prefix) && !is_null($vars->item->invoice_prefix) ? $vars->item->invoice_prefix : '',
                    'options' => array('class' => 'form-control')
                ),
                'show_postpayment_orderlink' => array(
                    'label' => 'J2STORE_CONF_SHOW_POSTPAYMENT_ORDERLINK_LABEL',
                    'type' => 'radiolist',
                    'name' => 'show_postpayment_orderlink',
                    'value' => isset($vars->item->show_postpayment_orderlink) && !is_null($vars->item->show_postpayment_orderlink) ? $vars->item->show_postpayment_orderlink : 1,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_SHOW_POSTPAYMENT_ORDERLINK_DESC'
                ),
                'download_area' => array(
                    'label' => 'J2STORE_CONF_SHOW_DOWNLOAD_AREA_LABEL',
                    'type' => 'list',
                    'name' => 'download_area',
                    'value' => isset($vars->item->download_area) && !is_null($vars->item->download_area) ? $vars->item->download_area : 1,
                    'options' => array('class' => 'form-select', 'options' => array(0 => Text::_('JHIDE'), 1 => Text::_('JSHOW'))),
                    'desc' => 'J2STORE_CONF_SHOW_DOWNLOAD_AREA_DESC'
                ),
                'limit_orderstatuses' => array(
                    'label' => 'J2STORE_CONF_LIMIT_ORDERSTATUSES_LABEL',
                    'type' => 'list',
                    'name' => 'limit_orderstatuses[]',
                    'value' => isset($vars->item->limit_orderstatuses) && !is_null($vars->item->limit_orderstatuses) ? $vars->item->limit_orderstatuses : '*',
                    'options' => array('class' => 'form-select','multiple' => true, 'options' => $order_status),
                    'desc' => 'J2STORE_CONF_LIMIT_ORDERSTATUSES_DESC'
                ),
                'show_thumb_email' => array(
                    'label' => 'J2STORE_CONF_SHOW_THUMB_EMAIL_LABEL',
                    'type' => 'radiolist',
                    'name' => 'show_thumb_email',
                    'value' => isset($vars->item->show_thumb_email) && !is_null($vars->item->show_thumb_email) ? $vars->item->show_thumb_email : 0,
                    'options' => array('class' => 'radio-list', 'options' => array(0 => Text::_('JHIDE'), 1 => Text::_('JSHOW'))),
                    'desc' => 'J2STORE_CONF_SHOW_THUMB_EMAIL_DESC'
                ),
                'show_logout_myprofile' => array(
                    'label' => 'J2STORE_CONF_SHOW_LOGOUT_MYPROFILE_LABEL',
                    'type' => 'radiolist',
                    'name' => 'show_logout_myprofile',
                    'value' => isset($vars->item->show_logout_myprofile) && !is_null($vars->item->show_logout_myprofile) ? $vars->item->show_logout_myprofile : 0,
                    'options' => array('class' => 'radio-list', 'options' => array(0 => Text::_('JHIDE'), 1 => Text::_('JSHOW'))),
                    'desc' => 'J2STORE_CONF_SHOW_LOGOUT_MYPROFILE_DESC'
                ),
                'backend_voucher_to_shipping' => array(
                    'label' => 'J2STORE_BACKEND_VOUCHER_TO_SHIPPING_LABEL',
                    'type' => 'radiolist',
                    'name' => 'backend_voucher_to_shipping',
                    'value' => isset($vars->item->backend_voucher_to_shipping) && !is_null($vars->item->backend_voucher_to_shipping) ? $vars->item->backend_voucher_to_shipping : 1,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES')))
                ),
                'export_column_per_product_option' => array(
                    'label' => 'J2STORE_CONF_EXPORT_PRODUCT_OPTIONS_PER_COLUMN_LABEL',
                    'type' => 'radiolist',
                    'name' => 'export_column_per_product_option',
                    'value' => isset($vars->item->export_column_per_product_option) && !is_null($vars->item->export_column_per_product_option) ? $vars->item->export_column_per_product_option : 0,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_EXPORT_PRODUCT_OPTIONS_PER_COLUMN_DESC'
                ),
            )
        );

        $vars->field_sets[] = array(
            'id' => 'mail_template_settings',
            'is_pro' => true,
            'label' => 'J2STORE_MAIL_TEMPLATE_SETTINGS',
            'class' => 'options-form',
            'fields' => array(
                'send_default_email_template' => array(
                    'label' => 'J2STORE_CONF_SEND_DEFAULT_EMAIL_TEMPLATE',
                    'type' => 'list',
                    'name' => 'send_default_email_template',
                    'value' => isset($vars->item->send_default_email_template) && !is_null($vars->item->send_default_email_template) ? $vars->item->send_default_email_template : 1,
                    'options' => array('class' => 'form-select', 'options' => array(0 => Text::_('J2STORE_ONLY_SEND_CONFIGURED_MAIL_TEMPLATES'), 1 => Text::_('J2STORE_SEND_DEFAULT_MAIL'))),
                    'desc' => 'J2STORE_CONF_SEND_DEFAULT_EMAIL_TEMPLATE_DESC'
                ),
            )
        );
        $vars->field_sets[] = array(
            'id' => 'update_settings',
            'is_pro' => true,
            'label' => 'J2STORE_UPDATE_SETTINGS',
            'class' => 'options-form',
            'fields' => array(
                'downloadid' => array(
                    'label' => 'J2STORE_CONF_UPDATE_DOWNLOADID',
                    'type' => 'text',
                    'name' => 'downloadid',
                    'value' => isset($vars->item->downloadid) && !is_null($vars->item->downloadid) ? $vars->item->downloadid : '',
                    'options' => array('id' => 'downloadid', 'class' => 'form-control'),
                    'desc' => 'J2STORE_CONF_UPDATE_DOWNLOADID_DESC'
                ),
            )
        );
        $vars->field_sets[] = array(
            'id' => 'misc_settings',
            'label' => 'J2STORE_MISC_SETTINGS',
            'class' => 'options-form',
            'fields' => array(
                'show_terms' => array(
                    'label' => 'J2STORE_CONF_SHOW_TERMS_LABEL',
                    'type' => 'radiolist',
                    'name' => 'show_terms',
                    'value' => isset($vars->item->show_terms) && !is_null($vars->item->show_terms) ? $vars->item->show_terms : 1,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_SHOW_TERMS_DESC'
                ),
                'terms_display_type' => array(
                    'label' => 'J2STORE_CONF_TERMS_DISPLAY_TYPE_LABEL',
                    'type' => 'list',
                    'name' => 'terms_display_type',
                    'value' => isset($vars->item->terms_display_type) && !is_null($vars->item->terms_display_type) ? $vars->item->terms_display_type : 'link',
                    'options' => array('class' => 'form-select', 'options' => array('link' => Text::_('J2STORE_CONF_TERMS_DISPLAY_OPTION_LINK'), 'checkbox' => Text::_('J2STORE_CONF_TERMS_DISPLAY_OPTION_CHECKBOX'))),
                    'desc' => 'J2STORE_CONF_TERMS_DISPLAY_TYPE_DESC'
                ),
                'termsid' => array(
                    'label' => 'J2STORE_CONF_TERMSID_LABEL',
                    'type' => 'modal_article',
                    'name' => 'termsid',
                    'value' => isset($vars->item->termsid) && !is_null($vars->item->termsid) ? $vars->item->termsid : 0,
                    'options' => array(),
                    'desc' => 'J2STORE_CONF_TERMSID_DESC'
                ),
                'prepare_content' => array(
                    'label' => 'J2STORE_CONF_PREPARE_CONTENT_LABEL',
                    'type' => 'radiolist',
                    'name' => 'prepare_content',
                    'value' => isset($vars->item->prepare_content) && !is_null($vars->item->prepare_content) ? $vars->item->prepare_content : 0,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_PREPARE_CONTENT_DESC'
                ),
                'enable_falang_support' => array(
                    'label' => 'J2STORE_CONF_ENABLE_FALANG_SUPPORT_LABEL',
                    'type' => 'radiolist',
                    'name' => 'enable_falang_support',
                    'value' => isset($vars->item->enable_falang_support) && !is_null($vars->item->enable_falang_support) ? $vars->item->enable_falang_support : 0,
                    'options' => array('class' => 'radio-list','options' => array(0 => Text::_('JNO'), 1 => Text::_('JYES'))),
                    'desc' => 'J2STORE_CONF_ENABLE_FALANG_SUPPORT_DESC'
                ),
            )
        );
        echo $this->_getLayout('tab', $vars, 'edit');
        $platform->addInlineScript('
            document.addEventListener("DOMContentLoaded", function() {
                const continueShoppingPage = document.querySelector("#continue_shopping_page");
                const cartEmptyRedirect = document.querySelector("#cart_empty_redirect");

                if (continueShoppingPage) {
                    continueShoppingPage.addEventListener("change", function() {
                        const continueShoppingUrl = document.querySelector("#continue_shopping_url")?.closest(".control-group");
                        const continueShoppingMenu = document.querySelector("#continue_shopping_menu")?.closest(".control-group");

                        if (this.value === "previous") {
                            if (continueShoppingUrl) continueShoppingUrl.style.display = "none";
                            if (continueShoppingMenu) continueShoppingMenu.style.display = "none";
                        } else if (this.value === "menu") {
                            if (continueShoppingMenu) continueShoppingMenu.style.display = "";
                            if (continueShoppingUrl) continueShoppingUrl.style.display = "none";
                        } else if (this.value === "url") {
                            if (continueShoppingUrl) continueShoppingUrl.style.display = "";
                            if (continueShoppingMenu) continueShoppingMenu.style.display = "none";
                        }
                    });
                }

                if (cartEmptyRedirect) {
                    cartEmptyRedirect.addEventListener("change", function() {
                        console.log(this.value);
                        const cartRedirectMenu = document.querySelector("#continue_cart_redirect_menu")?.closest(".control-group");
                        const cartRedirectPageUrl = document.querySelector("#cart_redirect_page_url")?.closest(".control-group");

                        if (this.value === "cart") {
                            if (cartRedirectMenu) cartRedirectMenu.style.display = "none";
                            if (cartRedirectPageUrl) cartRedirectPageUrl.style.display = "none";
                        } else if (this.value === "menu") {
                            if (cartRedirectMenu) cartRedirectMenu.style.display = "";
                            if (cartRedirectPageUrl) cartRedirectPageUrl.style.display = "none";
                        } else if (this.value === "url") {
                            if (cartRedirectPageUrl) cartRedirectPageUrl.style.display = "";
                            if (cartRedirectMenu) cartRedirectMenu.style.display = "none";
                        }
                    });
                }
            });
        ');

        $platform->addInlineScript("(function($) {
		$(document).on('click', '#j2store_testemail', function(e) {
			e.preventDefault();
			var email = $('#admin_email').val();
			$.ajax({
				url: 'index.php?option=com_j2store&view=configurations&task=testemail&admin_email='+email,
				dataType: 'json',
				beforeSend: function() {
					$('#email_message').remove();
					$('#j2store_testemail').after('<span class=\"wait\">&nbsp;<img src=\"" . Uri::root(true) . "/media/j2store/images/loader.gif\" alt=\"\" /></span>');
                },
                complete: function() {
                    $('.wait').remove();
                },
                success: function(json) {
                    if(json['success']){
                        $('#j2store_testemail').before(\"<div id='email_message'><span class='text-success'>\"+json['success']+\"</span><br></div>\");
                    }
                    if(json['error']){
                        $('#j2store_testemail').before(\"<div id='email_message'><span class='text-error'>\"+json['error']+\"</span><br></div>\");
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                }
            });
        });
    })(j2store.jQuery);");
        echo '<script>jQuery("#continue_shopping_page").trigger("change");
            jQuery("#cart_empty_redirect").trigger(\'change\');
            jQuery("#j2store_country_id").trigger("change");
			jQuery("#j2store_zone_id").trigger("liszt:updated");
    </script>';
    }

    /**
     * Method to cancel(non-PHPdoc)
     * @see F0FController::cancel()
     */
    public function cancel()
    {
        $platform = J2Store::platform();
        $url = 'index.php?option=com_j2store&view=cpanels';
        $platform->redirect($url);
    }

    /**
     * Method to save data
     * (non-PHPdoc)
     * @see F0FController::save()
     */
    public function save()
    {
        //security check
        Session::checkToken() or die('Invalid Token');
        $app = Factory::getApplication();
        $data = $app->input->getArray($_POST);
        $task = $this->getTask();
        $token = Session::getFormToken();
        unset($data['option']);
        unset($data['task']);
        unset($data['view']);
        unset($data[$token]);
        if ($task == 'populatedata') {
            $this->getPopulatedData($data);
        }
        $db = Factory::getContainer()->get('DatabaseDriver');
        $config = J2Store::config();
        $query = 'REPLACE INTO #__j2store_configurations (config_meta_key,config_meta_value) VALUES ';

        $filter = InputFilter::getInstance(array(), array(), 1, 1);
        $conditions = array();
        foreach ($data as $metakey => $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            //now clean up the value
            if ($metakey === 'store_billing_layout' || $metakey === 'store_shipping_layout' || $metakey === 'store_payment_layout') {
                $value = $app->input->get($metakey, '', 'raw');
                $clean_value = $filter->clean($value, 'html');

            } else {
                $clean_value = $filter->clean($value, 'string');
            }
            $config->set($metakey, $clean_value);
            $conditions[] = '(' . $db->q(strip_tags($metakey)) . ',' . $db->q($clean_value) . ')';
        }

        $query .= implode(',', $conditions);

        try {
            $db->setQuery($query);
            $db->execute();
            //update currencies
            J2Store::fof()->getModel('Currencies', 'J2StoreModel')->updateCurrencies(false);
            $msg = Text::_('J2STORE_CHANGES_SAVED');
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $msgType = 'Warning';
        }

        switch ($task) {
            case 'apply':
                $url = 'index.php?option=com_j2store&view=configuration';
                break;
            case 'populatedata':
                $url = 'index.php?option=com_j2store&view=configuration';
                break;
            case 'save':
                $url = 'index.php?option=com_j2store&view=cpanels';
                break;
        }
        J2Store::utilities()->clear_cache();
        J2Store::platform()->redirect($url, $msg, $msgType);
    }

    function getPopulatedData(&$data)
    {
        $data['store_billing_layout'] = '<div class="row">
		<div class="col-md-6">[first_name] [last_name] [email] [phone_1] [phone_2] [company] [tax_number]</div>
		<div class="col-md-6">[address_1] [address_2] [city] [zip] [country_id] [zone_id]</div>
		</div>';
        $data['store_shipping_layout'] = '<div class="row">
		<div class="col-md-6">[first_name] [last_name] [phone_1] [phone_2] [company]</div>
		<div class="col-md-6">[address_1] [address_2] [city] [zip] [country_id] [zone_id]</div>
		</div>';

        $app = Factory::getApplication();
        $app->input->set('store_billing_layout', $data['store_billing_layout']);
        $app->input->set('store_shipping_layout', $data['store_shipping_layout']);

    }

    function testemail()
    {
        $app = Factory::getApplication();
        //get the config class obj
        $config = Factory::getApplication()->getConfig();
        $json = array();
        $email = $app->input->getString('admin_email', '');

        if (isset($email) && empty($email)) {
            $json['error'] = Text::_('J2STORE_TEST_ADMIN_EMAIL_FIELD_EMPTY');
        } else {
            $admin_emails = explode(',', $email);
            //get the mailer class object
            $mailer = Factory::getContainer()->get(MailerFactoryInterface::class)->createMailer();
            foreach ($admin_emails as $admin_email) {
                $mailer->addRecipient($admin_email);
            }
            $sitename = $config->get('sitename');
            $subject = Text::sprintf("J2STORE_TEST_ADMIN_EMAIL_SUBJECT", $sitename);
            $body = Text::sprintf("J2STORE_TEST_ADMIN_EMAIL_BODY", $sitename);
            $mailer->setSubject($subject);
            $mailer->setBody($body);
            $mailer->IsHTML(1);
            $mailfrom = $config->get('mailfrom');
            $fromname = $config->get('fromname');
            $mailer->setSender(array($mailfrom, $fromname));

            if ($mailer->send()) {
                $json['success'] = Text::_('J2STORE_TEST_ADMIN_EMAIL_SUCCESS');
            } else {
                $json['error'] = Text::_('J2STORE_TEST_ADMIN_EMAIL_SUCCESS');
            }
        }
        echo json_encode($json);
        $app->close();
    }

    public function regenerateQueuekey()
    {
        $app = Factory::getApplication();
        $config = J2Store::config();
        $queue_string = Factory::getApplication()->getConfig()->get('sitename', '') . time();
        $queue_key = md5($queue_string);
        $config->saveOne('queue_key', $queue_key);
        $json = array(
            'queue_key' => $queue_key
        );
        echo json_encode($json);
        $app->close();
    }
}

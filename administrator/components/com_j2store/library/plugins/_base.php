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
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

/** Import library dependencies */
if (!defined('F0F_INCLUDED')) {
    require_once JPATH_LIBRARIES . '/f0f/include.php';
}
require_once(JPATH_ADMINISTRATOR . '/components/com_j2store/helpers/j2store.php');

class J2StorePluginBase extends CMSPlugin
{
    /**
     * @var $_element  string  Should always correspond with the plugin's filename,
     *                         forcing it to be unique
     */
    var $_element = '';

    var $_row = '';

    /**
     * Checks to make sure that this plugin is the one being triggered by the extension
     *
     * @access public
     * @return bool Parameter value
     * @since 2.5
     */
    function _isMe($row)
    {
        $element = $this->_element;

        $success = false;
        if (is_object($row) && !empty($row->element) && $row->element == $element) {
            $success = true;
        }

        if (is_string($row) && $row == $element) {
            $success = true;
        }

        return $success;
    }

    protected function _getMe()
    {
        if (empty($this->_row)) {
            $this->_row = J2Store::fof()->loadTable('Shipping', 'J2StoreTable', array('element' => $this->_element, 'folder' => 'j2store'));
        }
        return $this->_row;
    }

    /**
     * Prepares variables for the form
     *
     * @return string   HTML to display
     */
    function _renderForm($data)
    {
        $vars = new \stdClass();
        return $this->_getLayout('form', $vars);
    }

    /**
     * Prepares the 'view' tmpl layout
     *
     * @param array
     * @return string   HTML to display
     */
    function _renderView($options)
    {
        $vars = new \stdClass();
        return $this->_getLayout('view', $vars);
    }

    /**
     * Wraps the given text in the HTML
     *
     * @param string $message
     * @return string
     * @access protected
     */
    function _renderMessage($message = '')
    {
        $vars = new \stdClass();
        $vars->message = $message;
        return $this->_getLayout('message', $vars);
    }

    /**
     * Gets the parsed layout file
     *
     * @param string $layout The name of  the layout file
     * @param object $vars Variables to assign to
     * @param string $plugin The name of the plugin
     * @param string $group The plugin's group
     * @return string
     * @access protected
     */
    function _getLayout($layout, $vars = false, $plugin = '', $group = 'j2store')
    {
        if (empty($plugin)) {
            $plugin = $this->_element;
        }

        ob_start();
        $layout = $this->_getLayoutPath($plugin, $group, $layout, $vars);
        include($layout);
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    /**
     * Get the path to a layout file
     *
     * @param string $plugin The name of the plugin file
     * @param string $group The plugin's group
     * @param string $layout The name of the plugin layout file
     * @return  string  The path to the plugin layout file
     * @access protected
     * @throws Exception
     */
    function _getLayoutPath($plugin, $group, $layout = 'default', $vars = false)
    {
        $app = J2Store::platform()->application();
        // get the template and default paths for the layout
        $templatePath = JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/plugins/' . $group . '/' . $plugin . '/' . $layout . '.php';
        $defaultPath = JPATH_SITE . '/plugins/' . $group . '/' . $plugin . '/' . $plugin . '/tmpl/' . $layout . '.php';

        // if the site template has a layout override, use it

        if (file_exists($templatePath)) {
            return $templatePath;
        } else {
            return $defaultPath;
        }
    }

    /**
     * This displays the content article
     * specified in the plugin's params
     *
     * @return string
     */

    function _displayArticle()
    {
        $html = '';
        $article_id = (int)$this->params->get('articleid');
        if ($article_id && is_numeric($article_id)) {
            $html = J2Store::article()->display($article_id);
        }
        return $html;
    }

    /**
     * Checks for a form token in the request
     * Using a suffix enables multi-step forms
     *
     * @param string $suffix
     * @param string $method
     * @return boolean
     */
    function _checkToken($suffix = '', $method = 'post')
    {
        $token = Session::getFormToken();
        $token .= "." . strtolower($suffix);
        $app = J2Store::platform()->application();
        if ($app->input->get($token, '', $method, 'alnum')) {
            return true;
        }
        return false;
    }

    /**
     * Generates an HTML form token and affixes a suffix to the token
     * enabling the form to be identified as a step in a process
     *
     * @param string $suffix
     * @return string HTML
     */
    function _getToken($suffix = '')
    {
        $token = Session::getFormToken();
        $token .= "." . strtolower($suffix);
        $html = '<input type="hidden" name="' . $token . '" value="1" />';
        $html .= '<input type="hidden" name="tokenSuffix" value="' . $suffix . '" />';
        return $html;
    }

    /**
     * Gets the suffix affixed to the form's token
     * which helps identify which step this is
     * in a multi-step process
     *
     * @return string
     */
    function _getTokenSuffix($method = 'post')
    {
        $app = J2Store::platform()->application();
        $suffix = $app->input->get('tokenSuffix', '');
        if (!$this->_checkToken($suffix, $method)) {
            // what to do if there isn't this suffix's token in the request?
            // anything?
        }
        return $suffix;
    }

    function onJ2StoreCustomTablePath(&$paths)
    {
        $paths[] = JPATH_SITE . '/plugins/j2store/' . $this->_element . '/' . $this->_element . '/tables';
    }

    function onJ2StoreCustomModelPath(&$paths)
    {
        $paths[] = JPATH_SITE . '/plugins/j2store/' . $this->_element . '/' . $this->_element . '/models';
    }

    /**
     * Include a particular Custom Model
     * @param $name - name of the model
     * @param $plugin - name of the plugin in which the model is stored
     * @param $group - group of the plugin
     */
    protected function includeCustomModel($name, $plugin = '', $group = 'j2store')
    {
        if (empty($plugin)) {
            $plugin = $this->_element;
        }

        if (!class_exists('J2StoreModel' . $name)) {
            JLoader::import('plugins.' . $group . '.' . $plugin . '.' . $plugin . '.models.' . strtolower($name), JPATH_SITE);
        }
        J2Store::fof()->loadModelFilePath(JPATH_SITE . '/plugins/j2store/' . $this->_element . '/' . $this->_element . '/models');
    }

    public function getCountryById($country_id)
    {
        $fof_helper = J2Store::fof();
        return $fof_helper->loadTable('Country', 'J2StoreTable', array('j2store_country_id' => (int)$country_id));
    }

    public function getZoneById($zone_id)
    {
        $fof_helper = J2Store::fof();
        return $fof_helper->loadTable('Zone', 'J2StoreTable', array('j2store_zone_id' => (int)$zone_id));
    }

    /**
     * Load table object
     */
    public function getTable($table_name, $table_condition = array())
    {
        $table_name = ucfirst($table_name);
        $fof_helper = J2Store::fof();
        return $fof_helper->loadTable($table_name, 'J2StoreTable', $table_condition);
    }

    /**
     * Clean text
     */
    public function clean_title($text)
    {
        $text = str_replace('"', '', $text);
        $text = str_replace("'", '', $text);
        return $text;
    }

    /**
     * Gets admins data
     *
     * @return array|boolean
     * @access protected
     * @throws Exception
     */
    function _getAdmins()
    {
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('u.name, u.email');
        $query->from('#__users AS u');
        $query->join('LEFT', '#__user_usergroup_map AS ug ON u.id=ug.user_id');
        $query->where('u.sendEmail = 1');
        $query->where('ug.group_id = 8');

        $db->setQuery($query);
        $admins = $db->loadObjectList();
        if ($error = $db->getErrorMsg()) {
            Factory::getApplication()->enqueueMessage($error);
            return false;
        }

        return $admins;
    }

    /**
     * Simple logger
     *
     * @param string $text
     * @param string $type
     * @return void
     */
    function _log($text, $type = 'message')
    {
        if ($this->_isLog) {
            if (is_array($text) || is_object($text)) {
                $text = json_encode($text);
            }
            $file = JPATH_ROOT . "/cache/{$this->_element}.log";
            $date = Factory::getDate();

            $f = fopen($file, 'a');
            fwrite($f, "\n\n" . $date->format('Y-m-d H:i:s'));
            fwrite($f, "\n" . $type . ': ' . $text);
            fclose($f);
        }
    }

    public function onAjaxActivateLicence()
    {
        $platform = J2Store::platform();
        $app = $platform->application();
        $license = (string)$app->input->get('license', '');
        $id = (int)$app->input->get('id', 0);
        $data = array();
        if (!empty($license) && $id > 0) {
            $plugin = $this->getPluginData($id);
            require_once(JPATH_ADMINISTRATOR . '/components/com_j2store/helpers/license.php');
            $license_helper = J2License::getInstance();
            $baseURL = str_replace('/administrator', '', Uri::base());
            $params = array(
                'license' => $license,
                'url' => $baseURL,
                'item_name' =>  $plugin->element,
                'element' => $plugin->element
            );
            $response = $license_helper->activateLicense($params);
            if(is_null($response)){
                $data['success'] = false;
                $data['message'] = Text::_('J2STORE_LICENSE_ACTIVATION_FAILED');
                $data['response'] = $response;
                $this->saveparams($plugin,$data);
                echo json_encode($data);
                exit;
            }
            if (is_array($response) && $response['success'] == false) {
                $data['success'] = false;
                $data['message'] = Text::_('J2STORE_LICENSE_INVALID');
                $data['response'] = $response;
                $this->saveparams($plugin,$data);
                echo json_encode($data);
                exit;
            }
            $data['success'] = true;
            $data['message'] = Text::_('J2STORE_LICENSE_ACTIVATED');
            $data['response'] = $response;
            $this->saveparams($plugin,$data);
            echo json_encode($data);
            exit;
        }
        $data['success'] = false;
        $data['message'] = Text::_('J2STORE_LICENSE_ACTIVATION_FAILED');
        echo json_encode($data);
        exit;
    }

    public function onAjaxDeActivateLicence()
    {
        $platform = J2Store::platform();
        $app = $platform->application();
        $license = (string)$app->input->get('license', '');
        $id = (int)$app->input->get('id', 0);
        $data = array();
        if (!empty($license) && $id > 0) {
            $plugin = $this->getPluginData($id);
            require_once(JPATH_ADMINISTRATOR . '/components/com_j2store/helpers/license.php');
            $license_helper = J2License::getInstance();
            $baseURL = str_replace('/administrator', '', Uri::base());
            $params = array(
                'license' => $license,
                'url' => $baseURL,
                'item_name' =>  $plugin->element,
                'element' => $plugin->element
            );
            $response = $license_helper->deActivateLicense($params);
            if(is_null($response)){
                $data['success'] = false;
                $data['message'] = Text::_('J2STORE_LICENSE_DEACTIVATION_FAILED');
                $data['response'] = $response;
                $this->saveparams($plugin,$data);
                echo json_encode($data);
                exit;
            }
            if (is_array($response) && $response['success'] == false) {
                $data['success'] = false;
                $data['message'] = Text::_('J2STORE_LICENSE_DEACTIVATION_FAILED');
                $data['response'] = $response;
                $this->saveparams($plugin,$data);
                echo json_encode($data);
                exit;
            }
            $data['success'] = true;
            $data['message'] = Text::_('J2STORE_LICENSE_DEACTIVATED');
            $data['response'] = $response;
            $this->saveparams($plugin,$data);
            echo json_encode($data);
            exit;
        }
        $data['success'] = false;
        $data['message'] = Text::_('J2STORE_LICENSE_DEACTIVATION_FAILED');
        echo json_encode($data);
        exit;
    }

    function getModuleParam($extension_name)
    {
        if (empty($extension_name)) {
            return;
        }
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select("*")->from('#__modules')
            ->where($db->qn('module') . ' = ' . $db->q($extension_name));
        $db->setQuery($query);
        return $db->loadObject();
    }

    function getPluginData($extension_id)
    {
        if ($extension_id <= 0) {
            return;
        }
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select("*")->from('#__extensions')->where('extension_id=' . (int)$extension_id);
        $db->setQuery($query);
        return $db->loadObject();
    }

    function saveparams($plugin,$data)
    {
        $platform = J2Store::platform();
        $app = $platform->application();
        $post = $app->input->getArray($_POST);
        $is_module=false;
        if(is_object($plugin) && $plugin->type == 'module'){
            $plugin=$this->getModuleParam($plugin->element);
            $is_module=true;
        }
        $plugin_data = (is_object($plugin) && $plugin) ? json_decode($plugin->params) : '';
        $response = $data['response'];
        if(is_object($plugin_data) && isset($plugin_data->license_key)){
            $plugin_data->license_key->license = is_array($post) && isset($post['license']) && !empty($post['license']) ? $post['license'] : '';
            $plugin_data->license_key->status = is_array($response) && isset($response['success']) && !empty($response['success']) ? 'active' : 'in_active';
            $plugin_data->license_key->expire = is_array($response) && isset($response['expires']) && !empty($response['expires']) ? $response['expires'] : '';
            $save_params = $platform->getRegistry(json_encode($plugin_data));
            $json = $save_params->toString();
        }
        $db = Factory::getContainer()->get('DatabaseDriver');
        if($is_module){
            $query = $db->getQuery(true)->update($db->qn('#__modules'))->set($db->qn('params') . ' = ' . $db->q($json))->where($db->qn('id') . ' = ' . $db->q($plugin->id))->where($db->qn('module') . ' = ' . $db->q($plugin->element));
        }else{
            $query = $db->getQuery ( true )->update ( $db->qn ( '#__extensions' ) )->set ( $db->qn ( 'params' ) . ' = ' . $db->q ( $json ) )->where ( $db->qn ( 'element' ) . ' = ' . $db->q ( $plugin->element ) )->where ( $db->qn ( 'folder' ) . ' = ' . $db->q ( 'j2store' ) )->where ( $db->qn ( 'type' ) . ' = ' . $db->q ( 'plugin' ) );
        }
        $db->setQuery ( $query );
        $db->execute ();
    }
}

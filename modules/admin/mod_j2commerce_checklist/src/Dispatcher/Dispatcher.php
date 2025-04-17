<?php
/**
 * @package     Joomla.Module
 * @subpackage  J2Commerce.mod_j2commerce_checklist
 *
 * @copyright Copyright (C) 2025 J2Commerce, LLC. All rights reserved.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or later
 * @website https://www.j2commerce.com
 */

namespace J2Commerce\Module\Checklist\Administrator\Dispatcher;

use J2Commerce\Module\Checklist\Administrator\Helper\ChecklistHelper;
use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Uri\Uri;


// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

require_once (JPATH_ADMINISTRATOR.'/components/com_j2store/helpers/j2store.php');
require_once (JPATH_ADMINISTRATOR.'/components/com_j2store/models/orders.php' );
/**
 * Dispatcher class for mod_j2commerce_checklist
 *
 * @since  5.2.3
 */
class Dispatcher extends AbstractModuleDispatcher
{
    /**
     * Returns the layout data.
     *
     * @return  array
     *
     * @since   5.2.3
     */
    protected function getLayoutData()
    {
        $user = Factory::getApplication()->getIdentity();

        if(!$user->authorise('j2store.vieworder', 'com_j2store')) {
            return [];
        }
        $data = parent::getLayoutData();
        Factory::getApplication()->getLanguage()->load('com_j2store', JPATH_SITE);
        $order_model = \J2Store::fof()->getModel('Orders' ,'J2StoreModel');

        $helper = new ChecklistHelper;

        $data['j2c_logo'] = Uri::root().'media/mod_j2commerce_checklist/images/j2commerce_logo.webp';

        $menuItems = $helper->checkMenuItems();
        $menuCount = $helper->getExistenceCounts($menuItems);
        $data['visible_products'] = $helper->hasVisibleProducts();

        $data['success_count'] = $menuCount['exists'];
        $data['danger_count'] = $menuCount['does_not_exist'];

        $total_count = $menuCount['exists'] + $menuCount['does_not_exist'];
        $completion_percentage = ceil(($menuCount['exists'] / $total_count) * 100);
        $data['completion_percentage'] = $completion_percentage;
        $data['total_count'] = $total_count;
        $data['menuitems'] = $menuItems;
        $data['menucount'] = $menuCount;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = Factory::getApplication()->input;
            $link = $input->post->getString('link', '');
            $label = $input->post->getString('label', '');
            $publish = $input->post->getInt('publish', 0);
            $currentpage = $input->post->getString('pageurl', '');

            if (!empty($link) && !empty($label)) {
                if ($publish) {
                    $published = $helper->publishMenuItem($link, $label, $currentpage);
                    if ($published) {
                        echo new JsonResponse(['success' => true, 'message' => Text::_('MOD_J2COMMERCE_CHECKLIST_MESSAGE_PUBLISHED_SUCCESSFULLY')]);
                    } else {
                        echo new JsonResponse(['success' => false, 'message' => Text::_('MOD_J2COMMERCE_CHECKLIST_MESSAGE_PUBLISHED_UNSUCCESSFULLY')]);
                    }
                } else {
                    $created = $helper->createMenuItem($link, $label, $currentpage);
                    if ($created) {
                        echo new JsonResponse(['success' => true, 'message' => Text::_('MOD_J2COMMERCE_CHECKLIST_MESSAGE_CREATED_SUCCESSFULLY')]);
                    } else {
                        echo new JsonResponse(['success' => false, 'message' => Text::_('MOD_J2COMMERCE_CHECKLIST_MESSAGE_CREATED_UNSUCCESSFULLY')]);
                    }
                }
            } else {
                echo new JsonResponse(['success' => false, 'message' => Text::_('MOD_J2COMMERCE_CHECKLIST_MESSAGE_INVALID_DATA')]);
            }

            Factory::getApplication()->close();
        }

        return $data;
    }
}

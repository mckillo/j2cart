<?php

/**
 * @package     Joomla.Module
 * @subpackage  J2Commerce.mod_j2commerce_checklist
 *
 * @copyright   Copyright (C) 2025 J2Commerce, LLC
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or later
 * @website https://www.j2commerce.com
 */

namespace J2Commerce\Module\Checklist\Administrator\Helper;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class ChecklistHelper
{
    public static function checkMenuItems()
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        // Define menu links and corresponding language keys
        $menuItems = [
            [
                'link'     => 'index.php?option=com_j2store&view=checkout',
                'lang_key' => 'MOD_J2COMMERCE_CHECKLIST_CHECKOUT'
            ],
            [
                'link'     => 'index.php?option=com_j2store&view=myprofile',
                'lang_key' => 'MOD_J2COMMERCE_CHECKLIST_MYPROFILE'
            ],
            [
                'link'     => 'index.php?option=com_j2store&view=carts',
                'lang_key' => 'MOD_J2COMMERCE_CHECKLIST_CART'
            ],
            [
                'link'     => 'index.php?option=com_j2store&view=checkout&layout=postpayment',
                'lang_key' => 'MOD_J2COMMERCE_CHECKLIST_POSTPAYMENT'
            ]
        ];

        $results = [];

        foreach ($menuItems as $item)
        {
            $query = $db->getQuery(true)
                ->select($db->quoteName('published'))
                ->from($db->quoteName('#__menu'))
                ->where($db->quoteName('link') . ' = ' . $db->quote($item['link']))
                ->where($db->quoteName('published') . ' IN (0,1)'); // Check for both published and unpublished

            $db->setQuery($query);
            $published_array = $db->loadColumn(); // Several menu items can exist

            if (empty($published_array)) {
                $exists = 0; // Menu item does not exist
                $published = 0;
            } else {
                $exists = 1; // Menu item exists
                if (count($published_array) > 1) {
                    $published = 0; // Default to not published
                    foreach ($published_array as $p) {
                        if ((int)$p === 1) {
                            $published = 1; // At least one is published
                            break;
                        }
                    }
                } else {
                    $published = (int) $published_array[0];
                }
            }

            // Store result with the link, existence, and translated label
            $results[] = [
                'link'   => $item['link'],
                'exists' => $exists,
                'published' => $published,
                'label'  => Text::_($item['lang_key'])
            ];
        }

        return $results;
    }

    public static function getExistenceCounts(array $menuStatus)
    {
        $counts = [
            'exists' => 0,
            'does_not_exist' => 0
        ];

        // Check menu item existence
        foreach ($menuStatus as $menuItem) {
            if ($menuItem['exists'] && $menuItem['published']) {
                $counts['exists']++;
            } else {
                $counts['does_not_exist']++;
            }
        }

        // Check if visible products exist in the database
        if (self::hasVisibleProducts()) {
            $counts['exists']++;
        } else {
            $counts['does_not_exist']++;
        }

        return $counts;
    }

    public static function createJ2CommerceMenu()
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        // Check if J2Commerce menu exists
        $query = $db->getQuery(true)
            ->select('id')
            ->from($db->quoteName('#__menu_types'))
            ->where($db->quoteName('menutype') . ' = ' . $db->quote('j2commerce'));

        $db->setQuery($query);
        $menuId = $db->loadResult();

        if (!$menuId) {
            self::createMenuType('j2commerce', 'J2Commerce');
        }

        return $menuId;
    }

    public static function createMenuItem($link, $label, $currentpage)
    {
        $app = Factory::getApplication();
        $db = Factory::getContainer()->get('DatabaseDriver');
        $menuId = self::createJ2CommerceMenu();

        // Check if the menu item already exists (to avoid duplicates)
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__menu'))
            ->where($db->quoteName('link') . ' = ' . $db->quote($link))
            ->where($db->quoteName('published') . ' IN (0,1)'); // Some menu items can be trashed

        $db->setQuery($query);

        if ((int) $db->loadResult() > 0) {
            return false; // Item already exists
        }

        // Check if the menu item has been trashed
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__menu'))
            ->where($db->quoteName('link') . ' = ' . $db->quote($link))
            ->where($db->quoteName('published') . ' = -2');

        $db->setQuery($query);

        if ((int) $db->loadResult() > 0) {
            $msg = Text::sprintf('MOD_J2COMMERCE_CHECKLIST_MESSAGE_MENUITEM_TRASHED', $label);

            $app->enqueueMessage($msg, 'warning');
            $app->redirect($currentpage);

            return false; // Item is trashed
        }

        $alias = strtolower(str_replace(' ', '-', $label));
        $path = $alias;
        $componentID = self::getComponentId('com_j2store');

        // Create a new menu item
        $factory = $app->bootComponent('com_menus')->getMVCFactory();
        $model = $factory->createModel('Item', 'Administrator', ['ignore_request' => true]);

        $data = [];

        $data['id'] = 0;
        $data['menutype'] = 'j2commerce';
        $data['title'] = Text::_($label);
        $data['alias'] = $alias;
        $data['note'] = '';
        $data['path'] = $path;
        $data['link'] = $link;
        $data['type'] = 'component';
        $data['published'] = 1;
        $data['parent_id'] = 1;
        $data['level'] = 1;
        $data['component_id'] = $componentID;
        $data['access'] = 1;
        $data['img'] = '';
        $data['template_style_id'] = 0;
        $data['params'] = '{}';
        $data['client_id'] = 0;
        $data['language'] = '*';

        if (!$model->save($data)) {
            return false;
        }

        $msg = Text::sprintf('MOD_J2COMMERCE_CHECKLIST_MESSAGE_MENUITEM_CREATION_SUCCESS', $label);

        $app->enqueueMessage($msg, 'success');
        $app->redirect($currentpage);

        return true;
    }

    public static function publishMenuItem($link, $label, $currentpage)
    {
        $app = Factory::getApplication();
        $db = Factory::getContainer()->get('DatabaseDriver');

        // Publish all menu items with the same link
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__menu'))
            ->set($db->quoteName('published') . ' = 1')
            ->where($db->quoteName('link') . ' = ' . $db->quote($link));

        $db->setQuery($query);

        try {
            $db->execute();
        } catch (\Exception $e) {
            return false;
        }

        $msg = Text::sprintf('MOD_J2COMMERCE_CHECKLIST_MESSAGE_MENUITEM_PUBLICATION_SUCCESS', $label);

        $app->enqueueMessage($msg, 'success');
        $app->redirect($currentpage);

        return true;
    }

    public static function createMenuType($menuType, $title)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        // Check if the menutype already exists
        $query = $db->getQuery(true)
            ->select('menutype')
            ->from($db->quoteName('#__menu_types'))
            ->where($db->quoteName('menutype') . ' = ' . $db->quote($menuType));

        $db->setQuery($query);
        if ($db->loadResult()) {
            return false; // Menutype already exists
        }

        // Create a new MenuType instance
        $menuTypeTable = Table::getInstance('MenuType', 'Joomla\\CMS\\Table\\');
        $menuTypeTable->menutype = $menuType;
        $menuTypeTable->title = $title;
        $menuTypeTable->description = "Menu for {$title}";

        if ($menuTypeTable->store()) {
            return true; // Menutype created successfully
        }

        return false; // Failed to create the menutype
    }

    public static function getComponentId($component)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        // Query to get the component_id for com_j2store
        $query = $db->getQuery(true)
            ->select($db->quoteName('extension_id'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('element') . ' = ' . $db->quote($component))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'));

        $db->setQuery($query);
        $componentId = $db->loadResult();

        if ($componentId) {
            return (int) $componentId;
        }

        return null; // Return null if not found
    }

    public static function hasVisibleProducts()
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        // Build the query
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__j2store_products'))
            ->where($db->quoteName('j2store_product_id') . ' > 0')
            ->where($db->quoteName('visibility') . ' = 1');

        $db->setQuery($query);

        // Check if at least one product exists
        $count = (int) $db->loadResult();

        return $count > 0;
    }

}

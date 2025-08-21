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

namespace J2Commerce\Plugin\Finder\J2store\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Menu\AbstractMenu;

class J2storemenuitemField extends ListField
{
    protected $type = 'J2storemenuitem';

    protected function getOptions()
    {
        $options = [];

        $menus = AbstractMenu::getInstance('site');

        foreach ($menus->getMenu() as $item) {
            if ($item->type === 'component'){
                if (isset($item->query['option']) && $item->query['option'] === 'com_j2store') {
                    if (isset($item->query['catid'])) {
                        $options[] = HTMLHelper::_('select.option', $item->id, $item->title, 'value', 'text');
                    }
                }
            }
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}

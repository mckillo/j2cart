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

use Joomla\CMS\Language\Text;
?>
<div class="pro-feature">
    <div class="alert alert-info text-center" role="alert">
        <h2 class="alert-heading fs-2"><?php echo Text::_('J2STORE_PART_OF_PRO_FEATURE'); ?></h2>
        <p class="mb-5"><?php echo Text::_('J2STORE_PART_OF_PRO_FEATURE2'); ?></p>
        <a class="btn btn-primary text-white" target="_blank" href="<?php echo J2Store::buildSiteLink('download', 'prolink'); ?>"><span class="fas fa-solid fa-external-link-alt me-2"></span><?php echo Text::_('J2STORE_PART_OF_PRO_FEATURE_BTN_UPGRADE');?><span class="fas fa-solid fa-arrow-right ms-2"></span></a>
    </div>
</div>

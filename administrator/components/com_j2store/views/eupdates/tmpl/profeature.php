<?php
/**
 * @package J2Store
 * @copyright Copyright (c)2014-17 Ramesh Elamathi / J2Store.org
 * @copyright Copyright (c) 2024 J2Commerce . All rights reserved.
 * @license GNU GPL v3 or later
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;

?>
<div class="pro-feature">
    <div class="alert alert-primary text-center" role="alert">
        <h3 class="alert-heading fs-3"><?php echo Text::_('J2STORE_PART_OF_PRO_FEATURE'); ?></h3>
        <p class="mb-5"><?php echo Text::_('J2STORE_PART_OF_PRO_FEATURE2'); ?></p>
        <a class="btn btn-primary" target="_blank" href="<?php echo J2Store::buildHelpLink('download', 'prolink'); ?>"><span class="fas fa-solid fa-external-link-alt me-2"></span><?php echo Text::_('J2STORE_PART_OF_PRO_FEATURE_BTN_UPGRADE');?><span class="fas fa-solid fa-arrow-right ms-2"></span></a>
    </div>
</div>

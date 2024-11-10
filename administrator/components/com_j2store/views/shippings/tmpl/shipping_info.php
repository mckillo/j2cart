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
use Joomla\CMS\Router\Route;

$row_class = 'row';
$col_class = 'col-lg-';
if (version_compare(JVERSION, '3.99.99', 'lt')) {
    $row_class = 'row-fluid';
    $col_class = 'span';
}
?>
<div class="shipping-content inline-content my-5">
	<div class="<?php echo $row_class; ?>">

		<div class="<?php echo $col_class; ?>6 align-self-stretch mb-3 mb-lg-0">
            <div class="d-flex flex-column text-center h-100">
                <div class="mt-auto">
                    <span class="fa-4x mb-2 fa-solid fas fa-circle-info"></span>
                    <h2 class="fs-1 fw-bold"><?php echo Text::_('J2STORE_SHIPPING_HELP_TITLE');?></h2>
                    <p class="fs-3 text-muted mb-5"><?php echo Text::_('J2STORE_SHIPPING_HELP_DESC');?></p>
                </div>

                <div class="text-center mt-auto mb-4">
                    <a class="btn btn-outline-primary app-button-open" href="<?php echo J2Store::buildHelpLink('support/shipping-methods', 'shipping'); ?>" target="_blank"><span class="fas fa-solid fa-arrow-up-right-from-square me-2"></span><?php echo Text::_('J2STORE_SHIPPING_HELP_BUTTON_GUIDE_LABEL'); ?></a>
                    <a class="btn btn-primary app-button-open" href="<?php echo Route::_('index.php?option=com_j2store&view=shippingtroubles'); ?>" target="_blank"><?php echo Text::_('COM_J2STORE_TITLE_SHIPPINGTROUBLES'); ?></a>
                </div>
            </div>
		</div>
		<div class="<?php echo $col_class ?>6 align-self-stretch">
            <div class="d-flex flex-column text-center h-100">
                <div class="mt-auto">
                    <span class="fa-4x mb-2 fa-solid fas fa-truck-fast"></span>
                    <h2 class="fs-1 fw-bold"><?php echo Text::_('J2STORE_SHIPPING_APPS_TITLE');?></h2>
                    <p class="fs-3 text-muted mb-5"><?php echo Text::_('J2STORE_SHIPPING_APPS_DESC');?></p>
                </div>

                <div class="text-center mt-auto mb-4">
                    <a class="btn btn-outline-primary app-button-open" href="<?php echo J2Store::buildHelpLink('extensions/shipping-plugins', 'shipping'); ?>" target="_blank"><span class="fas fa-solid fa-arrow-up-right-from-square me-2"></span><?php echo Text::_('J2STORE_SHIPPING_APPS_BUTTON_LABEL'); ?></a>
                </div>
            </div>
		</div>
	</div>
</div>
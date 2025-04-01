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
<div class="payment-content inline-content my-5">
    <div class="row">
        <div class="col-md-6 align-self-stretch mb-3 mb-lg-0">
            <div class="d-flex flex-column text-center h-100">
                <div class="mt-auto">
                    <span class="fa-4x mb-2 fa-solid fas fa-circle-info"></span>
                    <h2 class="fs-1 fw-bold"><?php echo Text::_('J2STORE_PAYMENT_HELP_TITLE');?></h2>
                    <p class="fs-3 text-muted mb-5"><?php echo Text::_('J2STORE_PAYMENT_HELP_DESC');?></p>
                </div>

                <div class="text-center mt-auto mb-4">
                    <a class="btn btn-outline-primary app-button-open" href="<?php echo J2Store::buildHelpLink('payment-methods', 'payment'); ?>" target="_blank"><span class="fas fa-solid fa-arrow-up-right-from-square me-2"></span><?php echo Text::_('J2STORE_PAYMENT_HELP_BUTTON_GUIDE_LABEL'); ?></a>
                    <a class="btn btn-primary app-button-open" href="<?php echo J2Store::buildSiteLink('support', 'support'); ?>" target="_blank"><span class="fas fa-solid fa-arrow-up-right-from-square me-2"></span><?php echo Text::_('J2STORE_SHIPPING_HELP_BUTTON_SUPPORT_LABEL'); ?></a>
                </div>
            </div>
        </div>
        <div class="col-md-6 align-self-stretch">
            <div class="d-flex flex-column text-center h-100">
                <div class="mt-auto">
                    <span class="fa-4x mb-2 fa-solid fas fa-credit-card"></span>
                    <h2 class="fs-1 fw-bold"><?php echo Text::_('J2STORE_PAYMENT_APPS_TITLE');?></h2>
                    <p class="fs-3 text-muted mb-5"><?php echo Text::_('J2STORE_PAYMENT_APPS_DESC');?></p>
                </div>

                <div class="text-center mt-auto mb-4">
                    <a class="btn btn-outline-primary app-button-open" href="<?php echo J2Store::buildSiteLink('extensions/payment-plugins', 'gateways'); ?>" target="_blank"><span class="fas fa-solid fa-arrow-up-right-from-square me-2"></span><?php echo Text::_('J2STORE_PAYMENT_APPS_BUTTON_LABEL'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>

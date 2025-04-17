<?php
/**
 * --------------------------------------------------------------------------------
 * Module - Orders
 * --------------------------------------------------------------------------------
 * @package     Joomla 5.x
 * @subpackage  J2 Store
 * @copyright   Copyright (c)2014-17 Ramesh Elamathi / J2Store.org
 * @copyright   Copyright (C) 2025 J2Commerce, LLC. All rights reserved.
 * @license     GNU GPL v3 or later
 * @link        https://www.j2commerce.com
 * --------------------------------------------------------------------------------
 *
 * */

// No direct access to this file
defined ( '_JEXEC' ) or die ();
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
$currency =J2Store::currency();
?>
<?php if($orders):?>
    <div class="j2store_latest_orders">
        <div class="card mb-3">
            <div class="card-header justify-content-between">
                <h2 class="h3 mb-0"><i class="fas fa-solid fa-shopping-cart me-2"></i><?php echo Text::_('J2STORE_LATEST_ORDERS');?></h2>
                <div class="module-actions dropdown">
                    <button type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-haspopup="true" aria-expanded="false" class="btn btn-link btn-sm py-0" id="dropdownOrders">
                        <span class="icon-cogs" aria-hidden="true"></span>
                        <span class="visually-hidden"><?php echo Text::_('J2STORE_LATEST_ORDERS');?></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownOrders" data-bs-popper="static">
                        <a class="dropdown-item" href="<?php echo 'index.php?option=com_j2store&view=orders';?>"><?php echo Text::_('J2STORE_ORDERS');?></a>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0" id="j2commerce<?php echo $module->id;?>">
                    <caption class="visually-hidden"><?php echo Text::_('J2STORE_LATEST_ORDERS');?></caption>
                    <thead>
                        <tr>
                            <th scope="col" class="w-20"><?php echo Text::_('J2STORE_DATE')?></th>
                            <th scope="col" class="w-20 text-center"><?php echo Text::_('J2STORE_INVOICE_NO'); ?></th>
                            <th scope="col" class="w-40"><?php echo Text::_('J2STORE_EMAIL'); ?></th>
                            <th scope="col" class="w-20"><?php echo Text::_('J2STORE_AMOUNT'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($orders as $order):
                        if(isset($order->invoice_number) && $order->invoice_number > 0) {
                            $invoice_number = $order->invoice_prefix.$order->invoice_number;
                        }else {
                            $invoice_number = $order->j2store_order_id;
                        }
                        $link 	= 'index.php?option=com_j2store&view=order&id='. $order->j2store_order_id;
                        ?>
                        <tr>
                            <td><?php echo HTMLHelper::_('date', $order->created_on, $params->get('date_format', Text::_('DATE_FORMAT_LC1'))); ?></td>
                            <td class="text-center"><strong><a href="<?php echo $link; ?>"><?php echo $invoice_number; ?></a></strong></td>
                            <td><?php echo $order->user_email; ?></td>
                            <td><?php echo $currency->format( $order->order_total, $order->currency_code, $order->currency_value ); ?></td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif;?>

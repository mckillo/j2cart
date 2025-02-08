<?php
/**
 * --------------------------------------------------------------------------------
 * Module - Stats
 * --------------------------------------------------------------------------------
 * @package     Joomla.Administrator
 * @subpackage  mod_j2store_stats
 * @copyright   Copyright (c)2014-17 Ramesh Elamathi / J2Store.org
 * @copyright   Copyright (c) 2024 J2Commerce . All rights reserved.
 * @license     GNU GPL v3 or later
 * @link        https://www.j2commerce.com
 * --------------------------------------------------------------------------------
 */

defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

require_once (JPATH_ADMINISTRATOR.'/components/com_j2store/helpers/j2store.php');
$currency = J2Store::currency();
$order_status = $params->get('order_status',array('*'));
?>
<div class="j2store_statistics">
    <div class="card mb-3">
        <div class="card-header">
            <h2 class="h3 mb-0"><i class="fas fa-solid fa-chart-bar me-2"></i><?php echo Text::_('J2STORE_ORDER_STATISTICS');?></h2>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0" id="j2commerce<?php echo $module->id;?>">
                <caption class="visually-hidden"><?php echo Text::_('J2STORE_ORDER_STATISTICS');?></caption>
                <thead>
                <tr>
                    <th scope="col" class="w-60"></th>
                    <th scope="col" class="w-20 text-center"><?php echo Text::_('J2STORE_TOTAL'); ?></th>
                    <th scope="col" class="w-20"><?php echo Text::_('J2STORE_AMOUNT'); ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?php echo Text::_('J2STORE_TOTAL_ORDERS'); ?></td>
                    <td class="text-center">
						<?php echo F0FModel::getTmpInstance('Orders', 'J2StoreModel')->clearState()->orderstatus($order_status)->nozero(1)->getOrdersTotal();?>
                    </td>
                    <td>
						<?php echo $currency->format(F0FModel::getTmpInstance('Orders', 'J2StoreModel')
							->clearState()
							->orderstatus($order_status)
							->nozero(1)
							->moneysum(1)
							->getOrdersTotal());
						?>
                    </td>
                </tr>

                <tr>
                    <td><?php echo Text::_('J2STORE_TOTAL_CONFIRMED_ORDERS_LAST_YEAR'); ?></td>
                    <td class="text-center">
						<?php
						echo F0FModel::getTmpInstance('Orders', 'J2StoreModel')->clearState()
							->since((gmdate('Y')-1).'-01-01 00:00:00')
							->until((gmdate('Y')-1).'-12-31 23:59:59')
							->orderstatus($order_status)
							->nozero(1)
							->getOrdersTotal();
						?>
                    </td>
                    <td>
						<?php
						echo $currency->format(
							F0FModel::getTmpInstance('Orders', 'J2StoreModel')->clearState()
								->since((gmdate('Y')-1).'-01-01 00:00:00')
								->until((gmdate('Y')-1).'-12-31 23:59:59')
								->orderstatus($order_status)
								->nozero(1)
								->moneysum(1)
								->getOrdersTotal()
						);
						?>
                    </td>
                </tr>

                <tr>
                    <td><?php echo Text::_('J2STORE_TOTAL_CONFIRMED_ORDERS_THIS_YEAR'); ?></td>
                    <td class="text-center">
						<?php
						echo F0FModel::getTmpInstance('Orders', 'J2StoreModel')->clearState()
							->since(gmdate('Y').'-01-01')
							->until(gmdate('Y').'-12-31 23:59:59')
							->orderstatus($order_status)
							->nozero(1)
							->getOrdersTotal();
						?>
                    </td>
                    <td>
						<?php
						echo $currency->format(
							F0FModel::getTmpInstance('Orders', 'J2StoreModel')->clearState()
								->since(gmdate('Y').'-01-01')
								->until(gmdate('Y').'-12-31 23:59:59')
								->orderstatus($order_status)
								->nozero(1)
								->moneysum(1)
								->getOrdersTotal()
						);
						?>
                    </td>
                </tr>

                <tr>
                    <td><?php echo Text::_('J2STORE_TOTAL_CONFIRMED_ORDERS_LAST_MONTH'); ?></td>
                    <td class="text-center">
						<?php
						$y = gmdate('Y');
						$m = gmdate('m');
						if($m == 1) {
							$m = 12; $y -= 1;
						} else {
							$m -= 1;
						}
						switch($m) {
							case 1: case 3: case 5: case 7: case 8: case 10: case 12:
							$lmday = 31; break;
							case 4: case 6: case 9: case 11:
							$lmday = 30; break;
							case 2:
								if( !($y % 4) && ($y % 400) ) {
									$lmday = 29;
								} else {
									$lmday = 28;
								}
						}
						if($y < 2011) $y = 2011;
						if($m < 1) $m = 1;
						if($lmday < 1) $lmday = 1;
						?>
						<?php
						echo F0FModel::getTmpInstance('Orders', 'J2StoreModel')->clearState()
							->since($y.'-'.$m.'-01')
							->until($y.'-'.$m.'-'.$lmday.' 23:59:59')
							->orderstatus($order_status)
							->nozero(1)
							->getOrdersTotal();
						?>
                    </td>
                    <td>
						<?php
						echo $currency->format(
							F0FModel::getTmpInstance('Orders', 'J2StoreModel')->clearState()
								->since($y.'-'.$m.'-01')
								->until($y.'-'.$m.'-'.$lmday.' 23:59:59')
								->orderstatus($order_status)
								->nozero(1)
								->moneysum(1)
								->getOrdersTotal()
						);
						?>
                    </td>
                </tr>

                <tr>
                    <td><?php echo Text::_('J2STORE_TOTAL_CONFIRMED_ORDERS_THIS_MONTH'); ?></td>
                    <td class="text-center">
						<?php
						switch(gmdate('m')) {
							case 1: case 3: case 5: case 7: case 8: case 10: case 12:
							$lmday = 31; break;
							case 4: case 6: case 9: case 11:
							$lmday = 30; break;
							case 2:
								$y = gmdate('Y');
								if( !($y % 4) && ($y % 400) ) {
									$lmday = 29;
								} else {
									$lmday = 28;
								}
						}
						if($lmday < 1) $lmday = 28;
						?>
						<?php
						echo F0FModel::getTmpInstance('Orders', 'J2StoreModel')->clearState()
							->since(gmdate('Y').'-'.gmdate('m').'-01')
							->until(gmdate('Y').'-'.gmdate('m').'-'.$lmday.' 23:59:59')
							->orderstatus($order_status)
							->nozero(1)
							->getOrdersTotal();
						?>
                    </td>
                    <td>
						<?php
						echo $currency->format(
							F0FModel::getTmpInstance('Orders', 'J2StoreModel')->clearState()
								->since(gmdate('Y').'-'.gmdate('m').'-01')
								->until(gmdate('Y').'-'.gmdate('m').'-'.$lmday.' 23:59:59')
								->orderstatus($order_status)
								->nozero(1)
								->moneysum(1)
								->getOrdersTotal()
						);
						?>
                    </td>
                </tr>
				<?php
				$tz = Factory::getConfig()->get('offset');
				$previous = Factory::getDate ('now -7 days',$tz)->format ( 'Y-m-d' );
				$today = Factory::getDate ('now',$tz)->format ( 'Y-m-d' );
				?>
                <tr>
                    <td><?php echo Text::_('J2STORE_TOTAL_CONFIRMED_ORDERS_LAST7DAYS'); ?></td>
                    <td class="text-center">
						<?php
						echo F0FModel::getTmpInstance('Orders', 'J2StoreModel')->clearState()
							->since( $previous )
							->until( $today.' 23:59:59' )
							->orderstatus($order_status)
							->nozero(1)
							->getOrdersTotal();
						?>
                    </td>
                    <td>
						<?php
						echo $currency->format(
							F0FModel::getTmpInstance('Orders', 'J2StoreModel')->clearState()
								->since( $previous )
								->until( $today.' 23:59:59' )
								->orderstatus($order_status)
								->nozero(1)
								->moneysum(1)
								->getOrdersTotal()
						);
						?>
                    </td>
                </tr>


                <tr>
                    <td><?php echo Text::_('J2STORE_TOTAL_CONFIRMED_ORDERS_YESTERDAY'); ?></td>
                    <td class="text-center">
						<?php
						$yesterday = Factory::getDate ('now -1 days',$tz)->format ( 'Y-m-d' );
						?>
						<?php
						echo F0FModel::getTmpInstance('Orders', 'J2StoreModel')->clearState()
							->since( $yesterday )
							->until( $yesterday.' 23:59:59' )
							->orderstatus($order_status)
							->nozero(1)
							->getOrdersTotal();
						?>
                    </td>
                    <td>
						<?php
						echo $currency->format(
							F0FModel::getTmpInstance('Orders', 'J2StoreModel')->clearState()
								->since( $yesterday )
								->until( $yesterday.' 23:59:59' )
								->orderstatus($order_status)
								->nozero(1)
								->moneysum(1)
								->getOrdersTotal()
						);
						?>
                    </td>
                </tr>


                <tr>
                    <td><strong><?php echo Text::_('J2STORE_TOTAL_CONFIRMED_ORDERS_TODAY'); ?></strong></td>
                    <td class="text-center"><strong>
							<?php
							$tomorrow = Factory::getDate ('now +1 days',$tz)->format ( 'Y-m-d' );
							?>
							<?php
							echo F0FModel::getTmpInstance('Orders', 'J2StoreModel')->clearState()
								->since( $today )
								->until( $today.' 23:59:59' )
								->orderstatus($order_status)
								->nozero(1)
								->getOrdersTotal();
							?>
                        </strong>
                    </td>
                    <td>
                        <strong>
							<?php
							echo $currency->format(
								F0FModel::getTmpInstance('Orders', 'J2StoreModel')->clearState()
									->since( $today )
									->until( $today.' 23:59:59' )
									->orderstatus($order_status)
									->nozero(1)
									->moneysum(1)
									->getOrdersTotal()
							);
							?>
                        </strong>
                    </td>
                </tr>

                <tr>
                    <td><strong><?php echo Text::_('J2STORE_TOTAL_CONFIRMED_ORDERS_AVERAGE'); ?></strong></td>

					<?php
					switch(gmdate('m')) {
						case 1: case 3: case 5: case 7: case 8: case 10: case 12:
						$lmday = 31; break;
						case 4: case 6: case 9: case 11:
						$lmday = 30; break;
						case 2:
							$y = gmdate('Y');
							if( !($y % 4) && ($y % 400) ) {
								$lmday = 29;
							} else {
								$lmday = 28;
							}
					}
					if($lmday < 1) $lmday = 28;
					if($y < 2011) $y = 2011;
					$daysin = gmdate('d');
					$numsubs = F0FModel::getTmpInstance('Orders', 'J2StoreModel')->clearState()
						->since(gmdate('Y').'-'.gmdate('m').'-01')
						->until(gmdate('Y').'-'.gmdate('m').'-'.$lmday.' 23:59:59')
						->nozero(1)
						->orderstatus($order_status)
						->getOrdersTotal();
					$summoney = F0FModel::getTmpInstance('Orders', 'J2StoreModel')->clearState()
						->since(gmdate('Y').'-'.gmdate('m').'-01')
						->until(gmdate('Y').'-'.gmdate('m').'-'.$lmday.' 23:59:59')
						->moneysum(1)
						->orderstatus($order_status)
						->getOrdersTotal();
					?>

                    <td class="text-center">
                        <strong><?php echo sprintf('%01.1f', $numsubs/$daysin)?><strong>
                    </td>
                    <td>
                        <strong>
							<?php
							echo $currency->format(
								sprintf('%01.2f', $summoney/$daysin)
							);
							?>
                        </strong>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>

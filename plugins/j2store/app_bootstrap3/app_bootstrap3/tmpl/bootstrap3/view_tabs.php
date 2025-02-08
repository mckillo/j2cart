<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  J2Store.app_bootstrap3
 *
 * @copyright Copyright (c)2014-17 Ramesh Elamathi / J2Store.org
 * @copyright Copyright (C) 2025 J2Commerce, LLC. All rights reserved.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or later
 * @website https://www.j2commerce.com
 */
// No direct access
defined('_JEXEC') or die;
?>
	<div class="row">
		<div class="col-sm-12">
			<ul class="nav nav-tabs" id="j2store-product-detail-tab">
				<?php
					$set_specification_active =true;
					if($this->params->get('item_show_sdesc') ||  $this->params->get('item_show_ldesc')){
						$set_specification_active = false;
				}
				if($this->params->get('item_show_sdesc') || $this->params->get('item_show_ldesc')):?>
					<li class="active"><a href="#description" data-toggle="tab"><?php echo JText::_('J2STORE_PRODUCT_DESCRIPTION')?></a>
				<?php endif;?>

				<?php if($this->params->get('item_show_product_specification')):?>
					<li class="<?php echo isset($set_specification_active) && $set_specification_active ? 'active' : '';?>" >
						<a href="#specs" data-toggle="tab"><?php echo JText::_('J2STORE_PRODUCT_SPECIFICATIONS')?></a>
					</li>
				<?php endif;?>
			</ul>

			<div class="tab-content">
				<?php if($this->params->get('item_show_sdesc') || $this->params->get('item_show_ldesc') ):?>
				<div class="tab-pane fade in active" id="description">
					<?php echo $this->loadTemplate('sdesc'); ?>
					<?php echo $this->loadTemplate('ldesc'); ?>
				</div>
				<?php endif;?>

				<?php if($this->params->get('item_show_product_specification')):?>
					<div class="tab-pane fade in <?php echo isset($set_specification_active) && $set_specification_active ? 'active' : '';?>" id="specs">
						<?php echo $this->loadTemplate('specs'); ?>
					</div>
				<?php endif;?>
			</div>

		</div>
	</div>
<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  J2Store.app_bootstrap5
 *
 * @copyright Copyright (c)2014-17 Ramesh Elamathi / J2Store.org
 * @copyright Copyright (C) 2025 J2Commerce, LLC. All rights reserved.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or later
 * @website https://www.j2commerce.com
 */
// No direct access
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
?>
<div class="row">
    <div class="col-sm-12">
        <ul class="nav nav-tabs" id="j2store-product-detail-tab" role="tablist">
			<?php
			$set_specification_active =true;
			if($this->params->get('item_show_sdesc') ||  $this->params->get('item_show_ldesc')){
				$set_specification_active = false;
			}
			if($this->params->get('item_show_sdesc') || $this->params->get('item_show_ldesc')):?>
                <li class="nav-item">
                    <a class="nav-link active" href="#description" data-bs-toggle="tab"><?php echo Text::_('J2STORE_PRODUCT_DESCRIPTION')?></a>
                </li>
			<?php endif;?>

			<?php if($this->params->get('item_show_product_specification')):?>
                <li class="nav-item" >
                    <a href="#specs" class="nav-link <?php echo isset($set_specification_active) && $set_specification_active ? 'active' : '';?>" data-bs-toggle="tab"><?php echo Text::_('J2STORE_PRODUCT_SPECIFICATIONS')?></a>
                </li>
			<?php endif;?>
			<?php echo J2Store::plugin()->eventWithHtml('AfterRenderingTabLink', array($this->product)); ?>
        </ul>

        <div class="tab-content">
			<?php if($this->params->get('item_show_sdesc') || $this->params->get('item_show_ldesc') ):?>
                <div class="tab-pane fade show active" id="description">
					<?php echo $this->loadTemplate('sdesc'); ?>
					<?php echo $this->loadTemplate('ldesc'); ?>
                </div>
			<?php endif;?>

			<?php if($this->params->get('item_show_product_specification')):?>
                <div class="tab-pane fade show <?php echo isset($set_specification_active) && $set_specification_active ? 'active' : '';?>" id="specs">
					<?php echo $this->loadTemplate('specs'); ?>
                </div>
			<?php endif;?>
			<?php echo J2Store::plugin()->eventWithHtml('AfterRenderingTabContent', array($this->product)); ?>
        </div>

    </div>
</div>
	
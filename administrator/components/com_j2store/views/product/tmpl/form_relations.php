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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

J2Store::plugin()->importCatalogPlugins();
?>
	<div class="j2store-product-relations">
        <div class="alert alert-info alert-block">
            <strong><?php echo Text::_('J2STORE_NOTE'); ?></strong> <?php echo Text::_('J2STORE_FEATURE_AVAILABLE_IN_J2STORE_PRODUCT_LAYOUTS'); ?>
        </div>
        <fieldset class="options-form product-upsells mb-4">
            <legend><?php echo Text::_('J2STORE_PRODUCT_UP_SELLS');?></legend>
            <div class="table-responsive">
                <table class="table itemList">
                    <thead>
                    <tr>
                        <th scope="col"><?php echo Text::_('J2STORE_PRODUCT_NAME');?></th>
                        <th scope="col" class="text-center w-1"><?php echo Text::_('J2STORE_REMOVE');?></th>
                    </tr>
                    </thead>
                    <tbody id="addedProductUpsell">
					<?php
					if(isset($this->item->up_sells) && !empty($this->item->up_sells)):
						$upsells = J2Store::product()->getRelatedProducts($this->item->up_sells);
						?>
						<?php foreach($upsells as $key=>$related_product):?>
						<?php
						$app = Factory::getApplication();
						$app->triggerEvent('onJ2StoreAfterGetProduct', array(&$related_product));
						?>
						<?php if(isset($related_product->product_source_id)):?>
                            <tr id="upSell-<?php echo $related_product->j2store_product_id;?>">
                                <td class="addedProductUpsell">
									<?php if($app->isClient('site')):?>
										<?php echo isset($related_product->sku) && !empty($related_product->sku) ? $this->escape($related_product->product_name)."(".$this->escape($related_product->sku).")" : $related_product->product_name;?>
									<?php else: ?>
                                        <a href="<?php echo $related_product->product_edit_url; ?>" target="_blank">
											<?php echo isset($related_product->sku) && !empty($related_product->sku) ? $this->escape($related_product->product_name)."(".$this->escape($related_product->sku).")" : $related_product->product_name;?>
                                        </a>
									<?php endif; ?>
                                    <input type="hidden" value="<?php echo $related_product->j2store_product_id;?>"  name="<?php echo $this->form_prefix.'[up_sells]' ;?>[<?php echo $related_product->j2store_product_id;?>]" />
                                </td>
                                <td class="text-center">
                                    <a href="javascript:void(0);" onclick="removeThisRelatedRow('upSell',<?php echo $related_product->j2store_product_id;?>)">
                                        <span class="icon icon-trash text-danger"></span>
                                    </a>
                                </td>
                            </tr>
						<?php endif;?>
					<?php endforeach;?>
					<?php endif;?>
                    </tbody>
                    <tbody>
                    <tr>
                        <td colspan="2">
                            <small><strong><?php echo Text::_('J2STORE_SEARCH_AND_RELATED_PRODUCTS');?></strong></small>
							<?php echo J2Html::text('upsellSelector' ,'' , array('id'=>'upsellSelector','class'=>'form-control ms-2'));?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </fieldset>
        <fieldset class="options-form product-crosssells mb-4">
            <legend><?php echo Text::_('J2STORE_PRODUCT_CROSS_SELLS');?></legend>
            <div class="table-responsive">
                <table class="table itemList">
                    <thead>
                        <tr>
                            <th scope="col"><?php echo Text::_('J2STORE_PRODUCT_NAME');?></th>
                            <th scope="col" class="text-center w-1"><?php echo Text::_('J2STORE_REMOVE');?></th>
                        </tr>
                    </thead>
                    <tbody id="addedProductCrosssell">
                        <?php if(isset($this->item->cross_sells) && !empty($this->item->cross_sells)):
                            $crosssells = J2Store::product()->getRelatedProducts($this->item->cross_sells);
                            ?>
                            <?php foreach($crosssells as $key=>$related_product):
                                $app = Factory::getApplication();
                                $app->triggerEvent('onJ2StoreAfterGetProduct', array(&$related_product));
                            ?>
                                <?php if(isset($related_product->product_source_id)):?>
                                    <tr id="crossSell-<?php echo $related_product->j2store_product_id;?>">
                                        <td class="addedProductCrosssell">
                                            <?php if($app->isClient('site')):?>
                                                <?php echo isset($related_product->sku) && !empty($related_product->sku) ? $this->escape($related_product->product_name).'('.$this->escape($related_product->sku).')' : $this->escape($related_product->product_name);?>
                                            <?php else: ?>
                                                <a href="index.php?option=com_content&task=article.edit&id=<?php echo $related_product->product_source_id;?>" target="_blank">
                                                    <?php echo isset($related_product->sku) && !empty($related_product->sku) ? $this->escape($related_product->product_name).'('.$this->escape($related_product->sku).')' : $this->escape($related_product->product_name);?>
                                                </a>
                                            <?php endif;?>
                                            <input type="hidden" value="<?php echo $related_product->j2store_product_id;?>" name="<?php echo $this->form_prefix.'[cross_sells]' ;?>[<?php echo $related_product->j2store_product_id;?>]" />
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:void(0);" onclick="removeThisRelatedRow('crossSell',<?php echo $related_product->j2store_product_id;?>)">
                                                <span class="icon icon-trash text-danger"></span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endif;?>
                            <?php endforeach;?>
                        <?php endif;?>
                    </tbody>
                    <tbody>
                    <tr>
                        <td colspan="2">
                            <small><strong><?php echo Text::_('J2STORE_SEARCH_AND_RELATED_PRODUCTS');?></strong></small>
			                <?php echo J2Html::text('crossSellSelector' ,'', array('id'=>'crossSellSelector','class'=>'form-control ms-2'));?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </fieldset>
</div>

<script type="text/javascript">
    (function($) {
		$(document).ready(function() {
			$('#upsellSelector').autocomplete({
				source : function(request, response) {
					var upsell = {
						option: 'com_j2store',
						view: 'products',
						task: 'getRelatedProducts',
						product_id: '<?php echo $this->item->j2store_product_id;?>',
					q: request.term
				};
				$.ajax({
					type : 'post',
					url  : '<?php echo Route::_('index.php');?>',
					data : upsell,
					dataType : 'json',
					success : function(data) {
						$('#upsellSelector').removeClass('optionsLoading');
						response($.map(data['products'], function(item) {
							return {
								label: item.product_name,
								value: item.j2store_product_id
							}
						}));
					}
				});
			},
			minLength : 2,
			select : function(event, ui) {
				$('<tr id="upSell-'+ui.item.value+'"><td class=\"addedProductUpsell\">' + ui.item.label+ '<input type="hidden" value="'+ ui.item.value+'"  name=\"<?php echo $this->form_prefix.'[up_sells]' ;?>['+ ui.item.value+']\" /></td><td class=\"text-center\"><a href=\"javascript:void(0);\" onclick=\"removeThisRelatedRow(\'upSell\','+ui.item.value+')\"><span class="icon icon-trash text-danger"></span></a></td></tr>').appendTo('#addedProductUpsell');
				this.value = '';
				return false;
			},
			search : function(event, ui) {
				$('#upsellSelector').addClass('optionsLoading');
			}
		});
	});
	})(j2store.jQuery);


(function($) {
	$(document).ready(function() {
		$('#crossSellSelector').autocomplete({
			source : function(request, response) {
				var crosssell = {
					option: 'com_j2store',
					view: 'products',
					task: 'getRelatedProducts',
					product_id: '<?php echo $this->item->j2store_product_id;?>',
					q: request.term
				};
				$.ajax({
					type : 'post',
					url  : '<?php echo Route::_('index.php');?>',
					data : crosssell,
					dataType : 'json',
					success : function(data) {
						$('#crossSellSelector').removeClass('optionsLoading');
						response($.map(data['products'], function(item) {
							return {
								label: item.product_name,
								value: item.j2store_product_id
							}
						}));
					}
				});
			},
			minLength : 2,
			select : function(event, ui) {
				$('<tr id="crossSell-'+ui.item.value+'"><td class=\"addedProductCrosssell\">' + ui.item.label+ '<input type="hidden" value="'+ ui.item.value+'"  name=\"<?php echo $this->form_prefix.'[cross_sells]' ;?>['+ ui.item.value+']\" /></td><td class=\"text-center\"><a href=\"javascript:void(0);\" onclick=\"removeThisRelatedRow(\'crossSell\','+ui.item.value+')\"><span class="icon icon-trash text-danger"></span></a></td></tr>').appendTo('#addedProductCrosssell');
				this.value = '';
				return false;
			},
			search : function(event, ui) {
				$('#crossSellSelector').addClass('optionsLoading');
			}
		});
	});
	})(j2store.jQuery);


	function removeThisRelatedRow(type,p_id){
		(function($){
			$("#"+type+'-'+p_id).remove();
		})(j2store.jQuery);
	}
</script>

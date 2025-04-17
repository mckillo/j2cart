<?php
/**
 * @package     Joomla.Module
 * @subpackage  J2Commerce.mod_j2commerce_checklist
 *
 * @copyright Copyright (C) 2025 J2Commerce, LLC. All rights reserved.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or later
 * @website https://www.j2commerce.com
 */

// No direct access to this file
defined ( '_JEXEC' ) or die ();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

require_once (JPATH_ADMINISTRATOR.'/components/com_j2store/helpers/j2store.php');

$wa  = Factory::getApplication()->getDocument()->getWebAssetManager();
/*$wa->registerAndUseScript('j2store-chart-script',Uri::root().'media/j2store/js/chart.js',[],[],[]);*/

$currency = J2Store::currency();
$currency_symbol = J2Store::currency()->getSymbol();

$style = '#dayChart{min-height: 220px;};';
//$wa->addInlineStyle($style, [], []);

$currentUri = Uri::getInstance()->toString();
$encodedUrl = htmlspecialchars($currentUri, ENT_QUOTES, 'UTF-8');
?>
<?php if($danger_count > 0):?>
    <div class="j2commerce_checklist">
        <div class="alert alert-warning mb-0 rounded-bottom-0" role="alert">
            <?php echo Text::sprintf('MOD_J2COMMERCE_CHECKLIST_ALERT_WARNING',$success_count, $total_count);?>
            <div class="progress rounded-0" style="background-color:var(--state-warning-bg);" role="progressbar" aria-label="Example with label" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar" style="background-color:var(--state-warning-text);width: <?php echo $completion_percentage;?>%"><?php echo $completion_percentage;?>%</div>
            </div>
        </div>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="j2commerceChecklist" aria-labelledby="j2commerceChecklistLabel">
            <div class="offcanvas-header">
                <h2 class="offcanvas-title" id="j2commerceChecklistLabel"><img src="<?php echo $j2c_logo;?>" alt="" class="img-fluid"></h2>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <h4 class="mb-3 pb-2 border-bottom"><?php echo Text::_('MOD_J2COMMERCE_CHECKLIST_QUICKSTART');?></h4>
                <ul class="list-unstyled mb-0">
                    <?php foreach ($menuitems as $menuItem) : ?>
                        <?php if ($menuItem['exists'] && $menuItem['published']) : ?>
                            <li class="mb-3 mt-0 alert alert-success border-0 py-2 px-3">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <span class="fas fa-check-circle fa-solid fa-circle-check text-success text-opacity-50 fs-3"></span>
                                    </div>
                                    <div class="ms-2">
                                        <span><?php echo htmlspecialchars($menuItem['label']).' '.Text::_('MOD_J2COMMERCE_CHECKLIST_MENU'); ?></span>
                                    </div>
                                </div>
                            </li>
                        <?php elseif ($menuItem['exists'] && !$menuItem['published']) :?>
                            <li class="mb-3 mt-0 alert alert-warning border-0 py-2 px-3">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <span class="fas fa-exclamation-circle fa-solid fa-circle-exclamation text-warning text-opacity-50 fs-3"></span>
                                    </div>
                                    <div class="ms-2">
                                        <span><?php echo htmlspecialchars($menuItem['label']).' '.Text::_('MOD_J2COMMERCE_CHECKLIST_MENU'); ?></span>
                                    </div>
                                    <div class="ms-auto">
                                        <form class="menu-publish-form" method="POST" action="">
                                            <input type="hidden" name="link" value="<?php echo htmlspecialchars($menuItem['link']); ?>">
                                            <input type="hidden" name="label" value="<?php echo htmlspecialchars($menuItem['label']); ?>">
                                            <input type="hidden" name="publish" value="1">
                                            <input type="hidden" name="pageurl" value="<?php echo $encodedUrl; ?>">
                                            <button type="submit" class="btn btn-outline-warning btn-sm"><?php echo Text::_('MOD_J2COMMERCE_CHECKLIST_MENU_PUBLISH');?></button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        <?php else:?>
                            <li class="mb-3 mt-0 alert alert-danger border-0 py-2 px-3">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <span class="fas fa-times-circle fa-solid fa-circle-xmark text-danger text-opacity-50 fs-3"></span>
                                    </div>
                                    <div class="ms-2">
                                        <span><?php echo htmlspecialchars($menuItem['label']).' '.Text::_('MOD_J2COMMERCE_CHECKLIST_MENU'); ?></span>
                                    </div>
                                    <div class="ms-auto">
                                        <form class="menu-create-form" method="POST" action="">
                                            <input type="hidden" name="link" value="<?php echo htmlspecialchars($menuItem['link']); ?>">
                                            <input type="hidden" name="label" value="<?php echo htmlspecialchars($menuItem['label']); ?>">
                                            <input type="hidden" name="pageurl" value="<?php echo $encodedUrl; ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm"><?php echo Text::_('MOD_J2COMMERCE_CHECKLIST_MENU_ADD');?></button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        <?php endif;?>
                    <?php endforeach; ?>
                    <?php if($visible_products){ ?>
                        <li class="mb-3 mt-0 alert alert-success border-0 py-2 px-3">
                            <div class="d-flex align-items-center">
                                <div>
                                    <span class="fas fa-check-circle fa-solid fa-circle-check text-success text-opacity-50 fs-3"></span>
                                </div>
                                <div class="ms-2">
                                    <span><?php echo Text::_('MOD_J2COMMERCE_CHECKLIST_VISIBLE_PRODUCTS_YES'); ?></span>
                                </div>
                                <div class="ms-auto">
                                    <a role="button" href="<?php echo Route::_('index.php?option=com_j2store&view=products');?>" class="btn btn-outline-success btn-sm" title="<?php echo Text::_('MOD_J2COMMERCE_CHECKLIST_PRODUCT_VIEW');?>"><?php echo Text::_('MOD_J2COMMERCE_CHECKLIST_PRODUCT_VIEW');?></a>
                                </div>
                            </div>
                        </li>
                    <?php } else { ?>
                        <li class="mb-3 mt-0 alert alert-danger border-0 py-2 px-3">
                            <div class="d-flex align-items-center">
                                <div>
                                    <span class="fas fa-times-circle fa-solid fa-circle-xmark text-danger text-opacity-50 fs-3"></span>
                                </div>
                                <div class="ms-2">
                                    <span><?php echo Text::_('MOD_J2COMMERCE_CHECKLIST_VISIBLE_PRODUCTS_NO'); ?></span>
                                </div>
                                <div class="ms-auto">
                                    <a role="button" href="<?php echo Route::_('index.php?option=com_content&view=article&layout=edit');?>" class="btn btn-outline-danger btn-sm" title="<?php echo Text::_('MOD_J2COMMERCE_CHECKLIST_PRODUCT_ADD');?>"><?php echo Text::_('MOD_J2COMMERCE_CHECKLIST_PRODUCT_ADD');?></a>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
    <script>
        document.querySelectorAll('.menu-create-form').forEach(form => {
            form.addEventListener('submit', event => {
                event.preventDefault(); // Prevent default form submission

                const formData = new FormData(form);
                const submitButton = form.querySelector('button');

                fetch('<?php echo $_SERVER['REQUEST_URI']; ?>', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload(); // Reload page to update the status
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(err => {
                        // console.error('Error:', err);
                        //alert('An error occurred while creating the menu item.');
                    });

                submitButton.disabled = true; // Disable button to prevent double submission
            });
        });
        document.querySelectorAll('.menu-publish-form').forEach(form => {
            form.addEventListener('submit', event => {
                event.preventDefault(); // Prevent default form submission

                const formData = new FormData(form);
                const submitButton = form.querySelector('button');

                fetch('<?php echo $_SERVER['REQUEST_URI']; ?>', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload(); // Reload page to update the status
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(err => {
                        // console.error('Error:', err);
                        //alert('An error occurred while creating the menu item.');
                    });

                submitButton.disabled = true; // Disable button to prevent double submission
            });
        });
    </script>
<?php endif;?>

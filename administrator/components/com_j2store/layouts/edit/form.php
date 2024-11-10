<?php
/**
* @package J2Store
* @copyright Copyright (c)2014-17 Ramesh Elamathi / J2Store.org
* @copyright Copyright (c) 2024 J2Commerce . All rights reserved.
* @license GNU GPL v3 or later
*/


// No direct access to this file
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$platform = J2Store::platform();
$platform->loadExtra('behavior.formvalidator');
$row_class = 'row';
$col_class = 'col-md-';
$alert_html = '<joomla-alert type="danger" close-text="Close" dismiss="true" role="alert" style="animation-name: joomla-alert-fade-in;"><div class="alert-heading"><span class="error"></span><span class="visually-hidden">Error</span></div><div class="alert-wrapper"><div class="alert-message" >'.Text::_('J2STORE_INVALID_INPUT_FIELD').'</div></div></joomla-alert>' ;
if (version_compare(JVERSION, '3.99.99', 'lt')) {
    $row_class = 'row-fluid';
    $col_class = 'span';
    $alert_html = '<div class="alert alert-error alert-danger">'.Text::_('J2STORE_INVALID_INPUT_FIELD').'<button type="button" class="close" data-dismiss="alert">×</button></div>' ;
}
?>
    <script  type="text/javascript">
        Joomla.submitbutton = function(pressbutton) {
            var form = document.adminForm;
            if (pressbutton == 'cancel') {
                document.adminForm.task.value = pressbutton;
                form.submit();
            }else{
                if (document.formvalidator.isValid(form)) {
                    document.adminForm.task.value = pressbutton;
                    form.submit();
                }
                else {
                    let msg = [];
                    msg.push('<?php echo $alert_html; ?>');
                    document.getElementById('system-message-container').innerHTML =  msg.join('\n') ;
                }
            }
        }
    </script>

    <div class="j2store_<?php echo $vars->view; ?>_edit">
        <div class="main-card card">
            <div class="card-body">
                <form id="adminForm" class="form-horizontal form-validate" action="<?php echo $vars->action_url; ?>" method="post" name="adminForm">
                    <?php echo J2Html::hidden('option', 'com_j2store'); ?>
                    <?php echo J2Html::hidden('view', $vars->view); ?>
                    <?php if (isset($vars->primary_key) && !empty($vars->primary_key)): ?>
                        <?php echo J2Html::hidden($vars->primary_key, $vars->id); ?>
                    <?php endif; ?>
                    <?php echo J2Html::hidden('task', '', array('id' => 'task')); ?>
                    <?php echo HTMLHelper::_( 'form.token' ); ?>
                    <div class="<?php echo $row_class; ?>">
                        <?php if (isset($vars->field_sets) && !empty($vars->field_sets)): ?>
                            <?php foreach ($vars->field_sets as $field_set): ?>
                                <?php if (isset($field_set['fields']) && !empty($field_set['fields'])): ?>
                                    <div <?php echo isset($field_set['id']) && $field_set['id'] ? 'id="' . $field_set['id'] . '"' : ''; ?>
                                        <?php echo isset($field_set['class']) && is_array($field_set['class']) ? 'class="' . implode(' ', $field_set['class']) . '"' : ''; ?>>
                                        <fieldset class="options-form">
	                                        <?php if (isset($field_set['label']) && !empty($field_set['label'])): ?>
                                                <legend><?php echo Text::_($field_set['label']); ?></legend>
	                                        <?php endif; ?>
                                            <div class="form-grid">
                                                <?php foreach ($field_set['fields'] as $field_name => $field): ?>
                                                    <?php $is_required = isset($field['options']['required']) && !empty($field['options']['required']) ? true : false; ?>
                                                    <div class="control-group">
                                                        <div class="control-label">
                                                            <label for="<?php echo $field['name'];?>"><?php echo Text::_($field['label']); ?><?php echo $is_required ? "<span>*</span>" : ''; ?></label>
                                                        </div>
                                                        <div class="controls">
                                                            <?php if (isset($field['type']) && in_array($field['type'], array('number', 'text', 'email', 'password', 'textarea', 'file', 'radio', 'checkbox', 'button', 'submit'))): ?>
                                                                <?php echo J2Html::input($field['type'], $field['name'], $field['value'], $field['options']); ?>
                                                            <?php else: ?>
                                                                <?php echo J2Html::custom($field['type'], $field['name'], $field['value'], $field['options']); ?>
                                                            <?php endif; ?>
                                                            <?php if (isset($field['desc']) && !empty($field['desc'])): ?>
                                                                <small><?php echo Text::_($field['desc']); ?></small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </fieldset>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>                
    </div>



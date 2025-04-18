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

class JFormFieldCustomeditlink extends F0FFormFieldText
{
	protected $type = 'customeditlink';

/**
	 * Get the rendering of this field type for a repeatable (grid) display,
	 * e.g. in a view listing many item (typically a "browse" task)
	 *
	 * @since 2.0
	 *
	 * @return  string  The field HTML
	 */
	public function getRepeatable()
	{
		// Initialise
		$class					= $this->id;
		$format_string			= '';
		$format_if_not_empty	= false;
		$parse_value			= false;
		$show_link				= false;
		$link_url				= '';
		$empty_replacement		= '';

		// Get field parameters
		if ($this->element['class'])
		{
			$class = (string) $this->element['class'];
		}

		if ($this->element['format'])
		{
			$format_string = (string) $this->element['format'];
		}

		if ($this->element['show_link'] === 'true')
		{
			$show_link = true;
		}

		if ($this->element['format_if_not_empty'] === 'true')
		{
			$format_if_not_empty = true;
		}

		if ($this->element['parse_value'] === 'true')
		{
			$parse_value = true;
		}


		if ($this->element['empty_replacement'])
		{
			$empty_replacement = (string) $this->element['empty_replacement'];
		}

		// Get the (optionally formatted) value
		$value = $this->value;

		if (!empty($empty_replacement) && empty($this->value))
		{
			$value = Text::_($empty_replacement);
		}

		if ($parse_value)
		{
			$value = $this->parseFieldTags($value);
		}

		if (!empty($format_string) && (!$format_if_not_empty || ($format_if_not_empty && !empty($this->value))))
		{
			$format_string = $this->parseFieldTags($format_string);
			$value = sprintf($format_string, $value);
		}
		else
		{
			$value = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
		}

		// Create the HTML
		$html = '<span class="' . $class . '">';
		$view =  Factory::getApplication()->input->getString('view');
		if($this->item->element === 'shipping_standard'||
		$this->item->element === 'shipping_postcode' ||
		$this->item->element === 'shipping_flatrate_advanced'
		){
			$link_url = 'index.php?option=com_j2store&view='.$view.'&task=view&layout=view&id='.$this->item->extension_id;
			$html .= '<a href="' . $link_url . '">';
		}else{
			$link_url ='index.php?option=com_plugins&task=plugin.edit&extension_id='.$this->item->extension_id;
			$html .='<a class="link" target="_blank" href="'.$link_url.'">';
		}

		$html .= $value;
		$html .= '</a>';
		$html .= '</span>';
		return $html;
	}
}

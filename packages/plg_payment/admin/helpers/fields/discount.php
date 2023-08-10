<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/textbox.php';

class RSFormProFieldDiscount extends RSFormProFieldTextbox
{
	public function getPreviewInput()
	{
		$value 		 = (string) $this->getProperty('COUPONS', '');
		$caption 	 = $this->getProperty('CAPTION','');
		$size 		 = $this->getProperty('SIZE', 0);
		$placeholder = $this->getProperty('PLACEHOLDER', '');
		$codeIcon = $this->hasCode($value) ? RSFormProHelper::getIcon('php') : '';

		$html = '<td>'.$caption.'</td>';
		$html .= '<td>'.$codeIcon.' <span class="rsficon" style="font-size:24px;margin-right:5px">%</span><input type="text" value="" size="'.(int) $size.'" '.(!empty($placeholder) ? 'placeholder="'.$this->escape($placeholder).'"' : '').'/></td>';

		return $html;
	}

	public function getFormInput()
	{
		RSFormProAssets::addStyleSheet(JHtml::_('stylesheet', 'plg_system_rsfppayment/style.css', array('relative' => true, 'pathOnly' => true)));
		RSFormProAssets::addScript(JHtml::_('script', 'plg_system_rsfppayment/script.js', array('pathOnly' => true, 'relative' => true)));

		$value 			= isset($this->value[$this->name]) ? $this->value[$this->name] : '';
		$name 			= $this->getName();
		$id 			= $this->getId();
		$size 			= $this->getProperty('SIZE', 0);
		$maxlength 		= $this->getProperty('MAXSIZE', 0);
		$placeholder 	= $this->getProperty('PLACEHOLDER', '');
		$type 			= 'text';
		$attr 			= $this->getAttributes();
		$additional 	= '';

		$html = '<input';
		if ($attr) {
			foreach ($attr as $key => $values) {
				// @new feature - Some HTML attributes (type, size, maxlength) can be overwritten
				// directly from the Additional Attributes area
				if (($key == 'type' || $key == 'size' || $key == 'maxlength') && strlen($values)) {
					${$key} = $values;
					continue;
				}
				$additional .= $this->attributeToHtml($key, $values);
			}
		}
		// Set the type & value
		$html .= ' type="'.$this->escape($type).'"'.
			' value="'.$this->escape($value).'"';
		// Size
		if ($size) {
			$html .= ' size="'.(int) $size.'"';
		}
		// Maxlength
		if ($maxlength && in_array($type, array('text', 'email', 'tel', 'url'))) {
			$html .= ' maxlength="'.(int) $maxlength.'"';
		}

		// Placeholder
		if (!empty($placeholder)) {
			$html .= ' placeholder="'.$this->escape($placeholder).'"';
		}

		$html .= ' oninput="getPrice_' . $this->formId . '();"';

		// Name & id
		$html .= ' name="'.$this->escape($name).'"'.
			' id="'.$this->escape($id).'"';
		// Additional HTML
		$html .= $additional;
		// Close the tag
		$html .= ' />';

		return $html;
	}
}
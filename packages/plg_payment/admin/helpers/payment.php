<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2015 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

abstract class RSFormProPaymentHelper
{
	protected static $products = array();

	public static function getPriceMask($txt, $val)
	{
		$nodecimals = RSFormProHelper::getConfig('payment.nodecimals');
		$decimal    = RSFormProHelper::getConfig('payment.decimal');
		$thousands  = RSFormProHelper::getConfig('payment.thousands');
		$currency   = RSFormProHelper::getConfig('payment.currency');

		$mask = RSFormProHelper::getConfig('payment.mask');

		$formattedPrice = number_format((float) $val, $nodecimals, $decimal, $thousands);
		$replacements   = array(
			'{product}' 	=> $txt,
			'{price}' 		=> $formattedPrice,
			'{currency}' 	=> $currency,
		);

		return str_replace(array_keys($replacements), array_values($replacements), $mask);
	}

	public static function getTotalMask($val)
	{
		$nodecimals = RSFormProHelper::getConfig('payment.nodecimals');
		$decimal    = RSFormProHelper::getConfig('payment.decimal');
		$thousands  = RSFormProHelper::getConfig('payment.thousands');
		$currency   = RSFormProHelper::getConfig('payment.currency');

		$mask = RSFormProHelper::getConfig('payment.totalmask');

		$formattedPrice = number_format((float) $val, $nodecimals, $decimal, $thousands);
		$replacements   = array(
			'{price}' 		=> $formattedPrice,
			'{currency}' 	=> $currency,
			'{grandtotal}' 	=> 0,
			'{tax}' 		=> 0,
		);

		return str_replace(array_keys($replacements), array_values($replacements), $mask);
	}

	public static function getPayments($formId)
	{
		$items = array();

		JFactory::getApplication()->triggerEvent('onRsformGetPayment', array(&$items, $formId));

		return $items;
	}

	public static function addProduct($componentId, $price, $txt)
	{
		if (!isset(self::$products[$componentId]))
		{
			self::$products[$componentId] = array();
		}

		self::$products[$componentId][] = array(
			'val' => $price,
			'txt' => $txt
		);
	}

	public static function getProducts($componentId)
	{
		if (isset(self::$products[$componentId]))
		{
			return self::$products[$componentId];
		}

		return array();
	}
}
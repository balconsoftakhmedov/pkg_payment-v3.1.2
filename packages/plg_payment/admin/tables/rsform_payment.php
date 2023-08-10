<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableRSForm_Payment extends JTable
{
	public $form_id;
	public $params;
	
	public function __construct(& $db)
	{
		parent::__construct('#__rsform_payment', 'form_id', $db);
	}

	// Validate data before save
	public function check()
	{
		// Convert $params to serialized object
		if (!is_string($this->params))
		{
			$this->params = serialize($this->params);
		}

		return true;
	}

	public function hasPrimaryKey()
	{
		$db 	= $this->getDbo();
		$key 	= $this->getKeyName();
		$table	= $this->getTableName();

		$query = $db->getQuery(true)
			->select($db->qn($key))
			->from($db->qn($table))
			->where($db->qn($key) . ' = ' . $db->q($this->{$key}));

		return $db->setQuery($query)->loadResult() !== null;
	}

	public function setDefaultParams()
	{
		if (!is_object($this->params))
		{
			$this->params = new stdClass();
		}

		foreach (array('UserEmail', 'AdminEmail', 'AdditionalEmails', 'DisableDeferOfflinePayment', 'SilentPost', 'Mappings') as $def_param)
		{
			if (!isset($this->params->{$def_param}))
			{
				$this->params->{$def_param} = 0;
			}
		}
	}

	public function bind($src, $ignore = array())
	{
		if (isset($src['params']))
		{
			if (is_array($src['params']))
			{
				$src['params'] = (object) $src['params'];
			}
			elseif (is_string($src['params']))
			{
				$src['params'] = @unserialize($src['params']);

				if ($src['params'] === false || !is_object($src['params']))
				{
					$src['params'] = new stdClass();
				}
			}

			foreach (array('UserEmail', 'AdminEmail', 'AdditionalEmails', 'DisableDeferOfflinePayment', 'SilentPost', 'Mappings') as $def_param)
			{
				if (!isset($src['params']->{$def_param}))
				{
					$src['params']->{$def_param} = 0;
				}
			}
		}

		return parent::bind($src, $ignore);
	}
}
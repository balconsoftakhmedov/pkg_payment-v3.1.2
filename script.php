<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2018 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class pkg_RSFormProMultiPaymentPluginsInstallerScript
{
	protected static $minJoomla = '3.10.0';
	protected static $minComponent = '3.1.2';
	
	public function preflight($type, $parent)
	{
		if ($type == 'uninstall')
		{
			return true;
		}
		
		try
		{
			$source = $parent->getParent()->getPath('source');
		
			$jversion = new JVersion();
			if (!$jversion->isCompatible(static::$minJoomla))
			{
				throw new Exception(sprintf('Please upgrade to at least Joomla! %s before continuing!', static::$minJoomla));
			}

			if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php'))
			{
				throw new Exception('Please install the RSForm! Pro component before continuing.');
			}

			if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/assets.php') || !file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/version.php'))
			{
				throw new Exception(sprintf('Please upgrade RSForm! Pro to at least version %s before continuing!', static::$minComponent));
			}

			// Check version matches
			require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/version.php';

			if (!class_exists('RSFormProVersion') || version_compare((string) new RSFormProVersion, static::$minComponent, '<'))
			{
				throw new Exception(sprintf('Please upgrade RSForm! Pro to at least version %s before continuing!', static::$minComponent));
			}
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}
		
		return true;
	}
	
	public function postflight($type, $parent) {
		if ($type == 'uninstall') {
			return true;
		}
		
		$this->enablePlugin('rsfpmultipaypal');
	}
	
	protected function runSQL($source, $file, $package='')
	{
		$db 	= JFactory::getDbo();
		$driver = strtolower($db->name);
		if (strpos($driver, 'mysql') !== false)
		{
			$driver = 'mysql';
		}
		
		if ($package)
		{
			$source .= '/packages/'.$package;
		}

		$sqlfile = $source . '/sql/' . $driver . '/' . $file . '.sql';
		
		if (file_exists($sqlfile))
		{
			$buffer = file_get_contents($sqlfile);
			if ($buffer !== false)
			{
				$queries = $db->splitSql($buffer);
				foreach ($queries as $query)
				{
					$query = trim($query);
					if ($query != '')
					{
						$db->setQuery($query)->execute();
					}
				}
			}
		}
	}
	
	protected function enablePlugin($element) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		
		$query->update('#__extensions')
			  ->set($db->qn('enabled').'='.$db->q(1))
			  ->where($db->qn('type').'='.$db->q('plugin'))
			  ->where($db->qn('folder').'='.$db->q('system'))
			  ->where($db->qn('element').'='.$db->q($element));
		$db->setQuery($query);
		return $db->execute();
	}
}
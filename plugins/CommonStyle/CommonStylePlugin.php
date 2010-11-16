<?php
/**
 * MyTest Plugin
 *
 * @category Plugin
 * @package  Statusnet
 * @author   chuck911
 * @license  http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @version  CommonStylePlugin.php,v 0.1
 *
 */

if (!defined('STATUSNET')) {
    exit(1);
}

class CommonStylePlugin extends Plugin
{
	// function onStartShowFooter($action)
	//     {
	//         //return false;
	//     }

	public function onStartShowSections($action)
	{
		if(!property_exists($action,'user'))return true;
		$briefStats = new BriefStats($action,$action->user);
		$briefStats->show();
	}
	
	function onEndShowDesign($action)
	{
	    $user = common_current_user();

	    if (empty($user) || $user->viewdesigns) {
		$design = $action->getDesign();

		if (!empty($design)) {
		    $css = '';
		    //$design->showCSS($this);
		    //$css .= '#aside_primary { background-color: #'. $sbcolor->hexValue() . ' }' . "\n";
		    $sbcolor = Design::toWebColor($design->sidebarcolor);
		    if (!empty($sbcolor)) {
			$css = '#site_nav_local_views,#brief_stats_container { background-color: #'. $sbcolor->hexValue() . ' }' . "\n";
		    }
		    if (0 != mb_strlen($css)) {
			$action->style($css);
		    }
		}
	    }
	}
		
	function onAutoload($cls)
	{
	    $dir = dirname(__FILE__);
    
	    switch ($cls)
	    {
		    case 'BriefStats':
		    include_once $dir . '/'.$cls.'.php';
		return false;
		    default:
		    return true;
	    }
	}
}
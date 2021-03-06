<?php
/**
 * CommonStyle Plugin
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

require_once 'ColorConverter.php';

class CommonStylePlugin extends Plugin
{
	// function onStartShowFooter($action)
	//     {
	//         //return false;
	//     }
	
	//add local nav and briefStats in sidebar 
	public function onStartShowSections($action)
	{
	    //common_log(7,$action->trimmed('action'));
	    if(property_exists($action,'user')){
		$briefStats = new BriefStats($action,$action->user);
		$briefStats->show();
	    }		
	    $action->showLocalNavBlock();
	}
	
	//remove local nav from original position
	function onStartShowLocalNavBlock($action){
	    
	    return false;
	}
	
	function onStartShowPageTitle($action){
	    $actionName = $action->trimmed('action');
	    if (common_logged_in()) {
		$cur = common_current_user();
		if($actionName=='public' || $actionName=='all' && $cur->id == $action->profile->id)
		    $action->showNoticeForm();
	    }
	    return true;
	}
	
	//add Index to main menu
	function onStartPrimaryNav($action){
	    $action->menuItem(common_local_url(''),'首页');
	}
	
	//隐藏页脚导航
	function onStartSecondaryNav($action){
	    return false;
	}
	
	function onStartShowNoticeForm($action){
	    return false;
	}
	
	function onStartShowUAStyles($action)
	{
	    return false;
	}
	
	function onStartRegistrationFormData($action)
	{
	    $action->elementStart('li');
            $action->input('nickname', _('Nickname'), $action->trimmed('nickname'),
                         _('1-64 lowercase letters or numbers, '.
                           'no punctuation or spaces. Required.'));
            $action->elementEnd('li');
            $action->elementStart('li');
            $action->password('password', _('Password'),
                            _('6 or more characters. Required.'));
            $action->elementEnd('li');
            $action->elementStart('li');
            $action->password('confirm', _('Confirm'),
                            _('Same as password above. Required.'));
            $action->elementEnd('li');
            $action->elementStart('li');
            if ($action->invite && $action->invite->address_type == 'email') {
                $action->input('email', _('Email'), $action->invite->address,
                             _('Used only for updates, announcements, '.
                               'and password recovery'));
            } else {
                $action->input('email', _('Email'), $action->trimmed('email'),
                             _('Used only for updates, announcements, '.
                               'and password recovery'));
            }
            $action->elementEnd('li');
            $action->elementStart('li');
            $action->input('fullname', _('Full name'),
                         $action->trimmed('fullname'),
                         _('Longer name, preferably your "real" name'));
            $action->elementEnd('li');
	    
	    Event::handle('EndRegistrationFormData', array($action));
            $action->elementStart('li', array('id' => 'settings_rememberme'));
            $action->checkbox('rememberme', _('Remember me'),
                            $action->boolean('rememberme'),
                            _('Automatically login in the future; '.
                              'not for shared computers!'));
            $action->elementEnd('li');
            $attrs = array('type' => 'checkbox',
                           'id' => 'license',
                           'class' => 'checkbox',
                           'name' => 'license',
                           'value' => 'true');
            if ($action->boolean('license')) {
                $attrs['checked'] = 'checked';
            }
            $action->elementStart('li');
            $action->element('input', $attrs);
            $action->elementStart('label', array('class' => 'checkbox', 'for' => 'license'));
            $action->raw($action->licenseCheckbox());
            $action->elementEnd('label');
            $action->elementEnd('li');
	    return false;
	}
	
	function onEndShowDesign($action)
	{
	    $css = '#aside_primary { background: none }';
	    $user = common_current_user();

	    if (empty($user) || $user->viewdesigns) {
		$design = $action->getDesign();

		if (!empty($design)) {
		    //$design->showCSS($this);
		    //$css .= '#aside_primary { background-color: #'. $sbcolor->hexValue() . ' }' . "\n";
		    $sbcolor = Design::toWebColor($design->sidebarcolor);
		    if (!empty($sbcolor)) {
			$css .= '#core { background-color: #'. $sbcolor->hexValue() . '; }' . "\n";
			$hex = $this->emphasizeColor($sbcolor);
			$css .= '#content,#site_nav_local_views li,#site_nav_local_views li.current { border-color: '. $hex . '; }' . "\n";
			$css .= '#site_nav_local_views a:hover { background-color: '. $hex . '; }' . "\n";
		    }
		    if (0 != mb_strlen($css)) {
			$action->style($css);
		    }
		}
	    }
	}
	
	function emphasizeColor($color){
	    $colorConverter = new ColorConverter;
	    $hsl = $colorConverter->RGB2HSL($color->red,$color->green,$color->blue);
	    $hsl[2]>50 ? $hsl[2] -= 10 : $hsl[2] += 10;
	    if($hsl[2]>100)$hsl[2]=100;
	    else if($hsl[2]<0)$hsl[2]=0;
	    return $colorConverter->HSL2HEX($hsl[0],$hsl[1],$hsl[2]);
	}
	
	function onEndShowScripts($action){
	    $action->script('local/plugins/CommonStyle/commonstyle.js');
	}
	
	function onRouterInitialized($m)
	{
	    $m->connect('notice/form',
			array('action' => 'NoticeForm'));
    
	    return true;
	}
	
	function onAutoload($cls)
	{
	    $dir = dirname(__FILE__);
    
	    switch ($cls)
	    {
		case 'BriefStats':
		case 'NoticeFormAction':
		    include_once $dir . '/'.$cls.'.php';
	    	return false;
		    default:
		    return true;
	    }
	}
}
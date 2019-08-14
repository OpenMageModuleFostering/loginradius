<?php
class Loginradius_Sociallogin_Block_Share extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface {
	private $loginRadiusShare;
	public function __construct(){
		$this->loginRadiusShare = new Loginradius_Sociallogin_Block_Sociallogin();
	}
    protected function _toHtml() {
        $content = "";
		if ($this->loginRadiusShare->shareEnable() == "1" ){
            $content = "<div class='lrsharecontainer'></div>";
		}
        return $content;
    }
    protected function _prepareLayout() {
        parent::_prepareLayout();
    }
}
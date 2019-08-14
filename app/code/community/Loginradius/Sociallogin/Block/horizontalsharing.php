<?php
class Loginradius_Sociallogin_Block_Horizontalsharing extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface {
	private $loginRadiusHorizontalSharing;
	public function __construct(){
		$this->loginRadiusHorizontalSharing = new Loginradius_Sociallogin_Block_Sociallogin();
	}
    protected function _toHtml() {
        $content = "";
		if ($this->loginRadiusHorizontalSharing->horizontalShareEnable() == "1" ){
            $content = "<div class='loginRadiusHorizontalSharing'></div>";
		}
        return $content;
    }
    protected function _prepareLayout() {
        parent::_prepareLayout();
    }
}
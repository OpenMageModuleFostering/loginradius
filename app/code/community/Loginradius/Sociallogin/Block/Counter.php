<?php
class Loginradius_Sociallogin_Block_Counter extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface {
	private $loginRadiusCounter;
	public function __construct(){
		$this->loginRadiusCounter = new Loginradius_Sociallogin_Block_Sociallogin();
	}
    protected function _toHtml() {
        $content = "";
		if ($this->loginRadiusCounter->counterEnable() == "1" ){
            $content = "<div class='lrcounter_simplebox'></div>";
		}
        return $content;
    }
    protected function _prepareLayout() {
        parent::_prepareLayout();
    }
}
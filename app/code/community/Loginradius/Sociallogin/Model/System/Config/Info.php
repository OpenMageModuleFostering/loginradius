<?php
class Loginradius_Sociallogin_Model_System_Config_Info extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
	/**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element){
        $html = $element->getComment();
        if(!$html){
        	$html = $element->getText();
    	}
		?>
		
							<fieldset class="loginRadiusFieldset" style="margin-right:13px; background-color:#EAF7FF; border-color:rgb(195, 239, 250); padding-bottom:25px; width:65%">
		<h4 style="color:#000"><strong>Thank you for installing the LoginRadius Social Plugin!</strong></h4>
		<p>To activate the plugin, you will need to first configure it (manage your desired social networks, etc.) from your LoginRadius account. If you do not have an account, click <a target="_blank" href="http://www.loginradius.com/">here</a> and create one for FREE!</p>
		<p>
		We also offer Social Plugins for  <a href="http://www.loginradius.com/addons/wordpress" target="_blank">Wordpress</a>, <a href="http://www.loginradius.com/addons/joomla" target="_blank">Joomla</a>, <a href="http://www.loginradius.com/addons/drupal" target="_blank">Drupal</a>, <a href="http://www.loginradius.com/addons/vbulletin" target="_blank">vBulletin</a>, <a href="http://www.loginradius.com/addons/vanilla" target="_blank">VanillaForum</a>, <a href="http://www.loginradius.com/addons/oscommerce" target="_blank">OSCommerce</a>, <a href="http://www.loginradius.com/addons/prestashop" target="_blank">PrestaShop</a>, <a href="http://www.loginradius.com/addons/Xcart" target="_blank">X-Cart</a>, <a href="http://www.loginradius.com/addons/zencart" target="_blank">Zen-Cart</a>, <a href="http://www.loginradius.com/addons/dotnetnuke" target="_blank">DotNetNuke</a> and <a href="http://www.loginradius.com/addons/blogengine" target="_blank">BlogEngine</a>!
		</p>
		<div style="margin-top:10px">
		<a style="text-decoration:none;" href="https://www.loginradius.com/" target="_blank">
			<input class="form-button" type="button" value="Set up my FREE account!">
		</a>
		<a class="loginRadiusHow" target="_blank" href="http://support.loginradius.com/customer/portal/articles/593954">(How to set up an account?)</a>
		</div>
		</fieldset>
		<!-- Get Updates -->			
		<fieldset class="loginRadiusFieldset" style="width:26%; background-color: rgb(231, 255, 224); border: 1px solid rgb(191, 231, 176); padding-bottom:6px;">
		<h4 style="border-bottom:#d7d7d7 1px solid;"><strong>Get Updates</strong></h4>
		<p>To receive updates on new features, future releases, etc, please connect with us via Facebook and Twitter-</p>
		<div>
			<div style="float:left">
				<iframe rel="tooltip" scrolling="no" frameborder="0" allowtransparency="true" style="border: none; overflow: hidden; width: 46px;
							height: 61px; margin-right:10px" src="//www.facebook.com/plugins/like.php?app_id=194112853990900&amp;href=http%3A%2F%2Fwww.facebook.com%2Fpages%2FLoginRadius%2F119745918110130&amp;send=false&amp;layout=box_count&amp;width=90&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font=arial&amp;height=90" data-original-title="Like us on Facebook"></iframe>
				</div>
		<div>
		<div class="twitter_box"><span id="followers">880</span></div>
		<iframe allowtransparency="true" frameborder="0" scrolling="no" src="http://platform.twitter.com/widgets/follow_button.1363148939.html#_=1363649731067&amp;id=twitter-widget-0&amp;lang=en&amp;screen_name=LoginRadius&amp;show_count=false&amp;show_screen_name=false&amp;size=m" class="twitter-follow-button twitter-follow-button" style="width: 60px; height: 20px;" title="Twitter Follow Button" data-twttr-rendered="true"></iframe>
		</div>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</div>
		</fieldset>
		<!-- Help & Documentation -->
		<fieldset class="loginRadiusHelpDiv" style="margin-right:13px; width:65%">
		<h4 style="border-bottom:#d7d7d7 1px solid;"><strong>Help &amp; Documentations</strong></h4>
		<ul style="float:left; margin-right:43px">
			<li><a target="_blank" href="http://support.loginradius.com/customer/portal/articles/1056696-magento-social-login-installation-configuration-and-troubleshooting">Extension Installation, Configuration and Troubleshooting</a></li>
			<li><a target="_blank" href="http://support.loginradius.com/customer/portal/articles/677100-how-to-get-loginradius-api-key-and-secret">How to get LoginRadius API Key &amp; Secret</a></li>
			<li><a target="_blank" href="http://support.loginradius.com/customer/portal/articles/1056696-magento-social-login-installation-configuration-and-troubleshooting#multisite">Magento Multisite Feature</a></li>
			<li><a target="_blank" href="http://www.loginradius.com/product/sociallogin">LoginRadius Products</a></li>
		</ul>
		<ul style="float:left; margin-right:43px">
			<li><a target="_blank" href="http://community.loginradius.com/">Discussion Forum</a></li>
			<li><a target="_blank" href="http://www.loginradius.com/loginradius/about">About LoginRadius</a></li>
			<li><a target="_blank" href="http://www.loginradius.com/addons">Social Plugins</a></li>
			<li><a target="_blank" href="http://www.loginradius.com/sdks/loginradiussdk">Social SDKs</a></li>
		</ul>
		</fieldset>
		<!-- Support Us -->
		<fieldset class="loginRadiusFieldset" style="margin-right:5px; background-color: rgb(231, 255, 224); border: 1px solid rgb(191, 231, 176); width:26%; height:112px">
		<h4 style="border-bottom:#d7d7d7 1px solid;"><strong>Support Us</strong></h4>
		<p>
		If you liked our FREE open-source plugin, please send your feedback/testimonial to <a href="mailto:feedback@loginradius.com">feedback@loginradius.com</a> !</p>
		</fieldset>
		
		<div style='clear:both'></div>
						
		<?php
    }
}
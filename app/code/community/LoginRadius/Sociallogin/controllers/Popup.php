<?php
function SL_popUpWindow( $loginRadiusPopupTxt, $socialLoginMsg = "", $loginRadiusShowForm = true, $profileData = array(), $emailRequired = true, $hideZipcode = false){
	$blockObj = new Loginradius_Sociallogin_Block_Sociallogin();
?>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<!--css of email block	-->
	<style type="text/css">
	.LoginRadius_overlay {
		background: none no-repeat scroll 0 0 rgba(127, 127, 127, 0.6);
		height: 100%;
		left: 0;
		overflow: auto;
		padding: 0px 20px 130px;
		position: fixed;
		top: 0;
		width: 100%;
		z-index: 100001;
	}
	#popupouter{
		-moz-border-radius:4px;
		-webkit-border-radius:4px;
		border-radius:4px;
		margin-left:-185px;
		left:45%;	
		background:#f3f3f3;
		padding:1px 0px 1px 0px;
		width:432px;
		position: absolute;
		top:35%;
		z-index:9999;
		margin-top:-96px;
	}
	#popupinner {
		background: none repeat scroll 0 0 #FFFFFF;
		border-radius: 4px 4px 4px 4px;
		margin: 10px;
		overflow: auto;
		padding: 10px 8px 4px;
	}	
	#textmatter {
		color: #666666;
		font-family: Arial,Helvetica,sans-serif;
		font-size: 14px;
		margin: 10px 0;
		float:left
	}	
	.loginRadiusText{
		font-family:Arial, Helvetica, sans-serif;
		color:#a8a8a8;
		font-size:11px;
		border:#e5e5e5 1px solid;
		width:280px;
		height:27px;
		margin:5px 0px 15px 0px;
		float:left
	}
	.inputbutton{
		border:#dcdcdc 1px solid;
		-moz-border-radius:2px;
		-webkit-border-radius:2px;
		border-radius:2px;
		text-decoration:none;
		color:#6e6e6e;
		font-family:Arial, Helvetica, sans-serif;
		font-size:13px;
		cursor:pointer;
		background:#f3f3f3;
		padding:6px 7px 6px 8px;
		margin:0px 8px 0px 0px;
		float:left
	}
	.inputbutton:hover{
		border:#00ccff 1px solid;
		-moz-border-radius:2px;
		-webkit-border-radius:2px;
		border-radius:2px;
		khtml-border-radius:2px;
		text-decoration:none;
		color:#000000;
		font-family:Arial, Helvetica, sans-serif;
		font-size:13px;
		cursor:pointer;
		padding:6px 7px 6px 8px;
		-moz-box-shadow: 0px 0px  4px #8a8a8a;
		-webkit-box-shadow: 0px 0px  4px #8a8a8a;
		box-shadow: 0px 0px  4px #8a8a8a;
		background:#f3f3f3;
		margin:0px 8px 0px 0px;
	}
	#textdivpopup{
		text-align:right;
		font-family:Arial, Helvetica, sans-serif;
		font-size:11px;
		color:#000000;
	}
	.spanpopup{
		font-family:Arial, Helvetica, sans-serif;
		font-size:11px;
		color:#00ccff;
	}
	.span1{
		font-family:Arial, Helvetica, sans-serif;
		font-size:11px;
		color:#333333;
	}
	<!--[if IE]>
	.LoginRadius_content_IE
	{background:black;
	-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=90)";
	filter: alpha(opacity=90);
	}
	.loginRadiusDiv{
		float:left;
		margin: 0 0 4px 2px;
	}
	.loginRadiusDiv label{
		width: 94px;
		float: left;
		margin: 5px 10px 10px 0;
		display: block;
		text-align: left;
	}
	<![endif]-->
	</style>
	<script type="text/javascript">
	// variable to check if submit button of popup is clicked
	var loginRadiusPopupSubmit = true;
	// get trim() worked in IE 
	if(typeof String.prototype.trim !== 'function') {
		  String.prototype.trim = function() {
			return this.replace(/^\s+|\s+$/g, ''); 
		  }
	}
	// validate numeric data 
	function isNumber(n) {
	  return !isNaN(parseFloat(n)) && isFinite(n);
	}
	// validate required fields form
	function loginRadiusValidateForm(){
		var loginRadiusForm = document.getElementById('loginRadiusForm');
		if(!loginRadiusPopupSubmit){
			loginRadiusForm.setAttribute('action', '<?php echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK); ?>');
			return true;
		}
		var loginRadiusErrorDiv = document.getElementById('textmatter');
		if(document.getElementById('loginRadiusCountry').value.trim() == "US"){
			var validateProvince = true;
		}else{
			var validateProvince = false;
		}
		for(var i = 0; i < loginRadiusForm.elements.length; i++){
			if(!validateProvince && loginRadiusForm.elements[i].id == "loginRadiusProvince"){
				continue;
			}
			if(loginRadiusForm.elements[i].value.trim() == ""){
				loginRadiusErrorDiv.innerHTML = "<?php echo __("Please fill all the fields."); ?>";
				loginRadiusErrorDiv.style.backgroundColor = "rgb(255, 235, 232)";
				loginRadiusErrorDiv.style.border = "1px solid rgb(204, 0, 0)";
				loginRadiusErrorDiv.style.padding = "2px 5px";
				loginRadiusErrorDiv.style.width = "94%";
				loginRadiusErrorDiv.style.textAlign = "left";
				return false;
			}
			if(loginRadiusForm.elements[i].id == "loginRadiusEmail"){
				var email = loginRadiusForm.elements[i].value.trim();
				var atPosition = email.indexOf("@");
				var dotPosition = email.lastIndexOf(".");
				if(atPosition < 1 || dotPosition < atPosition+2 || dotPosition+2>=email.length){
					loginRadiusErrorDiv.innerHTML = "<?php echo trim($blockObj -> getPopupError()) != "" ? trim($blockObj -> getPopupError()) : __('Please enter a valid email address'); ?>";
					loginRadiusErrorDiv.style.backgroundColor = "rgb(255, 235, 232)";
					loginRadiusErrorDiv.style.border = "1px solid rgb(204, 0, 0)";
					loginRadiusErrorDiv.style.padding = "2px 5px";
					loginRadiusErrorDiv.style.width = "94%";
					loginRadiusErrorDiv.style.textAlign = "left";
					return false;
				}
			}
		}
		return true;
	}
	</script>
	</head>
	<body>
	<div id="fade" class="LoginRadius_overlay">	
	<div id="popupouter">
	<div id="popupinner">
	<div id="textmatter"><strong><?php echo __(Mage::helper('core')->htmlEscape($loginRadiusPopupTxt)); ?></strong></div>
	<div style="clear:both;"></div>
	<div style="color:red; text-align:justify"><?php echo __(Mage::helper('core')->htmlEscape($socialLoginMsg)); ?></div>
	<?php
	if( $loginRadiusShowForm ){
	?>
		<form id="loginRadiusForm" method="post" action="<?php echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . 'sociallogin' ?>" onSubmit="return loginRadiusValidateForm()">
		<?php
		if($emailRequired){
			?>
			<div class="loginRadiusDiv">
			<label for="loginRadiusEmail"><?php echo __("Email"); ?> *</label>
			<input type="text" name="loginRadiusEmail" id="loginRadiusEmail" class="loginRadiusText" />
			</div>
			<?php
		}
		if(isset($profileData['Address']) && $profileData['Address'] == ""){
			?>
			<div class="loginRadiusDiv">
			<label for="loginRadiusAddress"><?php echo __("Address"); ?> *</label>
			<input type="text" name="loginRadiusAddress" id="loginRadiusAddress" class="loginRadiusText" />
			</div>
			<?php
		}
		if(isset($profileData['City']) && $profileData['City'] == ""){
			?>
			<div class="loginRadiusDiv">
			<label for="loginRadiusCity"><?php echo __("City") ?> *</label>
			<input type="text" name="loginRadiusCity" id="loginRadiusCity" class="loginRadiusText" />
			</div>
			<?php
		}
		if(!$hideZipcode){
			?>
			<div class="loginRadiusDiv">
			<label for="loginRadiusCountry"><?php echo __("Country"); ?> *</label>
			<?php
			$countries = Mage::getResourceModel('directory/country_collection')
                                    ->loadData()
                                    ->toOptionArray(false);
			if(count($countries) > 0){ ?>
				<select onChange="if(this.value == 'US'){ document.getElementById('loginRadiusProvinceContainer').style.display = 'block' }else{ document.getElementById('loginRadiusProvinceContainer').style.display = 'none' }" name="loginRadiusCountry" id="loginRadiusCountry" class="loginRadiusText">
					<option value="">-- <?php echo __("Please Select"); ?> --</option>
					<?php foreach($countries as $country): ?>
						<option value="<?php echo $country['value'] ?>">
							<?php echo $country['label'] ?>
						</option>
					<?php endforeach; ?>
				</select>
				</div>
				<!-- United States province -->
				<div style="display:none" id="loginRadiusProvinceContainer" class="loginRadiusDiv">
				<label for="loginRadiusCountry"><?php echo __("State/Province") ?> *</label>
				<select id="loginRadiusProvince" name="loginRadiusProvince" class="loginRadiusText">
<option value="" selected="selected">-- <?php echo __("Please select") ?> --</option><option value="1">Alabama</option><option value="2">Alaska</option><option value="3">American Samoa</option><option value="4">Arizona</option><option value="5">Arkansas</option><option value="6">Armed Forces Africa</option><option value="7">Armed Forces Americas</option><option value="8">Armed Forces Canada</option><option value="9">Armed Forces Europe</option><option value="10">Armed Forces Middle East</option><option value="11">Armed Forces Pacific</option><option value="12">California</option><option value="13">Colorado</option><option value="14">Connecticut</option><option value="15">Delaware</option><option value="16">District of Columbia</option><option value="17">Federated States Of Micronesia</option><option value="18">Florida</option><option value="19">Georgia</option><option value="20">Guam</option><option value="21">Hawaii</option><option value="22">Idaho</option><option value="23">Illinois</option><option value="24">Indiana</option><option value="25">Iowa</option><option value="26">Kansas</option><option value="27">Kentucky</option><option value="28">Louisiana</option><option value="29">Maine</option><option value="30">Marshall Islands</option><option value="31">Maryland</option><option value="32">Massachusetts</option><option value="33">Michigan</option><option value="34">Minnesota</option><option value="35">Mississippi</option><option value="36">Missouri</option><option value="37">Montana</option><option value="38">Nebraska</option><option value="39">Nevada</option><option value="40">New Hampshire</option><option value="41">New Jersey</option><option value="42">New Mexico</option><option value="43">New York</option><option value="44">North Carolina</option><option value="45">North Dakota</option><option value="46">Northern Mariana Islands</option><option value="47">Ohio</option><option value="48">Oklahoma</option><option value="49">Oregon</option><option value="50">Palau</option><option value="51">Pennsylvania</option><option value="52">Puerto Rico</option><option value="53">Rhode Island</option><option value="54">South Carolina</option><option value="55">South Dakota</option><option value="56">Tennessee</option><option value="57">Texas</option><option value="58">Utah</option><option value="59">Vermont</option><option value="60">Virgin Islands</option><option value="61">Virginia</option><option value="62">Washington</option><option value="63">West Virginia</option><option value="64">Wisconsin</option><option value="65">Wyoming</option></select>
			<?php }else{
			  	?>
				<input type="text" name="loginRadiusCountry" id="loginRadiusCountry" class="loginRadiusText" />
				<?php
			  }
			 ?>
			</div>
			<div class="loginRadiusDiv">
			<label for="loginRadiusZipcode"><?php echo __("Zipcode") ?> *</label>
			<input type="text" name="loginRadiusZipcode" id="loginRadiusZipcode" class="loginRadiusText" />
			</div>
		<?php
		}
		if(isset($profileData['PhoneNumber']) && $profileData['PhoneNumber'] == ""){
			?>
			<div class="loginRadiusDiv">
			<label for="loginRadiusPhone"><?php echo __("Phone Number") ?> *</label>
			<input type="text" name="loginRadiusPhone" id="loginRadiusPhone" class="loginRadiusText" />
			</div>
			<?php
		}
		?>
		<div class="loginRadiusDiv">
		<input type="submit" id="LoginRadiusRedSliderClick" name="LoginRadiusRedSliderClick" value="<?php echo __("Submit") ?>" onClick="loginRadiusPopupSubmit = true" class="inputbutton" />
		<input type="submit" value="<?php echo __("Cancel") ?>" class="inputbutton" name="LoginRadiusPopupCancel" onClick="loginRadiusPopupSubmit = false" />
		</div>
		</form>
	<?php
	}else{
		?>
		<input type="button" value="<?php echo __("Okay") ?>" class="inputbutton" onClick="location.href = '<?php echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK); ?>'" />
		<?php
	}
	?>
	</div>
	</div>
	</div>
<?php
}
<?php
define("MODE","testing");
function showDie($line,$msg){
	if(defined("MODE")){
		echo "<p>Message from $line, message is $msg </p>";
		die;
	}
}
function SL_popUpWindow( $loginRadiusPopupTxt, $socialLoginMsg = "", $loginRadiusShowForm = true ){	
?>
	<!--css of email block		-->
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
		left:50%;	
		background:#f3f3f3;
		padding:1px 0px 1px 0px;
		width:370px;
		position: absolute;
		top:50%;
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
	.inputtxt{
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
	<![endif]-->
	</style>
	</head>
	<body>
	<div id="fade" class="LoginRadius_overlay">	
	<div id="popupouter">
	<div id="popupinner">
	<div id="textmatter"><strong><?php echo $loginRadiusPopupTxt; ?></strong></div>
	<div style="clear:both;"></div>
	<div style="color:red; text-align:justify"><?php echo $socialLoginMsg ?></div>
	<?php
	if( $loginRadiusShowForm ){
	?>
		<form method="post" action="">
		<div><input type="text" name="SL_EMAIL" id="SL_EMAIL" class="inputtxt" /></div>
		<div>
		<input type="submit" id="LoginRadiusRedSliderClick" name="LoginRadiusRedSliderClick" value="Submit" class="inputbutton">
		<input type="submit" value="Cancel" class="inputbutton" name="LoginRadiusPopupCancel" />
		</div>
		</form>
	<?php
	}else{
		?>
		<input type="button" value="OK" onClick="location.href = '<?php echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK); ?>'" />
		<?php
	}
	?>
	</div>
	</div>
	</div>
<?php
}
function getMazeTable($tbl){
	$tableName = Mage::getSingleton('core/resource')->getTableName($tbl);
	return($tableName);
}?>
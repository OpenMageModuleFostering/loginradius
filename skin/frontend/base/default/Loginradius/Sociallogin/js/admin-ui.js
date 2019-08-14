jQuery.noConflict();
window.onload = function(){
	// hide counter providers hidden field
	document.getElementById('row_sociallogin_options_counter_counterProvidersHidden').style.display = 'none';
	// set left margin for first radio button in Horzontal counter
	var loginRadiusHorizontalCounter = document.getElementById('row_sociallogin_options_counter_horizontalCounter').getElementsByTagName('input');
	loginRadiusHorizontalCounter[0].style.marginLeft = '6px';
	// set left margin for first radio button in Horzontal sharing
	var loginRadiusHorizontalSharing = document.getElementById('row_sociallogin_options_sharing_horizontalSharing').getElementsByTagName('input');
	loginRadiusHorizontalSharing[0].style.marginLeft = '6px';
	// set left margin for first radio button in login redirection
	var loginRadiusRedirectionOptions = document.getElementById('row_sociallogin_options_messages_redirect').getElementsByTagName('input');
	loginRadiusRedirectionOptions[0].style.marginLeft = '6px';
	// sharing theme
	var loginRadiusSharingTheme = document.getElementById('sociallogin_options_sharing_sharingTheme');
	// show sharing themes according to the currently selected theme type
	loginRadiusToggleSharing(loginRadiusSharingTheme.value);
	// counter theme
	var loginRadiusCounterTheme = document.getElementById('sociallogin_options_counter_counterTheme');
	// show counter themes according to the currently selected theme type
	loginRadiusToggleCounter(loginRadiusCounterTheme.value);
	// show sharing themes according to the selected option
	loginRadiusSharingTheme.onchange = loginRadiusToggleSharing;
	// show counter themes according to the selected option	
	loginRadiusCounterTheme.onchange = loginRadiusToggleCounter;
	// get counter providers table-row reference
	var loginRadiusCounterProvidersRow = document.getElementById('row_sociallogin_options_counter_counterProviders');
	// get counter providers checkboxes reference
	var loginRadiusCounterProviders = loginRadiusCounterProvidersRow.getElementsByTagName('input');
	for(var i = 0; i < loginRadiusCounterProviders.length; i++){
		loginRadiusCounterProviders[i].onclick = loginRadiusPopulateCounter;
	}
	// check the counter providers that were saved previously in the hidden field
	var loginRadiusCounterProvidersHidden = document.getElementById('sociallogin_options_counter_counterProvidersHidden').value.trim();
	if(loginRadiusCounterProvidersHidden != ""){
		var loginRadiusCounterProviderArray = loginRadiusCounterProvidersHidden.split(',');
		for(var i = 0; i < loginRadiusCounterProviderArray.length; i++){
			document.getElementById("sociallogin_options_counter_counterProviders_"+loginRadiusCounterProviderArray[i]).checked = true;
			loginRadiusPopulateCounter(document.getElementById("sociallogin_options_counter_counterProviders_"+loginRadiusCounterProviderArray[i]));
		}
	}else{
		document.getElementById("sociallogin_options_counter_counterProviders_Reddit").checked = false;
		var loginRadiusCounterProviderArray = ["Facebook Like", "Google+ +1", "Twitter Tweet", "LinkedIn Share"];
		for(var i = 0; i < loginRadiusCounterProviderArray.length; i++){
			document.getElementById("sociallogin_options_counter_counterProviders_"+loginRadiusCounterProviderArray[i]).checked = true;
			loginRadiusPopulateCounter(document.getElementById("sociallogin_options_counter_counterProviders_"+loginRadiusCounterProviderArray[i]), true);
		}
	}
	// get sharing providers table-row reference
	var loginRadiusSharingProvidersRow = document.getElementById('row_sociallogin_options_sharing_sharingProviders');
	// get sharing providers checkboxes reference
	var loginRadiusSharingProviders = loginRadiusSharingProvidersRow.getElementsByTagName('input');
	for(var i = 0; i < loginRadiusSharingProviders.length; i++){
		loginRadiusSharingProviders[i].onclick = loginRadiusShowIcon;
	}
	// check the sharing providers that were saved previously in the hidden field
	var loginRadiusSharingProvidersHidden = document.getElementById('sociallogin_options_sharing_sharingProvidersHidden').value.trim();
	if(loginRadiusSharingProvidersHidden != ""){
		var loginRadiusSharingProviderArray = loginRadiusSharingProvidersHidden.split(',');
		for(var i = 0; i < loginRadiusSharingProviderArray.length; i++){
			document.getElementById("sociallogin_options_sharing_sharingProviders_"+loginRadiusSharingProviderArray[i]).checked = true;
			loginRadiusShowIcon(document.getElementById("sociallogin_options_sharing_sharingProviders_"+loginRadiusSharingProviderArray[i]));
		}
	}else{
		document.getElementById("sociallogin_options_sharing_sharingProviders_dotnetkicks").checked = false;
		var loginRadiusSharingProviderArray = ["facebook", "googleplus", "twitter", "email", "print"];
		for(var i = 0; i < loginRadiusSharingProviderArray.length; i++){
			document.getElementById("sociallogin_options_sharing_sharingProviders_"+loginRadiusSharingProviderArray[i]).checked = true;
			loginRadiusShowIcon(document.getElementById("sociallogin_options_sharing_sharingProviders_"+loginRadiusSharingProviderArray[i]), true);
		}
	}
}
// show sharing themes according to the selected option
function loginRadiusToggleSharing(theme){
	if(typeof this.value == "undefined"){
		var sharingTheme = theme;	
	}else{
		var sharingTheme = this.value;
	}
	if(sharingTheme == "horizontal"){
		document.getElementById('row_sociallogin_options_sharing_verticalSharing').style.display = 'none';
		document.getElementById('row_sociallogin_options_sharing_horizontalSharing').style.display = 'table-row';
		document.getElementById('row_sociallogin_options_sharing_sharingVerticalAlignment').style.display = 'none';
		document.getElementById('row_sociallogin_options_sharing_sharingOffset').style.display = 'none';
	}else if(sharingTheme == "vertical"){
		document.getElementById('row_sociallogin_options_sharing_verticalSharing').style.display = 'table-row';
		document.getElementById('row_sociallogin_options_sharing_horizontalSharing').style.display = 'none';
		document.getElementById('row_sociallogin_options_sharing_sharingVerticalAlignment').style.display = 'table-row';
		document.getElementById('row_sociallogin_options_sharing_sharingOffset').style.display = 'table-row';
	}
}
// show counter themes according to the selected option
function loginRadiusToggleCounter(theme){
	if(typeof this.value == "undefined"){
		var counterTheme = theme;	
	}else{
		var counterTheme = this.value;
	}
	if(counterTheme == "horizontal"){
		document.getElementById('row_sociallogin_options_counter_verticalCounter').style.display = 'none';
		document.getElementById('row_sociallogin_options_counter_horizontalCounter').style.display = 'table-row';
		document.getElementById('row_sociallogin_options_counter_counterVerticalAlignment').style.display = 'none';
		document.getElementById('row_sociallogin_options_counter_counterOffset').style.display = 'none';
	}else if(counterTheme == "vertical"){
		document.getElementById('row_sociallogin_options_counter_verticalCounter').style.display = 'table-row';
		document.getElementById('row_sociallogin_options_counter_horizontalCounter').style.display = 'none';
		document.getElementById('row_sociallogin_options_counter_counterVerticalAlignment').style.display = 'table-row';
		document.getElementById('row_sociallogin_options_counter_counterOffset').style.display = 'table-row';
	}
}
// limit maximum number of providers selected in sharing
function loginRadiusSharingLimit(elem){
	var checkCount = 0;
	// get providers table-row reference
	var loginRadiusSharingProvidersRow = document.getElementById('row_sociallogin_options_sharing_sharingProviders');
	// get sharing providers checkboxes reference
	var loginRadiusSharingProviders = loginRadiusSharingProvidersRow.getElementsByTagName('input');
	for(var i = 0; i < loginRadiusSharingProviders.length; i++){
		if(loginRadiusSharingProviders[i].checked){
			// count checked providers
			checkCount++;
			if(checkCount >= 10){
				elem.checked = false;
				if(document.getElementById('loginRadiusErrorDiv') == null){
					// create and show div having error message
					var errorDiv = document.createElement('div');
					errorDiv.setAttribute('id', 'loginRadiusErrorDiv');
					errorDiv.innerHTML = "You can select only 9 providers.";
					errorDiv.style.color = 'red';
					errorDiv.style.marginBottom = '10px';
					// append div to the <td> containing sharing provider checkboxes
					var rearrangeTd = loginRadiusSharingProvidersRow.getElementsByTagName('td');
					jQuery(rearrangeTd[1]).find('ul').before(errorDiv);
				}
				return;
			}
		}
	}
}
// add/remove icons from counter hidden field
function loginRadiusPopulateCounter(elem, lrDefault){
	if(typeof this.checked != "undefined"){
		elem = this;
	}
	// get providers hidden field value
	var providers = document.getElementById('sociallogin_options_counter_counterProvidersHidden');
	if(elem.checked){
		// add selected providers in the hiddem field value
		if(typeof this.checked != "undefined" || lrDefault == true){
			if(providers.value == ""){
				providers.value = elem.value;
			}else{
				providers.value += ","+elem.value;
			}
		}
	}else{
		if(providers.value.indexOf(',') == -1){
			providers.value = providers.value.replace(elem.value, ""); 
		}else{
			if(providers.value.indexOf(","+elem.value) == -1){
				providers.value = providers.value.replace(elem.value+",", "");
			}else{
				providers.value = providers.value.replace(","+elem.value, "");
			}
		}
	}
}
// show selected providers in rearrange option
function loginRadiusShowIcon(elem, lrDefault){
	if(typeof this.checked != "undefined"){
		elem = this;
		loginRadiusSharingLimit(elem);
	}
	// get providers hidden field value
	var providers = document.getElementById('sociallogin_options_sharing_sharingProvidersHidden');
	if(elem.checked){
		// get reference to "rearrange providers" <ul> element
		var ul = document.getElementById('loginRadiusRearrangeSharing');
		// if <ul> is not already created
		if(ul == null){
			// create <ul> element
			var ul = document.createElement('ul');
			ul.setAttribute('id', 'loginRadiusRearrangeSharing');
			jQuery(ul).sortable({
				update: function(e, ui) {
					var val = jQuery(this).children().map(function() {
						return jQuery(this).attr('title');
					}).get().join();
					jQuery(providers).val(val);
				},
			revert: true});
		}
		// create list items
		var listItem = document.createElement('li');
		listItem.setAttribute('id', 'loginRadiusLI'+elem.value);
		listItem.setAttribute('title', elem.value);
		listItem.setAttribute('class', 'lrshare_iconsprite32 lrshare_'+elem.value);
		ul.appendChild(listItem);
		// add selected providers in the hiddem field value
		if(typeof this.checked != "undefined" || lrDefault == true){
			if(providers.value == ""){
				providers.value = elem.value;
			}else{
				providers.value += ","+elem.value;
			}
		}
		// append <ul> to the <td>
		var rearrangeRow = document.getElementById('row_sociallogin_options_sharing_sharingProvidersHidden');
		var rearrangeTd = rearrangeRow.getElementsByTagName('td');
		rearrangeTd[1].appendChild(ul);
	}else{
		var remove = document.getElementById('loginRadiusLI'+elem.value);
		remove.parentNode.removeChild(remove);
		if(providers.value.indexOf(',') == -1){
			providers.value = providers.value.replace(elem.value, ""); 
		}else{
			if(providers.value.indexOf(","+elem.value) == -1){
				providers.value = providers.value.replace(elem.value+",", "");
			}else{
				providers.value = providers.value.replace(","+elem.value, "");
			}
		}
	}
}
// twitter follow script
jQuery(function(){
    function m(n, d){
        P = Math.pow;
        R = Math.round
        d = P(10, d);
        i = 7;
        while(i) {
            (s = P(10, i-- * 3)) <= n && (n = R(n * d / s) / d + "KMGTPE"[i])
        }
        return n;
    }
    jQuery.ajax({
        url: 'http://api.twitter.com/1/users/show.json',
        data: {
            screen_name: 'LoginRadius'
        },
        dataType: 'jsonp',
        success: function(data) {
           count = data.followers_count;
           jQuery('#followers').html(m(count, 1));
        }
    });
});
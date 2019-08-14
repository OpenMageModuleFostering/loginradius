// get trim() worked in IE
if(typeof String.prototype.trim !== 'function') {
    String.prototype.trim = function() {
        return this.replace(/^\s+|\s+$/g, '');
    }
}
// validate numeric data
function loginRadiusIsNumber(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}
var $loginRadiusJquery = jQuery.noConflict();
// prepare admin UI on window load
function loginRadiusPrepareAdminUI(){

    // highlight API Key and Secret notification
    if(jQuery('#loginRadiusKeySecretNotification')){
        jQuery('#loginRadiusKeySecretNotification').animate({'backgroundColor' : 'rgb(241, 142, 127)'}, 1000).animate({'backgroundColor' : '#FFFFE0'}, 1000).animate({'backgroundColor' : 'rgb(241, 142, 127)'}, 1000).animate({'backgroundColor' : '#FFFFE0'}, 1000);
    }

    // show warning, if number of social login icons is < 2 or if non-numeric
    document.getElementById('sociallogin_options_advancedSettings_iconsPerRow').onblur = function(){
        if(document.getElementById('sociallogin_options_advancedSettings_iconsPerRow').value.trim() < 2 || !loginRadiusIsNumber(document.getElementById('sociallogin_options_messages_iconsPerRow').value.trim())){
            if($loginRadiusJquery('#loginRadiusNoColumnsError').html() == undefined){
                $loginRadiusJquery('#sociallogin_options_advancedSettings_iconsPerRow').before('<span id="loginRadiusNoColumnsError" style="color:red">Please enter a valid number greater than 1.</span>');
            }else{
                $loginRadiusJquery('#loginRadiusNoColumnsError').html('Please enter a valid number greater than 1.');
            }
        }else{
            $loginRadiusJquery('#loginRadiusNoColumnsError').html('');
        }
    }

    var horizontalSharingTheme, verticalSharingTheme;
    // fetch horizontal and vertical sharing providers dynamically from LoginRadius on window load
    var sharingType = ['horizontal', 'vertical'];
    var sharingModes = ['Sharing', 'Counter'];
    // show the sharing/counter providers according to the selected sharing theme
    for(var j = 0; j < sharingType.length; j++){
        var loginRadiusHorizontalSharingThemes = document.getElementById('row_sociallogin_options_'+sharingType[j]+'Sharing_'+sharingType[j]+'SharingTheme').getElementsByTagName('input');
        for(var i = 0; i < loginRadiusHorizontalSharingThemes.length; i++){
            if(sharingType[j] == 'horizontal'){
                loginRadiusHorizontalSharingThemes[i].onclick = function(){
                    loginRadiusToggleSharingProviders(this, 'horizontal');
                }
            }else if(sharingType[j] == 'vertical'){
                loginRadiusHorizontalSharingThemes[i].onclick = function(){
                    loginRadiusToggleSharingProviders(this, 'vertical');
                }
            }
            if(loginRadiusHorizontalSharingThemes[i].checked == true){
                if(sharingType[j] == 'horizontal'){
                    horizontalSharingTheme = loginRadiusHorizontalSharingThemes[i].value;
                }else if(sharingType[j] == 'vertical'){
                    verticalSharingTheme = loginRadiusHorizontalSharingThemes[i].value;
                }
                loginRadiusToggleSharingProviders(loginRadiusHorizontalSharingThemes[i], sharingType[j]);
            }
        }
    }
    // set left margin for first radio button in Social Login Icon Size
    jQuery("#sociallogin_options_basicSettings_redirectAfterLoginsame,#sociallogin_options_basicSettings_redirectAfterRegistrationsame,#sociallogin_options_horizontalSharing_horizontalSharingTheme32").css({"margin-right": "12px"});

    // if selected sharing theme is worth showing rearrange icons, then show rearrange icons and manage sharing providers in hidden field
    for(var j = 0; j < sharingType.length; j++){
        for(var jj = 0; jj < sharingModes.length; jj++){
            // get sharing providers table-row reference
            var loginRadiusHorizontalSharingProvidersRow = document.getElementById('row_sociallogin_options_'+sharingType[j]+'Sharing_'+sharingType[j]+sharingModes[jj]+'Providers');
            // get sharing providers checkboxes reference
            var loginRadiusHorizontalSharingProviders = loginRadiusHorizontalSharingProvidersRow.getElementsByTagName('input');
            for(var i = 0; i < loginRadiusHorizontalSharingProviders.length; i++){
                if(sharingType[j] == 'horizontal'){
                    if(sharingModes[jj] == 'Sharing'){
                        loginRadiusHorizontalSharingProviders[i].onclick = function(){
                            loginRadiusShowIcon(false, this, 'horizontal');
                        }
                    }else{
                        loginRadiusHorizontalSharingProviders[i].onclick = function(){
                            loginRadiusPopulateCounter(this, 'horizontal');
                        }
                    }
                }else if(sharingType[j] == 'vertical'){
                    if(sharingModes[jj] == 'Sharing'){
                        loginRadiusHorizontalSharingProviders[i].onclick = function(){
                            loginRadiusShowIcon(false, this, 'vertical');
                        }
                    }else{
                        loginRadiusHorizontalSharingProviders[i].onclick = function(){
                            loginRadiusPopulateCounter(this, 'vertical');
                        }
                    }
                }
            }

            // check the sharing providers that were saved previously in the hidden field
            var loginRadiusSharingProvidersHidden = document.getElementById('sociallogin_options_'+sharingType[j]+'Sharing_'+sharingType[j]+sharingModes[jj]+'ProvidersHidden').value.trim();
            if(loginRadiusSharingProvidersHidden != ""){
                var loginRadiusSharingProviderArray = loginRadiusSharingProvidersHidden.split(',');
                if(sharingModes[jj] == 'Sharing'){
                    for(var i = 0; i < loginRadiusSharingProviderArray.length; i++){
                        if(document.getElementById(sharingType[j]+"_"+sharingModes[jj]+"_"+loginRadiusSharingProviderArray[i])){
                            document.getElementById(sharingType[j]+"_"+sharingModes[jj]+"_"+loginRadiusSharingProviderArray[i]).checked = true;
                            loginRadiusShowIcon(true, document.getElementById(sharingType[j]+"_"+sharingModes[jj]+"_"+loginRadiusSharingProviderArray[i]), sharingType[j]);
                        }
                    }
                }else{
                    for(var i = 0; i < loginRadiusSharingProviderArray.length; i++){
                        if(document.getElementById(sharingType[j]+"_"+sharingModes[jj]+"_"+loginRadiusSharingProviderArray[i])){
                            document.getElementById(sharingType[j]+"_"+sharingModes[jj]+"_"+loginRadiusSharingProviderArray[i]).checked = true;
                        }
                    }
                }
            }else{
                if(sharingModes[jj] == 'Sharing'){
                    var loginRadiusSharingProviderArray = ["Facebook", "GooglePlus", "Twitter", "Pinterest", "Email", "Print"];
                    for(var i = 0; i < loginRadiusSharingProviderArray.length; i++){
                        document.getElementById(sharingType[j]+"_"+sharingModes[jj]+"_"+loginRadiusSharingProviderArray[i]).checked = true;
                        loginRadiusShowIcon(true, document.getElementById(sharingType[j]+"_"+sharingModes[jj]+"_"+loginRadiusSharingProviderArray[i]), sharingType[j], true);
                    }
                }else{
                    var loginRadiusSharingProviderArray = ["Facebook Like", "Google+ +1", "Twitter Tweet", "Pinterest Pin it", "Hybridshare"];
                    for(var i = 0; i < loginRadiusSharingProviderArray.length; i++){
                        document.getElementById(sharingType[j]+"_"+sharingModes[jj]+"_"+loginRadiusSharingProviderArray[i]).checked = true;
                        loginRadiusPopulateCounter(document.getElementById(sharingType[j]+"_"+sharingModes[jj]+"_"+loginRadiusSharingProviderArray[i]), sharingType[j]);
                    }
                }
            }
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
function loginRadiusSharingLimit(elem, sharingType){
    var checkCount = 0;
    // get providers table-row reference
    var loginRadiusSharingProvidersRow = document.getElementById('row_sociallogin_options_'+sharingType+'Sharing_'+sharingType+'SharingProviders');
    // get sharing providers checkboxes reference
    var loginRadiusSharingProviders = loginRadiusSharingProvidersRow.getElementsByTagName('input');
    for(var i = 0; i < loginRadiusSharingProviders.length; i++){
        if(loginRadiusSharingProviders[i].checked){
            // count checked providers
            checkCount++;
            if(checkCount >= 10){
                elem.checked = false;
                if(document.getElementById('loginRadius'+sharingType+'ErrorDiv') == null){
                    // create and show div having error message
                    var errorDiv = document.createElement('div');
                    errorDiv.setAttribute('id', 'loginRadius'+sharingType+'ErrorDiv');
                    errorDiv.innerHTML = "You can select only 9 providers.";
                    errorDiv.style.color = 'red';
                    errorDiv.style.marginBottom = '10px';
                    // append div to the <td> containing sharing provider checkboxes
                    var rearrangeTd = loginRadiusSharingProvidersRow.getElementsByTagName('td');
                    $loginRadiusJquery(rearrangeTd[1]).find('ul').before(errorDiv);
                }
                return;
            }
        }
    }
}
// add/remove icons from counter hidden field
function loginRadiusPopulateCounter(elem, sharingType, lrDefault){
    if(elem.value != 1){
        // get providers hidden field value
        var providers = document.getElementById('sociallogin_options_'+sharingType+'Sharing_'+sharingType+'CounterProvidersHidden');
        if(elem.checked){
            // add selected providers in the hiddem field value
            if(typeof elem.checked != "undefined" || lrDefault == true){
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
}
// show selected providers in rearrange option
function loginRadiusShowIcon(pageRefresh, elem, sharingType, lrDefault){
    loginRadiusSharingLimit(elem, sharingType);
    if(elem.value != 1){
        // get providers hidden field value
        var providers = document.getElementById('sociallogin_options_'+sharingType+'Sharing_'+sharingType+'SharingProvidersHidden');
        if(elem.checked){
            // get reference to "rearrange providers" <ul> element
            var ul = document.getElementById('loginRadius'+sharingType+'RearrangeSharing');
            // if <ul> is not already created
            if(ul == null){
                // create <ul> element
                var ul = document.createElement('ul');
                ul.setAttribute('id', 'loginRadius'+sharingType+'RearrangeSharing');
                $loginRadiusJquery(ul).sortable({
                    update: function(e, ui) {
                        var val = $loginRadiusJquery(this).children().map(function() {
                            return $loginRadiusJquery(this).attr('title');
                        }).get().join();
                        $loginRadiusJquery(providers).val(val);
                    },
                    revert: true});
            }
            // create list items
            var listItem = document.createElement('li');
            listItem.setAttribute('id', 'loginRadius'+sharingType+'LI'+elem.value);
            listItem.setAttribute('title', elem.value);
            listItem.setAttribute('class', 'lrshare_iconsprite32 lrshare_'+elem.value.toLowerCase());
            ul.appendChild(listItem);
            // add selected providers in the hiddem field value
            if(!pageRefresh || lrDefault == true){
                if(providers.value == ""){
                    providers.value = elem.value;
                }else{
                    providers.value += ","+elem.value;
                }
            }
            // append <ul> to the <td>
            var rearrangeRow = document.getElementById('row_sociallogin_options_'+sharingType+'Sharing_'+sharingType+'SharingProvidersHidden');
            var rearrangeTd = rearrangeRow.getElementsByTagName('td');
            rearrangeTd[1].appendChild(ul);
        }else{
            var remove = document.getElementById('loginRadius'+sharingType+'LI'+elem.value);
            if(remove){
                remove.parentNode.removeChild(remove);
            }
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
}

jQuery(document).ready(function(){
    loginradiusChangeInheritCheckboxHidden('horizontalSharing','horizontalCounter');
    loginradiusChangeInheritCheckboxHidden('verticalSharing','verticalCounter');
    loginradiusChangeInheritCheckboxHidden('horizontalSharing','horizontalSharing');
    loginradiusChangeInheritCheckboxHidden('verticalSharing','verticalSharing');
    jQuery("#sociallogin_options_horizontalSharing_horizontalCounterProviders_inherit").click(function(){
        loginradiusChangeInheritCheckbox('horizontalSharing','horizontalCounter');
    });
    jQuery("#sociallogin_options_verticalSharing_verticalCounterProviders_inherit").click(function(){
        loginradiusChangeInheritCheckbox('verticalSharing','verticalCounter');
    });
    jQuery("#sociallogin_options_horizontalSharing_horizontalSharingProviders_inherit").click(function(){
        loginradiusChangeInheritCheckbox('horizontalSharing','horizontalSharing');
    });
    jQuery("#sociallogin_options_verticalSharing_verticalSharingProviders_inherit").click(function(){
        loginradiusChangeInheritCheckbox('verticalSharing','verticalSharing');
    });
    jQuery("#sociallogin_options_horizontalSharing_horizontalSharingProvidersHidden_inherit").click(function(){
        loginradiusChangeInheritCheckboxHidden('horizontalSharing','horizontalSharing');
    });
    jQuery("#sociallogin_options_verticalSharing_verticalSharingProvidersHidden_inherit").click(function(){
        loginradiusChangeInheritCheckboxHidden('verticalSharing','verticalSharing');
    });

    loginradiusToggleRedirection();
    jQuery("#row_sociallogin_options_basicSettings_redirectAfterLogin").change(function() {
        loginradiusToggleRedirection();
    });
    jQuery("#sociallogin_options_basicSettings_redirectAfterRegistration").change(function() {
        loginradiusToggleRedirection();
    });

});
function loginradiusChangeInheritValueSharing(sharingType){
    if(jQuery("#sociallogin_options_"+sharingType+"Sharing_"+sharingType+"SharingProviders_inherit").is(':checked')){
        jQuery("#sociallogin_options_"+sharingType+"Sharing_"+sharingType+"SharingProvidersHidden_inherit").attr('checked', true);
    } else {
        jQuery("#sociallogin_options_"+sharingType+"Sharing_"+sharingType+"SharingProvidersHidden_inherit").attr('checked', false);
        jQuery("#sociallogin_options_"+sharingType+"Sharing_"+sharingType+"SharingProvidersHidden").prop("disabled", false);
    }
}
function loginradiusChangeInheritValue(sharingType){
    if(jQuery("#sociallogin_options_"+sharingType+"Sharing_"+sharingType+"CounterProviders_inherit").is(':checked')){
        jQuery("#sociallogin_options_"+sharingType+"Sharing_"+sharingType+"CounterProvidersHidden_inherit").attr('checked', true);
    } else {
        jQuery("#sociallogin_options_"+sharingType+"Sharing_"+sharingType+"CounterProvidersHidden_inherit").attr('checked', false);
        jQuery("#sociallogin_options_"+sharingType+"Sharing_"+sharingType+"CounterProvidersHidden").prop("disabled", false);
    }
}

function loginradiusToggleRedirection(){
    if(jQuery('#sociallogin_options_basicSettings_redirectAfterLogin').val() == 'custom'){

        jQuery('#row_sociallogin_options_basicSettings_customUrlLogin').show();
    }else{
        jQuery('#row_sociallogin_options_basicSettings_customUrlLogin').hide();
    }
    if(jQuery('#sociallogin_options_basicSettings_redirectAfterRegistration').val() == 'custom'){
        jQuery('#row_sociallogin_options_basicSettings_customUrlRegistration').show();
    }else{
        jQuery('#row_sociallogin_options_basicSettings_customUrlRegistration').hide();
    }
}
function loginradiusChangeInheritCheckbox(shareId1,shareId2){
    if(jQuery("#sociallogin_options_"+shareId1+"_"+shareId2+"Providers_inherit").is(':checked')){
        jQuery("#sociallogin_options_"+shareId1+"_"+shareId2+"ProvidersHidden_inherit").attr('checked',true);
        jQuery("#sociallogin_options_"+shareId1+"_"+shareId2+"ProvidersHidden").attr("disabled", true);
    }else{
        jQuery("#sociallogin_options_"+shareId1+"_"+shareId2+"ProvidersHidden_inherit").attr('checked',false);
        jQuery("#sociallogin_options_"+shareId1+"_"+shareId2+"ProvidersHidden").attr("disabled", false);
    }
}

function loginradiusChangeInheritCheckboxHidden(shareId1,shareId2){
    if(jQuery("#sociallogin_options_"+shareId1+"_"+shareId2+"ProvidersHidden_inherit").is(':checked')){
        jQuery("#sociallogin_options_"+shareId1+"_"+shareId2+"Providers_inherit").attr('checked',true);
        jQuery("#sociallogin_options_"+shareId1+"_"+shareId2+"ProvidersHidden").attr("disabled", true);
    }else{
        jQuery("#sociallogin_options_"+shareId1+"_"+shareId2+"Providers_inherit").attr('checked',false);
        jQuery("#sociallogin_options_"+shareId1+"_"+shareId2+"ProvidersHidden").attr("disabled", false);
    }
}
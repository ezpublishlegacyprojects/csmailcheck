$.postJSON = function(url, data, callback) {
	$.post(url, data, callback, "json");
};

jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options = $.extend({}, options); // clone object since it's unexpected behavior if the expired property were changed
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // NOTE Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};

var csmailcheck = {
	
	initbox : '/mailcheck/initbox/',		
	checkmail : '/mailcheck/check/',	
	logouturl : '/mailcheck/logout/',
    loadingImagePath: null,
	
	siteURL: null,		
	COOKIE_NAME:'CS_MAIL_HASH',
		
	initMail : function ()
	{
	    var hash = $.cookie(this.COOKIE_NAME) == null ? '' : $.cookie(this.COOKIE_NAME);
	    	    
	    $.getJSON(this.siteURL + this.initbox + hash, {} , function(data){	
			if (data.error == 'false')
			{				    
			    if (data.authentificationscript != '')
			    {
			         var s=document.createElement('script');
		             s.setAttribute('src',data.authentificationscript);
		             document.getElementsByTagName('head')[0].appendChild(s);
			    }
			    
			   $('#infobox-csmailcheck').html(data.result);	       	
			}
           return true;	          
		});
	},
	
	login : function ()
	{	   
	       
	    if ($('#status-login').is('*')) {			        			        	
	        $('#status-login').html('<img src="'+this.loadingImagePath+'" alt="" />');
	    } else {
	        $('#infobox-csmailcheck').prepend('<div id="status-login"><img src="'+this.loadingImagePath+'" alt="" /></div>');
	    }	        
	        		 
	     var pdata = {
				imapserver	: $("#CSMailImapServer").val(),
				username	: $("#CSMailUsername").val(),
				passwd		: $("#CSMailPasswd").val(),
				rememberme	: $("#CSMailRemember").attr('checked')
		}
				
	    $.postJSON(this.siteURL + this.checkmail, pdata , function(data){	
			if (data.error == 'false')
			{		
                // If webmail client disabled this variable becames ''
			    if (data.authentificationscript != '')		
			    {    
    			    var s=document.createElement('script');
    		        s.setAttribute('src',data.authentificationscript);
    		        document.getElementsByTagName('head')[0].appendChild(s);		
			    }
			    
			   $('#infobox-csmailcheck').html(data.result);	     
			   $("#status-login").remove();
			     	
			} else {
			    $('#csmail-error').remove();
			    $("#status-login").html(data.result);
			}
           return true;	          
		});
	},
	
	logout : function ()
	{	   	    
		var hash = $.cookie(this.COOKIE_NAME) == null ? '' : $.cookie(this.COOKIE_NAME);
	    	    
	    $.getJSON(this.siteURL + this.logouturl + hash, {} , function(data){	
			 $('#infobox-csmailcheck').html(data.result);      	
           return true;	          
		});
	},
	
	setLoadingImage : function (path)
	{	   	    
		this.loadingImagePath = path;
	},
		
	setPath : function (path)
	{		
		this.siteURL = path;
	}
	
}





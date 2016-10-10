/*----- Hamdi Amin ----- */

function init() {
   'use strict';   
	 var website = new HAMDYWORLD.WebSite();

	  website._hiddenSec();
	  website._getBullets();	  
      website._niceScroll();	

}


var HAMDYWORLD = HAMDYWORLD || {};

HAMDYWORLD.WebSite=function  () {
	'use strict';
	var that = this;
	 that.section=$("#slider section");
	 that.index = 1;
	 that.lenSec = that.section.length;
	 
	 that._hiddenSec = function () {
		  $('#slider section:gt(0)').hide(); 
	 };
	 
	  that._niceScroll = function () {
		  $("html").niceScroll({cursorcolor:"#ff7400"});
	 };
	
	 setInterval(function () { that._forwardSlider() ;},5000);
};

 HAMDYWORLD.WebSite.prototype = { 

  _getBullets : function () {
	  'use strict';
	  var that = this;
	  for(var i=1;i<=that.lenSec;i++){
	   $('#pager').append("<a href='#'></a> ");
	   }
	   $('#pager a').first().addClass('current').end().click(function(e) {
                 $('#pager a').removeClass('current');
				   $(this).addClass('current');
				   that.section.fadeOut(1500);
				   that.section.eq($(this).index()).fadeIn(1500);	   
				   e.preventDefault();
        });
    },	
	
	_forwardSlider : function () {
		'use strict';
		var that = this;
		 if(that.index < that.lenSec){
	        $('#pager a.current').next().trigger("click");	
         }
	   that.index++;
	   if(that.index > that.lenSec){
		   that.index=1;
		   $('#pager a:first').trigger("click");
	   } 
	}
  
 };
 
	 
  $(window).load(function() {
	  'use strict';
           init();
    });
	


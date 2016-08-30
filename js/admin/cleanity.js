$(function(){	
	var navSelector = "ul#menu li";/** define the main navigation selector **/

	/** set up rounded corners for the selected elements **/
	$('.box-container').corners("5px bottom");
	 $('.box h4').corners("5px top");
	 $('ul.tab-menu li a').corners("5px top");
	 $('textarea#wysiwyg').wysiwyg();
	 $("div#sys-messages-container a, div#to-do-list ul li a").colorbox({fixedWidth:"50%", transitionSpeed:"100", inline:true, href:"#sample-modal"}); /** jquery colorbox modal boxes for system
	 messages and to-do list - see colorbox help docs for help: http://colorpowered.com/colorbox/ **/

	$('#to-do').tabs();		 
	$("#calendar").datepicker();/** jquery ui calendar/date picker - see jquery ui docs for help: http://jqueryui.com/demos/ **/
	$("ul.list-links").accordion({collapsible: true, navigation: true});/** side menu accordion - see jquery ui docs for help:  http://jqueryui.com/demos/  **/

 
	jQuery(navSelector).find('a').css( {backgroundPosition: "0 0"} );
	
	jQuery(navSelector).hover(function(){/** build animated dropdown navigation **/
		jQuery(this).find('ul:first:hidden').css({visibility: "visible",display: "none"}).show("fast");
		jQuery(this).find('a').stop().animate({backgroundPosition:"(0 -40px)"},{duration:150});
 	   jQuery(this).find('a.top-level').addClass("blue");
		},function(){
		jQuery(this).find('ul:first').css({visibility: "hidden", display:"none"});
		jQuery(this).find('a').stop().animate({backgroundPosition:"(0 0)"}, {duration:75});
		jQuery(this).find('a.top-level').removeClass("blue");
		});
	});

	ddaccordion.init({
		headerclass: "expandable", //Shared CSS class name of headers group that are expandable
		contentclass: "categoryitems", //Shared CSS class name of contents group
		revealtype: "click", //Reveal content when user clicks or onmouseover the header? Valid value: "click", "clickgo", or "mouseover"
		mouseoverdelay: 200, //if revealtype="mouseover", set delay in milliseconds before header expands onMouseover
		collapseprev: false, //Collapse previous content (so only one open at any time)? true/false
		defaultexpanded: [0], //index of content(s) open by default [index1, index2, etc]. [] denotes no content
		onemustopen: false, //Specify whether at least one header should be open always (so never all headers closed)
		animatedefault: false, //Should contents open by default be animated into view?
		persiststate: true, //persist state of opened contents within browser session?
		toggleclass: ["", "openheader"], //Two CSS classes to be applied to the header when it's collapsed and expanded, respectively ["class1", "class2"]
		togglehtml: ["prefix", "", ""], //Additional HTML added to the header when it's collapsed and expanded, respectively  ["position", "html1", "html2"] (see docs)
		animatespeed: "fast", //speed of animation: integer in milliseconds (ie: 200), or keywords "fast", "normal", or "slow"
		oninit:function(headers, expandedindices){ //custom code to run when headers have initalized
			//do nothing
		},
		onopenclose:function(header, index, state, isuseractivated){ //custom code to run whenever a header is opened or closed
			//do nothing
		}
	});
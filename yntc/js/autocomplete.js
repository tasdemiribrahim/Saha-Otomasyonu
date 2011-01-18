/**
 * AutoComplete Field - JavaScript Code
 *
 * This is a sample source code provided by fromvega.
 * Search for the complete article at http://www.fromvega.com
 *
 * Enjoy!
 *
 * @author fromvega
 *
 */

// global variables
var acListTotal   =  0;
var acListCurrent = -1;
var acDelay		  = 500;
var acURL		  = null;
var acSearchId	  = null;
var acResultsId	  = null;
var acSearchField = null;
var acResultsDiv  = null;

function setAutoComplete(field_id, results_id, get_url){

	// initialize vars
	
	acSearchId  = "#" + field_id;
	acResultsId = "#" + results_id;
	acURL 		= get_url;

	// create the results div
	$("body").append('<div id="' + results_id + '"></div>');

	// register mostly used vars
	acSearchField	= $(acSearchId);
	acResultsDiv	= $(acResultsId);

	// reposition div
	repositionResultsDiv();
	
	// on blur listener
	acSearchField.blur(function(){ setTimeout("clearAutoComplete()", 200) });

	// on key up listener
	acSearchField.keyup(function (e) {

		// get keyCode (window.event is for IE)
		var keyCode = e.keyCode || window.event.keyCode;
		var lastVal = acSearchField.val();

		// check an treat up and down arrows
		if(updownArrow(keyCode)){
			return;
		}

		// check for an ENTER or ESC
		if(keyCode == 13 || keyCode == 27){
			clearAutoComplete();
			return;
		}

		// if is text, call with delay
		setTimeout(function () {autoComplete(lastVal)}, acDelay);
	});
}

// treat the auto-complete action (delayed function)
function autoComplete(lastValue)
{
	// get the field value
	var part = acSearchField.val();

	// if it's empty clear the resuts box and return
	if(part == ''){
		clearAutoComplete();
		return;
	}

	// if it's equal the value from the time of the call, allow
	if(lastValue != part){
		return;
	}

	// get remote data as JSON
	$.getJSON(acURL + part, function(json){

		// get the total of results
		var ansLength = acListTotal = json.length;

		// if there are results populate the results div
		if(ansLength > 0){

			var newData = '';

			// create a div for each result
			for(i=0; i < ansLength; i++) {
				newData += '<div class="unselected">' + json[i] + '</div>';
			}

			// update the results div
			acResultsDiv.html(newData);
			acResultsDiv.css("display","block");
			
			// for all divs in results
			var divs = $(acResultsId + " > div");
		
			// on mouse over clean previous selected and set a new one
			divs.mouseover( function() {
				divs.each(function(){ this.className = "unselected"; });
				this.className = "selected";
			})
		
			// on click copy the result text to the search field and hide
			divs.click( function() {
				acSearchField.val(this.childNodes[0].nodeValue);
				clearAutoComplete();
			});

		} else {
			clearAutoComplete();
		}
	});
}

// clear auto complete box
function clearAutoComplete()
{
	acResultsDiv.html('');
	acResultsDiv.css("display","none");
}

// reposition the results div accordingly to the search field
function repositionResultsDiv()
{
	// get the field position
	var sf_pos    = acSearchField.offset();
	var sf_top    = sf_pos.top;
	var sf_left   = sf_pos.left;

	// get the field size
	var sf_height = acSearchField.height();
	var sf_width  = acSearchField.width();

	// apply the css styles - optimized for Firefox
	acResultsDiv.css("position","absolute");
	acResultsDiv.css("left", sf_left - 2);
	acResultsDiv.css("top", sf_top + sf_height + 5);
	acResultsDiv.css("width", sf_width - 2);
}


// treat up and down key strokes defining the next selected element
function updownArrow(keyCode) {
	if(keyCode == 40 || keyCode == 38){

		if(keyCode == 38){ // keyUp
			if(acListCurrent == 0 || acListCurrent == -1){
				acListCurrent = acListTotal-1;
			}else{
				acListCurrent--;
			}
		} else { // keyDown
			if(acListCurrent == acListTotal-1){
				acListCurrent = 0;
			}else {
				acListCurrent++;
			}
		}

		// loop through each result div applying the correct style
		acResultsDiv.children().each(function(i){
			if(i == acListCurrent){
				acSearchField.val(this.childNodes[0].nodeValue);
				this.className = "selected";
			} else {
				this.className = "unselected";
			}
		});

		return true;
	} else {
		// reset
		acListCurrent = -1;
		return false;
	}
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Ajax upload
 * Project page - http://valums.com/ajax-upload/
 * Copyright (c) 2008 Andris Valums, http://valums.com
 * Licensed under the MIT license (http://valums.com/mit-license/)
 * Version 3.5 (23.06.2009)
 */

/**
 * Changes from the previous version:
 * 1. Added better JSON handling that allows to use 'application/javascript' as a response
 * 2. Added demo for usage with jQuery UI dialog
 * 3. Fixed IE "mixed content" issue when used with secure connections
 * 
 * For the full changelog please visit: 
 * http://valums.com/ajax-upload-changelog/
 */

(function(){
	
var d = document, w = window;

/**
 * Get element by id
 */	
function get(element){
	if (typeof element == "string")
		element = d.getElementById(element);
	return element;
}

/**
 * Attaches event to a dom element
 */
function addEvent(el, type, fn){
	if (w.addEventListener){
		el.addEventListener(type, fn, false);
	} else if (w.attachEvent){
		var f = function(){
		  fn.call(el, w.event);
		};			
		el.attachEvent('on' + type, f)
	}
}


/**
 * Creates and returns element from html chunk
 */
var toElement = function(){
	var div = d.createElement('div');
	return function(html){
		div.innerHTML = html;
		var el = div.childNodes[0];
		div.removeChild(el);
		return el;
	}
}();

function hasClass(ele,cls){
	return ele.className.match(new RegExp('(\\s|^)'+cls+'(\\s|$)'));
}
function addClass(ele,cls) {
	if (!hasClass(ele,cls)) ele.className += " "+cls;
}
function removeClass(ele,cls) {
	var reg = new RegExp('(\\s|^)'+cls+'(\\s|$)');
	ele.className=ele.className.replace(reg,' ');
}

// getOffset function copied from jQuery lib (http://jquery.com/)
if (document.documentElement["getBoundingClientRect"]){
	// Get Offset using getBoundingClientRect
	// http://ejohn.org/blog/getboundingclientrect-is-awesome/
	var getOffset = function(el){
		var box = el.getBoundingClientRect(),
		doc = el.ownerDocument,
		body = doc.body,
		docElem = doc.documentElement,
		
		// for ie 
		clientTop = docElem.clientTop || body.clientTop || 0,
		clientLeft = docElem.clientLeft || body.clientLeft || 0,
		
		// In Internet Explorer 7 getBoundingClientRect property is treated as physical,
		// while others are logical. Make all logical, like in IE8.
		
		
		zoom = 1;
		if (body.getBoundingClientRect) {
			var bound = body.getBoundingClientRect();
			zoom = (bound.right - bound.left)/body.clientWidth;
		}
		if (zoom > 1){
			clientTop = 0;
			clientLeft = 0;
		}
		var top = box.top/zoom + (window.pageYOffset || docElem && docElem.scrollTop/zoom || body.scrollTop/zoom) - clientTop,
		left = box.left/zoom + (window.pageXOffset|| docElem && docElem.scrollLeft/zoom || body.scrollLeft/zoom) - clientLeft;
				
		return {
			top: top,
			left: left
		};
	}
	
} else {
	// Get offset adding all offsets 
	var getOffset = function(el){
		if (w.jQuery){
			return jQuery(el).offset();
		}		
			
		var top = 0, left = 0;
		do {
			top += el.offsetTop || 0;
			left += el.offsetLeft || 0;
		}
		while (el = el.offsetParent);
		
		return {
			left: left,
			top: top
		};
	}
}

function getBox(el){
	var left, right, top, bottom;	
	var offset = getOffset(el);
	left = offset.left;
	top = offset.top;
						
	right = left + el.offsetWidth;
	bottom = top + el.offsetHeight;		
		
	return {
		left: left,
		right: right,
		top: top,
		bottom: bottom
	};
}

/**
 * Crossbrowser mouse coordinates
 */
function getMouseCoords(e){		
	// pageX/Y is not supported in IE
	// http://www.quirksmode.org/dom/w3c_cssom.html			
	if (!e.pageX && e.clientX){
		// In Internet Explorer 7 some properties (mouse coordinates) are treated as physical,
		// while others are logical (offset).
		var zoom = 1;	
		var body = document.body;
		
		if (body.getBoundingClientRect) {
			var bound = body.getBoundingClientRect();
			zoom = (bound.right - bound.left)/body.clientWidth;
		}

		return {
			x: e.clientX / zoom + d.body.scrollLeft + d.documentElement.scrollLeft,
			y: e.clientY / zoom + d.body.scrollTop + d.documentElement.scrollTop
		};
	}
	
	return {
		x: e.pageX,
		y: e.pageY
	};		

}
/**
 * Function generates unique id
 */		
var getUID = function(){
	var id = 0;
	return function(){
		return 'ValumsAjaxUpload' + id++;
	}
}();

function fileFromPath(file){
	return file.replace(/.*(\/|\\)/, "");			
}

function getExt(file){
	return (/[.]/.exec(file)) ? /[^.]+$/.exec(file.toLowerCase()) : '';
}			

// Please use AjaxUpload , Ajax_upload will be removed in the next version
Ajax_upload = AjaxUpload = function(button, options){
	if (button.jquery){
		// jquery object was passed
		button = button[0];
	} else if (typeof button == "string" && /^#.*/.test(button)){					
		button = button.slice(1);				
	}
	button = get(button);	
	
	this._input = null;
	this._button = button;
	this._disabled = false;
	this._submitting = false;
	// Variable changes to true if the button was clicked
	// 3 seconds ago (requred to fix Safari on Mac error)
	this._justClicked = false;
	this._parentDialog = d.body;
	
	if (window.jQuery && jQuery.ui && jQuery.ui.dialog){
		var parentDialog = jQuery(this._button).parents('.ui-dialog');
		if (parentDialog.length){
			this._parentDialog = parentDialog[0];
		}
	}			
					
	this._settings = {
		// Location of the server-side upload script
		action: 'upload.php',			
		// File upload name
		name: 'userfile',
		// Additional data to send
		data: {},
		// Submit file as soon as it's selected
		autoSubmit: true,
		// The type of data that you're expecting back from the server.
		// Html and xml are detected automatically.
		// Only useful when you are using json data as a response.
		// Set to "json" in that case. 
		responseType: false,
		// When user selects a file, useful with autoSubmit disabled			
		onChange: function(file, extension){},					
		// Callback to fire before file is uploaded
		// You can return false to cancel upload
		onSubmit: function(file, extension){},
		// Fired when file upload is completed
		// WARNING! DO NOT USE "FALSE" STRING AS A RESPONSE!
		onComplete: function(file, response) {}
	};

	// Merge the users options with our defaults
	for (var i in options) {
		this._settings[i] = options[i];
	}
	
	this._createInput();
	this._rerouteClicks();
}
			
// assigning methods to our class
AjaxUpload.prototype = {
	setData : function(data){
		this._settings.data = data;
	},
	disable : function(){
		this._disabled = true;
	},
	enable : function(){
		this._disabled = false;
	},
	// removes ajaxupload
	destroy : function(){
		if(this._input){
			if(this._input.parentNode){
				this._input.parentNode.removeChild(this._input);
			}
			this._input = null;
		}
	},				
	/**
	 * Creates invisible file input above the button 
	 */
	_createInput : function(){
		var self = this;
		var input = d.createElement("input");
		input.setAttribute('type', 'file');
		input.setAttribute('name', this._settings.name);
		var styles = {
			'position' : 'absolute'
			,'margin': '-5px 0 0 -175px'
			,'padding': 0
			,'width': '220px'
			,'height': '30px'
			,'fontSize': '14px'								
			,'opacity': 0
			,'cursor': 'pointer'
			,'display' : 'none'
			,'zIndex' :  2147483583 //Max zIndex supported by Opera 9.0-9.2x 
			// Strange, I expected 2147483647					
		};
		for (var i in styles){
			input.style[i] = styles[i];
		}
		
		// Make sure that element opacity exists
		// (IE uses filter instead)
		if ( ! (input.style.opacity === "0")){
			input.style.filter = "alpha(opacity=0)";
		}
							
		this._parentDialog.appendChild(input);

		addEvent(input, 'change', function(){
			// get filename from input
			var file = fileFromPath(this.value);	
			if(self._settings.onChange.call(self, file, getExt(file)) == false ){
				return;				
			}														
			// Submit form when value is changed
			if (self._settings.autoSubmit){
				self.submit();						
			}						
		});
		
		// Fixing problem with Safari
		// The problem is that if you leave input before the file select dialog opens
		// it does not upload the file.
		// As dialog opens slowly (it is a sheet dialog which takes some time to open)
		// there is some time while you can leave the button.
		// So we should not change display to none immediately
		addEvent(input, 'click', function(){
			self.justClicked = true;
			setTimeout(function(){
				// we will wait 3 seconds for dialog to open
				self.justClicked = false;
			}, 3000);			
		});		
		
		this._input = input;
	},
	_rerouteClicks : function (){
		var self = this;
	
		// IE displays 'access denied' error when using this method
		// other browsers just ignore click()
		// addEvent(this._button, 'click', function(e){
		//   self._input.click();
		// });
				
		var box, dialogOffset = {top:0, left:0}, over = false;							
		addEvent(self._button, 'mouseover', function(e){
			if (!self._input || over) return;
			over = true;
			box = getBox(self._button);
					
			if (self._parentDialog != d.body){
				dialogOffset = getOffset(self._parentDialog);
			}	
		});
		
	
		// we can't use mouseout on the button,
		// because invisible input is over it
		addEvent(document, 'mousemove', function(e){
			var input = self._input;			
			if (!input || !over) return;
			
			if (self._disabled){
				removeClass(self._button, 'hover');
				input.style.display = 'none';
				return;
			}	
										
			var c = getMouseCoords(e);

			if ((c.x >= box.left) && (c.x <= box.right) && 
			(c.y >= box.top) && (c.y <= box.bottom)){			
				input.style.top = c.y - dialogOffset.top + 'px';
				input.style.left = c.x - dialogOffset.left + 'px';
				input.style.display = 'block';
				addClass(self._button, 'hover');				
			} else {		
				// mouse left the button
				over = false;
				if (!self.justClicked){
					input.style.display = 'none';
				}
				removeClass(self._button, 'hover');
			}			
		});			
			
	},
	/**
	 * Creates iframe with unique name
	 */
	_createIframe : function(){
		// unique name
		// We cannot use getTime, because it sometimes return
		// same value in safari :(
		var id = getUID();
		
		// Remove ie6 "This page contains both secure and nonsecure items" prompt 
		// http://tinyurl.com/77w9wh
		var iframe = toElement('<iframe src="javascript:false;" name="' + id + '" />');
		iframe.id = id;
		iframe.style.display = 'none';
		d.body.appendChild(iframe);			
		return iframe;						
	},
	/**
	 * Upload file without refreshing the page
	 */
	submit : function(){
		var self = this, settings = this._settings;	
					
		if (this._input.value === ''){
			// there is no file
			return;
		}
										
		// get filename from input
		var file = fileFromPath(this._input.value);			

		// execute user event
		if (! (settings.onSubmit.call(this, file, getExt(file)) == false)) {
			// Create new iframe for this submission
			var iframe = this._createIframe();
			
			// Do not submit if user function returns false										
			var form = this._createForm(iframe);
			form.appendChild(this._input);
			
			form.submit();
			
			d.body.removeChild(form);				
			form = null;
			this._input = null;
			
			// create new input
			this._createInput();
			
			var toDeleteFlag = false;
			
			addEvent(iframe, 'load', function(e){
					
				if (// For Safari
					iframe.src == "javascript:'%3Chtml%3E%3C/html%3E';" ||
					// For FF, IE
					iframe.src == "javascript:'<html></html>';"){						
					
					// First time around, do not delete.
					if( toDeleteFlag ){
						// Fix busy state in FF3
						setTimeout( function() {
							d.body.removeChild(iframe);
						}, 0);
					}
					return;
				}				
				
				var doc = iframe.contentDocument ? iframe.contentDocument : frames[iframe.id].document;

				// fixing Opera 9.26
				if (doc.readyState && doc.readyState != 'complete'){
					// Opera fires load event multiple times
					// Even when the DOM is not ready yet
					// this fix should not affect other browsers
					return;
				}
				
				// fixing Opera 9.64
				if (doc.body && doc.body.innerHTML == "false"){
					// In Opera 9.64 event was fired second time
					// when body.innerHTML changed from false 
					// to server response approx. after 1 sec
					return;				
				}
				
				var response;
									
				if (doc.XMLDocument){
					// response is a xml document IE property
					response = doc.XMLDocument;
				} else if (doc.body){
					// response is html document or plain text
					response = doc.body.innerHTML;
					if (settings.responseType && settings.responseType.toLowerCase() == 'json'){
						// If the document was sent as 'application/javascript' or
						// 'text/javascript', then the browser wraps the text in a <pre>
						// tag and performs html encoding on the contents.  In this case,
						// we need to pull the original text content from the text node's
						// nodeValue property to retrieve the unmangled content.
						// Note that IE6 only understands text/html
						if (doc.body.firstChild && doc.body.firstChild.nodeName.toUpperCase() == 'PRE'){
							response = doc.body.firstChild.firstChild.nodeValue;
						}
						if (response) {
							response = window["eval"]("(" + response + ")");
						} else {
							response = {};
						}
					}
				} else {
					// response is a xml document
					var response = doc;
				}
																			
				settings.onComplete.call(self, file, response);
						
				// Reload blank page, so that reloading main page
				// does not re-submit the post. Also, remember to
				// delete the frame
				toDeleteFlag = true;
				
				// Fix IE mixed content issue
				iframe.src = "javascript:'<html></html>';";		 								
			});
	
		} else {
			// clear input to allow user to select same file
			// Doesn't work in IE6
			// this._input.value = '';
			d.body.removeChild(this._input);				
			this._input = null;
			
			// create new input
			this._createInput();						
		}
	},		
	/**
	 * Creates form, that will be submitted to iframe
	 */
	_createForm : function(iframe){
		var settings = this._settings;
		
		// method, enctype must be specified here
		// because changing this attr on the fly is not allowed in IE 6/7		
		var form = toElement('<form method="post" enctype="multipart/form-data"></form>');
		form.style.display = 'none';
		form.action = settings.action;
		form.target = iframe.name;
		d.body.appendChild(form);
		
		// Create hidden input element for each data key
		for (var prop in settings.data){
			var el = d.createElement("input");
			el.type = 'hidden';
			el.name = prop;
			el.value = settings.data[prop];
			form.appendChild(el);
		}			
		return form;
	}	
};
})();
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
This is a JavaScript library that will allow you to easily add some basic DHTML
drop-down datepicker functionality to your Notes forms. This script is not as
full-featured as others you may find on the Internet, but it's free, it's easy to
understand, and it's easy to change.

You'll also want to include a stylesheet that makes the datepicker elements
look nice. An example one can be found in the database that this script was
originally released with, at:

http://www.nsftools.com/tips/NotesTips.htm#datepicker

I've tested this lightly with Internet Explorer 6 and Mozilla Firefox. I have no idea
how compatible it is with other browsers.

version 1.5
December 4, 2005
Julian Robichaux -- http://www.nsftools.com
*/

var datePickerDivID = "datepicker";
var iFrameDivID = "datepickeriframe";

var dayArrayShort = new Array('Pz', 'Pzt', 'Sa', 'Car', 'Per', 'Cu', 'Cmt');
var dayArrayMed = new Array('Pz', 'Pzt', 'Sa', 'Car', 'Per', 'Cu', 'Cmt');
var dayArrayLong = new Array('Pazar', 'Pazartesi', 'Sali', 'Carsamba', 'Persembe', 'Cuma', 'Cumartesi');
var monthArrayShort = new Array('Oc', 'Su', 'Ma', 'Ni', 'May', 'Haz', 'Tem', 'Agu', 'Ey', 'Ek', 'Ka', 'Ara');
var monthArrayMed = new Array('Oc', 'Su', 'Ma', 'Ni', 'May', 'Haz', 'Tem', 'Agu', 'Ey', 'Ek', 'Ka', 'Ara');
var monthArrayLong = new Array('Ocak', 'Subat', 'Mart', 'Nisan', 'Mayis', 'Haziran', 'Temmuz', 'Agustos', 'Eylul', 'Ekim', 'Kasim', 'Aralik');
 
// these variables define the date formatting we're expecting and outputting.
// If you want to use a different format by default, change the defaultDateSeparator
// and defaultDateFormat variables either here or on your HTML page.
var defaultDateSeparator = "-";        // common values would be "/" or "."
var defaultDateFormat = "ymd"    // valid values are "mdy", "dmy", and "ymd"
var dateSeparator = defaultDateSeparator;
var dateFormat = defaultDateFormat;

/**
This is the main function you'll call from the onClick event of a button.
Normally, you'll have something like this on your HTML page:

Start Date: <input name="StartDate">
<input type=button value="select" onclick="displayDatePicker('StartDate');">

That will cause the datepicker to be displayed beneath the StartDate field and
any date that is chosen will update the value of that field. If you'd rather have the
datepicker display beneath the button that was clicked, you can code the button
like this:

<input type=button value="select" onclick="displayDatePicker('StartDate', this);">

So, pretty much, the first argument (dateFieldName) is a string representing the
name of the field that will be modified if the user picks a date, and the second
argument (displayBelowThisObject) is optional and represents an actual node
on the HTML document that the datepicker should be displayed below.

In version 1.1 of this code, the dtFormat and dtSep variables were added, allowing
you to use a specific date format or date separator for a given call to this function.
Normally, you'll just want to set these defaults globally with the defaultDateSeparator
and defaultDateFormat variables, but it doesn't hurt anything to add them as optional
parameters here. An example of use is:

<input type=button value="select" onclick="displayDatePicker('StartDate', false, 'dmy', '.');">

This would display the datepicker beneath the StartDate field (because the
displayBelowThisObject parameter was false), and update the StartDate field with
the chosen value of the datepicker using a date format of dd.mm.yyyy
*/
function displayDatePicker(dateFieldName, displayBelowThisObject, dtFormat, dtSep)
{
  var targetDateField = document.getElementsByName (dateFieldName).item(0);
 
  // if we weren't told what node to display the datepicker beneath, just display it
  // beneath the date field we're updating
  if (!displayBelowThisObject)
    displayBelowThisObject = targetDateField;
 
  // if a date separator character was given, update the dateSeparator variable
  if (dtSep)
    dateSeparator = dtSep;
  else
    dateSeparator = defaultDateSeparator;
 
  // if a date format was given, update the dateFormat variable
  if (dtFormat)
    dateFormat = dtFormat;
  else
    dateFormat = defaultDateFormat;
 
  var x = displayBelowThisObject.offsetLeft;
  var y = displayBelowThisObject.offsetTop + displayBelowThisObject.offsetHeight ;
 
  // deal with elements inside tables and such
  var parent = displayBelowThisObject;
  while (parent.offsetParent) {
    parent = parent.offsetParent;
    x += parent.offsetLeft;
    y += parent.offsetTop ;
  }
 
  drawDatePicker(targetDateField, x, y);
}


/**
Draw the datepicker object (which is just a table with calendar elements) at the
specified x and y coordinates, using the targetDateField object as the input tag
that will ultimately be populated with a date.

This function will normally be called by the displayDatePicker function.
*/
function drawDatePicker(targetDateField, x, y)
{
  var dt = getFieldDate(targetDateField.value);
 
  // the datepicker table will be drawn inside of a <div> with an ID defined by the
  // global datePickerDivID variable. If such a div doesn't yet exist on the HTML
  // document we're working with, add one.
  if (!document.getElementById(datePickerDivID)) {
    // don't use innerHTML to update the body, because it can cause global variables
    // that are currently pointing to objects on the page to have bad references
    //document.body.innerHTML += "<div id='" + datePickerDivID + "' class='dpDiv'></div>";
    var newNode = document.createElement("div");
    newNode.setAttribute("id", datePickerDivID);
    newNode.setAttribute("class", "dpDiv");
    newNode.setAttribute("style", "visibility: hidden;");
    document.body.appendChild(newNode);
  }
 
  // move the datepicker div to the proper x,y coordinate and toggle the visiblity
  var pickerDiv = document.getElementById(datePickerDivID);
  pickerDiv.style.position = "absolute";
  pickerDiv.style.left = x + "px";
  pickerDiv.style.top = y + "px";
  pickerDiv.style.visibility = (pickerDiv.style.visibility == "visible" ? "hidden" : "visible");
  pickerDiv.style.display = (pickerDiv.style.display == "block" ? "none" : "block");
  pickerDiv.style.zIndex = 10000;
 
  // draw the datepicker table
  refreshDatePicker(targetDateField.name, dt.getFullYear(), dt.getMonth(), dt.getDate()+1);
}


function refreshDatePicker(dateFieldName, year, month, day)
{
  // if no arguments are passed, use today's date; otherwise, month and year
  // are required (if a day is passed, it will be highlighted later)
  var thisDay = new Date();

  if ((month >= 0) && (year > 0)) {
    thisDay = new Date(year, month, 1);
  } else {
    day = thisDay.getDate() + 1;
    thisDay.setDate(1);
  }

  // the calendar will be drawn as a table
  // you can customize the table elements with a global CSS style sheet,
  // or by hardcoding style and formatting elements below
  var crlf = "\r\n";
  var TABLE = "<table cols=7 class='dpTable'>" + crlf;
  var xTABLE = "</table>" + crlf;
  var TR = "<tr class='dpTR'>";
  var TR_title = "<tr class='dpTitleTR'>";
  var TR_days = "<tr class='dpDayTR'>";
  var TR_todaybutton = "<tr class='dpTodayButtonTR'>";
  var xTR = "</tr>" + crlf;
  var TD = "<td class='dpTD' onMouseOut='this.className=\"dpTD\";' onMouseOver=' this.className=\"dpTDHover\";' ";    // leave this tag open, because we'll be adding an onClick event
  var TD_title = "<td colspan=5 class='dpTitleTD'>";
  var TD_buttons = "<td class='dpButtonTD'>";
  var TD_todaybutton = "<td colspan=7 class='dpTodayButtonTD'>";
  var TD_days = "<td class='dpDayTD'>";
  var TD_selected = "<td class='dpDayHighlightTD' onMouseOut='this.className=\"dpDayHighlightTD\";' onMouseOver='this.className=\"dpTDHover\";' ";    // leave this tag open, because we'll be adding an onClick event
  var xTD = "</td>" + crlf;
  var DIV_title = "<div class='dpTitleText'>";
  var DIV_selected = "<div class='dpDayHighlight'>";
  var xDIV = "</div>";

  // start generating the code for the calendar table
  var html = TABLE;

  // this is the title bar, which displays the month and the buttons to
  // go back to a previous month or forward to the next month
  html += TR_title;
  html += TD_buttons + getButtonCode(dateFieldName, thisDay, -1, "<") + xTD;
  html += TD_title + DIV_title + monthArrayLong[ thisDay.getMonth()] + " " + thisDay.getFullYear() + xDIV + xTD;
  html += TD_buttons + getButtonCode(dateFieldName, thisDay, 1, ">") + xTD;
  html += xTR;

  // this is the row that indicates which day of the week we're on
  html += TR_days;
  for(i = 1; i < dayArrayShort.length+1; i++){
    if(i==dayArrayShort.length){
        html += TD_days + dayArrayShort[0] + xTD;
        break;
    }
   html += TD_days + dayArrayShort[i] + xTD;
  }
  html += xTR;

  // now we'll start populating the table with days of the month
  html += TR;


	// begin added by ali bakan
	var dayOfWeek = thisDay.getDay();
	if (dayOfWeek == 0)
		dayOfWeek = 6;
	else
		dayOfWeek--;
	// end added by ali bakan

  // first, the leading blanks
  for (i = 0; i < dayOfWeek; i++)
    html += TD + "&nbsp;" + xTD;

  // now, the days of the month
  do {
    dayNum = thisDay.getDate();
    TD_onclick = " onclick=\"updateDateField('" + dateFieldName + "', '" + getDateString(thisDay) + "');\">";

    if ((dayNum + 1) == day)
      html += TD_selected + TD_onclick + DIV_selected + dayNum + xDIV + xTD;
    else
      html += TD + TD_onclick + dayNum + xTD;

    // if this is a Saturday, start a new row
    if (dayOfWeek == 6)
      html += xTR + TR;

    // increment the day
    thisDay.setDate(thisDay.getDate() + 1 );

	// begin added by ali bakan
	dayOfWeek++;
	dayOfWeek = dayOfWeek % 7;
	// end added by ali bakan

  } while (thisDay.getDate() > 1)

  // fill in any trailing blanks
  if (dayOfWeek > 0) {
    for (i = 6; i > dayOfWeek; i--)
      html += TD + "&nbsp;" + xTD;
  }
  html += xTR;

  // add a button to allow the user to easily return to today, or close the calendar
  var today = new Date();
  var todayString = "Today is " + dayArrayMed[today.getDay()] + ", " + monthArrayMed[ today.getMonth()] + " " + today.getDate();
  html += TR_todaybutton + TD_todaybutton;
  html += "<button class='dpTodayButton' onClick='refreshDatePicker(\"" + dateFieldName + "\");'>bugun</button> ";
  html += "<button class='dpTodayButton' onClick='updateDateField(\"" + dateFieldName + "\");'>kapat</button>";
  html += xTD + xTR;

  // and finally, close the table
  html += xTABLE;

  document.getElementById(datePickerDivID).innerHTML = html;
  // add an "iFrame shim" to allow the datepicker to display above selection lists
  adjustiFrame();
}

/**
Convenience function for writing the code for the buttons that bring us back or forward
a month.
*/
function getButtonCode(dateFieldName, dateVal, adjust, label)
{
  var newMonth = (dateVal.getMonth () + adjust) % 12;
  var newYear = dateVal.getFullYear() + parseInt((dateVal.getMonth() + adjust) / 12);
  if (newMonth < 0) {
    newMonth += 12;
    newYear += -1;
  }
 
  return "<button class='dpButton' onClick='refreshDatePicker(\"" + dateFieldName + "\", " + newYear + ", " + newMonth + ");'>" + label + "</button>";
}


/**
Convert a JavaScript Date object to a string, based on the dateFormat and dateSeparator
variables at the beginning of this script library.
*/
function getDateString(dateVal)
{
  var dayString = "00" + dateVal.getDate();
  var monthString = "00" + (dateVal.getMonth()+1);
  dayString = dayString.substring(dayString.length - 2);
  monthString = monthString.substring(monthString.length - 2);
 
  switch (dateFormat) {
    case "dmy" :
      return dayString + dateSeparator + monthString + dateSeparator + dateVal.getFullYear();
    case "ymd" :
      return dateVal.getFullYear() + dateSeparator + monthString + dateSeparator + dayString;
    case "mdy" :
    default :
      return monthString + dateSeparator + dayString + dateSeparator + dateVal.getFullYear();
  }
}


/**
Convert a string to a JavaScript Date object.
*/
function getFieldDate(dateString)
{
  var dateVal;
  var dArray;
  var d, m, y;
 
  try {
    dArray = splitDateString(dateString);
    if (dArray) {
      switch (dateFormat) {
        case "dmy" :
          d = parseInt(dArray[0], 10);
          m = parseInt(dArray[1], 10) - 1;
          y = parseInt(dArray[2], 10);
          break;
        case "ymd" :
          d = parseInt(dArray[2], 10);
          m = parseInt(dArray[1], 10) - 1;
          y = parseInt(dArray[0], 10);
          break;
        case "mdy" :
        default :
          d = parseInt(dArray[1], 10);
          m = parseInt(dArray[0], 10) - 1;
          y = parseInt(dArray[2], 10);
          break;
      }
      dateVal = new Date(y, m, d);
    } else if (dateString) {
      dateVal = new Date(dateString);
    } else {
      dateVal = new Date();
    }
  } catch(e) {
    dateVal = new Date();
  }
 
  return dateVal;
}


/**
Try to split a date string into an array of elements, using common date separators.
If the date is split, an array is returned; otherwise, we just return false.
*/
function splitDateString(dateString)
{
  var dArray;
  if (dateString.indexOf("/") >= 0)
    dArray = dateString.split("/");
  else if (dateString.indexOf(".") >= 0)
    dArray = dateString.split(".");
  else if (dateString.indexOf("-") >= 0)
    dArray = dateString.split("-");
  else if (dateString.indexOf("\\") >= 0)
    dArray = dateString.split("\\");
  else
    dArray = false;
 
  return dArray;
}

/**
Update the field with the given dateFieldName with the dateString that has been passed,
and hide the datepicker. If no dateString is passed, just close the datepicker without
changing the field value.

Also, if the page developer has defined a function called datePickerClosed anywhere on
the page or in an imported library, we will attempt to run that function with the updated
field as a parameter. This can be used for such things as date validation, setting default
values for related fields, etc. For example, you might have a function like this to validate
a start date field:

function datePickerClosed(dateField)
{
  var dateObj = getFieldDate(dateField.value);
  var today = new Date();
  today = new Date(today.getFullYear(), today.getMonth(), today.getDate());
 
  if (dateField.name == "StartDate") {
    if (dateObj < today) {
      // if the date is before today, alert the user and display the datepicker again
      alert("Please enter a date that is today or later");
      dateField.value = "";
      document.getElementById(datePickerDivID).style.visibility = "visible";
      adjustiFrame();
    } else {
      // if the date is okay, set the EndDate field to 7 days after the StartDate
      dateObj.setTime(dateObj.getTime() + (7 * 24 * 60 * 60 * 1000));
      var endDateField = document.getElementsByName ("EndDate").item(0);
      endDateField.value = getDateString(dateObj);
    }
  }
}

*/
function updateDateField(dateFieldName, dateString)
{
  var targetDateField = document.getElementsByName (dateFieldName).item(0);
  if (dateString)
    targetDateField.value = dateString;
 
  var pickerDiv = document.getElementById(datePickerDivID);
  pickerDiv.style.visibility = "hidden";
  pickerDiv.style.display = "none";
 
  adjustiFrame();
  targetDateField.focus();
 
  // after the datepicker has closed, optionally run a user-defined function called
  // datePickerClosed, passing the field that was just updated as a parameter
  // (note that this will only run if the user actually selected a date from the datepicker)
  if ((dateString) && (typeof(datePickerClosed) == "function"))
    datePickerClosed(targetDateField);
}


/**
Use an "iFrame shim" to deal with problems where the datepicker shows up behind
selection list elements, if they're below the datepicker. The problem and solution are
described at:

http://dotnetjunkies.com/WebLog/jking/archive/2003/07/21/488.aspx
http://dotnetjunkies.com/WebLog/jking/archive/2003/10/30/2975.aspx
*/
function adjustiFrame(pickerDiv, iFrameDiv)
{
  // we know that Opera doesn't like something about this, so if we
  // think we're using Opera, don't even try
  var is_opera = (navigator.userAgent.toLowerCase().indexOf("opera") != -1);
  if (is_opera)
    return;
  
  // put a try/catch block around the whole thing, just in case
  try {
    if (!document.getElementById(iFrameDivID)) {
      // don't use innerHTML to update the body, because it can cause global variables
      // that are currently pointing to objects on the page to have bad references
      //document.body.innerHTML += "<iframe id='" + iFrameDivID + "' src='javascript:false;' scrolling='no' frameborder='0'>";
      var newNode = document.createElement("iFrame");
      newNode.setAttribute("id", iFrameDivID);
      newNode.setAttribute("src", "javascript:false;");
      newNode.setAttribute("scrolling", "no");
      newNode.setAttribute ("frameborder", "0");
      document.body.appendChild(newNode);
    }
    
    if (!pickerDiv)
      pickerDiv = document.getElementById(datePickerDivID);
    if (!iFrameDiv)
      iFrameDiv = document.getElementById(iFrameDivID);
    
    try {
      iFrameDiv.style.position = "absolute";
      iFrameDiv.style.width = pickerDiv.offsetWidth;
      iFrameDiv.style.height = pickerDiv.offsetHeight ;
      iFrameDiv.style.top = pickerDiv.style.top;
      iFrameDiv.style.left = pickerDiv.style.left;
      iFrameDiv.style.zIndex = pickerDiv.style.zIndex - 1;
      iFrameDiv.style.visibility = pickerDiv.style.visibility ;
      iFrameDiv.style.display = pickerDiv.style.display;
    } catch(e) {
    }
 
  } catch (ee) {
  }
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////
var sayfaKayitSay = 5;
function sayfaNoEkle()
{   
    var aktifSayfa = 0;
    var satirSay = $(".tbl_list").find('tbody tr').length;
    var sayfaSay = Math.ceil(satirSay / sayfaKayitSay);
	$('<ul id="pagination-flickr"></ul>').appendTo($("#sayfaNo"));
	$('<li class="active"><a class="sayfaLink">1</a></li>').appendTo($("#pagination-flickr"));
    for (var i=1; i< sayfaSay; i++) 
            $('<li><a href="#" class="sayfaLink">'+(i + 1) +' </a></li>').appendTo($("#pagination-flickr"));
    sayfaDegistir(aktifSayfa);
}

function sayfaDegistir(aktifSayfa)
{
    var bas = aktifSayfa * sayfaKayitSay;
    var son = (aktifSayfa + 1) * sayfaKayitSay - 1;

    $(".tbl_list").find("tbody tr").show().end().find("tbody tr:lt("+bas+")").hide().end().find("tbody tr:gt("+son+")").hide();
}

function sayfaClick()
{
	$(".active a").attr('href','#');
    $(".active").removeClass('active');
    $(this).removeAttr('href').parent().addClass('active');
    var aktifSayfa = $(this).html() - 1;
    sayfaDegistir(aktifSayfa);
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////

(function($) {
		  
	$.fn.jSuggest = function(options) {
		// merge users option with default options
		var opts = $.extend({}, $.fn.jSuggest.defaults, options);		
		var jH = ".jSuggestHover";
		var jsH = "jSuggestHover";
		var iniVal = this.value;
		var textBox = this;
		var textVal = this.value;	
		var jC = "#jSuggestContainer";
		
		$("body").append('<div id="jSuggestContainer"></div>');
		$(jC).hide();
		$(this).bind("keyup click", function(e){
			textBox = this;
			textVal = this.value;
			if (this.value.length >= opts.minchar && $.trim(this.value)!="Search Terms") {
				var offSet = $(this).offset();
				
				$(jC).css({
					position: "absolute",
					top: offSet.top + $(this).outerHeight() + "px",
					left: offSet.left,
					width: $(this).outerWidth()-2 + "px",
					opacity: opts.opacity,
					zIndex: opts.zindex
				}).show();
				
				// if escape key
				if (e.keyCode == 27 ) {
					$(jC).hide();
				}
				
				// if enter key
				else if (e.keyCode == 13 ) {
					if ($(jH).length == 1)
                    {
                        value = find($(jH).text())
                        $(textBox).val(value);
                    }    
                    $(jC).hide();
                    iniVal = textBox.value;
				}
				// if down arrow
				else if (e.keyCode == 40) {
					// if any suggestion is highlighted
					if ($(jH).length == 1) {
						if (!$(jH).next().length == 0) {
							$(jH).next().addClass(jsH);
							$(".jSuggestHover:eq(0)").removeClass(jsH);
							if (opts.autoChange)
                            {
                                value = find($(jH).text())
                                $(textBox).val(value);
                            }
						}
					}
					else {
						$("#jSuggestContainer ul li:first-child").addClass(jsH);
						if (opts.autoChange)
                        {
                            value = find($(jH).text())
							$(textBox).val(value);
                        }
					}
					
				}
				
				// if up arrow
				else if (e.keyCode == 38) {
					// if any suggestion is highlighted
					if ($(jH).length == 1 ) {
						if (!$(jH).prev().length == 0) {
							$(jH).prev().addClass(jsH);
							$(".jSuggestHover:eq(1)").removeClass(jsH);
							if (opts.autoChange)
                            {
                                value = find($(jH).text())
                                $(textBox).val(value);
                            }
						}
						// if is first child
						else {
							$(jH).removeClass(jsH);
							$(textBox).val(iniVal);
						}
					}
				}
				// new query detected
				else if (textBox.value != iniVal){
					iniVal = textBox.value;
					if ($(".jSuggestLoading").length==0)
						$('<div class="jSuggestLoading">'+ opts.loadingText+'</div>').prependTo("#jSuggestContainer");
					
					$(".jSuggestLoading").show();
					$(jC).find('ul').remove();
					
					if (opts.data == '')
						opts.data = $(this).serialize();
					else 
						opts.data = opts.data + "=" + $(this).val();
					// optimize server performance by loading at intervals
					setTimeout(function () {
						$.ajax({
							type: opts.type,
							url: opts.url,
							data: opts.data,
							success: function(msg){
								$(jC).find('ul').remove();
								$(jC).append(msg);
								$("#jSuggestContainer ul li").bind("mouseover",	function(){
										$(jH).removeClass(jsH);
										$(this).addClass(jsH);
										textVal = $(this).text();
										if (opts.autoChange)
											$(textBox).val($(jH).text());
								});
								$("#jSuggestContainer ul li").click(function(){
									$(this).addClass(jsH);
                                    value = find($(jH).text())
                                    $(textBox).val(value);
								});
								$(".jSuggestLoading").hide();
							}
						});
					}, opts.delay);
				}
			}
			// if text is too short do nothing and hide everything
			else {
				$(jH).removeClass(jsH);
				$(jC).hide();
			}
			
			// no bubbling, click is binded to textBox to prevent document bind from firing
			return false;
		});
		
		// why no use $(this).blur ?, because jSuggest box is hidden before click fires so this is the only way to do it
		// alternate way is to say that text blur will fire before$("#jSuggestContainer ul li") click.
		$(document).bind("click", function(){
			$(jC).hide();
            iniVal = textBox.value;
		});

	};

    function find(val)
    {
        var index = val.indexOf(' ');
        if(index>0)
            return val.substring(0,index);
        else return val;
    }
	$.fn.jSuggest.defaults = {
		minchar: 1,
		opacity: 1.0,
		zindex: 20000,
		delay: 500,
		loadingText: 'Yükleniyor...',
		autoChange: false,
		url: "",
		type: "POST",
		data: ""
	};
})(jQuery);
    
$.fn.disable = function() 
{
  return this.each(function() 
  {
	if (typeof this.disabled != "undefined") this.disabled = true;
  });
};

$.fn.enable = function() 
{
  return this.each(function() 
  {
	if (typeof this.disabled != "undefined") this.disabled = false;
  });
};
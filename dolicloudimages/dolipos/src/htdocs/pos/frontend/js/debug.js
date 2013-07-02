/*
 *	DEMO HELPERS
 */


/**
 *	debugData
 *
 *	Pass me a data structure {} and I'll output all the key/value pairs - recursively
 *
 *	@example var HTML = debugData( oElem.style, "Element.style", { keys: "top,left,width,height", recurse: true, sort: true, display: true, returnHTML: true });	
 *
 *	@param Object	o_Data   A JSON-style data structure
 *	@param String	s_Title  Title for dialog (optional)
 *	@param Hash	options  Pass additional options in a hash
 */
function debugData (o_Data, s_Title, options) {
	options = options || {};
	var
		str=(s_Title || 'DATA')
	//	maintain backward compatibility with OLD 'recurseData' param
	,	recurse=(typeof options=='boolean' ? options : options.recurse !==false)
	,	keys=(options.keys?','+options.keys+',':false)
	,	display=options.display !==false
	,	html=options.returnHTML !==false
	,	sort=options.sort !==false
	,	D=[], i=0 // Array to hold data, i=counter
	,	hasSubKeys = false
	,	k, t, skip, x	// loop vars
	;
	s_Title=s_Title ? s_Title+'\n':'';

	if (typeof o_Data != 'object') {
		alert( s_Title + o_Data );
		return;
	}
	if (o_Data.jquery) {
		str=s_Title+'jQuery Collection ('+ o_Data.length +')\n    context="'+ o_Data.context +'"';
	}
	else if (o_Data.tagName && typeof o_Data.style == 'object') {
		str=s_Title+o_Data.tagName;
		var id = o_Data.id, cls=o_Data.className, src=o_Data.src, hrf=o_Data.href;
		if (id)  str+='\n    id="'+		id+'"';
		if (cls) str+='\n    class="'+	cls+'"';
		if (src) str+='\n    src="'+	src+'"';
		if (hrf) str+='\n    href="'+	hrf+'"';
	}
	else {
		parse(o_Data,''); // recursive parsing
		if (sort && !hasSubKeys) D.sort(); // sort by keyName - but NOT if has subKeys!
		str+='\n***'+'****************************'.substr(0,str.length);
		str+='\n'+ D.join('\n'); // add line-breaks
	}

	if (display) alert(str); // display data
	if (html) str=str.replace(/\n/g, ' <br>').replace(/  /g, ' &nbsp;'); // format as HTML
	return str;

	function parse ( data, prefix ) {
		if (typeof prefix=='undefined') prefix='';
		try {
			$.each( data, function (key, val) {
				k = prefix+key+':  ';
				skip = (keys && keys.indexOf(','+key+',') == -1);
				if (typeof val=='function') { // FUNCTION
					if (!skip) D[i++] = k +'function()';
				}
				else if (typeof val=='string') { // STRING
					if (!skip) D[i++] = k +'"'+ val +'"';
				}
				else if (typeof val !='object') { // NUMBER or BOOLEAN
					if (!skip) D[i++] = k + val;
				}
				else if (isArray(val)) { // ARRAY
					if (!skip) {
						if (val.length && typeof val[0] == "object") { // array of objects (hashs or arrays)
							D[i++] = k +'{';
							parse( val, prefix+'    '); // RECURSE
							D[i++] = prefix +'}';
						}
						else
							D[i++] = k +'[ '+ val.toString() +' ]'; // output delimited array
					}
				}
				else if (val.jquery) {
					if (!skip) D[i++] = k +'jQuery ('+ val.length +') context="'+ val.context +'"';
				}
				else if (val.tagName && typeof val.style == 'object') {
					var id = val.id, cls=val.className, src=val.src, hrf=val.href;
					if (skip) D[i++] = k +' '+
						id  ? 'id="'+	id+'"' :
						src ? 'src="'+	src+'"' :
						hrf ? 'href="'+	hrf+'"' :
						cls ? 'class="'+cls+'"' :
						'';
				}
				else { // Object or JSON
					if (!recurse || !hasKeys(val)) { // show an empty hash
						if (!skip) D[i++] = k +'{ }';
					}
					else { // recurse into JSON hash - indent output
						D[i++] = k +'{';
						parse( val, prefix+'    '); // RECURSE
						D[i++] = prefix +'}';
					}
				}
			});
		} catch (e) {}
		function isArray(o) {
			return (o && typeof o==='object' && !o.propertyIsEnumerable('length') && typeof o.length==='number');
		}
		function hasKeys(o) {
			var c=0;
			for (x in o) c++;
			if (!hasSubKeys) hasSubKeys = !!c;
			return !!c;
		}
	}
};

if (!window.console) window.console = { log: debugData };


/**
 *	timer
 *
 *	Utility for debug timing of events
 *	Can track multiple timers and returns either a total time or interval from last event
 *	@param String	timerName	Name of the timer - defaults to debugTimer
 *	@param String	action		Keyword for action or return-value...
 *	action: 'reset' = reset; 'clear' = delete; 'total' = ms since init; 'step' or '' = ms since last event
 */
/**
 *	timer
 *
 *	Utility method for timing performance
 *	Can track multiple timers and returns either a total time or interval from last event
 *
 *	returns time-data: {
 *		start:	Date Object
 *	,	last:	Date Object
 * 	,	step:	99 // time since 'last'
 *	,	total:	99 // time since 'start'
 *	}
 *
 *	USAGE SAMPLES
 *	=============
 *	timer('name'); // create/init timer
 *	timer('name', 'reset'); // re-init timer
 *	timer('name', 'clear'); // clear/remove timer
 *	var i = timer('name');  // how long since last timer request?
 *	var i = timer('name', 'total'); // how long since timer started?
 *
 *	@param String	timerName	Name of the timer - defaults to debugTimer
 *	@param String	action		Keyword for action or return-value...
 *	@param Hash		options		Options to customize return data
 *	action: 'reset' = reset; 'clear' = delete; 'total' = ms since init; 'step' or '' = ms since last event
 */
function timer (timerName, action, options) {
	var
		name	= timerName || 'debugTimer'
	,	Timer	= window[ name ]
	,	defaults = {
			returnString:	true
		,	padNumbers:		true
		,	timePrefix:		''
		,	timeSuffix:		''
		}
	;

	// init the timer first time called
	if (!Timer || action == 'reset') { // init timer
		Timer = window[ name ] = {
			start:	new Date()
		,	last:	new Date()
		,	step:	0 // time since 'last'
		,	total:	0 // time since 'start'
		,	options: $.extend({}, defaults, options)
		};
	}
	else if (action == 'clear') { // remove timer
		window[ name ] = null;
		return null;
	}
	else { // update existing timer
		Timer.step	= (new Date()) - Timer.last;  // time since 'last'
		Timer.total	= (new Date()) - Timer.start; // time since 'start'
		Timer.last	= new Date();
	}

	var
		time = (action == 'total') ? Timer.total : Timer.step
	,	o = Timer.options // alias
	;

	if (o.returnString) {
		time += ""; // convert integer to string
		// pad time to 4 chars with underscores
		if (o.padNumbers)
			switch (time.length) {
				case 1:	time = "&ensp;&ensp;&ensp;"+ time;	break;
				case 2:	time = "&ensp;&ensp;"+ time;	break;
				case 3:	time = "&ensp;"+ time;	break;
			}
		// add prefix and suffix
		if (o.timePrefix || o.timeSuffix)
			time = o.timePrefix + time + o.timeSuffix;
	}

	return time;
};


/**
 *	showOptions
 *
 *	Pass a layout-options object, and the pane/key you want to display
 */
function showOptions (o_Settings, key, options) {
	var data = o_Settings.options;
	$.each(key.split("."), function() {
		data = data[this]; // resurse through multiple levels
	});
	debugData( data, 'options.'+key, options );
};

/**
 *	showState
 *
 *	Pass a layout-options object, and the pane/key you want to display
 */
function showState (o_Settings, key) {
	debugData( o_Settings.state[key], 'state.'+key );
};


/**
 *	addThemeSwitcher
 *
 *	Remove the cookie set by the UI Themeswitcher to reset a page to default styles
 *
 *	Dependancies: /lib/js/themeswitchertool.js
 */
function addThemeSwitcher ( container, position ) {
	var pos = { top: '10px', right: '10px', zIndex: 10 };
	$('<div id="themeContainer" style="position: absolute; overflow-x: hidden;"></div>')
		.css( $.extend( pos, position ) )
		.appendTo( container || 'body')
		.themeswitcher()
	;
};

/**
 *	removeUITheme
 *
 *	Remove the cookie set by the UI Themeswitcher to reset a page to default styles
 */
function removeUITheme ( cookieName, removeCookie ) {
	$('link.ui-theme').remove();
	$('.jquery-ui-themeswitcher-title').text( 'Switch Theme' );
	if (removeCookie !== false)
		$.cookie( cookieName || 'jquery-ui-theme', null );
};


/**
 * Form Validation & Handling class
 *
 * @todo a LOT of clean up; fix inconsistencies and optimize thoroughly
 *
 * @since 201201182202
 * @version 1.6.5
 * @inherits jQuery >= 1.4.2
 *
 * @param string	form			Targeted form-element jQuery-type selector
 * @param string	url				Controller e.g. '/register'
 * @param string	urlAction		Controller action e.g. 'save'
 * @param string	urlVld			Controller action for whole validation e.g. 'validateform'
 * @param string	urlVldField		(optional) Controller action for field validation e.g. 'validatefiled'
 * @param array		initPreStack	(optional)
 * @param array		initPostStack 	(optional)
 */
var AS_Validation_TABINDEX = 1; // STATIC
function AS_Validation( form, url, urlAction, urlVld, urlVldField, initPreStack, initPostStack )
{
	this.f = form;
	this.url = url;
	this.urlAction = urlAction;
	this.urlVld = urlVld;
	this.rt = urlVldField;
	this.preStack = [];
	this.eachStack = [];
	this.postStack = [];
	this.preStackSingle = [];
	this.eachStackSingle = [];
	this.postStackSingle = [];
	this.vld = null;
	this.vldSingle = null;
	this.excluded = {};
	this.self = this;
	this.initPreStack = initPreStack ? initPreStack : [];
	this.initPostStack = initPostStack ? initPostStack : [];
	this.plugins = [];
	this.error = false;
	this.submitted = false;

	this.field = function( e, element )
	{
		if ( element.Iref )
			clearTimeout( element.Iref );
		var id = element.id;
		var t = this;

		for ( var i = 0; i < t.preStackSingle.length; i++ ) {
			if ( typeof t.preStackSingle[i] != 'function' )
				continue;
			t.preStackSingle[i]( t );
		}

		var data = {};
		data['p'] = id;
		data[ id ] = $(this.f+  ' #'+ id).val();
		var ref = $(this.f+  ' #'+ id).attr( 'ref' );
		if ( ref )
			data[ ref ] = $(this.f+  ' #'+ ref ).val();

		$.post( t.url+ '/'+ t.rt, data, function(resp) {
			if ( t.submitted )
				return;
			for ( var i = 0; i < t.eachStackSingle.length; i++ ) {
				if ( typeof t.eachStackSingle[i] != 'function' )
					continue;
				t.eachStackSingle[i]( t.f, id );
			}

			var iLength = 0;
            for (i in resp[id]) {
                iLength++;
            }
			if ( resp.error != 0 )
				t.alerterSingle( resp );
		});
	}

	this.onVldFld = function( e, element )
	{
		if (e && e.which == 13 || window.event && window.event.keyCode == 13
		|| e && e.which == 9 || window.event && window.event.keyCode == 9 )
			return;

		if ( !element.Iref )
			element.Iref = null;
		clearTimeout( element.Iref );
		var t = this;
		element.Iref = setTimeout( function(e){t.field( e, element );}, 500 );
	};

	this.addPre = function( func )
	{
        this.preStack.push( func );
    }

	this.removePre = function( index )
	{
        this.preStack.splice(  index, 1 );
    }

	this.addPost = function( func )
	{
        this.postStack.push( func );
    }

	this.onSuccess = function( t, resp )
	{ 
		window.location.replace( resp.redirect );
	}
	this.eventSuccess = this.onSuccess;

	this.setOnSuccess = function( func )
	{
		this.eventSuccess = func;
	}

	this.onError = function( t, resp )
	{ 
		for ( var key in resp.message ) {
			$(t.f+   ' #' + key)
				.focus()
				.select();
			break;
		}
		t.alerter( resp );
	}
	this.eventError = this.onError;

	this.setOnError = function( func )
	{
		this.eventError = func;
	}

	this.onSubmit = function( t, data )
	{
		$.post( t.url+ '/'+ t.urlAction, data, function( resp ) {
			if ( resp.error == 0 ) {
				t.eventSuccess( t, resp );
			} else
				t.eventError( t, resp );
		});
	}
	this.eventSubmit = this.onSubmit;

	this.setOnSubmit = function( func )
	{
		this.eventSubmit = func;
	}

	this.setAlerter = function( func, params )
	{
		var vld = new func( this.f, this, params );

		this.preStack.push( vld.pre );
		this.eachStack.push( vld.each );
		this.postStack.push( vld.post );
		this.alerter = vld.validate;

		this.vld = vld;
		return vld;
	}

	this.setAlerterSingle = function( func, params )
	{
		this.vldSingle = new func( this.f, this, params );

		this.preStackSingle.push( this.vldSingle.pre );
		this.eachStackSingle.push( this.vldSingle.each );
		this.postStackSingle.push( this.vldSingle.post );
		this.alerterSingle = this.vldSingle.validate;

		return this.vldSingle;
	}

	this.alert = function( errors )
	{
		alert( errors );
	}
	//this.alerter = this.alert;
	this.setAlerter( AS_Alerter_Default );
	this.alerterSingle = this.alert;

	/**
	 * PreStack
	 */
	this.runPreStack = function( t, e ) {
		for ( var i = 0; i < t.preStack.length; i++ ) {
			if ( typeof t.preStack[i] != 'function' )
				continue;
			t.preStack[i]( t, e );
		}
	}

	/**
	 * PostStack
	 */
	this.runPostStack = function( t ) {
		for ( var i = 0; i < t.postStack.length; i++ ) {
			if ( typeof t.postStack[i] != 'function' )
				continue;
			t.postStack[i]( t );
		}
	}

	/**
	 * Adds a plugin 
	 * @return uint Index of added plugin
	 */
	this.addPlugin = function( func, params )
	{
		var p = new func( this, params );
		return this.plugins.push( p ) - 1;
	}

	/**
	 * CONSTRUCTOR
	 * (must be defined last)
	 */
	this.init = function()
	{
		var t = this;

		// Tab-index fix for FFX4...
		$(this.f+ ' input, '+ this.f+ ' select, '+ this.f+ ' textarea, '+ this.f+ ' button').each(function() { 
			if (this.type != 'hidden') {
				var $input = $(this);
				$input.attr( 'tabindex', AS_Validation_TABINDEX );
				AS_Validation_TABINDEX++;
			}
		});

		for ( var i = 0; i < t.initPreStack.length; i++ ) {
			if ( typeof t.initPreStack[i] != 'function' )
				continue;
			t.initPreStack[i]( t ); 
		}

		if ( t.rt ) {
			$(this.f+ " input:not(input[type='button'], input[type='submit'], input[type=radio], input[type=checkbox], input[type='file']), "+ this.f+ " textarea")
				.live( 'keyup', function(e){ t.onVldFld( e, this ); } )
				.live( 'blur', function(e){ t.field( e, this ); } );
			$(this.f+ " select")
				.live( 'change', function(e){ t.onVldFld( e, this ); } )
				.live( 'blur', function(e){ t.field( e, this ); } );
		}

		this.addPre( function( t, e ) {
			e.preventDefault();
		});

		$(this.f).live( 'submit', function(e) {

			t.submitted = true;
			t.runPreStack( t ,e );

			var data = {};
			$(t.f+  " select, "+ t.f+ " textarea, "
			+ t.f+ " input[type=radio]:checked, "
			+ t.f+ " input:not(input[type='button'], input[type='submit'], input[type=radio], input[type=checkbox], input[type='file'])").each( function() {
				data[this.name] = $(this).val();
			});
			$(t.f+ " input[type=checkbox]:checked").each( function() { 
				if ( typeof data[this.name] != 'object' ) {
					data[this.name] = [];
				} 
				data[this.name].push( $(this).val() );
			});
		
			if ( t.urlVld ) {
				$.post( t.url+ '/'+t.urlVld, data, function(resp) {

					var vldStack = [];
					var errs = {};
					var first = null;
					for ( var id in resp ) {

						if ( t.excluded[ id ]  ) {
							continue;
						}
						for ( var i = 0; i < t.eachStack.length; i++ ) {
							if ( typeof t.eachStack[i] != 'function' )
								continue;
							t.eachStack[i]( t, id );
						}
						var iLength = 0;
						for ( var j in resp[id] ) {
							iLength++;
						}
						if ( iLength > 0 )
							vldStack.push(id);

						if ( first == null ) {
							$(t.f+   ' #'+ id)
								.focus()
								.select();
							first = id;
						}
						for ( var errMsg in resp[id] ) {
							if ( !errs[ id ] )
								errs[ id ] = [];
							errs[ id ].push( resp[ id ][ errMsg ] );
						}
					}

					if ( vldStack.length == 0 ) {
						t.eventSubmit( t, data );
						t.error = false;
						t.runPostStack( t );
					} else {
						t.eventError( t, resp );
						t.error = true;
						t.runPostStack( t );
					}
					t.submitted = false;
				});
			} else {
				t.eventSubmit( t, data );
				t.error = false;
				t.runPostStack( t );
				t.submitted = false;
			}
		});
	}
	this.init();
}

/**
 * DEFAULT
 */
function AS_Alerter_Default( form, t, params )
{
	this.f = form;

	this.pre = function( t ){}

	this.each = function( f, id ){}

	this.validate = function( errors )
	{
		alert(errors);
	}

	this.post = function(){}
}

/**
 * Default alerter - Whole form
 */
function AS_Alerter_Label( form, t, params )
{
	this.f = form;

	this.pre = function( t )
	{
		$(t.f+ ' ul.errors').remove();
	}

	this.each = function( f, id ){}

	this.validate = function( errors )
	{
		for ( id in errors ) {
			var o = '<ul id="errors-' + id + '" class="errors">';
			for ( errorKey in errors[id] ) {

				o += '<li>' + errors[id][errorKey] + '</li>';
			}
			o += '</ul>';
            if ( params.output == 'append' )
                $(this.f+  ' #' + id).parent().append( o );
            else
                $(this.f+  ' #' + id).parent().prepend( o );
		}
	}

	this.post = function(){}
}

/**
 * Default alerter - Single form field
 */
function AS_Alerter_LabelSingle( form, t, params )
{
	this.f = form;

	this.pre = function( t ){}

	this.each = function( f, id )
	{
		$(f+  ' #' + id).parent().find('.errors').remove();
	}

	this.validate = function( errors )
	{
		for ( id in errors ) {
			var o = '<ul id="errors-' + id + '" class="errors">';
			for ( errorKey in errors[id] ) {

				o += '<li>' + errors[id][errorKey] + '</li>';
			}
			o += '</ul>';
            if ( params.output == 'append' )
                $(this.f+  ' #' + id).parent().append( o );
            else
                $(this.f+  ' #' + id).parent().prepend( o );
		}
	}

	this.post = function(){}
}

/**
 * PreInit - Sets up input fields that have labels inside of them
 */
function AS_PreInit_InlineLabel( t )
{
	var selector = $( t.f+ " input:not(input[type='button'], input[type='submit'], input[type=radio], input[type=checkbox], input[type='file'], input[type='hidden']), "+ t.f+ " textarea" );
	t.addPre( function(){
		selector.each( function() {
			if ( this._defVal == this.value )
				this.value = '';
		});
	});
	selector.each( function() {
		this._defVal = this.value;
	});
	selector.live( 'focus', function(e) {
		if ( this.value == this._defVal )
			this.value = '';
	});
	selector.live( 'blur', function(e) {
		if ( this.value == '' ) {
			this.value = this._defVal;
		}
	});
}

/**
 * Plugin - Adds the "loading" modal widget notifier
 * @extends jQuery loader plugin
 */
function AS_Plugin_Loader( t )
{
	t.addPre( function ( t ) {
		$.loader();
	});

	t.addPost( function ( t ) {
		if ( t.error )
			$.loader('close');
	});
}

/**
 * Plugin - Reverts the form to submit via Iframe ( i.e. when you need file uploads )
 */
function AS_Plugin_SubmitToIframe( tf, p )
{
	tf.setOnSubmit( function( t ) { 
		$(t.f).die( 'submit', t.submit );
		$(t.f)[0].target = p.iframeID;
		$(t.f)[0].action = t.url+ '/'+ t.urlAction;
		$(t.f)[0].submit();

		$('#'+p.iframeID).load( function( e ) { 
			t.runPreStack( t, e );
			
			var resp = $('#'+p.iframeID)[0].contentWindow.document.body.innerHTML;
			resp = $.parseJSON( resp ); 
			if ( resp.redirect ) {
				window.location.href = ( resp.redirect );
				return;
			}
			t.vld.pre( t );
			t.eventError( t, resp.message );
			t.error = true;
			t.runPostStack( t );
		});
	});
}

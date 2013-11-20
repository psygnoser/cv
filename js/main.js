$(function(){
	$('.add').show();
	
	$( "#accordion" ).find( ".ui-tabs-nav" ).sortable({
			axis: "x",
			//handle: "ul#tabs li",
			stop: function(e, ui) {
				var items = []; 
                $( "#accordion #tabs" ).children('li').each(function(){ 
                    $id = $(this).attr('id');
                    $id = $id.slice(1,$id.length);
                    items.push( $id ); 
                });
				$.post( 
					"index/position/type/sections", 
					{data: items},
					function ( data ) {
						//console.log( data )
					});
			}
		});


	$('#accordion .fieldsets > div.fs').sortable({
			axis: "y",
			handle: "div.bull",
			start: function(e, ui) {
				ui.item.css({opacity: 0.5});
			},
			stop: function(e, ui) {
				var items = $(this).sortable('toArray');
				$.post( 
					"index/position/type/fieldsets", 
					{data: items},
					function ( data ) {
						//console.log( data )
					});
				ui.item.css({opacity: 1.0});
			}
		});
	$('#accordion .fields').sortable({
			axis: "y",
			handle: "div",
			start: function(e, ui) {
				ui.item.css({opacity: 0.5});
			},
			stop: function(e, ui) {
				var items = $(this).sortable('toArray');
				$.post( 
					"index/position/type/fields", 
					{data: items},
					function ( data ) {
						//console.log( data )
					});
				ui.item.css({opacity: 1.0});
			}
		});

	$('#accordion #tabs div.edit').editable('index/save/type/sections/name/name', { 
		indicator: 'Saving...',
		tooltip: 'Click to edit...'
	});

	$('#accordion .fieldsets legend div.edit').editable('index/save/type/fieldsets/name/name', { 
		indicator: 'Saving...',
		tooltip: 'Click to edit...'
	});

	$('#accordion .fields div.edit').editable('index/save/type/fields/name/name', {
		indicator: 'Saving...',
		tooltip: 'Click to edit...'
	});
	$('#accordion .fields dd.edit').editable('index/save/type/fields/name/data', { 
		type: 'textarea',
		cancel: 'Cancel',
		submit: 'OK',
		indicator: 'Saving...',
		tooltip: 'Click to edit...'
	});

	$('.controls.fields .add, dl > .remove, fieldset > .remove, .controls.fieldsets .add, .controls.accord .add').live('click', function(event){
		event.stopImmediatePropagation();
		event.preventDefault();
	});
	$('.controls.fields .add').live('mouseup', function(event){

		var fieldset_id = $(this).parent().prev().parent().attr('id');
		var position = $(this).parent().prev().children().length;
		var t = $(this);

		$.post( 
			'index/create/type/fields', 
			{'parent_id': fieldset_id, 'position': position},
			function ( data ) {	

			$.post( 'index/sub/tpl/dl', function( node ) {

				node = $(node);
				node.attr('id', data.fid );
				node.children().eq(0).children().eq(1).attr('id', data.fid);
				node.children().eq(0).children().eq(1).text('...');
				node.children().eq(1).attr('id', data.fid);
				node.children().eq(1).text('...');
				t.parent().prev().append(node);

				$('#accordion .fields div.edit[id='+ data.fid+ ']').editable('index/save/type/fields/name/name', {
					indicator: 'Saving...',
					tooltip: 'Click to edit...'
				});
				$('#accordion .fields dd.edit[id='+ data.fid+ ']').editable('index/save/type/fields/name/data', { 
					type: 'textarea',
					cancel: 'Cancel',
					submit: 'OK',
					indicator: 'Saving...',
					tooltip: 'Click to edit...'
				});
			});
		});
	});

	$('dl > .remove').live('mouseup', function(event){
		var t = $(this);
		var id = $(this).parent().attr('id');
		$.post( 'index/remove/type/fields', {'id': id}, function ( data ) {	
			if ( data.error )
				alert( data.msg );
			else
				t.parent().remove();
		});
	});
	$('fieldset > .remove').live('mouseup', function(event){
		var t = $(this);
		var id = $(this).parent().attr('id');
		$.post( 'index/remove/type/fieldsets', {'id': id}, function ( data ) {	
			if ( data.error )
				alert( data.msg );
			else
				t.parent().remove();
		});
	});
    
	$('.controls.fieldsets .add').live('mouseup', function(event){

		var section_id = $(this).parent().parent().parent().attr('idtab');
		var position = $(this).parent().prev().children().length;
		var t = $(this);
		$.post( 
			'index/create/type/fieldsets', 
			{'parent_id': section_id, 'position': position},
			function ( data ) {

			$.post( 'index/sub/tpl/fieldset', function( node ) {

				node = $(node);
				node.attr('id', data.fid );
				node.children().eq(1).children().eq(1).attr('id', data.fid);
				node.children().eq(1).children().eq(1).text('...');
				t.parent().prev().append(node);

				$('#accordion .fieldsets legend div.edit[id='+ data.fid+ ']').editable('index/save/type/fieldsets/name/name', { 
					indicator: 'Saving...',
					tooltip: 'Click to edit...'
				});
			});
		});
	});
    
    $('.controls.accord').show();
    $('.controls.accord .add').live('mouseup', function(event){ 

		var position = $('#tabs').children().length;
		var t = $(this);
		$.post( 
			'index/create/type/sections', 
			{'position': position},
			function ( data ) {

			$.post( 'index/sub/tpl/accord', function( node ) {

				node = $(node);
				node.attr('id', 'tab'+data.fid );
                node.attr('idtab', data.fid );
				$('div#sections > #accordion').append(node);
                $('#accordion').tabs('add', '#tab'+data.fid, '...');
                //console.log('#accordion #tabs div.edit[id='+ data.fid+ ']');
                $('#accordion #tabs div.edit[id='+ data.fid+ ']').editable('index/save/type/sections/name/name', { 
					indicator: 'Saving...',
					tooltip: 'Click to edit...'
				});
			});
		});
	});

	$('dl').live('mouseover', function(event) { 
			$(this).children().eq(0).children().eq(0).show();
			$(this).children().eq(2).show();
			$(this).children().eq(2).css('display', 'block');
		})
		.live('mouseout', function(event) {
			$(this).children().eq(0).children().eq(0).hide();
			$(this).children().eq(2).hide();
		});
	$('legend').live('mouseover', function(event) { 
			$(this).children().eq(0).css('visibility','visible');
		})
		.live('mouseout', function(event){
			$(this).children().eq(0).css('visibility','hidden');
		});
	$('fieldset').live('mouseover', function(event) { 
			$(this).children().eq(0).show();
			$(this).children().eq(0).css('display', 'block');
		})
		.live('mouseout', function(event){
			$(this).children().eq(0).hide();
		});
});
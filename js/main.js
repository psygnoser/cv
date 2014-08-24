$(function(){
    _edit = true;
    $("#tabs .ui-state-active .ui-icon.ui-icon-close, .bullw").show();
    editFunc();
        
    $('.add').show();

    $( "#accordion" ).find( ".ui-tabs-nav" ).sortable({
            stop: function(e, ui) {
                var items = [];
                $( "#accordion #tabs" ).children('li').each(function(){ 
                    $id = $(this).attr('id');
                    $id = $id.slice(1,$id.length);
                    items.push( $id ); 
                });
                $.post("index/position/type/sections", {data: items});
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
                $.post("index/position/type/fieldsets", {data: items});
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
                $.post("index/position/type/fields", {data: items});
                ui.item.css({opacity: 1.0});
            }
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

    $('#content').on('click', '.controls.fields .add, dl > .remove, fieldset > .remove, #accordion > .remove, \n\
.controls.fieldsets .add, .controls.accord .add', function(event){ //console.log('klik');
        event.stopImmediatePropagation();
        event.preventDefault();
    });
    $('#content').on('mouseup', '.controls.fields .add', function(event){

        var fieldset_id = $(this).parent().prev().parent().attr('id');
        var position = $(this).parent().prev().children().length;
        var t = $(this);
        $.post( 'index/create/type/fields', {'parent_id': fieldset_id, 'position': position}, function ( data ) {
            $.post( 'index/sub/tpl/dl', function( node ) {
                node = $(node);
                node.attr('id', data.fid );
                node.children().eq(0).children().eq(1).attr('id', data.fid).text('...');
                node.children().eq(1).attr('id', data.fid).text('...');
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
    $('#content').on('mouseup', '.controls.fieldsets .add', function(event){

        var section_id = $(this).parent().parent().parent().attr('idtab');
        var position = $(this).parent().prev().children().length;
        var t = $(this);
        $.post( 'index/create/type/fieldsets', {'parent_id': section_id, 'position': position},function ( data ) {
            $.post( 'index/sub/tpl/fieldset', function( node ) {
                node = $(node);
                node.attr('id', data.fid );
                node.children().eq(1).children().eq(1).attr('id', data.fid).text('...');
                t.parent().prev().append(node);

                $('#accordion .fieldsets legend div.edit[id='+ data.fid+ ']').editable('index/save/type/fieldsets/name/name', {
                    indicator: 'Saving...',
                    tooltip: 'Click to edit...'
                });
            });
        });
    });
    $('.controls.accord').show();
    $('#accordion > .remove').show();
    $('#content').on('mouseup', '.controls.accord .add', function(event){ //console.log('sdfsf');

        var position = $('#tabs').children().length;
        var t = $(this);
        $.post( 'index/create/type/sections', {'position': position}, function ( data ) {
            $.post( 'index/sub/tpl/accord', function( node ) {
                node = $(node);
                node.attr('id', 'tab'+data.fid );
                node.attr('idtab', data.fid );
                $('div#sections > #accordion').append(node);
                var tabs = $('#accordion').tabs();
                var ul = tabs.find( "ul" );
                $( '<li id="t'+data.fid+'"><a href="'+_PATH+'#tab'+data.fid+'">...</a>\n\
                <div id="'+data.fid+'" class="edit">...</div> \n\
                 <span class="ui-icon ui-icon-close" role="presentation">Remove tab</span> \n\
                </li>' ).appendTo( ul );
                tabs.tabs( "refresh" );
                $('#accordion #tabs li[id=t'+data.fid+']').off('keydown');
                $('#accordion #tabs li').last().attr('id', 't'+data.fid);
                $('#accordion #tabs div.edit[id='+ data.fid+ ']').editable('index/save/type/sections/name/name', { 
                    indicator: 'Saving...',
                    tooltip: 'Click to edit...',
                    callback: function(){
                        $('#accordion #tabs .ui-state-active a').html( $('#accordion #tabs .ui-state-active div.edit').html() );
                    }
                });
                $('#accordion #tabs, #accordion #tabs li, #accordion #tabs li a').off('keydown');
            });
        });
    });

    var removeField = function(event){
        var t = $(event.currentTarget);
        var id = t.parent().attr('id');
        $.post( 'index/remove/type/fields', {'id': id}, function ( data ) {
            if ( data.error ) alert( data.msg );
            else t.parent().remove();
        });
    };
    var removeFieldset = function(event){
        var t = $(event.currentTarget);
        var id = t.parent().attr('id');
        $.post( 'index/remove/type/fieldsets', {'id': id}, function ( data ) {
            if ( data.error ) alert( data.msg );
            else t.parent().remove();
        });
    };
    var removeTab = function(event){ 
        var id = $('#tabs .ui-state-active').attr('id');
        id = id.slice(1,id.length);  console.log('rTab');
        $.post( 'index/remove/type/sections', {'id': id}, function ( data ) {
            if ( data.error ) alert( data.msg );
            else {
                 var tabs = $('#accordion').tabs();
                 $( "#t" + id ).remove();
                 $( "#tab" + id ).remove();
                 tabs.tabs( "refresh" );
            }
        });
    };
    $('#content').on('mouseup', 'dl > .remove', function(event){
        $( "#remove-dialog" ).data('rFunc', {func:removeField, e:event}).dialog('open');
    });
    $('#content').on('mouseup', 'fieldset > .remove', function(event){
        $( "#remove-dialog" ).data('rFunc', {func:removeFieldset, e:event}).dialog('open');
    });
    $("#accordion").tabs().delegate("span.ui-icon-close", "click", function(event) {
        $( "#remove-dialog" ).data('rFunc', {func:removeTab, e:event}).dialog('open');
    });
    $( "#remove-dialog" ).dialog({
        resizable: false,
        height:165,
        modal: true,
        autoOpen: false,
        open: function(e, ui) {},
        buttons: {
            "Delete": function(e) {
                $(this).data('rFunc').func( $(this).data('rFunc').e );
                //$( "#remove-dialog" ).find('.msg').html($(e).attr('title'));
                $( this ).dialog( "close" );
            },
            Cancel: function() {
                $( this ).dialog( "close" );
            }
        }
    });

    $('fieldset').on('mouseover', 'dl', function(event) {
            $(this).children().eq(0).children().eq(0).show();
            $(this).children().eq(2).show().css('display', 'block');
        })
        .on('mouseout', 'dl', function(event) {
            $(this).children().eq(0).children().eq(0).hide();
            $(this).children().eq(2).hide();
        });
    $('#accordion').on('mouseover', 'legend', function(event) {
            $(this).children().eq(0).css('visibility','visible');
        })
        .on('mouseout', 'legend', function(event){
            $(this).children().eq(0).css('visibility','hidden');
        });
    $('#accordion').on('mouseover', 'fieldset', function(event) {
            $(this).children().eq(0).show().css('display', 'block');
        })
        .on('mouseout', 'fieldset', function(event){
            $(this).children().eq(0).hide();
        });
        
    //$('input,textarea').addClass('ui-corner-all ui-state-default');
});
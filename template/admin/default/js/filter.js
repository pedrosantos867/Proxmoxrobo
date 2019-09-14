var order = {field: '', type: ''};
var filter = {};
var page = 1;

var loader = {
    obj: null,
    init: function () {
        $('.loader').remove();
        var html = '<div class="loader">' +
            '<div class="bg"></div>' +
            '<div class="pre-loader">' +
            '<div class="box1"></div>' +
            '<div class="box2"></div>' +
            '<div class="box3"></div>' +
            '<div class="box4"></div>' +
            '<div class="box5"></div>' +
            '<div class="box6"></div>' +
            '<div class="box7"></div>' +
            '</div>' +
            ' </div>';
        //  alert(html);
        $('body').after(html);

    },

    display: function () {

        $('.loader').show();
    },
    hide: function () {
        $('.loader').hide();
    }
}

function createNoty(message, type) {
    var html = '<div class="alert alert-popup alert-' + type + ' alert-dismissible page-alert">';
    html += '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>';
    html += '<span class="message"><span class="glyphicon glyphicon-alert"></span> &nbsp;&nbsp;&nbsp;'+ message +'</span>';
    html += '</div>';
    $(html).prependTo('#noty-holder').slideDown();
    setTimeout(function(){
        $('#noty-holder div.alert').slideUp()
    }, 2000);
};


function parseFilterFields() {

    var nulled_filter = true;
    $('.filter').each(function (key) {

        var field = $(this).attr('name');
        var val = (this.value);
        var type = $(this).data('type');
        if(val){
            nulled_filter = false;
        }

        filter[field] = {value: val, type: type};
    });

   // if(!nulled_filter) {
        $.cookie('filter', JSON.stringify(filter), {expires: 1, path: window.location.pathname});
        jHash.val('filter', JSON.stringify(filter));
   // } else {
    //    $.cookie('filter', '', {expires: 1, path: window.location.pathname});

   // }
    getTableWithFilter();
    return !nulled_filter;
}

function getTableWithFilter() {
    loader.display();
    $.ajax({
        type: 'post',
        dataType: 'html',
        data: {order: order, ajax: 1, filter: filter, page: page},
        success: function (data) {
            $('.ajax-block').replaceWith(data);
            loader.hide();
        }
    })
}

function ajaxLoadBlock(url) {
    loader.display();
    $.ajax({
        url: url,
        type: 'post',
        dataType: 'html',

        data: {order: order, ajax: 1, filter: filter, page: page},
        success: function (data) {
            $('.ajax-block').replaceWith(data);
            loader.hide();
        }
    })
}

$(function () {
    $('body').on('click', 'a.ajax-load', function (e) {
        e.preventDefault();
        ajaxLoadBlock($(this).attr('href'));

    });

    loader.init();
    $('body').on('click', 'a.order', function () {
        var field = $(this).data('field');
        var type = $(this).data('type');

        order = {field: field, type: type}
        getTableWithFilter();

        return false;
    })

    $('body').on('keyup', '.filter', function (e) {
        if (e.keyCode == 13) {
            page = 1;
            jHash.val('page', '1');
            parseFilterFields();

        }
    })
    $('body').on('change', 'select.filter', function (e) {
        page = 1;
        jHash.val('page', '1');
        parseFilterFields();


    })

    $('body').on('click', 'a.change-page', function () {
        page = $(this).data('page');
        jHash.val('page', page);

        getTableWithFilter();
        return false;
    })

    $('body').on('click', 'a.ajax-action', function (e) {
        if ($(this).data('confirm')) {
            if (!confirm($(this).data('confirm'))) {
                return false;
            }
        }
        loader.display();
        var link = $(this);
        $.ajax({
            method: 'post',
            dataType: 'json',
            url: link.attr('href'),
            data: {ajax: 1},
            success: function (data) {
                loader.hide();
                if (data.result == 1) {
                    getTableWithFilter();
                    if(data['message']){
                        createNoty(data['message'], 'success');
                    }
                } else {
                    createNoty(data['message'], 'danger');
                }
            }

        })
        e.preventDefault();
    });

    $('body').on('click', 'a.ajax-modal', function (e) {
        // alert();
        loader.display();
        $('#ajaxModal').modal('hide');
        $('.modal-backdrop').remove();
        var link = $(this);
        $.ajax({
            method: 'post',
            async: false,
            dataType: 'html',
            url: link.attr('href'),
            data: {ajax: 1},
            success: function (data) {
                $('.loaded-block').remove();
                $('body').prepend(data);
                loader.hide();
                $('#ajaxModal').modal('show');
                $('#ajaxModal').on('shown.bs.modal', function () {
                    $('#ajaxModal input, #ajaxModal select, #ajaxModal textarea').trigger('blur');
                })

            }
        });
        e.preventDefault();
    });

    $('body').on('submit', '.ajax-form', function (e) {
        loader.display();
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            method: 'post',
            dataType: 'json',
            data: form.serialize(),
            success: function (data) {
                if (data.result) {
                    createNoty(data['message'], 'success');
                    //   Messenger().post({message: data['message'], type: "success"});
                } else {
                    createNoty(data['message'], 'danger');
                    //   Messenger().post({message: data['message'], type: "error"});
                }
                loader.hide();
                getTableWithFilter();
                $('#ajaxModal').modal('hide');
                //$('.loaded-block').remove();
            }
        })

    })

    function hashParse() {
        var load = false;
        if ((jHash.val('page') && jHash.val('page') != page)) {
            page = jHash.val('page');
            load = true;
        }
        if (jHash.val('filter')) {
            filter = JSON.parse(jHash.val('filter'));
            load = true;
        }


        if($.cookie('filter')){
            filter = JSON.parse($.cookie('filter'));
            load = true;
        }
        if(load || ($('.ajax-block').length  && !$('.ajax-block').html())) {
            getTableWithFilter();
        }

    }

    $(window).on('hashchange ', function (e) {
        hashParse();
    })
    hashParse();


})


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
};

function createNoty(message, type) {

    var html = '<div class="alert alert-popup alert-' + type + ' alert-dismissible page-alert">';
    html += '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>';
    html += '<span class="message"><span class="glyphicon glyphicon-alert"></span> &nbsp;&nbsp;&nbsp;'+ message +'</span>';
    html += '</div>';
    $('body').prepend('<div id="noty-holder"></div>');
    $(html).prependTo('#noty-holder').slideDown();
    setTimeout(function(){
        $('#noty-holder div.alert').slideUp()
    }, 2000);
    return true;
}

$(document).on('ready', function () {
   $("input[name=promocode_on]").on('change', function () {
       $('.promocode-inp-inner').toggle(300);
   })
});
function str_rand() {
    var result       = '';
    var words        = '0123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
    var max_position = words.length - 1;
    for( i = 0; i < 10; ++i ) {
        position = Math.floor ( Math.random() * max_position );
        result = result + words.substring(position, position + 1);
    }
    return result;
}

$(document).on("ready", function () {
    $("body").on("click",".generate-rand", function () {
        $($(this).data('input')).val(str_rand()).trigger("change");
    });
});
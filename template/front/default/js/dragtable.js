$(document).ready(function () {
    $.getScript("http://code.jquery.com/ui/1.9.2/jquery-ui.js").done(function (script, textStatus) {
        $('tbody').sortable({
            stop: function (event, ui) {
                var p = 1;
                var arr = {};
                $('table.dragable tr').each(function () {
                    var id_e = ($(this).data('id'));
                    if (id_e) {
                        arr[id_e] = p;
                        console.log(p + " " + id_e);
                        p++;
                    }
                })

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    data: {ajax: 1, action: 'setPositions', data: arr}
                });
            }
        });

    });

});

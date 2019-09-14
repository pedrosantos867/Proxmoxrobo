$(document).ready(function () {

        $('tbody').sortable({
            stop: function (event, ui) {
                var p = 1;
                var arr = {};
                var table_id = $('table.dragable').data('id');


                $('table.dragable tr').each(function () {
                    var id_e = ($(this).data('id'));
                    if (id_e) {
                        arr[id_e] = p;

                        p++;
                    }
                });

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    data: {ajax: 1, action: 'setPositions', data: arr, table_id: table_id}
                });
            }
        });



});

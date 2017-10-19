/**
 * JS for options/settings page
 */
(function ($, undefined) {



    $(function () {
        var nextIndex = -1,
            $table = $('#protected_values'),
            $buttonAddRow = $('#add_row'),
            $tableRowTemplate = $('#row_template');

        $buttonAddRow.on('click', function (event) {
            event.preventDefault();
            addTableRow();
        });

        function addTableRow() {
            var $newRow = $tableRowTemplate.clone();
            var html = $newRow.html().replace(/IDX/g, nextIndex--);
            $table.append('<tr>' + html + '</tr>');
        }


        function init() {
            $tableRowTemplate.detach();
        }

        init();
    });
}(jQuery));

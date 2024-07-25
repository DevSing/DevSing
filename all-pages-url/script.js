jQuery(document).ready(function($) {
    function loadFilteredData(type, user, paged) {
        $.ajax({
            url: allPageUrlAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'all_page_url_load_filtered_data',
                nonce: allPageUrlAjax.nonce,
                type: type,
                user: user,
                paged: paged
            },
            success: function(response) {
                $('#all-page-url-content').html(response);
            }
        });
    }

    $('#filter-type, #filter-user').change(function() {
        var type = $('#filter-type').val();
        var user = $('#filter-user').val();
        loadFilteredData(type, user, 1);
    });

    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var type = $('#filter-type').val();
        var user = $('#filter-user').val();
        var paged = $(this).attr('href').split('paged=')[1];
        loadFilteredData(type, user, paged);
    });

    function downloadCSV(csv, filename) {
        var csvFile;
        var downloadLink;

        csvFile = new Blob([csv], { type: "text/csv" });
        downloadLink = document.createElement("a");
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
    }

    window.exportTableToCSV = function(filename) {
        var csv = [];
        var rows = document.querySelectorAll("#all-page-url-content table tr");

        for (var i = 0; i < rows.length; i++) {
            var row = [], cols = rows[i].querySelectorAll("td, th");
            for (var j = 0; j < cols.length; j++)
                row.push(cols[j].innerText);
            csv.push(row.join(","));
        }

        downloadCSV(csv.join("\n"), filename);
    }

    window.exportTableToExcel = function(filename) {
        var table = document.querySelector("#all-page-url-content table");
        var downloadLink;
        var dataType = 'application/vnd.ms-excel';
        var tableHTML = table.outerHTML.replace(/ /g, '%20');

        filename = filename ? filename : 'excel_data.xls';

        downloadLink = document.createElement("a");
        document.body.appendChild(downloadLink);

        if (navigator.msSaveOrOpenBlob) {
            var blob = new Blob(['\ufeff', tableHTML], {
                type: dataType
            });
            navigator.msSaveOrOpenBlob(blob, filename);
        } else {
            downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
            downloadLink.download = filename;
            downloadLink.click();
        }
    }
});

// handles filtering weather events by country

$(document).ready(function(){
    var selected = $("#filterCountry").val();
    console.log(selected);

    //immediately show the first 10 entries when page load
    $.ajax({
        url: 'filterCountry.php',
        type: 'post',
        data: { filterCountryName: selected, page: getParameterByName('page') },
        success: function(response) {
            $("#resultContainer").html(response);
        },

    });


    //When filter bar changes, show the appropiate countries and hide the page number
    $("#filterCountry").on("change", function() {
        var selected = $(this).val();
        console.log(selected);

        $.ajax({
            url: 'filterCountry.php',
            type: 'post',
            data: { filterCountryName: selected, page: getParameterByName('page') },
            success: function(response) {
                $("#resultContainer").html(response);
                if (selected !== "") {
                    $(".page-numbers").hide();
                } else {
                    $(".page-numbers").show();
                }
            },            
        });
    });

    //Get paging data from URL to show data from page 2
    // https://stackoverflow.com/questions/53717122/how-to-write-regexp-to-get-a-paramater-from-url
    function getParameterByName(name) {
        var url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }
});

$(document).ready(function(){
    var selected = $("#filterCountry").val();
    console.log(selected);
    // paging_sql = "SELECT weatherevents.*, location.*, media.*  FROM weatherevents JOIN `location` ON weatherevents.locationID = location.locationID LEFT JOIN `mediainevent` ON weatherevents.eventID = mediainevent.eventID LEFT JOIN `media` ON mediainevent.mediaID = media.mediaID ORDER BY weatherevents.date DESC LIMIT 0,10"

    $.ajax({
        url: 'filterCountry.php',
        type: 'post',
        data: { filterCountryName: selected, page: getParameterByName('page') },
        success: function(response) {
            
            $("#resultContainer").html(response);
        },
        error: function(xhr, status, error) {
            console.error("Error: " + error);
        }
    });




    $("#filterCountry").on("change", function() {
        var selected = $(this).val();
        console.log(selected);
        // paging_sql = "SELECT weatherevents.*, location.*, media.* FROM weatherevents JOIN `location` ON weatherevents.locationID = location.locationID LEFT JOIN `mediainevent` ON weatherevents.eventID = mediainevent.eventID LEFT JOIN `media` ON mediainevent.mediaID = media.mediaID WHERE location.country = ? ORDER BY weatherevents.date DESC"

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
            error: function(xhr, status, error) {
                console.error("Error: " + error);
            }
            
        });
    });
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

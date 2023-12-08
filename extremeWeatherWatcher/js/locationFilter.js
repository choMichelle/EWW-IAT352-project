$(document).ready(function(){

    $("#filterCountry").on("change", function() {
        var selected = $(this).val().toLowerCase();
        $(".generic-event-container .event-container").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(selected) > -1);
        });
    });
});
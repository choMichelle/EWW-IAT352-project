$(document).ready(function(){
    // Assuming you have an element with id "filterCountry"
    $("#filterCountry").on("change", function() {
        var selected = $(this).val().toLowerCase();
        $("#eventTable a").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(selected) > -1);
        });
    });
});
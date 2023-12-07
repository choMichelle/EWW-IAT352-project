$(document).ready(function() {
    $('.removeButton').on('click', function(event) {
        // Make sure clicking this button will not redirect the page
        event.preventDefault();

        // Get the parent with data-prod_id
        var country = $(this).closest('li').attr('data-country');
        alert(country);

        $.ajax({
            url: 'removeCountryFromWatchlist.php',
            type: 'POST',
            data: {removedCountryName: country},
        })
        .done(function(res) {
            alert(res);
        })
        .fail(function(res) {
            console.log("error");
        });
    });
});

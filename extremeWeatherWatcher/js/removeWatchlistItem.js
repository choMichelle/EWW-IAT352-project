$(document).ready(function() {
    $('.removeButton').on('click', function(event) {
        event.preventDefault();
    
        var country = $(this).closest('li').attr('data-country');
        var listItem = $('#' + country);
    
        // Show loading indicator
        listItem.html('Removing...');
    
        $.ajax({
            url: 'removeCountryFromWatchlist.php',
            type: 'post',
            data: {removedCountryName: country},
            success: function(data){
                if (country) {
                    listItem.hide();
                }
            },
            error: function(xhr, status, error) {
                console.log("Error: " + error);
                // Handle error and update UI accordingly
                listItem.html('Error removing country.');
            }
        });
    });
});
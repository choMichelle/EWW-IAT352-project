$(document).ready(function() {
    $('.removeButton').on('click', function(event) {
        event.preventDefault();
    
        var country = $(this).closest('li').attr('data-country');
        var listItem = $('#' + country);
        console.log(country);
        // Show loading indicator
        listItem.html('Removing...');
    
        $.ajax({
            url: 'removeCountryFromWatchlist.php',
            type: 'post',
            data: {removedCountryName: country},
        });

        listItem.remove();
    });
});
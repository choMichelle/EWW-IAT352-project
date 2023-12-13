// handles removing items from the user's watchlist

$(document).ready(function() {
    $('.removeButton').on('click', function(event) {
        event.preventDefault();
    
        //When clicked on remove
        //get the data-country attribute value (country) from the closest tr element
        var country = $(this).closest('tr').attr('data-country');
        var listItem = $('#' + country);
        console.log(country);
    
        $.ajax({
            //send country data to .php page to remove corresponding record from db
            url: 'removeCountryFromWatchlist.php',
            type: 'post',
            data: {removedCountryName: country},
        });

        //remove the item from the list, notdatabase
        listItem.remove();
    });
});
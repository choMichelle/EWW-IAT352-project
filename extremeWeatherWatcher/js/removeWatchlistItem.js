$(document).ready(function() {
    $('.removeButton').on('click', function(event) {
        event.preventDefault();
    
        //When clicked on remove, remove the according tr element near the remove item
        var country = $(this).closest('tr').attr('data-country');
        var listItem = $('#' + country);
        console.log(country);
    
        $.ajax({
            //take it off the database here
            url: 'removeCountryFromWatchlist.php',
            type: 'post',
            data: {removedCountryName: country},
        });

        //remove the item from the list, notdatabase
        listItem.remove();
    });
});
/* 
 * Functions to manage productions filters
 */
var filterList = new Object(), filterButton;

$(document).ready(function(){
    
    var url = new URL(window.location.href);
    var category = url.searchParams.get("category");
    if (category) categoryButton = category;
    var location = url.searchParams.get("location");
    if (location) locationButton = location;
});

// category filters listener
$('.category-filter').each(function () {
    $(this).on('click', function () {
        if ($(this).hasClass('is-active')) {
            $(this).removeClass('is-active');
            categoryButton = '';
        } else {
            $('.category-filter').each(function () {
                $(this).removeClass('is-active');
            });
            $(this).addClass('is-active');
            categoryButton = $(this).attr('data-filter');
        }
        var $element = $(".nb-json");
        
        var request = $element.data("nb-json");
        delete $nb.json.data[$element.attr("id")];
        request.data = {
            'categoryButton': categoryButton,
            'locationButton': getActiveFilterValue('.location-filters')
        };
        $element.data("nb-json", request).html("");
        $nb.json.get($element);
    });
});
// location filters listener
$('.location-filter').each(function () {
    
    $(this).on('click', function () {
        console.log(getActiveFilterValue('.category-filters'))
        if ($(this).hasClass('is-active')) {
            $(this).removeClass('is-active');
            locationButton = '';
        } else {
            $('.location-filter').each(function () {
                $(this).removeClass('is-active');
            });
            $(this).addClass('is-active');
            locationButton = $(this).attr('data-filter');
        }
        var $element = $(".nb-json");
        
        var request = $element.data("nb-json");
        delete $nb.json.data[$element.attr("id")];
        request.data = {
            'locationButton': locationButton,
            'categoryButton': getActiveFilterValue('.category-filters')
        };
        $element.data("nb-json", request).html("");
        $nb.json.get($element);
    });
});

function getActiveFilterValue(filterClass) {
    var active = $(filterClass + ' .is-active');
    if (active.length) {
        return active.first().attr('data-filter');
    }
    return '';
}
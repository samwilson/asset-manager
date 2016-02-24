$(document).ready(function () {
    $(".focus-me").focus();
    $("input.datepicker").datepicker({dateFormat: 'yy-mm-dd'});

    /**
     * If a submit button has a data-new-action attribute, change the action of the form.
     */
    $("form input:submit").on("click", function () {
        var newAction = $(this).data("new-action");
        if (newAction) {
            $(this).closest("form").attr("action", newAction);
        }
    });

    /**
     * Category Trees.
     */
    var catTreeWidth = $("ol.categorytree:first").width();
    $("ol.categorytree ol").hide();
    $("ol.categorytree [class='description'] ol").show();
    $("ol.categorytree ol :checked").parents("ol").show();
    $("ol.categorytree ol :checked").parents("li").children(".toggle").removeClass("fa-plus-circle").addClass("fa-minus-circle");
    $("ol.categorytree .toggle").on("click", function () {
        if ($(this).hasClass("fa-plus-circle")) {
            $(this).removeClass("fa-plus-circle");
            $(this).addClass("fa-minus-circle");
            $(this).parents("li").first().children("ol").slideDown("fast");
        } else {
            $(this).removeClass("fa-minus-circle");
            $(this).addClass("fa-plus-circle");
            $(this).parents("li").first().children("ol").slideUp("fast");
        }
    });
    $("ol.categorytree:first").width(catTreeWidth);

    /**
     * Tag (and other tag-like) auto-completing.
     */
    $(":input.tagit").each(function () {
        var sourceUrl = ($(this).data("url")) ? $(this).data("url") : baseUrl + "/tags.json";
        $(this).tagit({
            "allowSpaces": true,
            "autocomplete": {"source": sourceUrl}
        });
    });

    /**
     * Markdown help note.
     */
    $(":input.markdown").after($("<small class='markdown'>You can use "
            + "<a href='https://help.github.com/articles/markdown-basics/' title='Read about Markdown (opens in new tab)' target='_blank'>"
            + "Markdown</a> syntax here.</small>"));

    /**
     * Maps
     */
    $(".map").each(function () {
        var attrib = 'Map data &copy; <a href="http://openstreetmap.org">OSM</a> contributors '
            + ' <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a> '
            + ' You can <a href="http://wiki.openstreetmap.org/wiki/Beginners\'_guide" target="_blank" title="Opens in new tab">edit this map</a>!';
        var centre = [0,0]; //[-32.05454466592707, 115.74644923210144]; // Fremantle!
        var map = L.map($(this).attr("id")).setView(centre, 16);
        L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: attrib}).addTo(map);

        // Single point?
        var latitude = $(this).data("latitude");
        var longitude = $(this).data("longitude");
        if (latitude && longitude) {
            var marker = L.marker(L.latLng(latitude, longitude));
            marker.addTo(map);
            map.panTo(marker.getLatLng());
        }

        // Geojson?
        var url = $(this).data("geojson");
        if (url) {
            $.ajax({
                url: url,
                dataType: 'json',
                success: function (response) {
                    var geojson = L.geoJson(response).addTo(map);
                    map.fitBounds(geojson.getBounds());
                }
            });
        }
    });

});

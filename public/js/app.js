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
        // OSM layer
        var osmLayer = L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: attrib}).addTo(map);
        // Bing layer. Imagery set can be: Aerial | AerialWithLabels | Birdseye | BirdseyeWithLabels | Road
        var bingLayer = new L.BingLayer(bingKey, {type: "AerialWithLabels"});
        map.addLayer(bingLayer);
        map.addControl(new L.Control.Layers({'OSM':osmLayer, "Bing":bingLayer}, {}));

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

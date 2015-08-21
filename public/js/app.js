$(document).ready(function () {
    $(".focus-me").focus();
    $("input.datepicker").datepicker({ dateFormat: 'yy-mm-dd' });

    /**
     * If a submit button has a data-new-action attribute, change the action of the form.
     */
    $("form input:submit").on("click", function() {
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
     * Tag auto-completing
     */
    $(":input.tagit").tagit({
        "allowSpaces": true,
        "autocomplete": {"source": baseUrl+"/tags.json"}
    });

});

$( document ).ready( function ()
{
    $(document).on("click", "button#addButton", function() {
        $(this).nextAll("form.addAncetors:first").slideToggle();
    });

});
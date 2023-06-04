$( document ).ready( function ()
{
    $(document).on("click", "button#addButton", function() {
        $(this).nextAll("form.addAncetors:first").slideToggle();
    });
    $(document).on("click", "button#editButton", function() {
        $(this).nextAll("form.editAncetors:first").slideToggle();
    });
    $(document).on("click", "button#deleteButton", function() {
        $(this).nextAll("form.deleteAncetors:first").slideToggle();
    });

});
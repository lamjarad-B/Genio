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
    $(document).on("click", "button#addPartnerButton", function() {
        $(this).nextAll("form.addPartner:first").slideToggle();
    });

    // La legends Vous 
    $("legend").filter(function() {
        return $(this).text().trim() === "Vous";
    }).addClass("vous");
    $("legend").filter(function() {
        return $(this).text().trim() === "Père";
    }).addClass("pere");
    $("legend").filter(function() {
        return $(this).text().trim() === "Mère";
    }).addClass("mere");


});
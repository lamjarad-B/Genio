$(document).ready(function() {
    $('.addAncetors').submit(function(event) {
        event.preventDefault(); 
        console.log(1); 
        let pere_nom = $( "input[name = 'pere_nom']" ).val();
        let pere_prenom = $( "input[name = 'pere_prenom']" ).val();
        let pere_date_naissance = $( "input[name = 'pere_date_naissance']" ).val();
        let pere_date_deces = $( "input[name = 'pere_date_deces']" ).val();
        let pere_lieu_naissance = $( "input[name = 'pere_lieu_naissance']" ).val();
        let mere_nom = $( "input[name = 'mere_nom']" ).val();
        let mere_prenom = $( "input[name = 'mere_prenom']" ).val();
        let mere_date_naissance = $( "input[name = 'mere_date_naissance']" ).val();
        let mere_date_deces = $( "input[name = 'mere_date_deces']" ).val();
        let mere_lieu_naissance = $( "input[name = 'mere_lieu_naissance']" ).val();
        fetch( "/addAncetors", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams( {
                personId: $(".addAncetors").attr( 'data-id' ),
                pere_nom: pere_nom,
                pere_prenom: pere_prenom,
                pere_date_naissance: pere_date_naissance,
                pere_date_deces: pere_date_deces,
                pere_lieu_naissance: pere_lieu_naissance,
                mere_nom: mere_nom,
                mere_prenom: mere_prenom,
                mere_date_naissance: mere_date_naissance,
                mere_date_deces: mere_date_deces,
                mere_lieu_naissance: mere_lieu_naissance
            } )
        } );
        $( this ).parent().find( "form" ).append(`
        <li>
           <strong>Père</strong>
       
           ` + pere_nom + ` ` + pere_prenom + `(M)
       
           <em>(` + pere_date_naissance + `)</em>
         </li>
       
         <li>
           <strong>Mère</strong> +
       
           ` + mere_nom + ` ` + mere_prenom + `(F)
       
           <em>(` + mere_date_naissance + `)</em>
         </li>`
       );

        return false;

      });
  });
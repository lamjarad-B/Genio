$( document ).ready( function ()
{
	function action( event )
	{
		event.preventDefault();

		const container = $( event.currentTarget ).parent();
		const form = container.children().first().next();

		const pere_nom = form.children().find( "input[name = 'pere_nom']" ).val();
		const pere_prenom = form.children().find( "input[name = 'pere_prenom']" ).val();
		const pere_date_naissance = form.children().find( "input[name = 'pere_date_naissance']" ).val();
		const pere_date_deces = form.children().find( "input[name = 'pere_date_deces']" ).val();
		const pere_lieu_naissance = form.children().find( "input[name = 'pere_lieu_naissance']" ).val();
		const mere_nom = form.children().find( "input[name = 'mere_nom']" ).val();
		const mere_prenom = form.children().find( "input[name = 'mere_prenom']" ).val();
		const mere_date_naissance = form.children().find( "input[name = 'mere_date_naissance']" ).val();
		const mere_date_deces = form.children().find( "input[name = 'mere_date_deces']" ).val();
		const mere_lieu_naissance = form.children().find( "input[name = 'mere_lieu_naissance']" ).val();

		fetch( "/addAncetors", {
			method: "POST",
			headers: {
				"Content-Type": "application/x-www-form-urlencoded"
			},
			body: new URLSearchParams( {
				personId: $( ".addAncetors" ).attr( "data-id" ),
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
		} ).then( function ( response )
		{
			return response.json();
		} ).then( function ( data )
		{
			container.children().first().remove(); // Bouton "Ajouter"
			container.children().first().remove(); // Formulaire
			container.append( `
				<li>
					<strong>Père</strong>

					` + pere_nom + ` ` + pere_prenom + ` (M)

					<em>(` + pere_date_naissance + `)</em>
				</li>

				<ul class="ancetors">
					<button id="addButton">Ajouter</button>
					<form class="addAncetors" data-id="` + data[ "idPere" ] + `">
						<div>
							<h2>Père</h2>
							<label for="pere_nom">Nom:</label>
							<input type="text" id="pere_nom" name="pere_nom" required><br>

							<label for="pere_prenom">Prénom:</label>
							<input type="text" id="pere_prenom" name="pere_prenom" required><br>

							<label for="pere_date_naissance">Date de naissance:</label>
							<input type="date" id="pere_date_naissance" name="pere_date_naissance" required><br>

							<label for="pere_date_deces">Date de décès:</label>
							<input type="date" id="pere_date_deces" name="pere_date_deces" required><br>

							<label for="pere_lieu_naissance">Lieu de naissance:</label>
							<input type="text" id="pere_lieu_naissance" name="pere_lieu_naissance" required><br>
						</div>

						<div>
							<h2>Mère</h2>
							<label for="mere_nom">Nom:</label>
							<input type="text" id="mere_nom" name="mere_nom" required><br>

							<label for="mere_prenom">Prénom:</label>
							<input type="text" id="mere_prenom" name="mere_prenom" required><br>

							<label for="mere_date_naissance">Date de naissance:</label>
							<input type="date" id="mere_date_naissance" name="mere_date_naissance" required><br>

							<label for="mere_date_deces">Date de décès:</label>
							<input type="date" id="mere_date_deces" name="mere_date_deces" required><br>

							<label for="mere_lieu_naissance">Lieu de naissance:</label>
							<input type="text" id="mere_lieu_naissance" name="mere_lieu_naissance" required><br>
						</div>

						<input type="submit" name="addAncetors" value="Envoyer">
					</form>
				</ul>

				<li>
					<strong>Mère</strong> +

					` + mere_nom + ` ` + mere_prenom + ` (F)

					<em>(` + mere_date_naissance + `)</em>
				</li>

				<ul class="ancetors">
					<button id="addButton">Ajouter</button>
					<form class="addAncetors" data-id="` + data[ "idMere" ] + `">
						<div>
							<h2>Père</h2>
							<label for="pere_nom">Nom:</label>
							<input type="text" id="pere_nom" name="pere_nom" required><br>

							<label for="pere_prenom">Prénom:</label>
							<input type="text" id="pere_prenom" name="pere_prenom" required><br>

							<label for="pere_date_naissance">Date de naissance:</label>
							<input type="date" id="pere_date_naissance" name="pere_date_naissance" required><br>

							<label for="pere_date_deces">Date de décès:</label>
							<input type="date" id="pere_date_deces" name="pere_date_deces" required><br>

							<label for="pere_lieu_naissance">Lieu de naissance:</label>
							<input type="text" id="pere_lieu_naissance" name="pere_lieu_naissance" required><br>
						</div>

						<div>
							<h2>Mère</h2>
							<label for="mere_nom">Nom:</label>
							<input type="text" id="mere_nom" name="mere_nom" required><br>

							<label for="mere_prenom">Prénom:</label>
							<input type="text" id="mere_prenom" name="mere_prenom" required><br>

							<label for="mere_date_naissance">Date de naissance:</label>
							<input type="date" id="mere_date_naissance" name="mere_date_naissance" required><br>

							<label for="mere_date_deces">Date de décès:</label>
							<input type="date" id="mere_date_deces" name="mere_date_deces" required><br>

							<label for="mere_lieu_naissance">Lieu de naissance:</label>
							<input type="text" id="mere_lieu_naissance" name="mere_lieu_naissance" required><br>
						</div>

						<input type="submit" name="addAncetors" value="Envoyer">
					</form>
				</ul>`
			);

			$( ".addAncetors" ).submit( action );
		} ).catch( function ( error )
		{
			console.log( error );
		} );

		return false;
	}

	$( ".addAncetors" ).submit( action );
} );
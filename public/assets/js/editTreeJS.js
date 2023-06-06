$( document ).ready( function ()
{
	function actionAjout( event )
	{
		event.preventDefault();

		const form = $( event.currentTarget );
		const pere_nom = form.find( "input[name='pere_nom']" ).val();
		const pere_prenom = form.find( "input[name='pere_prenom']" ).val();
		const pere_date_naissance = form.find( "input[name='pere_date_naissance']" ).val();
		const pere_date_deces = form.find( "input[name='pere_date_deces']" ).val();
		const pere_lieu_naissance = form.find( "input[name='pere_lieu_naissance']" ).val();
		const mere_nom = form.find( "input[name='mere_nom']" ).val();
		const mere_prenom = form.find( "input[name='mere_prenom']" ).val();
		const mere_date_naissance = form.find( "input[name='mere_date_naissance']" ).val();
		const mere_date_deces = form.find( "input[name='mere_date_deces']" ).val();
		const mere_lieu_naissance = form.find( "input[name='mere_lieu_naissance']" ).val();

		fetch( "/addAncetors", {
			method: "POST",
			headers: {
				"Content-Type": "application/x-www-form-urlencoded"
			},
			body: new URLSearchParams( {
				personId: form.attr( "data-id" ),
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
			// Transformation de la réponse en objet JSON.
			return response.json();
		} ).then( function ( data )
		{
			const parent = form.parent();
			const length = parent.children().length === 2;
			const container = length ? parent.parent() : parent;

			if ( length )
			{
				parent.remove();
			}
			else
			{
				container.find( "button#addButton" ).remove(); // Bouton "Ajouter"
				container.find( "button#editButton" ).remove(); // Bouton "Modifier"

				container.find( "form.deleteAncetors" ).remove(); // Formulaire de suppression
				container.find( "form.addAncetors" ).remove(); // Formulaire d'ajout
				container.find( "form.editAncetors" ).remove(); // Formulaire de modification
			}

			if ( pere_prenom )
			{
				container.prepend( `
					<li>
						<strong>Père</strong>

						` + pere_nom + ` ` + pere_prenom + ` (M)

						<em>(` + pere_date_naissance + `)</em>
					</li>

					<ul class="ancetors">
						<button id="addButton">Ajouter</button>
						<button id="editButton">Modifier</button>

						<form class="deleteAncetors" data-id="` + data[ "idPere" ] + `">
							<input type="submit" name="deleteAncetors" value="Supprimer">
						</form>

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

						<form class="editAncetors" data-id="` + data[ "idPere" ] + `">
							<div>
								<label for="nom">Nom:</label>
								<input type="text" id="nom" name="nom" value="` + pere_nom + `" ><br>

								<label for="prenom">Prénom:</label>
								<input type="text" id="prenom" name="prenom" value="` + pere_prenom + `" ><br>

								<label for="date_naissance">Date de naissance:</label>
								<input type="date" id="date_naissance" name="date_naissance" value="` + pere_date_naissance + `"><br>

								<label for="date_deces">Date de décès:</label>
								<input type="date" id="date_deces" name="date_deces" value="` + pere_date_deces + `"><br>

								<label for="lieu_naissance">Lieu de naissance:</label>
								<input type="text" id="lieu_naissance" name="lieu_naissance" value="` + pere_lieu_naissance + `"><br>
							</div>

							<input type="submit" name="editAncetors" value="Modifier">
						</form>
					</ul>
				`);
			}

			if ( mere_prenom )
			{
				container.append( `
					<li>
						<strong>Mère</strong> +

						` + mere_nom + ` ` + mere_prenom + ` (F)

						<em>(` + mere_date_naissance + `)</em>
					</li>

					<ul class="ancetors">
						<button id="addButton">Ajouter</button>
						<button id="editButton">Modifier</button>

						<form class="deleteAncetors" data-id="` + data[ "idMere" ] + `">
							<input type="submit" name="deleteAncetors" value="Supprimer">
						</form>

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

						<form class="editAncetors" data-id="` + data[ "idMere" ] + `">
							<div>
								<label for="nom">Nom:</label>
								<input type="text" id="nom" name="nom" value="` + mere_nom + `" ><br>

								<label for="prenom">Prénom:</label>
								<input type="text" id="prenom" name="prenom" value="` + mere_prenom + `" ><br>

								<label for="date_naissance">Date de naissance:</label>
								<input type="date" id="date_naissance" name="date_naissance" value="` + mere_date_naissance + `"><br>

								<label for="date_deces">Date de décès:</label>
								<input type="date" id="date_deces" name="date_deces" value="` + mere_date_deces + `"><br>

								<label for="lieu_naissance">Lieu de naissance:</label>
								<input type="text" id="lieu_naissance" name="lieu_naissance" value="` + mere_lieu_naissance + `"><br>
							</div>

							<input type="submit" name="editAncetors" value="Modifier">
						</form>
					</ul>`
				);
			}

			$( "form.addAncetors" ).submit( actionAjout );
		} ).catch( function ( error )
		{
			console.log( error );
		} );

		return false;
	}

	$( "form.addAncetors" ).submit( actionAjout );

	// Éditer une personne
	function actionEdit( event )
	{
		event.preventDefault();

		const form = $( event.currentTarget );
		const nom = form.find( "input[name='nom']" ).val();
		const prenom = form.find( "input[name='prenom']" ).val();
		const date_naissance = form.find( "input[name='date_naissance']" ).val();
		const date_deces = form.find( "input[name='date_deces']" ).val();
		const lieu_naissance = form.find( "input[name='lieu_naissance']" ).val();

		fetch( "/editAncetors", {
			method: "POST",
			headers: {
				"Content-Type": "application/x-www-form-urlencoded"
			},
			body: new URLSearchParams( {
				personId: form.attr( "data-id" ),
				nom: nom,
				prenom: prenom,
				date_naissance: date_naissance,
				date_deces: date_deces,
				lieu_naissance: lieu_naissance,
			} )
		} ).then( function ()
		{
			// Deuxième parent <ul> de classe "ancetors" ayant un élément <li> avec les informations de la personne.
			const element = form.parent().parent().find( "li[data-id=" + form.attr( "data-id" ) + "]" );
			const isPere = element.html().includes( "Père" );

			form.parent().parent().find( "li[data-id=" + form.attr( "data-id" ) + "]" ).html( `
				<strong>` + ( isPere ? "Père" : "Mère" ) + `</strong>

				` + nom + ` ` + prenom + ` (M)

				<em>(` + date_naissance + `)</em>
			` );

			// Disparition du formulaire avec animation jQuery.
			form.slideToggle();
		} ).catch( function ( error )
		{
			console.log( error );
		} );

		return false;
	}

	$( "form.editAncetors" ).submit( actionEdit );

	// Supprimer une personne
	function actionDelete( event )
	{
		event.preventDefault();

		const form = $( event.currentTarget );

		fetch( "/deleteAncetors", {
			method: "POST",
			headers: {
				"Content-Type": "application/x-www-form-urlencoded"
			},
			body: new URLSearchParams( {
				personId: form.attr( "data-id" ),
			} )
		} ).then( function ()
		{
			const container = form.parent().parent();

			const isPere = container.find( "li[data-id=" + form.attr( "data-id" ) + "]" ).html().includes( "Père" ); // Vérification si la personne est un père ou une mère.
			container.find( "li[data-id=" + form.attr( "data-id" ) + "]" ).remove(); // Suppression de l'élément <li> contenant les informations de la personne.

			form.prev( "button#addButton" ).remove(); // Suppression du bouton "Ajouter" précédant le formulaire.
			form.prev( "button#editButton" ).remove(); // Suppression du bouton "Modifier" précédant le formulaire.

			const parentId = container.parent().children().first().attr( "data-id" );
			form.next().attr( "data-id", parentId ); // Modification de l'attribut "data-id" du formulaire d'ajout.

			if ( isPere ) form.next().children().first().next().remove(); // Suppression du formulaire du père.
			else form.next().children().first().remove(); // Suppression du formulaire de la mère.

			form.next().next().remove(); // Suppression du formulaire de modification.

			form.remove(); // Suppression du formulaire de suppression.

			if ( container.children().length === 2 )
			{
				$( `
					<ul class="ancetors">
						<button id="addButton">Ajouter</button>

						<form class="addAncetors" data-id="` + parentId + `">
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
				`).insertAfter( container.parent().children().first() );

				container.remove();
			}
		} ).catch( function ( error )
		{
			console.log( error );
		} );

		return false;
	}

	$( "form.deleteAncetors" ).submit( actionDelete );
} );

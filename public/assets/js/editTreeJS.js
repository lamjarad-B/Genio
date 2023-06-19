$( document ).ready( function ()
{
	function actionAjout( event )
	{
		event.preventDefault();

		const form = $( event.currentTarget );
		let pere_nom = form.find( "input[name='pere_nom']" ).val();
		let pere_prenom = form.find( "input[name='pere_prenom']" ).val();
		let pere_date_naissance = form.find( "input[name='pere_date_naissance']" ).val();
		let pere_date_deces = form.find( "input[name='pere_date_deces']" ).val();
		let pere_lieu_naissance = form.find( "input[name='pere_lieu_naissance']" ).val();
		let mere_nom = form.find( "input[name='mere_nom']" ).val();
		let mere_prenom = form.find( "input[name='mere_prenom']" ).val();
		let mere_date_naissance = form.find( "input[name='mere_date_naissance']" ).val();
		let mere_date_deces = form.find( "input[name='mere_date_deces']" ).val();
		let mere_lieu_naissance = form.find( "input[name='mere_lieu_naissance']" ).val();

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
			if ( !data.idPere && !data.idMere )
			{
				const date = new Date( data.date_naissance[ "date" ] );
				const date_naissance = date.getDate() + "/" + ( date.getMonth() + 1 ) + "/" + date.getFullYear();
				const anwser = confirm( "Une personne avec ce nom et ce prénom existe déjà (" + data.sexe + " " + data.nom + " " + data.prenom + ", née le " + date_naissance + ").\nVoulez-vous utiliser ces informations ?" );

				if ( anwser )
				{
					if ( data.sexe === "M" )
					{
						pere_nom = data.nom;
						form.find( "input[name='pere_nom']" ).val( pere_nom );

						pere_prenom = data.prenom;
						form.find( "input[name='pere_prenom']" ).val( pere_prenom );

						pere_date_naissance = data.date_naissance;
						form.find( "input[name='pere_date_naissance']" ).val( new Date( pere_date_naissance[ "date" ] ).toISOString().split( 'T' )[ 0 ] );

						pere_date_deces = data.date_deces;
						form.find( "input[name='pere_date_deces']" ).val( new Date( pere_date_deces[ "date" ] ).toISOString().split( 'T' )[ 0 ] );

						pere_lieu_naissance = data.lieu_naissance;
						form.find( "input[name='pere_lieu_naissance']" ).val( pere_lieu_naissance );
					}
					else
					{
						mere_nom = data.nom;
						form.find( "input[name='mere_nom']" ).val( mere_nom );

						mere_prenom = data.prenom;
						form.find( "input[name='mere_prenom']" ).val( mere_prenom );

						mere_date_naissance = data.date_naissance;
						form.find( "input[name='mere_date_naissance']" ).val( new Date( mere_date_naissance[ "date" ] ).toISOString().split( 'T' )[ 0 ] );

						mere_date_deces = data.date_deces;
						form.find( "input[name='mere_date_deces']" ).val( new Date( mere_date_deces[ "date" ] ).toISOString().split( 'T' )[ 0 ] );

						mere_lieu_naissance = data.lieu_naissance;
						form.find( "input[name='mere_lieu_naissance']" ).val( mere_lieu_naissance );
					}
				}

				return;
			}

			const parent = form.parent();
			const parentId = parent.prev().attr( "data-id" );

			function html( content )
			{
				if ( data.idMere && !data.idPere )
				{
					parent.prev().prev().prev().html( content );
				}
				else
				{
					parent.prev().append( content );
				}
			}

			html( `
				<ul class="ancetors">
					<button id="editButton">Modifier</button>

					<form class="deleteAncetors" data-id="${ parentId }">
						<input type="submit" name="deleteAncetors" value="Supprimer">
					</form>

					<form class="editAncetors" data-id="${ parentId }">
						<div>
							<label for="nom">Nom:</label>
							<input type="text" id="nom" name="nom" value="" ><br>

							<label for="prenom">Prénom:</label>
							<input type="text" id="prenom" name="prenom" value="" ><br>

							<label for="date_naissance">Date de naissance:</label>
							<input type="date" id="date_naissance" name="date_naissance" value=""><br>

							<label for="date_deces">Date de décès:</label>
							<input type="date" id="date_deces" name="date_deces" value=""><br>

							<label for="lieu_naissance">Lieu de naissance:</label>
							<input type="text" id="lieu_naissance" name="lieu_naissance" value=""><br>
						</div>

						<input type="submit" name="editAncetors" value="Modifier">
					</form>
				</ul>
			` );

			const container1 = parent.prev().find( "ul" );
			const container2 = parent.prev();

			function ajout( content )
			{
				if ( pere_prenom && mere_prenom )
				{
					container1.append( content );
				}
				else
				{
					container2.after( content );
				}
			}

			if ( pere_prenom )
			{
				ajout( `
					<li data-id="${ data[ "idPere" ] }">
						<strong>Père</strong>

						${ pere_nom } ${ pere_prenom } (M)

						<em>(${ pere_date_naissance })</em>
					</li>

					<ul class="ancetors">
						<button id="editButton">Modifier</button>

						<form class="deleteAncetors" data-id="${ data[ "idPere" ] }">
							<input type="submit" name="deleteAncetors" value="Supprimer">
						</form>

						<form class="editAncetors" data-id="${ data[ "idPere" ] }">
							<div>
								<label for="nom">Nom:</label>
								<input type="text" id="nom" name="nom" value="${ pere_nom }" ><br>

								<label for="prenom">Prénom:</label>
								<input type="text" id="prenom" name="prenom" value="${ pere_prenom }" ><br>

								<label for="date_naissance">Date de naissance:</label>
								<input type="date" id="date_naissance" name="date_naissance" value="${ pere_date_naissance }"><br>

								<label for="date_deces">Date de décès:</label>
								<input type="date" id="date_deces" name="date_deces" value="${ pere_date_deces }"><br>

								<label for="lieu_naissance">Lieu de naissance:</label>
								<input type="text" id="lieu_naissance" name="lieu_naissance" value="${ pere_lieu_naissance }"><br>
							</div>

							<input type="submit" name="editAncetors" value="Modifier">
						</form>

						<button id="addButton">Ajouter</button>

						<form class="addAncetors" data-id="${ data[ "idPere" ] }">
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
				`);
			}

			if ( mere_prenom )
			{
				ajout( `
					<li data-id="${ data[ "idMere" ] }">
						<strong>Mère</strong>

						${ mere_nom } ${ mere_prenom } (F)

						<em>(${ mere_date_naissance })</em>
					</li>

					<ul class="ancetors">
						<button id="editButton">Modifier</button>

						<form class="deleteAncetors" data-id="${ data[ "idMere" ] }">
							<input type="submit" name="deleteAncetors" value="Supprimer">
						</form>

						<form class="editAncetors" data-id="${ data[ "idMere" ] }">
							<div>
								<label for="nom">Nom:</label>
								<input type="text" id="nom" name="nom" value="${ mere_nom }" ><br>

								<label for="prenom">Prénom:</label>
								<input type="text" id="prenom" name="prenom" value="${ mere_prenom }" ><br>

								<label for="date_naissance">Date de naissance:</label>
								<input type="date" id="date_naissance" name="date_naissance" value="${ mere_date_naissance }"><br>

								<label for="date_deces">Date de décès:</label>
								<input type="date" id="date_deces" name="date_deces" value="${ mere_date_deces }"><br>

								<label for="lieu_naissance">Lieu de naissance:</label>
								<input type="text" id="lieu_naissance" name="lieu_naissance" value="${ mere_lieu_naissance }"><br>
							</div>

							<input type="submit" name="editAncetors" value="Modifier">
						</form>

						<button id="addButton">Ajouter</button>

						<form class="addAncetors" data-id="${ data[ "idMere" ] }">
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
			}

			parent.remove();

			$( "form.addAncetors" ).off( "submit", actionAjout );
			$( "form.editAncetors" ).off( "submit", actionEdit );
			$( "form.deleteAncetors" ).off( "submit", actionDelete );

			$( "form.addAncetors" ).on( "submit", actionAjout );
			$( "form.editAncetors" ).on( "submit", actionEdit );
			$( "form.deleteAncetors" ).on( "submit", actionDelete );
		} ).catch( function ( error )
		{
			console.log( error );
		} );

		return false;
	}

	$( "form.addAncetors" ).on( "submit", actionAjout );

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
			const isMere = element.html().includes( "Mère" );

			form.parent().parent().find( "li[data-id=" + form.attr( "data-id" ) + "]" ).html( `
				<strong>` + ( isPere ? "Père" : ( isMere ? "Mère" : "" ) ) + `</strong>

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

	$( "form.editAncetors" ).on( "submit", actionEdit );

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
			const container = form.parent();
			const parent = container.parent();
			const parentId = parent.prev().attr( "data-id" );

			// Ajout du nouveau formulaire d'ajout de parents (père ou mère).
			const isPere = container.prev().html().includes( "Père" );

			if ( isPere )
			{
				// Ajout d'un nouveau père.
				container.prev().prev().after( `
					<ul class="ancetors">
						<button id="addButton">Ajouter</button>

						<form class="addAncetors" data-id="${ parentId }">
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

							<input type="submit" name="addAncetors" value="Envoyer">
						</form>
					</ul>
				`);
			}
			else
			{
				// Ajout d'une nouvelle mère.
				container.prev().prev().after( `
					<ul class="ancetors">
						<button id="addButton">Ajouter</button>

						<form class="addAncetors" data-id="${ parentId }">
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
				`);
			}

			container.prev().remove(); // Suppression du <li> avec les informations de la personne.

			// Suppression du <ul> contenant les formulaires et boutons.
			container.remove();

			if ( parent.find( "li" ).length === 0 )
			{
				// Suppression du conteneur parent si les deux parents ont été supprimés..
				parent.find( "ul" ).remove();

				// Ajout du nouveau bouton d'ajout de parents.
				parent.find( "form.editAncetors" ).after( `
					<button id="addButton">Ajouter</button>

					<form class="addAncetors" data-id="${ parentId }">
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
				`);
			}

			$( "form.addAncetors" ).off( "submit", actionAjout );
			$( "form.editAncetors" ).off( "submit", actionEdit );
			$( "form.deleteAncetors" ).off( "submit", actionDelete );

			$( "form.addAncetors" ).on( "submit", actionAjout );
			$( "form.editAncetors" ).on( "submit", actionEdit );
			$( "form.deleteAncetors" ).on( "submit", actionDelete );
		} ).catch( function ( error )
		{
			console.log( error );
		} );

		return false;
	}

	$( "form.deleteAncetors" ).on( "submit", actionDelete );
} );

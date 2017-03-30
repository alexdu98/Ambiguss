/**
 * Change la taille de la modale
 * @param size
 */
function setModalSize(size) {
	$('#modal-dialog').addClass(size);
}

/**
 * Change le titre de la modale
 * @param title
 */
function setModalTitle(title) {
	$('#modal-title').append(title);
}

/**
 * Change le contenu de la modale
 * @param body
 */
function setModalBody(body) {
	$('#modal-body').append(body);
}

/**
 * Remet à zéro la modale
 */
function cleanModal() {
	$('#modal-dialog').removeClass().addClass('modal-dialog');
	$('#modal-title').empty();
	$('#modal-body').empty();
}

// Quand la modale a été cachée, on exécute la fonction
$('#modal').on('hidden.bs.modal', cleanModal);

/**
 * Récupère les gloses d'un mot ambigu et les en <option> d'un <select>
 * @param prototype
 * @param motAmbigu
 */
function getGloses(select, motAmbigu) {
	var url = Routing.generate('ambiguss_glose_get_by_motambigu');
	$.post(url, {motAmbigu: motAmbigu}, function (data) {
		select.find('option').remove();
		select.append('<option value>Choississez une glose</option>');
		$.each(data, function (index) {
			select.append('<option value="' + data[index].id + '">' + data[index].valeur + '</option>')
		})
	}, "json");
}

/**
 * Ajoute un bouton pour ajouter une glose, exécute "fonction" au click
 * @param prototype
 * @param motAmbigu
 * @param fonction
 */
function addAddGloseButton(prototype, motAmbigu, fonction) {
	var addButton = $('<button type="button" style="margin-right:3px;" class="btn btn-primary" data-toggle="modal" ' +
		'data-target="#modal" id="addGloseModal">Ajouter une glose</button>');
	prototype.find('.addGloseButton').append(addButton);

	// Au click sur le bouton, on execute la fonction avec les paramètres
	addButton.click({motAmbigu: motAmbigu, prototype: prototype}, fonction);
}

/**
 * Créer la modale pour ajouter une glose
 * @param event
 */
function addGloseModal(event) {

	console.log(event.data);
	// Ajoute le mot ambigu dans le formulaire
	$('#modal-body').find('#glose_add_motAmbigu').val(event.data.motAmbigu);

	// Autocomplete le champ
	var input = $('#modal-body').find('#glose_add_valeur');
	input.autocomplete({
		minLength: 1, // Dès qu'il y a 1 caractère on autocomplete
		appendTo: '#modal', // Pour que l'affichage ce fasse (car dans une modale)
		source: function (request, response) {
			var url = Routing.generate('ambiguss_glose_get_autocomplete');
			$.getJSON(url + '?term=' + request.term, function (data) {
				input.parent().append('<div id="resnul" hidden>Aucune glose déjà existante à vous proposer</div>');
				if (data.length === 0) {
					input.parent().find('#resnul').show();
				} else {
					input.parent().find('#resnul').hide();
				}
				// On récupère une liste d'objet, on veut l'attribut valeur de l'objet
				response($.map(data, function (item) {
					return item.valeur;
				}));
			});
		}
	});

	// Envoi le formulaire via ajax
	$('#modal-body').find('form[name="glose_add"]').ajaxForm({
		beforeSubmit: function (arr, form, opt) {
			// On affiche l'image laoding en attendant la réponse
			$(form).after('<img src="' + urlImageLoading + '" id="loading">');
		},
		// Quand la réponse Ajax sera reçu, on appelle ce callback
		'success': function (data, status, xhr, form) {
			// On supprime l'image loading
			$(form).next().remove();
			if (data.status) {
				// On ajoute la nouvelle glose à la liste des gloses du select
				event.data.prototype.find('select.gloses').append('<option value="' + data.glose.id +
					'">' + data.glose.valeur + '</option>');
				// On referme la modale
				$('#modal').modal('hide');
			} else {
				$(form).after('<div class="alert alert-danser">Erreur</div>')
			}
		}
	});
}

/**
 * Affiche un message dans la modale pour dire qu'il faut se connecter
 */
function messageNeedConnectionModal() {
	setModalBody('<div class="alert alert-danger">Il faut être connecté pour utiliser cette fonctionnalité</div>');
}

$(document).ready(function () {

	$.ajaxSetup({cache: true});

	$('#login-facebook').click(function () {
		$.getScript('//connect.facebook.net/fr_FR/sdk.js', function () {
			FB.init({
				appId: '1793610560900722',
				version: 'v2.8'
			});

			FB.getLoginStatus(function (response) {
				if (response.status === 'connected') {
					FB.login(function (response) {
						if (response.authResponse) {
							FB.api('/me', {fields: 'id, email, gender'}, function (response) {
								saveFacebookUser(response);
							});
						}
					}, {scope: 'public_profile, email'});
				}
				else {
					FB.login(function (response) {
						if (response.authResponse) {
							FB.api('/me', {fields: 'id, email, gender'}, function (response) {
								saveFacebookUser(response);
							});
						}
					}, {scope: 'public_profile, email'});
				}
			});
		});
	});

	function saveFacebookUser(facebookUser) {
		var url = Routing.generate('user_inscription_provider', {provider: 'facebook'});
		$.post(url,
			{data: JSON.stringify(facebookUser)},
			function (data) {
				return true;
			}
		);
	}

});
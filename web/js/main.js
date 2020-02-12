/**
 * Valide du JSON
 * @param json
 */
function isValidJSON(json) {
	if (typeof json !== 'object') {
        try {
            JSON.parse(json);
        }
        catch (err) {
            console.log(err);
            return false;
        }
    }
    return true;
}

/**
 * Change la taille de la modale
 * @param size modal-lg | modal-sm
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
 * @param select
 * @param motAmbigu
 * @param callback
 */
function getGloses(select, motAmbigu, callback) {
	select.html('<option selected disabled value>Choisissez une glose (...)</option>');
	// Empêche la sélection pendant le chargement
	select.attr('disabled', 'disabled').addClass('loading');
	var url = Routing.generate('api_gloses_mot_ambigu_show');
	$.post(url, {motAmbigu: motAmbigu}, function (data) {
		var indication = "";
		if (data.links.length > 1)
			indication = data.links.length + ' existantes';
		else
			indication = data.links.length + ' existante';
		select.html('<option selected disabled value>Choisissez une glose (' + indication + ')</option>');
		$.each(data.links, function (index) {
			select.append('<option value="' + data.links[index].id + '">' + data.links[index].valeur + '</option>');
		});
		select.removeAttr('disabled').removeClass('loading');

		// Appel la fonction de callback si elle existe
		typeof callback === 'function' && callback();
	}, "json");
}

/**
 * Affiche un message dans la modale pour dire qu'il faut se connecter
 */
function messageNeedConnectionModal() {
	setModalBody('<div class="alert alert-danger">Il faut être connecté pour utiliser cette fonctionnalité</div>');
}

function updatePoints(points) {
	$('#points').html(parseInt($('#points').html()) + parseInt(points));
}

function updateCredits(credits) {
	$('#credits').html(parseInt($('#credits').html()) + parseInt(credits));
}

$(document).ready(function () {

	$.ajaxSetup({cache: true});

	// Active les tooltips bootstrap
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    // Si l'utilisateur n'a pas explicitement accepté les cookies, on affiche la modale
    if (!$.cookie('cookieInfo')) {
		$('#cookieModal').modal({backdrop: 'static', keyboard: false});
	}

	// Au survol d'une réponse on surligne le MA
	$('body').on('mouseenter', '.reponseGroupe', function () {
		var ordre = $(this).attr('id').replace(/rep/g, '');
		$(this).css('background', 'rgba(160, 210, 51, 0.6)');
		$('amb#ma' + ordre).css('background', 'rgba(160, 210, 51, 0.6)');
		$('amb#' + ordre).css('background', 'rgba(160, 210, 51, 0.6)');
	});
	$('body').on('mouseleave', '.reponseGroupe', function () {
		var ordre = $(this).attr('id').replace(/rep/g, '');
		$(this).removeAttr('style');
		$('amb#ma' + ordre).removeAttr('style');
		$('amb#' + ordre).removeAttr('style');
	});

	// Au survol d'un MA on surligne la réponse
	$('body').on('mouseenter', 'amb', function () {
		var ordre = $(this).attr('id').replace(/ma/g, '');
		$(this).css('background', 'rgba(160, 210, 51, 0.6)');
		$('#rep' + ordre).css('background', 'rgba(160, 210, 51, 0.6)');
	});
	$('body').on('mouseleave', 'amb', function () {
		var ordre = $(this).attr('id').replace(/ma/g, '');
		$(this).removeAttr('style');
		$('#rep' + ordre).removeAttr('style');
	});

});

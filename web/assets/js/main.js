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
 * @param select
 * @param motAmbigu
 */
function getGloses(select, motAmbigu) {
    select.html('<option selected disabled value>Choisissez une glose</option>');
    // Empêche la sélection pendant le chargement
    select.attr('disabled', 'disabled').addClass('loading');
    var url = Routing.generate('ambiguss_glose_get_by_motambigu');
    $.post(url, {motAmbigu: motAmbigu}, function (data) {
        $.each(data, function (index) {
            select.append('<option value="' + data[index].id + '">' + data[index].valeur + '</option>');
        });
        select.removeAttr('disabled').removeClass('loading');
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

    // Ajoute le mot ambigu dans le formulaire
    $('#modal-body').find('#glose_add_motAmbigu').val(event.data.motAmbigu);

    // Autocomplete le champ
    var input = $('#modal-body').find('#glose_add_valeur');
    input.autocomplete({
        minLength: 2, // Dès qu'il y a 2 caractères on autocomplete
        appendTo: '#modal', // Pour que l'affichage ce fasse (car dans une modale)
        source: function (request, response) {
            var url = Routing.generate('ambiguss_glose_get_autocomplete');
            $.getJSON(url + '?term=' + request.term, function (data) {
                input.parent().append('<div id="resnul" class="text-danger" hidden>Aucune glose déjà existante à vous proposer</div>');
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
        success: function (data, status, xhr, form) {
            // On supprime l'image loading
            $(form).next().remove();
            if (data.status) {
                // On ajoute la nouvelle glose à la liste des gloses du select
                select = event.data.prototype.find('select.gloses');
                motAmbigu = $(select).next().val();
                $("select.gloses").each(function () {
                    // Ajout la glose dans tous les select avec la même valeur de mot ambigu
                    if ($(this).next().val() === motAmbigu) {
                        // Si la glose n'était pas déjà présente dans le select, on l'ajoute
                        if (!$(this).find('option[value="' + data.glose.id + '"]').length) {
                            $(this).append('<option value="' + data.glose.id + '">' + data.glose.valeur + '</option>');
                        }
                        // On sélectionne la nouvelle glose si c'est le bouton de ce select qui a été click
                        if (select.attr('id') === $(this).attr('id')) {
                            $(this).find('option[value="' + data.glose.id + '"]').selected();
                        }
                    }
                });
                // On referme la modale
                $(form).after(
                    '<div class="alert alert-success alert-dismissible fade in" role="alert">'
                    + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>'
                    + 'Glose "' + data.glose.valeur + '" ajoutée à "' + event.data.motAmbigu + '"</div>'
                );
                $(form).clearForm();
            } else {
                $(form).after('<div class="alert alert-danger">Erreur</div>');
            }
        },
        error: function () {
            loading = $("#loading");
            next = loading.prev().nextAll();
            loading.before('<span class="text-danger">Erreur</span>');
            next.remove();
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
        //alert('ok')
        $.getScript('//connect.facebook.net/fr_FR/sdk.js', function () {
            FB.init({
                appId: '1810880632571404',
                version: 'v2.8',
                status: true
            });

            FB.getLoginStatus(function (response) {
                if (response.status === 'connected') {
                    FB.login(function (response) {
                        if (response.authResponse) {
                            FB.api('/me?fields=id,name,email,gender', function (response) {
                                saveFacebookUser(response);
                            });
                        }
                    }, {scope: 'public_profile, email'});
                    //	redirectLoginFacebook = Routing.generate('hwi_oauth_service_redirect', {service: 'facebook'});
                    //	document.location = redirectLoginFacebook;
                }
                else {
                    FB.login(function (response) {
                        if (response.authResponse) {
                            FB.api('/me?fields=id,name,email,gender', function (response) {
                                saveFacebookUser(response);
                                // redirection a la page de création de mot de passe
                            });
                        }
                        else {
                            alert('Annulation.');
                        }
                    }, {scope: 'public_profile, email'});
                }
            });
        });
    });

    function saveFacebookUser(facebookUser) {
        console.log(facebookUser);
        var url = Routing.generate('user_inscription_provider', {provider: 'facebook'});
        $.post(url,
            {data: JSON.stringify(facebookUser)},
            function (data) {
                return true;
            }
        );
    }

});
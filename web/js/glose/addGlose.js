/**
 * Créer la modale pour ajouter une glose
 */
function addGloseModal(params) {

    // Ajoute le mot ambigu dans le formulaire
    $('#modal-body').find('#glose_add_motAmbigu').val(params.motAmbigu);

    // Autocomplete le champ
    var input = $('#modal-body').find('#glose_add_valeur');
    input.autocomplete({
        minLength: 2, // Dès qu'il y a 2 caractères on autocomplete
        appendTo: '#modal', // Pour que l'affichage ce fasse (car dans une modale)
        source: function (request, response) {
            var url = Routing.generate('api_autocomplete_glose');
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
            if (data.succes) {
                // On ajoute la nouvelle glose à la liste des gloses du select
                select = params.select;
                $("select.gloses").each(function () {
                    // Ajout la glose dans tous les select avec la même valeur de mot ambigu
                    if ($(this).closest('.reponseGroupe').find('.motAmbigu').html().trim() === params.motAmbigu) {
                        // Si la glose n'était pas déjà présente dans le select, on l'ajoute
                        if (!$(this).find('option[value="' + data.glose.id + '"]').length) {
                            $(this).append('<option value="' + data.glose.id + '">' + data.glose.valeur + '</option>');
                        }
                        // On sélectionne la nouvelle glose si c'est le bouton de ce select qui a été click
                        if (select.attr('id') === $(this).attr('id')) {
                            $(this).find('option[value="' + data.glose.id + '"]').selected();
                        }
                        $(this).find("option:first-child").html('Choisissez une glose (' + ($(this).find("option").length - 1) + ' existantes)');
                    }
                });
                $(form).after(
                    '<div class="alert alert-success alert-dismissible fade in" role="alert">'
                    + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>'
                    + 'Glose "' + data.glose.valeur + '" ajoutée à "' + params.motAmbigu + '"</div>'
                );
                if (!data.liaisonExiste) {
                    costNewGlose = $('.costNewGlose');
                    updateCredits(-costNewGlose.html());
                    opt = params.select.find('option').length - 1;
                    if (opt >= 2)
                        costNewGlose.html(opt * parseInt(costNewGlose.data('cost')));
                }
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

$(document).ready(function () {

    $('.addGloseButton').on('click', function(){
        var reponseGroupe = $(this).closest('.reponseGroupe');
        var motAmbigu = reponseGroupe.find('.motAmbigu').html().trim();
        var selectGlose = reponseGroupe.find('select.gloses');

        setModalTitle('Ajouter une glose au mot ambigu "' + motAmbigu + '"');

        nbGloses = $(this).closest('.reponseGroupe').find('select.gloses option').length - 1;
        if (nbGloses >= 2)
            cost = costCreateGloseByGlosesOfMotAmbigu * nbGloses;
        else
            cost = 0;

        setModalBody('<div class="text-danger">Cela vous coûtera <b data-cost="' + costCreateGloseByGlosesOfMotAmbigu + '" class="costNewGlose">' + cost + '</b> crédit(s).</div><br>');

        if ($('#credits').html() > cost) {
            setModalBody(gloseAddForm);
            addGloseModal({motAmbigu: motAmbigu, select: selectGlose});
        }
    });

});

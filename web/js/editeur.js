$(document).ready(function () {
    // Regex pour avoir les contenus des balises <amb>
    // Exemple : L'<amb>avocat</amb> mange des <amb>avocats</amb>.
    // Donne : $1 = avocat, puis $1 = avocats
    var regAmb = new RegExp('<amb>(.*?)</amb>', 'ig');

    // Regex pour avoir les contenus des balises <amb> et leurs id
    // Exemple : L'<amb id="1">avocat</amb> mange des <amb id="2">avocats</amb>.
    // Donne : $1 = 1 et $3 = avocat, puis $1 = 2 et $3 = avocats
    var regAmbId = new RegExp('<amb id="([0-9]+)"( title=".*")?>(.*?)</amb>', 'ig');

    // Le formulaire d'édition
    var editorForm = $("#phrase-editor-form");

    // Div contenant le prototype du formulaire MAP
    var $container = $('div#proto_motsAmbigusPhrase');

    // La div modifiable
    var phraseEditor = $("div.phrase-editor");

    // Div des erreurs
    var errorForm = $('#form-errors');

    // Le mode actif
    var modeEditor = $('#nav-editor li.active').data('mode');

    // Pour numéroter le mot ambigu
    var indexMotAmbigu = 0;

    // Tableau des mots ambigus de la phrase
    var motsAmbigus = [];

    function getPhraseTexte() {
        return phraseEditor
            .html()
            .replace(/&nbsp;/ig, ' ')
            .replace(/&lt;/g, '<')
            .replace(/&gt;/g, '>')
            .replace(/<br>/g, '')
            .replace(/ style=""/g, '')
            .replace(/ title="Ce mot est ambigu \(id : [0-9]+\)"/ig, '');
    }

    // Mise à jour du mode d'éditeur
    $('#nav-editor li').on('click', function(){
        $('#nav-editor li.active').removeClass('active');
        $(this).addClass('active');

        var oldModeEditor = modeEditor;
        modeEditor = $(this).data('mode');

        if (oldModeEditor != modeEditor) {
            if (modeEditor === 'wysiwyg') {
                // Affiche la phrase en mode HTML
                phraseEditor.html(phraseEditor.text());
                $.each(phraseEditor.find('amb'), function (i, val) {
                    $(this).attr('title', 'Ce mot est ambigu (id : ' + $(this).attr('id') + ')');
                });
            }
            else if (modeEditor === 'source') {
                // Affiche la phrase en mode texte
                phraseEditor.text(getPhraseTexte());
            }
        }
    });

    // Ajout d'un mot ambigu
    $("#addAmb").on('click', function () {
        var sel = window.getSelection();
        var selText = sel.toString();

        // S'il y a bien un mot séléctionné
        if (selText.trim() !== '') {
            var regAlpha = /[a-zA-ZáàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ]/;

            var parentBase = sel.anchorNode.parentNode;
            var parentFocus = sel.focusNode.parentNode;

            var numPrevChar = Math.min(sel.focusOffset, sel.anchorOffset) - 1;
            var numFirstChar = Math.min(sel.focusOffset, sel.anchorOffset);
            var numLastChar = Math.max(sel.focusOffset, sel.anchorOffset) - 1;
            var numNextChar = Math.max(sel.focusOffset, sel.anchorOffset);
            var prevChar = sel.focusNode.textContent.charAt(numPrevChar);
            var firstChar = sel.focusNode.textContent.charAt(numFirstChar);
            var lastChar = sel.focusNode.textContent.charAt(numLastChar);
            var nextChar = sel.focusNode.textContent.charAt(numNextChar);

            errorForm.empty();
            var success = true;
            if (phraseEditor.html() != parentBase.innerHTML || phraseEditor.html() != parentFocus.innerHTML) {
                errorForm.append('Le mot sélectionné est déjà ambigu<br>');
                success = false;
            }
            if (sel.anchorNode != sel.focusNode) {
                errorForm.append('Le mot sélectionné contient déjà un mot ambigu<br>');
                success = false;
            }
            if (prevChar.match(regAlpha)) {
                errorForm.append('Le premier caractère sélectionné ne doit pas être précédé d\'un caractère alphabétique<br>');
                success = false;
            }
            if (!firstChar.match(regAlpha)) {
                errorForm.append('Le premier caractère sélectionné doit être alphabétique<br>');
                success = false;
            }
            if (!lastChar.match(regAlpha)) {
                errorForm.append('Le dernier caractère sélectionné doit être alphabétique<br>');
                success = false;
            }
            if (nextChar.match(regAlpha)) {
                errorForm.append('Le dernier caractère sélectionné ne doit pas être suivi d\'un caractère alphabétique<br>');
                success = false;
            }

            // S'il y a une erreur on affiche la div des erreurs
            if (!success) {
                errorForm.show();
                return false;
            }
            // Sinon on cache la div
            else {
                errorForm.hide();
            }

            // Transformation du texte sélectionné en mot ambigu selon le mode utilisé
            var range = document.getSelection().getRangeAt(0);
            var clone = $(range.cloneContents());

            range.deleteContents();
            range.insertNode($('<amb>').append(clone).get(0));
            document.getSelection().setPosition(null);
            phraseEditor.trigger('input');
        }
    });

    // A chaque modification de la phrase
    phraseEditor.on('input', function (){
        var phrase = getPhraseTexte();
        // Compte le nombre d'occurence de balise <amb>
        var replaced = phrase.search(regAmb) >= 0;
        // Si au moins 1
        if(replaced) {
            // On ajout dans la balise <amb> l'id du mot ambigu
            var temp = phrase.replace(regAmb, function ($0, motAmbigu) {
                indexMotAmbigu++;
                var indexLocal = indexMotAmbigu;
                motsAmbigus[indexMotAmbigu] = motAmbigu;

                // On ajoute le nom unique et l'id
                var template = $container.attr('data-prototype')
                    .replace(/__name__label__/g, '')
                    .replace(/__name__/g, indexMotAmbigu)
                    .replace(/__id__/g, indexMotAmbigu)
                    .replace(/__MA__/g, motAmbigu);

                var $prototype = $(template);
                // Trouve la balise qui à la class amb
                var amb = $prototype.find('.amb');
                // Ajoute la valeur du mot ambigu en supprimant les espaces avant et après le mot, et ajoute l'id
                amb.val(motAmbigu);
                $prototype.attr('id', 'rep' + indexMotAmbigu);

                var $deleteLink = $('<a href="#" class="sup-amb btn btn-danger">Supprimer le mot ambigu</a>');
                $prototype.find('.gloseAction').append($deleteLink);

                getGloses($prototype.find('select.gloses'), motAmbigu, function () {
                    // Pour la page d'édition, sélection des gloses automatique
                    if (typeof reponsesOri != 'undefined') {
                        reponsesOri.forEach((item, index) => {
                            if (item.map_ordre == indexLocal) {
                                $prototype.find('option[value=' + item.glose_id + ']').prop('selected', true)
                            }
                        });
                    }
                });
                $container.append($prototype);

                if (modeEditor == 'wysiwyg') {
                    return '<amb id="' + indexMotAmbigu + '" title="Ce mot est ambigu (id : ' + indexMotAmbigu + ')">' + motAmbigu + '</amb>';
                }
                else {
                    return '<amb id="' + indexMotAmbigu + '">' + motAmbigu + '</amb>';
                }
            });

            if (modeEditor == 'wysiwyg') {
                phraseEditor.html(temp);
            }
            else {
                phraseEditor.text(temp);
            }
        }

        var phrase = getPhraseTexte();
        phrase.replace(regAmbId, function ($0, $1, $2, $3) {
            var motAmbiguForm = $('#phrase_motsAmbigusPhrase_' + $1 + '_valeur');
            // Mot ambigu modifié dans la phrase -> passage en rouge du MA dans le formulaire
            if (motsAmbigus[$1] != $3) {
                motAmbiguForm.val($3).css('color', 'red');
            }
            // Si le MA modifié reprend sa valeur initiale -> efface la couleur rouge du MA dans le formulaire
            else if (motsAmbigus[$1] != motAmbiguForm.val($3)) {
                motAmbiguForm.val(motsAmbigus[$1]).css('color', '');
            }
        });

    });
    phraseEditor.trigger('input'); // Pour mettre en forme en cas de phrase au chargement (édition ou création échouée)

    // Mise à jour des gloses des mots ambigus
    phraseEditor.on('focusout', function () {
        var phrase = getPhraseTexte();
        phrase.replace(regAmbId, function ($0, $1, $2, $3) {
            if (motsAmbigus[$1] != $3) {
                $('#phrase_motsAmbigusPhrase_' + $1 + '_valeur').trigger('focusout');
                motsAmbigus[$1] = $3;
            }
        });
    });

    // Désactive la touche entrée dans l'éditeur de phrase
    phraseEditor.on('keypress', function(e) {
        var keyCode = e.which;

        if (keyCode == 13) {
            return false;
        }
    });

    // Coller sans le formatage
    phraseEditor.on('paste', function(e) {
        e.preventDefault();

        var text = (e.originalEvent || e).clipboardData.getData('text/plain');

        if (modeEditor == 'wysiwyg') {
            $(this).html(text);
        }
        else {
            $(this).text(text);
        }

        phraseEditor.trigger('input'); // Pour mettre en forme après avoir collé
    });

    // Modification d'un mot ambigu
    editorForm.on('input', '.amb', function () {
        // On récupère l'id qui est dans l'attribut id (id="rep1"), en supprimant le rep
        var id = $(this).closest('.reponseGroupe').attr('id').replace(/rep/, '');
        // Mot ambigu modifié -> passage en rouge du MA
        if (motsAmbigus[id] != $(this).val()) {
            $(this).css('color', 'red');
        }
        // Si le MA modifié reprend sa valeur initiale -> efface la couleur rouge du MA
        else {
            $(this).css('color', '');
        }

        var phrase = getPhraseTexte();
        // Regex pour trouver la bonne balise <amb id="">, et en récupérer le contenu
        var reg3 = new RegExp('<amb id="' + id + '">(.*?)' + '</amb>', 'g');
        // Met à jour le mot ambigu dans la phrase

        if (modeEditor == 'wysiwyg') {
            phraseEditor.html(phrase.replace(reg3, '<amb id="' + id + '" title="Ce mot est ambigu (id : ' + id + ')">' + $(this).val() + '</amb>'));
        }
        else {
            phraseEditor.text(phrase.replace(reg3, '<amb id="' + id + '">' + $(this).val() + '</amb>'));
        }
    });

    // Mise à jour des gloses d'un mot ambigu
    editorForm.on('focusout', '.amb', function (){
        // On récupère l'id qui est dans l'attribut id (id="rep1"), en supprimant le rep
        var id = $(this).closest('.reponseGroupe').attr('id').replace(/rep/, '');
        if (motsAmbigus[id] != $(this).val()) {
            $(this).css('color', '');

            motsAmbigus[id] = $(this).val();

            var phrase = getPhraseTexte();

            // Regex pour trouver la bonne balise <amb id="">, et en récupérer le contenu
            var reg3 = new RegExp('<amb id="' + id + '">(.*?)' + '</amb>', 'g');

            // Met à jour le mot ambigu dans la phrase
            if (modeEditor == 'wysiwyg') {
                phraseEditor.html(phrase.replace(reg3, '<amb id="' + id + '" title="Ce mot est ambigu (id : ' + id + ')">' + $(this).val() + '</amb>'));
            }
            else {
                phraseEditor.text(phrase.replace(reg3, '<amb id="' + id + '">' + $(this).val() + '</amb>'));
            }

            getGloses($(this).closest('.colAmb').next().find('select.gloses'), $(this).val());
        }
    });

    // Suppression d'un mot ambigu
    editorForm.on('click', '.sup-amb', function(e) {
        $(this).closest('.reponseGroupe').trigger('mouseleave');

        var phrase = getPhraseTexte();
        // On récupère l'id qui est dans l'attribut id (id="rep1"), en supprimant le rep
        var id = $(this).closest('.reponseGroupe').attr('id').replace(/rep/, '');
        delete motsAmbigus[id];

        // Regex pour trouver la bonne balise <amb id="">, et en récupérer le contenu
        var reg3 = new RegExp('<amb id="' + id + '">(.*?)</amb>', 'g');

        // Modifie le textarea pour supprimé la balise <amb id=""></amb> et remettre le contenu
        if (modeEditor == 'wysiwyg') {
            phraseEditor.html(phrase.replace(reg3, '$1'));
        }
        else {
            phraseEditor.text(phrase.replace(reg3, '$1'));
        }

        $(this).closest('.reponseGroupe').remove();

        e.preventDefault(); // Évite qu'un # soit ajouté dans l'URL
    });

    // A la soumission du formulaire
    $('.btn-phrase-editor').on('click', function(){
        $('#phrase_contenu').val(getPhraseTexte());
    });

});

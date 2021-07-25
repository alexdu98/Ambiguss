<?php

namespace App\Util;

abstract class InvalidPhraseMessage
{
    public static $EMPTY_PHRASE = 'La phrase ne doit pas être vide';
    public static $ONLY_AMB_TAG = 'Il ne faut que des balises <amb> et </amb>';
    public static $NESTED_AMB_TAG = 'Il ne faut pas de balise <amb> imbriquée';
    public static $NESTED_CAMB_TAG = 'Il ne faut pas de balise </amb> imbriquée';
    public static $WRONG_NB_AMB_TAG = 'Il n\'y a pas le même nombre de balise <amb> et </amb>';
    public static $NB_AMB_MIN = 'Il faut au moins 1 mot ambigu';
    public static $NB_AMB_MAX = 'Il ne faut pas dépasser 10 mots ambigus par phrase';
    public static $NB_CHAR_MAX = 'La phrase est trop longue. 255 caractères maximum hors balise <amb>.';
    public static $WRONG_SELECT_EXT = 'Un mot était mal sélectionné (le caractère précédent une balise <amb> ou suivant une balise </amb> ne doit pas être alphabétique)';
    public static $WRONG_SELECT_INT = 'Un mot était mal sélectionné (le caractère suivant une balise <amb> ou précédent une balise </amb> ne doit pas être un espace)';
    public static $SAME_ID_AMB = 'Les mots ambigus doivent avoir des identifiants différents';
}

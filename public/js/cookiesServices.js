function servicesGamePage() {
    var routeGame = $('meta[property="og:url"]').attr("content");
    var contenuPur = $('meta[property="og:description"]').attr("content");

    if (cookieIsActivated('facebook')) {
        (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s);
            js.id = id;
            js.src = "//connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v2.9&appId=1793610560900722";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

        var htmlFacebook = ' <div class="fb-share-button" data-href="' + routeGame + '"' +
        'data-layout="button" data-size="small" data-mobile-iframe="true">' +
        '<a class="fb-xfbml-parse-ignore" target="_blank"' +
        'href="https://www.facebook.com/sharer/sharer.php?hashtag=Ambiguss&u=' + routeGame +
        'src=sdkpreparse">Partager</a>' +
        '</div> ';
        $('.spanServices').append(htmlFacebook);
    }

    if (cookieIsActivated('twitter')) {
        window.twttr = (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0],
                t = window.twttr || {};
            if (d.getElementById(id)) return t;
            js = d.createElement(s);
            js.id = id;
            js.src = "https://platform.twitter.com/widgets.js";
            fjs.parentNode.insertBefore(js, fjs);

            t._e = [];
            t.ready = function (f) {
                t._e.push(f);
            };

            return t;
        }(document, "script", "twitter-wjs"));

        var htmlTwitter = ' <a class="twitter-share-button"' +
            'href="https://twitter.com/intent/tweet?text='+ contenuPur +'. Ambigu non %3F&hashtags=Ambiguss">Partager</a> ';
        $('.spanServices').append(htmlTwitter);
    }
}

function servicesConnexionPage() {
    if (cookieIsActivated('ambiguss')) {
        $('#remember_me').prop('disabled', false);
    }
}

function updateServicesCookiesForOnePage() {
    if (window.location.pathname.match(Routing.generate('game_show'))) {
        servicesGamePage();
    }
    else if (window.location.pathname.match(Routing.generate('fos_user_security_login'))) {
        servicesConnexionPage();
    }
}

function cookieIsActivated(service) {
    return (Cookies.get('cookieInfo') & bitWiseCookiesService[service]) > 0;
}

var deletableCookiesAmbiguss = ['helpAddPhrase', 'helpGame', 'helpResultat', 'helpEditModoPhrase', 'visite'];

$(document).ready(function () {
    $('a.configCookies').on('mouseenter', function() {
        var service = $(this).attr('id').replace(/login-/, '');
        if (!cookieIsActivated(service)) {
            $(this).attr('title', 'Des cookies de ' + service.charAt(0).toUpperCase() + service.slice(1) + ' seront utilisés le temps de la connexion.');
        }
    });

    $('a.configCookies').on('mouseout', function() {
        $(this).attr('title', '');
    });

    // Si clic sur le bouton "J'accepte" du bandeau d'information des cookies
    $('#cookieAccept').on('click', function(){
        // On calcul le bitwise selon les cookies acceptés
        var bitWiseCookies = 0;
        $.each($('.configCookies:checked'), function() {
            bitWiseCookies += bitWiseCookiesService[$(this).val()];
        });

        var ttl_cookie = bitWiseCookies == (2 ** Object.keys(bitWiseCookiesService).length) - 1 ? ttl_cookie_info : ttl_cookie_info_not_fully_accepted;

        if ($('.configCookies[value="ambiguss"]').prop('checked')) {
            // On créé un cookie pour ne plus réafficher la modal
            Cookies.set('cookieInfo', bitWiseCookies, {expires: ttl_cookie, secure: true, sameSite: 'strict'});
        }
        else {
            // On créé un cookie de session pour ne plus réafficher la modal le temps de la session
            Cookies.set('cookieInfo', bitWiseCookies, {secure: true, sameSite: 'strict'});

            // On supprime les  cookies d'ambiguss s'ils existent
            $.each(Object.keys(Cookies.get()), function() {
                var realname = this.valueOf();
                $.each(deletableCookiesAmbiguss, function() {
                    var regex = new RegExp(this, 'g');
                    if (realname.match(regex)) {
                        Cookies.remove(realname)
                    }
                });
            });
        }


        // On supprime la modal
        $('#cookieModal').modal('hide');

        // On met à jour les services acceptés
        updateServicesCookiesForOnePage();
    });
    updateServicesCookiesForOnePage();
});

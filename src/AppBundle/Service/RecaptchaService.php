<?php

namespace AppBundle\Service;

class RecaptchaService{

    private $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

	/**
	 * Vérifie si un captcha est valide
     *
	 * @param string $captcha
	 */
	public function check(string $captcha){

	    if(empty($captcha))
	        return array(
	            'success' => false,
                'error-codes' => array('Captcha non renseigné.')
            );

        // URL Google Recaptcha
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $url .= "?secret=" . $this->secret . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR'];

        // Désactive la vérification ssl
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );

        // Effectue la requête de vérification du captcha
        $res = file_get_contents($url, false, stream_context_create($arrContextOptions));

        // Renvoie le résultat sous forme de tableau
        return json_decode($res, true);
	}
}

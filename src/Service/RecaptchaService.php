<?php

namespace App\Service;

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
	 * @param string $ip
	 */
	public function check(string $captcha, $ip){

	    if(empty($captcha)) {
            return array(
                'success' => false,
                'error-codes' => array('Captcha non renseigné.')
            );
        }

        $data = array(
            'secret' => $this->secret,
            'response' => $captcha,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        );

	    // Préparation de la requête
        $verify = curl_init();
        curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($verify, CURLOPT_POST, true);
        curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);

        $res = curl_exec($verify);

        if (!$res) {
            $res = array(
                'success' => false,
                'error-codes' => array('Erreur lors de la vérification du captcha.')
            );
        }

        // Renvoie le résultat sous forme de tableau
        return $res;
	}
}

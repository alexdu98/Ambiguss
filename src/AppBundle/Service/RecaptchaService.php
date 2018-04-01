<?php

namespace AppBundle\Service;

class RecaptchaService{

    private $secret;
	public $succes;
	public $erreurs = array();

    public function __construct($secret)
    {
        $this->secret = $secret;
    }

	/**
	 * Vérifie si un captcha est valide
	 * @param $captcha
	 * @return RecaptchaService
	 */
	public function check($captcha){
		if(!empty($captcha)){
            $url = "https://www.google.com/recaptcha/api/siteverify";
            $url .= "?secret=" . $this->secret . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR'];

            $arrContextOptions = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );


			$res = file_get_contents($url, false, stream_context_create($arrContextOptions));
			$res = json_decode($res);
			$this->succes = $res->success;
			if(!$this->succes)
				$this->erreurs[] = "Captcha invalide.";
			if(!empty($res->error_codes))
				$this->erreurs = array_merge($this->erreurs, $res->error_codes);
		}
		else{
			$this->succes = false;
			$this->erreurs[] = "Captcha vide.";
		}

		return $this;
	}
}

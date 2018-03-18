<?php

namespace AppBundle\Service;

class Recaptcha{

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
	 * @return Recaptcha
	 */
	public function check($captcha){
		if(!empty($captcha)){
			$res = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $this->secret . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
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

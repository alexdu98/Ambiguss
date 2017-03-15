<?php

namespace AppBundle\Services;

class Recaptcha{

	const CLE_PRIVEE_V2 = "6LdIRhgTAAAAAMKShVrZyBTvvJP08hHu2la0P_ks";
	const CLE_PRIVEE_V3 = "6LcXBhkUAAAAAOXVtSYLei5DUOfYh1ZEfsIhz0yv";
	public $succes;
	public $erreurs = array();

	/**
	 * Vérifie si un captcha est valide
	 * @param $captcha
	 * @return Recaptcha
	 */
	public function check($captcha){
		if(!empty($captcha)){
			$res = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . self::CLE_PRIVEE_V3 . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
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
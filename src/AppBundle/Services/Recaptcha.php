<?php

namespace AppBundle\Services;

class Recaptcha{

	const CLE_PRIVEE_V3 = "secret";
	public $succes;
	public $erreurs = array();

	/**
	 * Vérifie si un captcha est valide
	 * @param $captcha
	 * @return Recaptcha
	 */
	public function check($captcha){
		if(!empty($captcha)){
			$data = array(
			    'secret' => self::CLE_PRIVEE_V3,
			    'response' => $captcha,
			    'remoteip' => $_SERVER['REMOTE_ADDR']
			);

		        $verify = curl_init();
		        curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
		        curl_setopt($verify, CURLOPT_POST, true);
		        curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
		        curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
		        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);

			$res = json_decode(curl_exec($verify));
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

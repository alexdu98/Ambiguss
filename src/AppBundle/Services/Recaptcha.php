<?php

namespace AppBundle\Services;

class Recaptcha{

	const CLE_PRIVEE = "6LdIRhgTAAAAAMKShVrZyBTvvJP08hHu2la0P_ks";

	/**
	 * Vérifie si un captcha est valide
	 * @param $captcha
	 * @return bool
	 */
	public function check($captcha){
		if(!empty($captcha)){
			$res = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . self::CLE_PRIVEE . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
			$res = json_decode($res);
			return $res->success == true;
		}
		return false;
	}
}
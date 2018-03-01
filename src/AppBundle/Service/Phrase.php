<?php

namespace AppBundle\Service;

class Phrase extends \Twig_Extension
{

	public function getStaticHTML($phrase)
	{
		$phraseO = new \AppBundle\Entity\Phrase();
		$phraseO->setContenu($phrase);

		return $phraseO->getContenuHTML();
	}

	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('getPhraseHTML', array(
				$this,
				'getStaticHTML',
			)),
		);
	}

	public function getName()
	{
		return 'getPhraseHTML';
	}
}
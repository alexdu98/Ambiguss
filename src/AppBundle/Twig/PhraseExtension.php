<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Phrase;

class PhraseExtension extends \Twig_Extension
{

    /**
     * Retourne le contenu de la phrase en mode HTML
     *
     * @param $phrase
     * @return null|string|string[]
     */
	public function getStaticHTML($phrase)
	{
		$phraseO = new Phrase();
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

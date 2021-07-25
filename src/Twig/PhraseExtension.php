<?php

namespace App\Twig;

use App\Entity\Phrase;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PhraseExtension extends AbstractExtension
{

    /**
     * Retourne le contenu de la phrase en mode HTML
     *
     * @param $phrase
     * @return null|string|string[]
     */
	public function getStaticHTML($something)
	{
		if ($something instanceof Phrase)
			return $something->getContenuHTML();

		$phrase = new Phrase();
		$phrase->setContenu($something);
		return $phrase->getContenuHTML();
	}

	public function getFilters()
	{
		return array(
			new TwigFilter('getPhraseHTML', array(
				$this,
				'getStaticHTML',
			)),
		);
	}
}

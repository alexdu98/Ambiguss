<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;

class Export
{

	private $em;
	private $rep;

	public function __construct(EntityManagerInterface $em, $downloadDir)
	{
		$this->em = $em;
		$this->rep = $downloadDir;
	}

	public function phrases()
	{
		$repoR = $this->em->getRepository('AppBundle:Reponse');
		$repoP = $this->em->getRepository('AppBundle:Phrase');

		$phrases = $repoP->findAll();

		$file = fopen($this->rep . 'export_phrases.json', 'wb+');

		$Marray = array();
		$finalarray = array();

		foreach($phrases as $phrase)
		{
			foreach($phrase->getMotsAmbigusPhrase() as $MA)
			{
				$glose = $repoR->findGlosesForExport($MA);

				$MAarray = array(
					'motAmbigu' => $MA->getMotAmbigu()->getValeur(),
					'ordre' => $MA->getOrdre(),
					'nbRep' => $MA->getReponses()->count(),
					'gloses' => $glose,
				);

				array_push($Marray, $MAarray);
			}
			$Parray = array(
				'phrase' => $phrase->getContenu(),
				'reponse' => $Marray,
			);

			$Marray = array();
			array_push($finalarray, $Parray);
		}

		fwrite($file, (json_encode(array(
			'infos' => 'Données collectées depuis le jeu Ambiguss. Site web réalisé en 2017 dans le cadre d\'un TER de première année de master informatique à l\'université de Montpellier. Groupe : Isna, Melissa, Nicolas, Alexandre. Tuteur : Mathieu Lafourcade.',
			'date' => date('d/m/Y H\hi'),
			'data' => $finalarray,
		), JSON_UNESCAPED_UNICODE)));

		return count($phrases);
	}

	public function motsAmbigus()
	{
		$repoAM = $this->em->getRepository('AppBundle:MotAmbigu');

		$MAs = $repoAM->findAll();

		$file = fopen($this->rep . 'export_motsAmbigus.json', 'wb+');

		$MAGarray = array();
		$finalarray = array();

		foreach($MAs as $MA)
		{
			$mag = $MA->getGloses();

			foreach($mag as $glose)
			{
				array_push($MAGarray, $glose->getValeur());
			}

			$Marray = array(
				'motAmbigu' => $MA->getValeur(),
				'gloses' => $MAGarray,
			);

			array_push($finalarray, $Marray);
			$MAGarray = array();
		}

		fwrite($file, (json_encode(array(
			'infos' => 'Données collectées depuis le jeu Ambiguss. Site web réalisé en 2017 dans le cadre d\'un TER de première année de master informatique à l\'université de Montpellier. Groupe : Isna, Melissa, Nicolas, Alexandre. Tuteur : Mathieu Lafourcade.',
			'date' => date('d/m/Y H\hi'),
			'data' => $finalarray,
		), JSON_UNESCAPED_UNICODE)));

		return count($MAs);
	}
}
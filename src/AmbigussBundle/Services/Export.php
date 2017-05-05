<?php

namespace AmbigussBundle\Services;

use Doctrine\ORM\EntityManager;

class Export
{

	private $em;
	private $rep;

	public function __construct(EntityManager $em, $rootdir)
	{
		$this->em = $em;
		$this->rep = $rootdir . '/../web/downloads/';
	}

	public function phrases()
	{
		$repoR = $this->em->getRepository('AmbigussBundle:Reponse');
		$repoP = $this->em->getRepository('AmbigussBundle:Phrase');

		$phrases = $repoP->findAll();

		$file = fopen($this->rep . 'export_phrases.json', 'wb');

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
			'date' => date('d/m/Y H\hi'),
			'data' => $finalarray,
		), JSON_UNESCAPED_UNICODE)));
	}

	public function motsAmbigus()
	{
		$repoAM = $this->em->getRepository('AmbigussBundle:MotAmbigu');

		$MAs = $repoAM->findAll();

		$file = fopen($this->rep . 'export_motsAmbigus.json', 'wb');

		$MAGarray = array();
		$finalarray = array();

		foreach($MAs as $MA)
		{
			$mag = $MA->getGloses();

			foreach($mag as $glose)
			{
				$Garray = array('valeur' => $glose->getValeur());
				array_push($MAGarray, $Garray);
			}

			$Marray = array(
				'motAmbigu' => $MA->getValeur(),
				'gloses' => $MAGarray,
			);

			array_push($finalarray, $Marray);
			$MAGarray = array();
		}

		fwrite($file, (json_encode(array(
			'date' => date('d/m/Y H\hi'),
			'data' => $finalarray,
		), JSON_UNESCAPED_UNICODE)));
	}
}
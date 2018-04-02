<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;

class ExportService
{
    private $em;
    private $downloadDir;
    private $infos;

    public function __construct(EntityManagerInterface $em, $downloadDir, $infos)
    {
        $this->em = $em;
        $this->downloadDir = $downloadDir;
        $this->infos = $infos;
    }

    public function addGlosePhrase(&$gloses, &$nbRepMA, $ligne)
    {
        array_push($gloses, array(
            'valeur' => $ligne['vg'],
            'nbRep' => $ligne['nbRep']
        ));
        $nbRepMA += $ligne['nbRep'];
    }

    public function addMotAmbiguPhrase(&$motsAmbigus, &$gloses, &$nbRepMA, $ligne)
    {
        array_push($motsAmbigus, array(
            'motAmbigu' => $ligne['vma'],
            'ordre' => $ligne['ordre'],
            'nbRep' => $nbRepMA,
            'gloses' => $gloses
        ));
    }

    public function addPhrase(&$phrases, &$motsAmbigus, &$gloses, &$nbRepMA, $ligne)
    {
        $this->addMotAmbiguPhrase($motsAmbigus, $gloses, $nbRepMA, $ligne);
        array_push($phrases, array(
            'phrase' => $ligne['phrase'],
            'motsAmbigus' => $motsAmbigus
        ));
        $motsAmbigus = array();
        $gloses = array();
        $nbRepMA = 0;
    }

    public function phrases()
    {
        $repoP = $this->em->getRepository('AppBundle:Phrase');
        $res = $repoP->export();

        $gloses = array();
        $nbRepMA = 0;
        $motsAmbigus = array();
        $phrases = array();

        $prevIdp = !empty($res[0]) ? $res[0]['idp'] : null;
        $prevOrdre = !empty($res[0]) ? $res[0]['ordre'] : null;

        $i = 0;
        $len = count($res);
        foreach ($res as $r) {
            // Même mot ambigu dans la même phrase mais glose différente
            // => On ajoute la glose
            if ($r['idp'] == $prevIdp && $r['ordre'] == $prevOrdre) {
                $this->addGlosePhrase($gloses, $nbRepMA, $r);
            }
            // Même phrase mais mot ambigu différent
            // => On ajoute le mot ambigu précédent
            elseif ($r['idp'] == $prevIdp) {
                $this->addMotAmbiguPhrase($motsAmbigus, $gloses, $nbRepMA, $res[$i - 1]);

                $gloses = array();
                $nbRepMA = 0;

                $this->addGlosePhrase($gloses, $nbRepMA, $r);
            }
            // Pas la même phrase
            // => On ajoute la phrase précédente et la nouvelle glose
            elseif ($r['idp'] != $prevIdp) {
                $this->addPhrase($phrases, $motsAmbigus, $gloses, $nbRepMA, $res[$i - 1]);
                $this->addGlosePhrase($gloses, $nbRepMA, $r);
            }

            // Si c'est le dernier résultat
            // => On ajoute la phrase
            if ($i == $len - 1) {
                $this->addPhrase($phrases, $motsAmbigus, $gloses, $nbRepMA, $r);
            }

            $prevIdp = $r['idp'];
            $prevOrdre = $r['ordre'];

            $i++;
        }

        $file = fopen($this->downloadDir . 'export_phrases.json', 'wb+');
        fwrite($file, (json_encode(array(
            'infos' => $this->infos,
            'date' => date('d/m/Y H:i'),
            'donnees' => $phrases,
        ), JSON_UNESCAPED_UNICODE)));

        return count($phrases);
    }

    public function addMotAmbigu(&$motsAmbigus, &$gloses, $ligne)
    {
        array_push($motsAmbigus, array(
            'motAmbigu' => $ligne['motAmbigu'],
            'gloses' => $gloses
        ));
        $gloses = array();
    }

    public function motsAmbigus()
    {
        $repoMA = $this->em->getRepository('AppBundle:MotAmbigu');
        $res = $repoMA->export();

        $gloses = array();
        $motsAmbigus = array();

        $prevIdMA = !empty($res[0]) ? $res[0]['idma'] : null;

        $i = 0;
        $len = count($res);
        foreach ($res as $r) {
            // Même mot ambigu mais glose différente
            // => On ajoute la glose
            if ($r['idma'] == $prevIdMA) {
                $gloses[] = $r['glose'];
            }
            // Pas le même mot ambigu
            // => On ajoute le mot ambigu précédent et la nouvelle glose
            else {
                $this->addMotAmbigu($motsAmbigus, $gloses, $res[$i - 1]);
                $gloses[] = $r['glose'];
            }

            // Si c'est le dernier résultat
            // => On ajoute le mot ambigu
            if ($i == $len - 1) {
                $this->addMotAmbigu($motsAmbigus, $gloses, $r);
            }

            $prevIdMA = $r['idma'];

            $i++;
        }

        $file = fopen($this->downloadDir . 'export_motsAmbigus.json', 'wb+');
        fwrite($file, (json_encode(array(
            'infos' => $this->infos,
            'date' => date('d/m/Y H\hi'),
            'donnees' => $motsAmbigus,
        ), JSON_UNESCAPED_UNICODE)));

        return count($motsAmbigus);
    }
}

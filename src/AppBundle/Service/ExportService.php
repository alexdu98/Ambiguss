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

    /**
     * Ajoute la glose de la ligne dans le tableau des gloses et
     * additionne le nombre de réponses pout le mot ambigu
     *
     * @param $gloses
     * @param $nbRepMA
     * @param $ligne
     */
    private function addGlosePhrase(&$gloses, &$nbRepMA, $ligne)
    {
        array_push($gloses, array(
            'valeur' => $ligne['vg'],
            'nbRep' => $ligne['nbRep']
        ));
        $nbRepMA += $ligne['nbRep'];
    }

    /**
     * Ajoute le mot ambigu de la ligne dans le tableau des mots ambigus avec son ordre,
     * son nombre de réponse et ses gloses
     *
     * @param $motsAmbigus
     * @param $gloses
     * @param $nbRepMA
     * @param $ligne
     */
    private function addMotAmbiguPhrase(&$motsAmbigus, &$gloses, &$nbRepMA, $ligne)
    {
        array_push($motsAmbigus, array(
            'motAmbigu' => $ligne['vma'],
            'ordre' => $ligne['ordre'],
            'nbRep' => $nbRepMA,
            'gloses' => $gloses
        ));
    }

    /**
     * Ajoute la phrase de la ligne dans le tableau des phrases avec ses mots ambigus,
     * remet à zéro les tableaux des mots ambigus et des gloses et remet à zéro le nombre de réponse du mot ambigu
     *
     * @param $phrases
     * @param $motsAmbigus
     * @param $gloses
     * @param $nbRepMA
     * @param $ligne
     */
    private function addPhrase(&$phrases, &$motsAmbigus, &$gloses, &$nbRepMA, $ligne)
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


    /**
     * Retourne les phrases et leurs réponses
     */
    private function phrases()
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

        return $phrases;
    }

    /**
     * Ajoute le mot ambigu de la ligne dans le tableau des mots ambigu avec ses gloses
     *
     * @param $motsAmbigus
     * @param $gloses
     * @param $ligne
     */
    private function addMotAmbigu(&$motsAmbigus, &$gloses, $ligne)
    {
        array_push($motsAmbigus, array(
            'motAmbigu' => $ligne['motAmbigu'],
            'gloses' => $gloses
        ));
        $gloses = array();
    }

    /**
     * Retourne les mots ambigus et leurs gloses
     */
    private function motsAmbigus()
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

        return $motsAmbigus;
    }

    /**
     * Enregistre les données $data dans un fichier $fileName au format JSON
     */
    private function save($fileName, $data)
    {
        $file = fopen($this->downloadDir . $fileName, 'wb+');

        return fwrite($file, (json_encode(array(
            'infos' => $this->infos,
            'date' => date('d/m/Y H:i'),
            'donnees' => $data,
        ), JSON_UNESCAPED_UNICODE)));
    }

    public function getDownloadDir()
    {
        return $this->downloadDir;
    }

    public function exportPhrases()
    {
        $phrases = $this->phrases();

        $this->save('export_phrases.json', $phrases);

        return count($phrases);
    }

    public function exportMotsAmbigus()
    {
        $motsAmbigus = $this->motsAmbigus();

        $this->save('export_motsAmbigus.json', $motsAmbigus);

        return count($motsAmbigus);
    }
}

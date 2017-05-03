<?php
/**
 * Created by PhpStorm.
 * User: Alexandre
 * Date: 02/04/2017
 * Time: 20:38
 */

namespace AmbigussBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ExportController extends Controller
{

    public function mainAction ()
    {

	    return $this->render('AmbigussBundle:Export:main.html.twig');
	}

    public function downloadAction()
    {
        $repoR = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Reponse');
        $repoP = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Phrase');
        $repoPMA = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:MotAmbiguPhrase');

        $phrases = $repoP->getAllLimit();

        $file = fopen('export_data.json', 'w+');

        $Marray = array();
        $MAGarray= array();
        $finalarray=array();

        foreach ($phrases as $phrase) {
            $mamb = $repoPMA->findBy(array('phrase' => $phrase));
            foreach ($mamb as $MA) {

                $glose = $repoR->findGlosesForExport($phrase->getContenu(), $MA->getMotAmbigu()->getValeur());
                $nb_rep = $repoR->findReponsesForExport($phrase->getContenu(), $MA->getMotAmbigu()->getValeur());

                foreach ($glose as $G) {
                    $phrase_rep = $repoR->findforExport($phrase->getContenu(), $MA->getMotAmbigu()->getValeur(), $G['valeurGlose']);

                    foreach ($phrase_rep as $item) {
                        $GArray = array(
                            'Valeur' => $item['valeurGlose'],
                            'Nombre de reponses' => $item['NombreDeReponsePourCetteGloses'],
                        );
                        array_push($MAGarray,$GArray);
                    }
                    $MAarray=array('Mot Ambigu' => $MA->getMotAmbigu()->getValeur(),
                        'Ordre'=> $MA->getOrdre(),
                        'Réponses total' => $nb_rep['nbt'],
                        'Gloses' => $MAGarray
                    );

                }
                array_push($Marray,$MAarray);
                $MAGarray=array();
            }
            $Parray=array('Phrase'=>$phrase->getContenu(),
                            'Réponses' =>$Marray);

            $Marray=array();
            array_push($finalarray,$Parray);
        }
        fwrite($file,(json_encode($finalarray, JSON_UNESCAPED_UNICODE )));

    }



    public function MAdownloadAction()
    {
        $repoR = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Reponse');
        $repoAM = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:MotAmbigu');

        $MAs = $repoAM->findAll();

        $file = fopen('export_ma_data.json', 'w+');

        $Marray = array();
        $MAGarray= array();
        $finalarray=array();
        foreach ($MAs as $MA) {
            $mag = $MA->getGloses();

            foreach ($mag as $glose) {
                $Garray=array('Valeur'=>$glose->getValeur());
                array_push($MAGarray,$Garray);
            }


            $Marray=array('Mot Ambigu'=>$MA->getValeur(),
                'Gloses'=>$MAGarray);

            array_push($finalarray,$Marray);
            $MAGarray=array();


        }
        fwrite($file,(json_encode($finalarray, JSON_UNESCAPED_UNICODE )));

        return $this->render('AmbigussBundle:Export:main.html.twig');
    }


}
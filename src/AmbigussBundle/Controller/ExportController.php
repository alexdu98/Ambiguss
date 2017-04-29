<?php
/**
 * Created by PhpStorm.
 * User: Alexandre
 * Date: 02/04/2017
 * Time: 20:38
 */

namespace AmbigussBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
class ExportController extends Controller
{

	public function mainAction ()
	{

	    return $this->render('AmbigussBundle:Export:main.html.twig');
	}

    public function downloadAction() // sera appelée depuis la page d'administration ou automatiquement
    {
        $repoR = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Reponse');
        $repoP = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Phrase');
        $repoPMA = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:MotAmbiguPhrase');

        $phrases= $repoP->findAll();
        $file = fopen('export_data.json','w+');
        fwrite($file,"{".PHP_EOL);
        fwrite($file,"\"Phrases\" : [".PHP_EOL);
        foreach ($phrases as $phrase){

            $mamb=$repoPMA->findBy(array('phrase' => $phrase));

            fwrite($file,"{".PHP_EOL."\"Phrase\" : \"".$phrase->getContenu()."\",".PHP_EOL);
            fwrite($file,"\"Reponses\" :[".PHP_EOL);
            foreach ($mamb as $MA){
                $glose=$repoR->findGlosesForExport($phrase->getContenu(), $MA->getMotAmbigu()->getValeur());
                $nb_rep=$repoR->findReponsesForExport($phrase->getContenu(), $MA->getMotAmbigu()->getValeur());


                fwrite($file,"{".PHP_EOL.
                    "\"Mot Ambigu\" : \"".$MA->getMotAmbigu()->getValeur()."\",".PHP_EOL.
                    "\"Ordre\" : \" ".$MA->getOrdre()."\",".PHP_EOL.
                    "\"Nb_rep_total\" : \" ". $nb_rep['nbt']."\",".PHP_EOL.
                    "\"Gloses\" :[ ".PHP_EOL);


                foreach ($glose as $G) {
                    $phrase_rep = $repoR->findforExport($phrase->getContenu(), $MA->getMotAmbigu()->getValeur(), $G['valeurGlose']);

                    $json = $this->encodeReponseDataToJson($phrase_rep,$nb_rep['nbt']);

                    /* sanity check */
                    if (json_decode($json) != null)
                    {

                        $json=str_replace(",",",".PHP_EOL,$json);
                        $json=str_replace("}",PHP_EOL."}",$json);
                        $json=str_replace("{","{".PHP_EOL,$json);
                        fwrite($file, $json.",");
                    }
                }

                fwrite($file,"]".PHP_EOL);
                fwrite($file,"},".PHP_EOL);
            }
            fwrite($file,"]".PHP_EOL);
            fwrite($file,"}".PHP_EOL);




        }
        fwrite($file,"]".PHP_EOL);
        fwrite($file,"}".PHP_EOL);

        fclose($file);
        //Supprimer les "},]"
        $str=file_get_contents('export_data.json');
        $str=str_replace("},]", "}]",$str);
        $str=str_replace("},".PHP_EOL."]", "}]",$str);
        $str=str_replace("}".PHP_EOL."{".PHP_EOL."\"Phrase\"","},".PHP_EOL."{".PHP_EOL."\"Phrase\"",$str);
        file_put_contents('export_data.json', $str);

        return $this->render('AmbigussBundle:Export:main.html.twig');
    }

    private function encodeReponseDataToJson($phrase_rep,$nb_rep)
    {
        foreach ($phrase_rep as $item) {
            $phraseData = array(

                        'Valeur' =>$item['valeurGlose'],
                        'Nombre de reponses' =>$item['NombreDeReponsePourCetteGloses'],

                );




        }$jsonEncoder = new JsonEncoder();


        return $jsonEncoder->encode($phraseData, $format = 'json');
    }

}
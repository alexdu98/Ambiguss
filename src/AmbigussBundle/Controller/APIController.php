<?php
/**
 * Created by PhpStorm.
 * User: Alexandre
 * Date: 21/03/2017
 * Time: 09:23
 */

namespace AmbigussBundle\Controller;

use AmbigussBundle\Entity\MotAmbigu;
use AmbigussBundle\Form\GloseAddType;
use JudgmentBundle\Entity\Jugement;
use JudgmentBundle\JudgmentBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;

class APIController extends Controller
{

    public function autocompleteGloseAction(Request $request)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Glose');
        $gloses = $repository->findByValeurAutoComplete($request->get('term'));

        return $this->json($gloses);
    }

    public function addGloseAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $glose = new \AmbigussBundle\Entity\Glose();
            $form = $this->get('form.factory')->create(GloseAddType::class, $glose, array('action' =>
                $this->generateUrl
                ('ambiguss_glose_add')));
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                $data->setAuteur($this->getUser());

                $repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Glose');
                $glose = $repository->findOneOrCreate($data);

                $repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:MotAmbigu');
                $motAmbigu = new MotAmbigu();
                $motAmbigu->setValeur($request->request->get('glose_add')['motAmbigu']);
                $motAmbigu = $repository->findOneOrCreate($motAmbigu);

                $motAmbigu->addGlose($glose);

                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($motAmbigu);
                    $em->flush();

                    $res = array(
                        'id' => $glose->getId(),
                        'valeur' => $glose->getValeur()
                    );
                    return $this->json(array('status' => 'succes', 'glose' => $res));
                } // Si la liaison motAmbigu-glose est déjà faite
                catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                    $res = array(
                        'id' => $glose->getId(),
                        'valeur' => $glose->getValeur()
                    );
                    return $this->json(array('status' => 'succes', 'glose' => $res));
                } catch (\Exception $e) {
                    return $this->json(array('status' => 'erreur', 'message' => $e));
                }
            }
            return $this->render('AmbigussBundle:Glose:add.html.twig', array(
                'form' => $form->createView(),
            ));
        }

        throw $this->createNotFoundException();
    }

    public function addJugementAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $jug= new \JudgmentBundle\Entity\Jugement();
            $form = $this->get('form.factory')->create(\JudgmentBundle\Form\JugementAddType::class, $jug, array('action' =>
                $this->generateUrl
                ('ambiguss_jugement_add')));
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                $data->setAuteur($this->getUser());
                $jugement = new \JudgmentBundle\Entity\Jugement();

                $jugement->setAuteur($data->getAuteur());
                $jugement->setDescription($form['description']->getData());

                $categrepository = $this->getDoctrine()->getManager()->getRepository('JudgmentBundle:CategorieJugement');
                $s=$form->get('categorieJugement')->getData();
                $categ = $categrepository->findOneBycategorieJugement($s->getCategorieJugement());
                $jugement->setCategorieJugement($categ);


                $objetrepository = $this->getDoctrine()->getManager()->getRepository('JudgmentBundle:TypeObjet');
                $objet= $objetrepository->findOneById(3); // phrase
                $jugement->setTypeObjet($objet);
                $jugement->setIdObjet(3); //

                //persist
                /*try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($jugement);
                    $em->flush();}
                    catch (Exception $e){}*/

            }
            return $this->json(array('status' => 'succes'));
        }
        // not found exception
    }


	public function getGlosesByMotAmbiguAction(Request $request){
		$repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Glose');
		$gloses = $repository->findGlosesValueByMotAmbiguValue($request->request->get('motAmbigu'));
		return $this->json($gloses);
	}

}
<?php

namespace GenjioBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use GenjioBundle\Entity\Upload;
use Vich\UploaderBundle\Form\Type\VichFileType;

class UploadController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/upload", name="upload")
     * @Security("has_role('ROLE_API')")
     * @Method("POST")
     */
    public function uploadAction(Request $request)
    {
        $upload = new Upload();
        $upload->setUser($this->getUser());
        $form = $this->getUploadForm($upload);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($upload);
            $em->flush();
            return new JsonResponse([
                'filename' => $upload->getUploadName(),
                'url' => 'https://i.genj.io/i/' . $upload->getUploadName(),
            ]);
        }

        //return new JsonResponse([]);
        return $this->render('default/test.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function getUploadForm($upload)
    {
        $form = $this->createFormBuilder($upload, ['csrf_protection' => false])
            ->add('uploadFile', VichFileType::class, [
                'required' => true,
            ]);
        return $form->getForm();
    }
}
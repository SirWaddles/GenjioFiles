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
        return $this->render('GenjioBundle:Default:view.html.twig', []);
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
                'id' => $upload->getId(),
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

    /**
     * @Route("/api/delete", name="delete")
     * @Security("has_role('ROLE_API')")
     * @Method("POST")
     */
    public function deleteAction(Request $request)
    {
        $requestObj = json_decode($request->getContent(), true);
        $fileId = $requestObj['id'];
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('GenjioBundle:Upload');

        $upload = $repo->find($fileId);
        if (!$upload) {
            return new JsonResponse(['success' => false, 'error' => 'File not found']);
        }
        if ($upload->getUser()->getId() !== $this->getUser()->getId()) {
            return new JsonResponse(['success' => false, 'error' => 'Incorrect permissions']);
        }
        $em->remove($upload);
        $em->flush();
        return new JsonResponse(['success' => true]);
    }

    /**
     * @Route("/api/list", name="list")
     * @Security("has_role('ROLE_API')")
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $uploadRepo = $em->getRepository('GenjioBundle:Upload');
        $uploads = $uploadRepo->getAllUploads($this->getUser());
        return new JsonResponse($uploads);
    }

    /**
     * @Route("/api/password", name="change_password")
     * @Security("has_role('ROLE_API')")
     */
    public function changePasswordAction(Request $request)
    {
        $requestObj = json_decode($request->getContent(), true);
        $newPassword = password_hash($requestObj['new_password'], PASSWORD_DEFAULT);

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $user->setPassword($newPassword);
        $em->persist($user);
        $em->flush();
        return new JsonResponse(['success' => true]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('GenjioBundle:User');

        // Really no idea how to do this without the weird manual authentication.
        // Exception listener maybe, but I dont know.
        $apiKey = $request->headers->get('Genjio-API-Key');
        $username = $request->headers->get('Genjio-API-Username');

        $user = $repo->findOneBy(['username' => $username]);
        if (!$user) {
            $user = $repo->find($username);
            if (!$user) {
                return new JsonResponse(['success' => false, 'error' => 'User not found']);
            }
        }

        if (!password_verify($apiKey, $user->getPassword())) {
            return new JsonResponse(['success' => false, 'error' => 'User not found']); // I don't really care about enumeration, but eh
        }

        return new JsonResponse(['success' => true, 'username' => $user->getId()]);
    }
}

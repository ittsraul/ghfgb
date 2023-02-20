<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Files;
use App\Entity\User;
use App\Repository\FilesRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/twig', name: 'app_')]
class TwigController extends AbstractController
{
    #[Route('/listUser/{page?}', name: 'User')]
    public function listUser(?int $page, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $User = $entityManager->getRepository(User::class)->findAll();
        $data = [];
        for ($i=0; $i < count($User); $i++) { 
          $data[$i] = [
            "id" => $User[$i]->getId(),
            "name" => $User[$i]->getName(),
            "email" => $User[$i]->getEmail(),
            "roles" => ($User[$i]->getRoles()[0] === "USER") ? "Usuario" : "Administrador",
            "phone" => $User[$i]->getPhone(),
            "surnames" => $User[$i]->getSurnames()
          ];
        }        
        return $this->render('User/AdminPanel.html.twig', [
            'data' => $data,
            'page' => $this->getLastPage($page, $session)
        ]);
    }

    #[Route('/detailUser/{usuario?null}', name: 'detailUser')]
    public function detailUser(EntityManagerInterface $entityManager, int $usuario): Response
    {
        $User = $entityManager->getRepository(User::class)->find($usuario);
        $data = [
            "id" => $User->getId(),
            "name" => $User->getName(),
            "surnames" => $User->getSurnames(),
            "email" => $User->getEmail(),
            "roles" => ($User->getRoles()[0] === "USER") ? "Usuario" : "Administrador",
            "files" => [], 
            "phone" => $User->getPhone(),
            "events" => []
          ];

          for($i = 0; $i < count($User->getFiles()); $i++){
            $data["files"][$i] = [
                "idFile" => $User->getFiles()[$i]->getIdFile(),
                "name" => $User->getfiles()[$i]->getName(),
                "type" => $User->getFiles()[$i]->getType(),
                "isSubmited" => html_entity_decode(($User->getFiles()[$i]->isIsSubmited()) ? '&#x2713;' : "")
            ];
          }

          for($i = 0; $i < count($User->getEvents()); $i++){
            $data["events"][$i] = [
                "id" => $User->getEvents()[$i]->getId(),
                "name" => $User->getEvents()[$i]->getName(),
                "place" => $User->getEvents()[$i]->getPlace()
            ];
          }
        return $this->render('User/AdminDetailPanel.html.twig', [
            'detalleClient' => $data 
        ]);
    }

    #[Route('/insertUser', name: 'insertUser')]
    public function insert(EntityManagerInterface $gestor, Request $request): Response
    {
        $container = $request->request->all();
        if (count($container) > 1) {
             $gestor->getRepository(User::class)->insertUser($request); 
        }
        return $this->render('User/AdminInsertPanel.html.twig', []);

    }
    #[Route('/insertFiles/{idUser}', name: 'insertFiles')]
    public function insertFile(int $idUser, EntityManagerInterface $doctrine, Request $request, FilesRepository $repository): Response{
        $user = $doctrine->getRepository(User::class)->find($idUser);

        if (count($request->request->all())) {
            $repository->insert($request, $idUser);
        }
        return $this->render('Files/insertFiles.html.twig', [
            'user' => $user
        ]);
    }
    #[Route('/updateFiles/{idUser}', name: 'updateFiles')]
    public function updateFiles(int $idUser, EntityManagerInterface $doctrine, Request $request, FilesRepository $repository): Response{
        $user = $doctrine->getRepository(User::class)->find($idUser);
        $userFiles = $user->getFiles();
        if (count($request->request->all())) {
            $repository->updateFiles($request, $idUser);
        }
        return $this->render('Files/updateFiles.html.twig', [
            'user' => $user,
            'userFiles' => $userFiles
        ]);
    }
    #[Route('/deleteFile/{id}/{idUser}', name: 'deleteFiles')]
    public function deleteFiles(EntityManagerInterface $doctrine, $id, $idUser): Response
    {
        $file = $doctrine->getRepository(Files::class)->find($id);
        $doctrine->getRepository(Files::class)->remove($file, true);
        return $this->redirect('/twig/detailUser/' . $idUser);
    }

    #[Route('/deleteUser/{usuario}', name: 'deleteUser')]
    public function delete(EntityManagerInterface $gestor, int $usuario, FilesRepository $filesRepository): Response
    {
        $user = $gestor->getRepository(User::class)->find($usuario);
        $gestor->getRepository(User::class)->delete($user, $filesRepository); 
        return $this->redirect('/twig/listUser');
    }

    #[Route('/updateUser/{usuario}', name: 'updateUser')]
    public function update(EntityManagerInterface $gestor, Request $request, int $usuario): Response
    {
        $container = $request->request->all();
        if (count($container) > 1) {
             $gestor->getRepository(User::class)->updateUser($usuario, $request); 
        }

        $user = $gestor->getRepository(User::class)->find($usuario);
        return $this->render('User/AdminUpdatePanel.html.twig', [
            "user" => $user
        ]);
    } 

    #[Route('/', name: 'twigRedirect')]
    public function redirectTwig(): Response
    {
        return $this->redirect('/');
    }
            
    private function getLastPage($page, $session): int
    {
    if ($page != null) {
        $session->set("page",$page);
        return $page;
    } elseif (!$session->has("page") || !is_numeric($session->get("page"))) {
        $session->set("page",1);
        return 1;
    }
    return $session->get("page");
    } 
}


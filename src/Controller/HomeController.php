<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\ChatFormType;
use App\Service\OpenRouterService;
use Symfony\Component\HttpFoundation\Request;

final class HomeController extends AbstractController
{
// HomeController.php
#[Route('/', name: 'app_home')]

public function index(Request $request, OpenRouterService $openAi): Response
{
    $form = $this->createForm(ChatFormType::class);
    $form->handleRequest($request);
    
    $response = null;
    
    if($form->isSubmitted() && $form->isValid()){
        $data = $form->getData();
        $result = $openAi->getResponse($data['chat']);
        // Extraire le contenu du message
        $response = $result['choices'][0]['message']['content'] ?? null;
    }
    
    return $this->render('home/index.html.twig', [
        'form' => $form->createView(),
        'response' => $response,
        'debug' => $result ?? null, // pour debug si nécessaire

    ]);
}


}

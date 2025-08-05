<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ClassSession;
use App\Repository\ClassSessionRepository;


final class ClassSessionController extends AbstractController
{
    #[OA\Get(
        path: '/api/class-sessions',
        summary: 'Lista todas as sessões de aula',
        tags: ['ClassSessions'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de sessões retornada com sucesso',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'classSessionsList',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer'),
                                    new OA\Property(property: 'date', type: 'string', format: 'date')
                                ]
                            )
                        )
                    ]
                )
            )
        ]
    )]
    #[OA\Tag(name: 'ClassSessions')]
    #[Route('/api/class-sessions', name: 'list_class_sessions', methods: ['GET'])]
    public function listClassSessions(EntityManagerInterface $entityManager): Response
    {        
        $classSessionsList = $entityManager->getRepository(ClassSession::class)->findAll();

        $data = array_map(function ($classSession) {
            return [
                'id' => $classSession->getId(),
                'date' => $classSession->getSessionDate(),
            ];
        }, $classSessionsList);

        return $this->json(['classSessionsList' => $data]);
    }

    #[OA\Post(
        path: '/api/class-sessions',
        summary: 'Cria uma nova sessão de aula',
        tags: ['ClassSessions'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                required: ['session_date'],
                properties: [
                    new OA\Property(property: 'session_date', type: 'string', format: 'date', example: '2025-08-05')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Sessão criada com sucesso',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'name', type: 'string', format: 'date')
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Dados inválidos')
        ]
    )]
    #[OA\Tag(name: 'ClassSessions')]
    #[Route('/api/class-sessions', name: 'create_class_session', methods: ['POST'])]
    public function createClassSession(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $classSession = new ClassSession();
        $classSession->setSessionDate(new \DateTime($data['session_date']));
        $entityManager->persist($classSession);
        $entityManager->flush();

        return $this->json([
            'id' => $classSession->getId(),
            'date' => $classSession->getSessionDate(),
        ], Response::HTTP_CREATED);
    }
}

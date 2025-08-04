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

    #[Route('/api/class-sessions/{id}', name: 'get_class_session', methods: ['GET'])]
    public function getClassSession(int $id, EntityManagerInterface $entityManager): Response
    {
        $classSession = $entityManager->getRepository(ClassSession::class)->find($id);

        if (!$classSession) {
            return $this->json(['error' => 'Class session not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $classSession->getId(),
            'name' => $classSession->getName(),
        ]);

    }

    #[Route('/api/class-sessions/create', name: 'create_class_session', methods: ['POST'])]
    public function createClassSession(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $classSession = new ClassSession();
        $classSession->setSessionDate($data['session_date']);
        $entityManager->persist($classSession);
        $entityManager->flush();

        return $this->json([
            'id' => $classSession->getId(),
            'name' => $classSession->getName(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/class-sessions/{id}', name: 'update_class_session', methods: ['PUT'])]
    public function updateClassSession(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $classSession = $entityManager->getRepository(ClassSession::class)->find($id);

        if (!$classSession) {
            return $this->json(['error' => 'Class session not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['session_date'])) {
            $classSession->setSessionDate(new \DateTime($data['session_date']));
        }

        $entityManager->persist($classSession);
        $entityManager->flush();

        return $this->json(['message' => 'Class session updated successfully']);
    }

    #[Route('/api/class-sessions/{id}', name: 'delete_class_session', methods: ['DELETE'])]
    public function deleteClassSession(int $id, EntityManagerInterface $entityManager): Response   
    {
        $classSession = $entityManager->getRepository(ClassSession::class)->find($id);

        if (!$classSession) {
            return $this->json(['error' => 'Class session not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($classSession);
        $entityManager->flush();
        
        return $this->json(['message' => 'Class session deleted successfully']);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Student;
use App\Repository\StudentRepository;
use App\Repository\AttendanceRepository;

final class StudentController extends AbstractController
{ 

    #[OA\Get(
        path: '/api/students',
        summary: 'Lista todos os estudantes',
        tags: ['Student'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de estudantes retornada com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'studentsList',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer'),
                                    new OA\Property(property: 'name', type: 'string'),
                                ],
                                type: 'object'
                            )
                        )
                    ],
                    type: 'object'
                )
            )
        ]
    )]
    #[OA\Tag(name: 'Student')]
    #[Route('/api/students', name: 'list_students', methods: ['GET'])]
    public function listStudents(EntityManagerInterface $entityManager): Response
    {
        
        $studentsList = $entityManager->getRepository(Student::class)->findAll();

        $data = array_map(function ($student) {
            return [
                'id' => $student->getId(),
                'name' => $student->getName(),
            ];
        }, $studentsList);

        return $this->json(['studentsList' => $data]);
    }

    #[OA\Get(
        path: '/api/students/by-session/{session_id}',
        summary: 'Lista de presenças dos estudantes em uma sessão',
        tags: ['Student'],
        parameters: [
            new OA\Parameter(
                name: 'session_id',
                in: 'path',
                required: true,
                description: 'ID da sessão de aula',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de presenças retornada com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'attendances',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer'),
                                    new OA\Property(property: 'name', type: 'string'),
                                    new OA\Property(property: 'is_present', type: 'boolean', nullable: true),
                                ],
                                type: 'object'
                            )
                        )
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Presenças não encontradas',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string')
                    ],
                    type: 'object'
                )
            )
        ]
    )]    
    #[OA\Tag(name: 'Student')]
    #[Route('/api/students/by-session/{session_id}', name: 'students_attendances_by_session', methods: ['GET'])]
    public function listByClassSession(int $session_id, StudentRepository $studentRepository): Response
    {
        $students = $studentRepository->findAttendancesBySession($session_id);

        if (empty($students)) {
            return $this->json(['error' => 'Attendance not found'], Response::HTTP_NOT_FOUND);
        }

        $data = array_map(function ($student) use ($session_id) {
            $isPresent = null;

            foreach ($student->getAttendances() as $a) {
                if ($a && $a->getClassSession()->getId() === $session_id) {
                    $isPresent = $a->isPresent();
                    break;
                }
            }
            return [
                'id' => $student->getId(),
                'name' => $student->getName(),
                'is_present' => $isPresent,
            ];
        }, $students);

        return $this->json(['attendances' => $data]);
    }

    #[OA\Get(
        path: '/api/students/{studentId}/attendance-summary',
        summary: 'Retorna um resumo de presença do estudante',
        tags: ['Student'],
        parameters: [
            new OA\Parameter(
                name: 'studentId',
                in: 'path',
                required: true,
                description: 'ID do estudante',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Resumo de frequência retornado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'attendance_summary',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'totalDays', type: 'integer'),
                                new OA\Property(property: 'presentDays', type: 'integer'),
                                new OA\Property(property: 'absentDays', type: 'integer'),
                            ]
                        )
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Estudante não encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string')
                    ],
                    type: 'object'
                )
            )
        ]
    )]    
    #[OA\Tag(name: 'Student')]
    #[Route('/api/students/{studentId}/attendance-summary', name: 'student_attendance_summary', methods: ['GET'])]
    public function getAttendanceSummary(int $studentId, AttendanceRepository $attendanceRepository): Response
    {
        $summary  = $attendanceRepository->getStudentAttendanceSummary($studentId);

        if (!$summary ) {
            return $this->json(['error' => 'Student not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'attendance_summary' => [
                'totalDays' => (int) $summary['totalClasses'],
                'presentDays' => (int) $summary['totalPresent'],
                'absentDays' => (int) $summary['totalAbsent'],
            ]
        ]);
    }
}

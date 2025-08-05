<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AttendanceRepository;

final class AttendanceController extends AbstractController
{
    #[OA\Get(
        path: '/api/attendance/list',
        summary: 'Lista todos os registros de presença',
        tags: ['Attendance'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de presenças retornada com sucesso',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'attendanceList',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer'),
                                    new OA\Property(property: 'student_id', type: 'integer'),
                                    new OA\Property(property: 'class_session_id', type: 'integer'),
                                    new OA\Property(property: 'is', type: 'boolean')
                                ]
                            )
                        )
                    ]
                )
            )
        ]
    )]
    #[OA\Tag(name: 'Attendance')]
    #[Route('/api/attendance/list', name: 'list_attendance', methods: ['GET'])]
    public function listAttendance(AttendanceRepository $attendanceRepository): Response
    {
        $attendanceList = $attendanceRepository->findAll();

        $data = array_map(function ($attendance) {
            return [
                'id' => $attendance->getId(),
                'student_id' => $attendance->getStudent()->getId(),
                'class_session_id' => $attendance->getClassSession()->getId(),
                'is' => $attendance->isPresent(),
            ];
        }, $attendanceList);

        return $this->json(['attendanceList' => $data]);
    }

    #[OA\Delete(
        path: '/api/class-sessions/{sessionId}/attendances',
        summary: 'Remove todas as presenças de uma sessão específica',
        tags: ['Attendance'],
        parameters: [
            new OA\Parameter(
                name: 'sessionId',
                in: 'path',
                required: true,
                description: 'ID da sessão de aula',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Presenças removidas com sucesso',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'deleted', type: 'integer')
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Erro ao remover presenças',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string'),
                        new OA\Property(property: 'details', type: 'string')
                    ]
                )
            )
        ]
    )]
    #[OA\Tag(name: 'Attendance')]
    #[Route('/api/class-sessions/{sessionId}/attendances', name: 'delete_attendances_session', methods: ['DELETE'])]
    public function deleteBySessionId(int $sessionId, EntityManagerInterface $em, AttendanceRepository $attendanceRepository): Response
    {
        $em->getConnection()->beginTransaction();

        try {
            $deleteCount = $attendanceRepository->deleteAttendancesBySessionId($sessionId);

            $em->getConnection()->commit();

            return $this->json([
                'message' => 'Presenças removidas com sucesso',
                'deleted' => $deleteCount
            ]);
        } catch (\Throwable $th) {
            $em->getConnection()->rollBack();

            return $this->json([
                'error' => 'Erro ao remover presenças',
                'details' => $th->getMessage()
            ], 500);
        }
    }

    #[OA\Put(
        path: '/api/attendances/bulk',
        summary: 'Atualiza múltiplos registros de presença para uma sessão',
        tags: ['Attendance'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                required: ['session_id', 'attendances'],
                properties: [
                    new OA\Property(property: 'session_id', type: 'integer'),
                    new OA\Property(
                        property: 'attendances',
                        type: 'array',
                        items: new OA\Items(
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'student_id', type: 'integer'),
                                new OA\Property(property: 'present', type: 'boolean')
                            ]
                        )
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Presenças atualizadas com sucesso',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string')
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Requisição inválida',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string')
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Erro no servidor',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string')
                    ]
                )
            )
        ]
    )]
    #[OA\Tag(name: 'Attendance')]
    #[Route('/api/attendances/bulk', name: 'bulk_session_attendances', methods: ['PUT'])]
    public function bulkSessionAttendances(Request $request, AttendanceRepository $attendanceRepository): Response
    {
        $data = json_decode($request->getContent(), true);

        if(!isset($data['session_id']) || !is_array($data['attendances'] ?? null)){
            return $this->json([
                'error' => 'Missing or invalid session_id or attendances'
            ], 400);
        }

        try {
            $attendanceRepository->saveAttendacesForSession(
                $data['session_id'],
                $data['attendances']
            );

            return $this->json([
                'message' => 'Attendances updated successfully'
            ]);
        } catch (\Throwable $th) {
            return $this->json([
                'error' => 'An error occurred: ' . $th->getMessage()
            ], 500);
        }
    }
}

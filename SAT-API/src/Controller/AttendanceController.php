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

    // Create route POST /api/class-sessions/{class_session_id}/attendances
    // registerForClassSession()

    // Create route GET /api/class-sessions/{class_session_id}/attendances
    // listByClassSession()

    // Create route PUT /api/attendances/{attendance_id}
    // updateAttendance()

    // Create route DELETE /api/attendances/{attendance_id}
    // deleteAttendance()

    // GET /api/students/{student_id}/attendance-summary
    // listAttendanceByStudent()
}

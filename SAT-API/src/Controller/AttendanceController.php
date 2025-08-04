<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AttendanceRepository;

final class AttendanceController extends AbstractController
{
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

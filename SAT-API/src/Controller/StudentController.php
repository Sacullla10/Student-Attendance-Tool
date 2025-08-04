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

    #[OA\Tag(name: 'Student')]
    #[Route('/api/students/{id}', name: 'get_student', methods: ['GET'])]
    public function getStudent(int $id, EntityManagerInterface $entityManager): Response
    {
        $student = $entityManager->getRepository(Student::class)->find($id);

        if (!$student) {
            return $this->json(['error' => 'Student not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $student->getId(),
            'name' => $student->getName(),
        ]);
    }

    #[OA\Tag(name: 'Student')]
    #[Route('/api/students', name: 'create_student', methods: ['POST'])]
    public function createStudent(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['name'])) {
            return $this->json(['error' => 'Name is required'], Response::HTTP_BAD_REQUEST);
        }

        $student = new Student();
        $student->setName($data['name']);
        $entityManager->persist($student);
        $entityManager->flush();

        return $this->json(['message' => 'Student created successfully', 'id' => $student->getId()], Response::HTTP_CREATED);
    }

    #[OA\Tag(name: 'Student')]
    #[Route('/api/students/{id}', name: 'update_student', methods: ['PUT'])]
    public function updateStudent(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $student = $entityManager->getRepository(Student::class)->find($id);

        if (!$student) {
            return $this->json(['error' => 'Student not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['name'])) {
            $student->setName($data['name']);
        }
        $entityManager->flush();
        return $this->json(['message' => 'Student updated successfully']);
    }

    #[OA\Tag(name: 'Student')]
    #[Route('/api/students/{id}', name: 'delete_student', methods: ['DELETE'])]
    public function deleteStudent(int $id, EntityManagerInterface $entityManager): Response
    {
        $student = $entityManager->getRepository(Student::class)->find($id);
        
        if (!$student) {
            return $this->json(['error' => 'Student not found'], Response::HTTP_NOT_FOUND);
        }
        
        $entityManager->remove($student);
        $entityManager->flush();

        return $this->json(['message' => 'Student deleted successfully']);               
    }

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

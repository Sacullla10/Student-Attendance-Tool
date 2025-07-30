<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Student;

final class StudentController extends AbstractController
{ 

    /**
     * Lists all students.
     *
     * @OA\Get(
     *     path="/api/student/list",
     *     summary="List all students",
     *     tags={"Student"},
     *     @OA\Response(
     *         response=200,
     *         description="List of students",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Student")
     *         )
     *     )
     * )
     */
    #[Route('/api/student/list', name: 'list_students', methods: ['GET'])]
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

    /**
     * Finds a student by ID.
     *
     * @OA\Get(
     *     path="/api/student/{id}",
     *     summary="Get a student by ID",
     *     tags={"Student"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="João da Silva")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Student not found")
     * )
     */
    #[Route('/api/student/{id}', name: 'get_student', methods: ['GET'])]
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

    /**
     * Creates a new student.
     *
     * @OA\Post(
     *     path="/api/student/create",
     *     summary="Create a new student",
     *     tags={"Student"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="João da Silva")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Student created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Student created successfully"),
     *             @OA\Property(property="id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid input")
     * )
     */
    #[Route('/api/student/create', name: 'create_student', methods: ['POST'])]
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

    /**
     * Updates an existing student by ID.
     *
     * @OA\Put(
     *     path="/api/student/update/{id}",
     *     summary="Update an existing student",
     *     tags={"Student"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="João da Silva")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Student updated successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Student not found")
     * )
     */
    #[Route('/api/student/{id}', name: 'update_student', methods: ['PUT'])]
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

    /**
     * Deletes a student by ID.
     *
     * @OA\Delete(
     *     path="/api/student/delete/{id}",
     *     summary="Delete a student",
     *     tags={"Student"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Student deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Student not found")
     * )
     */
    #[Route('/api/student/{id}', name: 'delete_student', methods: ['DELETE'])]
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
}

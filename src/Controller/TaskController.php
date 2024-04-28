<?php

namespace App\Controller;

// Importaciones de Symfony y Doctrine necesarias para el funcionamiento del controlador.
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Task;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskController extends AbstractController
{
    /**
     * Método para obtener todas las tareas.
     * @Route define la ruta de acceso a este método como '/tasks' y sólo permite métodos GET.
     * 
     * @param ManagerRegistry $doctrine Usado para acceder al sistema de gestión de entidades de Doctrine.
     * @return JsonResponse Devuelve una respuesta JSON con un array de tareas.
     */
    #[Route('/tasks', name: 'get_tasks', methods: ['GET'])]
    public function getTasks(ManagerRegistry $doctrine): JsonResponse
    {
        // Obtiene todas las tareas usando el repositorio de la entidad Task.
        $tasks = $doctrine->getRepository(Task::class)->findAll();
        $taskArray = [];

        // Itera sobre cada tarea y prepara un array para la respuesta JSON.
        foreach ($tasks as $task) {
            $taskArray[] = [
                'id' => $task->getId(),
                'name' => $task->getName(),
                'description' => $task->getDescription(),
                'dueDate' => $task->getDueDate()->format('Y-m-d H:i:s') // Formatea la fecha para la salida JSON.
            ];
        }

        // Devuelve las tareas como una respuesta JSON.
        return new JsonResponse($taskArray);
    }

    /**
     * Método para crear una nueva tarea.
     * @Route define la ruta como '/task' y solo permite métodos POST.
     *
     * @param Request $request Objeto de solicitud que contiene los datos enviados por el usuario.
     * @param ManagerRegistry $doctrine Usado para obtener el EntityManager de Doctrine.
     * @return JsonResponse Devuelve una respuesta JSON indicando si la tarea fue creada exitosamente.
     */
    #[Route('/task', name: 'add_task', methods: ['POST'])]
    public function addTask(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        // Obtiene el EntityManager para interactuar con la base de datos.
        $entityManager = $doctrine->getManager();
        $task = new Task();
        // Decodifica los datos JSON enviados en la solicitud.
        $data = json_decode($request->getContent(), true);

        // Asigna los valores a las propiedades de la nueva tarea.
        $task->setName($data['name']);
        $task->setDescription($data['description'] ?? null); // Utiliza null si 'description' no está definido.
        $task->setDueDate(new \DateTime($data['dueDate']));

        // Persiste la nueva tarea en la base de datos.
        $entityManager->persist($task);
        $entityManager->flush();

        // Devuelve una respuesta indicando el éxito de la operación.
        return new JsonResponse(['status' => 'Task created'], Response::HTTP_CREATED);
    }

    /**
     * Método para actualizar una tarea existente.
     * @Route("/task/{id}", name="update_task", methods={"PUT"})
     *
     * @param int $id ID de la tarea a actualizar.
     * @param Request $request Objeto de solicitud que contiene los datos enviados por el usuario.
     * @param ManagerRegistry $doctrine Usado para obtener el EntityManager de Doctrine.
     * @return JsonResponse Devuelve una respuesta JSON indicando si la tarea fue actualizada exitosamente.
     */
    #[Route('/task/{id}', name: 'update_task', methods: ['PUT'])]
    public function updateTask(Request $request, ManagerRegistry $doctrine, $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $task = $entityManager->getRepository(Task::class)->find($id);
    
        if (!$task) {
            return new JsonResponse(['message' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }
    
        $data = json_decode($request->getContent(), true);
        $task->setName($data['name']);
        $task->setDescription($data['description']);
        $task->setDueDate(new \DateTime($data['dueDate']));
    
        $entityManager->flush();
    
        return new JsonResponse(['message' => 'Task updated successfully'], Response::HTTP_OK);
    }

    /**
     * @Route("/task/{id}", name="delete_task", methods={"DELETE"})
     */
    #[Route('/task/{id}', name: 'delete_task', methods: ['DELETE'])]
    public function deleteTask(ManagerRegistry $doctrine, $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $task = $entityManager->getRepository(Task::class)->find($id);

        if (!$task) {
            return new JsonResponse(['message' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($task);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Task deleted successfully'], Response::HTTP_OK);
    }

}

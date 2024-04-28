<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;

// La anotación #[ORM\Entity] indica que esta clase es una entidad Doctrine
// y 'repositoryClass' especifica la clase de repositorio asociada que puede
// contener consultas personalizadas relacionadas con esta entidad.
#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    // #[ORM\Id] indica que esta propiedad es la clave primaria de la entidad.
    // #[ORM\GeneratedValue] especifica que el valor de esta propiedad se genera automáticamente.
    // #[ORM\Column] sin especificar tipo implica que es un integer (por defecto para IDs).
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // #[ORM\Column] define 'name' como una columna de tipo string y longitud máxima de 255 caracteres.
    #[ORM\Column(type: "string", length: 255)]
    private ?string $name = null;

    // La propiedad 'description' es opcional (nullable=true) y almacena texto,
    // útil para descripciones extendidas que pueden incluir texto en formato libre.
    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    // 'dueDate' también es opcional y almacena la fecha y hora,
    // indicando cuándo la tarea debe ser completada.
    #[ORM\Column(type: "datetime", nullable: true)]
    private ?DateTimeInterface $dueDate = null;

    // Métodos getter y setter para cada propiedad.

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDueDate(): ?DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(?DateTimeInterface $dueDate): self
    {
        $this->dueDate = $dueDate;
        return $this;
    }
}

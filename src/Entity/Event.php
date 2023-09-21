<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass: EventRepository::class)
 */
class Event
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    private ?EventType $eventType = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date)
    {
        $this->date = $date;

        return $this;
    }
}

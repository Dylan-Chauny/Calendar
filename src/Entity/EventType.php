<?php

namespace App\Entity;

use App\Repository\EventTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventTypeRepository::class)]
class EventType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $color = null;

    #[ORM\OneToMany(mappedBy: 'eventType', targetEntity: CalendarEvent::class)]
    private Collection $calendarEvents;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    public function __construct()
    {
        $this->calendarEvents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return Collection<int, CalendarEvent>
     */
    public function getCalendarEvents(): Collection
    {
        return $this->calendarEvents;
    }

    public function addCalendarEvent(CalendarEvent $calendarEvent): static
    {
        if (!$this->calendarEvents->contains($calendarEvent)) {
            $this->calendarEvents->add($calendarEvent);
            $calendarEvent->setEventType($this);
        }

        return $this;
    }

    public function removeCalendarEvent(CalendarEvent $calendarEvent): static
    {
        if ($this->calendarEvents->removeElement($calendarEvent)) {
            // set the owning side to null (unless already changed)
            if ($calendarEvent->getEventType() === $this) {
                $calendarEvent->setEventType(null);
            }
        }

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }
}

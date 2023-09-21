<?php

namespace App\Subscriber;

use App\Entity\EventType;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    const DAY_OFF_CODE     = "off";
    const DAY_WORKED_CODE  = "work";
    const DAY_HOLIDAY_CODE = "holiday";

    // MONTH => DAY
    const HOLIDAYS_ARRAY   = [
        [1 => 1],
        [5 => 1],
        [5 => 8],
        [7 => 14],
        [8 => 15],
        [11 => 1],
        [11 => 1],
        [12 => 25]
    ];

    const SATURDAY_PATTERN = [self::DAY_WORKED_CODE, self::DAY_WORKED_CODE, self::DAY_OFF_CODE, self::DAY_OFF_CODE];
    const MONDAY_PATTERN   = [self::DAY_OFF_CODE, self::DAY_OFF_CODE, self::DAY_WORKED_CODE, self::DAY_WORKED_CODE];

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    /**
     * @throws Exception
     */
    public function onCalendarSetData(CalendarEvent $calendar): void
    {
        $eventTypeDayWork    = $this->entityManager->getRepository(EventType::class)->findOneBy(['code' => self::DAY_WORKED_CODE]);
        $eventTypeDayOff     = $this->entityManager->getRepository(EventType::class)->findOneBy(['code' => self::DAY_OFF_CODE]);
        $eventTypeHoliday    = $this->entityManager->getRepository(EventType::class)->findOneBy(['code' => self::DAY_HOLIDAY_CODE]);

        $start               = $calendar->getStart();
        $end                 = $calendar->getEnd();
        $arraySaturdayEvents = [];

        $datePaquesTimestamp = easter_date($start->format('Y'));
        $datePaques          = date('d-m-Y', $datePaquesTimestamp);

        $this->createCalendarEvent($eventTypeHoliday, $datePaques);

        $end        = $end->add(new \DateInterval('P1Y'));
        $oneDay     = new \DateInterval('P1D');
        $datePeriod = new \DatePeriod($start, $oneDay, $end);

        foreach ($datePeriod as $day) {

            $this->createHolidaysEvent($eventTypeHoliday, $day);

            $dayOfWeek = (int)$day->format('N');

            // Si on dépasse du pattern, on le recommence
            if (!isset(self::SATURDAY_PATTERN[\count($arraySaturdayEvents)]))
            {
                $arraySaturdayEvents = [];
            }


            if ($dayOfWeek == 6 or $dayOfWeek == 1)
            {
                $event = ($dayOfWeek == 6) ? self::MONDAY_PATTERN[\count($arraySaturdayEvents)] : self::SATURDAY_PATTERN[\count($arraySaturdayEvents)];

                if ($event == self::DAY_OFF_CODE)
                {
                    if ($dayOfWeek == 6)
                        $arraySaturdayEvents[] = self::DAY_OFF_CODE;

                    $this->createCalendarEvent($eventTypeDayOff, $day->format('d-m-Y'));
                }
                else
                {
                    if ($dayOfWeek == 6)
                        $arraySaturdayEvents[] = self::DAY_WORKED_CODE;

                    $this->createCalendarEvent($eventTypeDayWork, $day->format('d-m-Y'));
                }
            }
        }


        $calendarDates = $this->entityManager->getRepository(\App\Entity\Event::class)->findAll();

        foreach ($calendarDates as $calendarDate)
        {
            $calendar->addEvent(new Event(
                            $calendarDate->getEventType()->getLabel(),
                            new \DateTime($calendarDate->getDate()),
                            null,
                            null,
                            ['color' => $calendarDate->getEventType()->getColor()]
                        ));
        }
    }

    public function createHolidaysEvent(EventType $eventType, $day): void
    {
        $monthOfDay = $day->format('n');
        $dayOfDay   = $day->format('j');

        if (in_array([$monthOfDay => $dayOfDay], self::HOLIDAYS_ARRAY))
        {
            $this->createCalendarEvent($eventType, $day->format('d-m-Y'));
        }
    }
    public function createCalendarEvent(EventType $eventType, $date) {
        $calendarEvent = $this->entityManager->getRepository(\App\Entity\Event::class)->findOneBy(['date' => $date]);

        if (!$calendarEvent)
        {
            $calendarEvent = new \App\Entity\Event();
            $calendarEvent->setEventType($eventType);
            $calendarEvent->setDate($date);

            $this->entityManager->persist($calendarEvent);
            $this->entityManager->flush();
        }
    }
}
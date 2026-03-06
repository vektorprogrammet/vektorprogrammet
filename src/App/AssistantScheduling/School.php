<?php

namespace App\AssistantScheduling;

class School implements \JsonSerializable
{
    /**
     * School constructor.
     *
     * @param int     $id
     * @param string  $name
     * @param mixed[] $capacity
     */
    public function __construct(
        /**
         * Group
         *   Day
         *     Capacity.
         */
        private $capacity,
        private $name,
        private $id,
    ) {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $day
     * @param int    $group
     *
     * @return int
     */
    public function capacityLeftOnDay($group, $day)
    {
        return $this->capacity[$group][$day];
    }

    /**
     * @param string $day
     * @param int    $group
     */
    public function addAssistant($group, $day)
    {
        --$this->capacity[$group][$day];
    }

    /**
     * @return array
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * @param array $capacity
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;
    }

    /**
     * @return bool
     */
    public function isFull()
    {
        foreach ($this->capacity as $weekDayCapacity) {
            foreach ($weekDayCapacity as $day => $capacityLeft) {
                if ($capacityLeft > 0) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'capacity' => $this->capacity,
        ];
    }
}

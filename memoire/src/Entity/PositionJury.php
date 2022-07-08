<?php
namespace App\Entity;

class PositionJury {
    private $position;

    /**
     * Get the value of position
     */ 
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set the value of position
     *
     * @return  self
     */ 
    public function setPosition(string $position)
    {
        $this->position = $position;

        return $this;
    }
}
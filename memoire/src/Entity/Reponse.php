<?php
namespace App\Entity;
class Reponse{
    private $reponse;

    /**
     * Get the value of reponse
     */ 
    public function getReponse()
    {
        return $this->reponse;
    }

    /**
     * Set the value of reponse
     *
     * @return  self
     */ 
    public function setReponse($reponse)
    {
        $this->reponse = $reponse;

        return $this;
    }
}
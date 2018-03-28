<?php
declare(strict_types=1);

namespace Shrtnr;

class Rule
{
    private $id;
    private $userId;
    private $from;
    private $to;
    private $createdAt;
    private $enabled;
    private $modifiedAt;

    public function __construct($userId, string $from, string $to, \DateTime $createdAt, bool $enabled, \DateTime $modifiedAt)
    {
        $this->userId  = $userId;
        $this->from  = $from;
        $this->to  = $to;
        $this->createdAt  = $createdAt;
        $this->enabled  = $enabled;
        $this->modifiedAt  = $modifiedAt;
    }

    /**
     * Get the value of userId
     */ 
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Get the value of from
     */ 
    public function getFrom() : string
    {
        return $this->from;
    }

    /**
     * Get the value of to
     */ 
    public function getTo() : string
    {
        return $this->to;
    }

    /**
     * Get the value of createdAt
     */ 
    public function getCreatedAt() : \DateTime
    {
        return $this->createdAt;
    }

    /**
     * Get the value of enabled
     */ 
    public function getEnabled() : bool
    {
        return $this->enabled;
    }

    /**
     * Get the value of modifiedAt
     */ 
    public function getModifiedAt() : \DateTime
    {
        return $this->modifiedAt;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId(int $id) : self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of id
     */ 
    public function getId() : int
    {
        return intval($this->id);
    }

    /**
     * Returns a string representation of a Rule
     * 
     * @return      string
     */
    public function __toString() : string
    {
        return sprintf("%i: '%s'->'%s' by %s on %s is %s",
            intval($this->id),
            $this->from,
            $this->to,
            strval($this->userId),
            $this->createdAt->format("d-m-Y H:i:s"),
            ($this->enabled ? "ENABLED" : "DISABLED")
        );
    }
}

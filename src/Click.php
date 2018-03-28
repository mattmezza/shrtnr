<?php
declare(strict_types=1);

namespace Shrtnr;

class Click 
{
    /**
     * The click datetime
     */
    private $clickedAt;
    /**
     * The shortened url the final user clicked
     */
    private $ruleId;
    /**
     * The value of `from` clickedAt the user clicked
     */
    private $from;
    /**
     * The final url where the final user is redirected
     */
    private $to;
    /**
     * The id of the single click
     */
    private $id;
    /**
     * The client IP
     */
    private $ip;

    /**
     * Builds an instance of the model
     */
    public function __construct(\DateTime $clickedAt, int $ruleId, string $to, string $from, string $ip = null)
    {
        $this->clickedAt  = $clickedAt;
        $this->ruleId  = $ruleId;
        $this->to  = $to;
        $this->from = $from;
        $this->ip = $ip;
    }

    /**
     * Get the click datetime
     */ 
    public function getClickedAt() : \DateTime
    {
        return $this->clickedAt;
    }

    /**
     * Get the shortened url the final user clicked
     */ 
    public function getRuleId() : int
    {
        return $this->ruleId;
    }

    /**
     * Get the final url where the final user is redirected
     */ 
    public function getTo() : string
    {
        return $this->to;
    }

    /**
     * Get the id of the single click
     */ 
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Set the id of the single click
     *
     * @return  self
     */ 
    public function setId(int $id) : self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the client IP
     */ 
    public function getIp() : string
    {
        return $this->ip;
    }
}

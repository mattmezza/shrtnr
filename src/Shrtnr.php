<?php
declare(strict_types=1);

namespace Shrtnr;

use Shrtnr\Rule;
use Shrtnr\DAO\Rules;
use Shrtnr\Click;
use Shrtnr\DAO\Clicks;

class Shrtnr
{
    private $rulesMgr;
    private $clicksMgr;

    public function __construct()
    {
        $this->rulesMgr = new Rules();
        $this->clicksMgr = new Clicks();
    }

    public function getTo(string $from) : string
    {
        $rule = $this->rulesMgr->getByFromUrl($from);
        return $rule->getTo();
    }

    public function registerClick(Rule $rule, string $ip = null) : Click
    {
        $registeredClick = $this->clicksMgr->add(
            $rule->getFrom(),
            $rule->getTo(),
            intval($rule->getId()),
            $ip
        );
        return $registeredClick;
    }

    public function relink(Rule $rule, string $newFrom, string $newTo) : void
    {
        $this->rulesMgr->edit(intval($rule->getId()), $newFrom, $newTo);
    }

}

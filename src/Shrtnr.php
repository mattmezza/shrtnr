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

    /**
     * Given a URI gives back the destination URL of the rule matching 
     * with the `from` field (or throws a `NoRuleException` if no rule 
     * is associated with the passed URI)
     * 
     * @param   string  $from   The URI from which to be redirected
     * @param   string  $ip     The IP address of the client
     * 
     * @throws  NoRuleException     Thrown when there is no matching rule to apply for give URI $from
     * 
     * @return  string  The URL where to redirect the client
     */
    public function shrtn(string $from, string $ip = null) : string
    {
        $rule = $this->rulesMgr->getByFromUrl($from);
        $click = $this->clicksMgr->add($rule, $ip);
        return $rule->getTo();
    }

}

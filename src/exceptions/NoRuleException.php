<?php
declare(strict_types=1);

namespace Shrtnr\Exception;

use Shrtnr\Rule;

class NoRuleException extends \Exception 
{
    public function __construct(string $from)
    {
        parent::__construct("No rule found for `from`=`$from`");
    }
}

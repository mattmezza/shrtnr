<?php
declare(strict_types=1);

namespace Shrtnr\Exception;

use Shrtnr\Rule;

class AddRuleException extends \Exception 
{
    public function __construct()
    {
        parent::__construct("Error adding rule");
    }
}

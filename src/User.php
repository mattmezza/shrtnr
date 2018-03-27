<?php
declare(strict_types=1);

namespace Shrtnr;

interface User
{
    /**
     * Gets the unique user identifier used to link clicks to users
     */
    public function getId();
}
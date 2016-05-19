<?php

namespace CG\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CGUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}

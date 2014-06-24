<?php

namespace Ben\MessageBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class BenMessageBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSMessageBundle';
    }
}

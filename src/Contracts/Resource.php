<?php

namespace Softpampa\Moip\Contracts;

interface Resource {

    public function addFilter($pattern, array $binds);

}

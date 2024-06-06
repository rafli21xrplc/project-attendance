<?php

namespace App\Contracts\Interfaces;

interface UpdateInterface
{
    public function update(mixed $id, array $data):mixed;

}

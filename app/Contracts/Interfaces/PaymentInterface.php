<?php

namespace App\Contracts\Interfaces;

interface PaymentInterface extends GetInterface, StoreInterface, UpdateInterface, DeleteInterface, ShowInterface
{
}

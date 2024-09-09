<?php

namespace App\Contracts\Interfaces;

interface StudentPaymentInterface extends GetInterface, StoreInterface, UpdateInterface, DeleteInterface, ShowInterface, GetStudentInterface, GetPaymentInterface, GetClassRoomInterface, GetTypeClassroomInterface, GetTypePaymentInterface
{
}

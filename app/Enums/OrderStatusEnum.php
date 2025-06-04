<?php

namespace App\Enums;

class OrderStatusEnum extends BaseEnum
{
    public const PENDING = 'pending';
    public const SUCCESS = 'success';
    public const CANCELED = 'canceled';
    public const DELIVERED = 'delivered';
}

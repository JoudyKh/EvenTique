<?php

namespace App\Constants;

class Constants
{
    const SERVICE_ORDER_STATUSES = [
        'REJECTED' => [
            'ar' => 'مرفوض',
            'en' => 'rejected'
        ],
        'PREPARING' => [
            'ar' => 'يتم التحضير',
            'en' => 'preparing'
        ],
        'PROCESSED' => [
            'ar' => 'تم التحضير',
            'en' => 'prcessed'
        ]
    ];
}

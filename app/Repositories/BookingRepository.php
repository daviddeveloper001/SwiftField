<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Booking;
use App\Repositories\BaseRepository;

class BookingRepository extends BaseRepository
{
    const RELATIONS = ['tenant', 'service', 'customer'];

    public function __construct(Booking $booking)
    {
        parent::__construct($booking, self::RELATIONS);
    }
   
}

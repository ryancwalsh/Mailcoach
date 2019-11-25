<?php

namespace App\Support;

use Laravel\Horizon\Contracts\MasterSupervisorRepository;

class HorizonStatus
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_PAUSED = 'paused';

    /** @var \Laravel\Horizon\Contracts\MasterSupervisorRepository */
    private $masterSupervisorRepository;

    public function __construct(MasterSupervisorRepository $masterSupervisorRepository)
    {
        $this->masterSupervisorRepository = $masterSupervisorRepository;
    }

    public function is(string $status): bool
    {
        return $this->get() === $status;
    }

    public function get()
    {
        if (!$masters = $this->masterSupervisorRepository->all()) {
            return static::STATUS_INACTIVE;
        }

        $isPaused = collect($masters)->contains(function ($master) {
            return $master->status === 'paused';
        });

        return $isPaused
            ? static::STATUS_PAUSED
            : static::STATUS_ACTIVE;
    }
}
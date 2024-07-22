<?php

namespace App\Models;

use Guava\Calendar\Contracts\Eventable;
use Guava\Calendar\ValueObjects\Event;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model implements Eventable
{
    protected $fillable = [
        'title',
        'description',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function toEvent(): array | Event
    {
        return Event::make($this)
            ->title($this->title)
            ->start($this->starts_at)
            ->end($this->ends_at)
        ;
    }
}

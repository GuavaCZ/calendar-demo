<?php

namespace App\Models;

use App\Enums\Priority;
use Guava\Calendar\Contracts\Eventable;
use Guava\Calendar\ValueObjects\Event;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sprint extends Model implements Eventable
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'priority',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'priority' => Priority::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function toEvent(): array | Event
    {
        return Event::make($this)
            ->title($this->title)
            ->start($this->starts_at)
            ->end($this->ends_at)
            ->extendedProp('priority', $this->priority->getLabel())
        ;
    }
}

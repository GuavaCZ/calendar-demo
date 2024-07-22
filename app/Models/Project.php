<?php

namespace App\Models;

use Guava\Calendar\Contracts\Resourceable;
use Guava\Calendar\ValueObjects\Resource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model implements Resourceable
{
    use HasFactory;

    protected $fillable = [
        'title',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function toResource(): array | Resource
    {
        return Resource::make($this->id)
            ->title($this->title)
        ;
    }
}

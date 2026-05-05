<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'group',
        'stream_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where('name', 'like', "%{$search}%");
    }

    public function scopeByGroup(Builder $query, ?string $group): Builder
    {
        if (empty($group) || $group === 'Todos') {
            return $query;
        }

        return $query->where('group', $group);
    }
}

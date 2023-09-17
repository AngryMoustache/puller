<?php

namespace App;

use App\Enums\MediaType;
use App\Models\Origin;
use App\Models\Pull;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Pulls extends Collection
{
    const KEY = 'pull-cache';

    public null | int $limit = null;

    public static function make($items = [])
    {
        session(['pulls-with-prompts' => false]);

        return new static(self::build()->toArray());
    }

    public function fetch(): Collection
    {
        $promptOrigins = Origin::prompts()->pluck('id');

        return Pull::query()
            ->orderByRaw('FIELD(id, ' . $this->pluck('id')->implode(',') . ')')
            ->when(! $this->hasPrompts(), fn ($query) => $query->whereNotIn('origin_id', $promptOrigins))
            ->unless(is_null($this->limit), fn ($query) => $query->limit($this->limit))
            ->find($this->pluck('id'));
    }

    public function limit(null | int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function withPrompts(bool $withPrompts = true): self
    {
        session(['pulls-with-prompts' => $withPrompts]);

        return $this;
    }

    public function hasPrompts(): bool
    {
        return session('pulls-with-prompts', false);
    }

    public static function build(bool $rebuild = false): Collection
    {
        if ($rebuild) {
            Cache::forget(static::KEY);
        }

        return Cache::rememberForever(static::KEY, function () {
            return static::getCacheData();
        });
    }

    public static function getCacheData(): Collection
    {
        return Pull::online()
            ->with('tags', 'origin', 'artist')
            ->get()
            ->filter(fn ($pull) => $pull->attachment)
            ->map(function (Pull $pull) {
                return collect([
                    'id' => $pull->id,
                    'name' => $pull->name,
                    'views' => $pull->views,
                    'verdict_at' => $pull->verdict_at,
                    'artists' => [$pull->artist?->slug],
                    'origins' => [$pull->origin?->slug],
                    'tags' => self::getCachedTagsForPull($pull),
                    'folders' => $pull->folders->pluck('slug'),
                    'media_type' => [
                        MediaType::IMAGE->value => $pull->attachments->isEmpty(),
                        MediaType::VIDEO->value => $pull->videos->isEmpty(),
                    ],
                ]);
            });
    }

    private static function getCachedTagsForPull(Pull $pull): Collection
    {
        $groups = $pull->tags
            ->groupBy('pivot.group')
            ->map(fn (Collection $tags, string $key) => [
                'name' => $key,
                'is_main' => $tags->first()?->pivot->is_main ?? false,
                'tags' => $tags->pluck('slug'),
            ])
            ->values();

        $mainTags = $groups->filter(fn ($group) => $group['is_main'])->pluck('tags')->flatten();

        // Add the main tags to the other groups
        return $groups->map(function ($group) use ($mainTags) {
            if (! $group['is_main']) {
                $group['tags'] = $group['tags']->merge($mainTags);
            }

            return $group;
        });
    }
}

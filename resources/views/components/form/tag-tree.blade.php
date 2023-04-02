<div {{ $attributes->except('tag') }} x-data="{ open{{ $tag->id }}: false }">
    <div class="flex items-center">
        <x-form.checkbox
            :label="$tag->name"
            wire:model.defer="fields.tags.{{ $tag->id }}"
            x-model="open{{ $tag->id }}"
        />

        <span
            class="ml-2 p-2 text-xl text-primary cursor-pointer rounded hover:bg-dark-hover hover:text-text"
            x-on:click="window.openModal('new-tag', {
                parent: {{ $tag->id }},
            })"
        >
            <x-heroicon-o-plus class="w-4 h-4" />
        </span>
    </div>

    @if ($tag->children->isNotEmpty())
        <div class="px-4 pt-2 flex flex-col gap-2" x-show="open{{ $tag->id }}">
            @foreach ($tag->children as $tag)
                <x-form.tag-tree :$tag />
            @endforeach
        </div>
    @endif
</div>

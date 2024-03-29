<?php

namespace App\Livewire\Folder;

use App\Livewire\Traits\HasPreLoading;
use App\Models\DynamicFolder;
use App\Models\Folder;
use Illuminate\Support\Collection;
use Livewire\Component;

class Index extends Component
{
    use HasPreLoading;

    public Collection $folders;

    public Collection $dynamicFolders;

    protected $listeners = [
        'refresh' => '$refresh',
    ];

    public function ready()
    {
        $this->loaded = true;

        $this->folders = Folder::orderBy('name')->get();
        $this->dynamicFolders = DynamicFolder::orderBy('name')->get();
    }

    public function render()
    {
        app('site')->title('Folders');

        if (! $this->loaded) {
            return $this->renderLoadingGridContainer(18);
        }

        return view('livewire.folder.index');
    }
}

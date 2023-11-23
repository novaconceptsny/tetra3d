@props([
    'surfaceId',
    'layoutId'
])
<div class="editor-actions d-flex" style="top: 92px">
    <button class="action btn btn-sm px-2" data-bs-toggle="modal" data-bs-target="#confirmation_modal"
            data-text="Save As">
        <i class="fa fal fa-folder-upload text-black"></i>
    </button>
    <a id="save_btn" class="action btn btn-sm hide px-2" href="javascript:void(0)" data-text="Save Changes"><i
            class="fa fal fa-floppy-disk text-black"></i></a>
    <a id="remove_btn" class="action btn btn-sm hide px-2" href="javascript:void(0)" data-text="Remove"><i
            class="fa fal fa-window-close text-black"></i></a>
    <a class="action btn btn-sm px-2"
       href="{{ route('surfaces.show', [$surfaceId, 'layout_id' => $layoutId, 'new' => 1]) }}"
       data-text="Add New Version">
        <i class="fa fal fa-plus text-black"></i>
    </a>
</div>

<div>
    <!-- Button to Open Modal for Creating Post -->
    <button wire:click="create" class="bg-blue-500 text-white px-4 py-2 rounded">
        Create Post
    </button>

    <!-- Modal for Creating/Editing Post -->
    <div x-data="{ isOpen: @entangle('isOpen') }" x-show="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg w-1/2">
            <h2 class="text-xl font-bold mb-4">{{ $postId ? 'Edit Post' : 'Create Post' }}</h2>
            <form wire:submit.prevent="store">
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" wire:model="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                    <textarea wire:model="content" id="content" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                    @error('content') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end">
                    <button type="button" wire:click="closeModal" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- List of Posts -->
    <div class="mt-6">
        @foreach ($posts as $post)
            <div class="bg-white shadow-md rounded-lg p-4 mb-4">
                <h3 class="text-lg font-bold">{{ $post->title }}</h3>
                <p class="text-gray-700">{{ $post->content }}</p>
                <div class="mt-2">
                    <button wire:click="edit({{ $post->id }})" class="bg-yellow-500 text-white px-4 py-2 rounded">
                        Edit
                    </button>
                    <button wire:click="delete({{ $post->id }})" class="bg-red-500 text-white px-4 py-2 rounded ml-2">
                        Delete
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>

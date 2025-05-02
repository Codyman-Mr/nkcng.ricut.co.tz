<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Post;

class PostsForm extends Component
{

    public $posts, $title, $content, $postId;
    public $isOpen = false;

    public function render()
    {
        $this->posts = Post::all();
        return view('livewire.posts-form');
    }

    public function create(){
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal(){
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function resetInputFields(){
        $this->title = '';
        $this->content = '';
        $this->postId = '';
    }

    public function store(){
        $this->validate([
            'title'=> 'required',
            'content'=> 'required',
        ]);

        Post::updateOrCreate(['id' => $this->postId], [
            'title' => $this->title,
            'content' => $this->content
        ]);

        session()->flash('message', $this->postId ? 'Post Updated Successfully' : 'Post Created Successfully');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id){
        $post = Post::findOrFail($id);
        $this->postId = $id;
        $this->title = $post->title;
        $this->content = $post->content;

        $this->openModal();
        }

    public function delete($id){
        Post::find($id)->delete();
        session()->flash('message', 'Post Deleted Successfully');
    }
}

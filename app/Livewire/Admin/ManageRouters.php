<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Router;
use Illuminate\Support\Facades\Hash;

class ManageRouters extends Component
{
    public $routers;
    public $name, $host, $username, $password, $port = 8728;
    public $routerId;
    public $isModalOpen = false;
    public $isEditing = false;

    public function render()
    {
        $this->routers = Router::all();
        return view('livewire.admin.manage-routers')->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isEditing = false;
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $router = Router::find($id);
        $this->routerId = $id;
        $this->name = $router->name;
        $this->host = $router->host;
        $this->port = $router->port;
        $this->username = $router->username;
        $this->password = $router->password;
        
        $this->isEditing = true;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $validationRules = [
            'name' => 'required',
            'host' => 'required', // Removed |ip to allow domains
            'username' => 'required',
        ];

        // Password required only on create
        if (!$this->isEditing) {
            $validationRules['password'] = 'required';
        }

        $this->validate($validationRules);

        $data = [
            'user_id' => 1,
            'name' => $this->name,
            'host' => $this->host,
            // Removed ip_address as it is not in schema
            'port' => $this->port,
            'username' => $this->username,
        ];

        if ($this->password) {
            $data['password'] = $this->password;
        }

        if ($this->isEditing) {
            Router::find($this->routerId)->update($data);
            session()->flash('message', 'Router Updated Successfully.');
        } else {
            Router::create($data);
            session()->flash('message', 'Router Created Successfully.');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        Router::find($id)->delete();
        session()->flash('message', 'Router Deleted Successfully.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->host = '';
        $this->username = '';
        $this->password = '';
        $this->port = 8728;
    }
}

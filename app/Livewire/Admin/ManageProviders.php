<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\AiProvider;

class ManageProviders extends Component
{
    public $providers;
    public $name, $base_url, $api_key, $model_identifier;
    public $type = 'standard'; // standard, gemini_interaction
    public $config = []; // Dynamic config array
    public $system_instruction; 
    
    // Interface Helpers
    public $selectedAgent = 'deep-research-pro-preview-12-2025';
    public $thinkingLevel = 'high';

    public $fetchedModels = [];
    public $isModalOpen = false;
    public $isEditing = false;
    public $providerIdBeingEdited = null;

    public function updatedType($value)
    {
        $this->fetchedModels = [];
        $this->model_identifier = '';
    }

    public function render()
    {
        $this->providers = AiProvider::all();
        return view('livewire.admin.manage-providers')->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
        $this->isEditing = false;
    }

    public function edit($id)
    {
        $provider = AiProvider::findOrFail($id);
        $this->providerIdBeingEdited = $id;
        $this->name = $provider->name;
        $this->base_url = $provider->base_url;
        $this->api_key = $provider->api_key;
        $this->model_identifier = $provider->model_identifier;
        $this->type = $provider->type ?? 'standard';
        $this->system_instruction = $provider->system_instruction;
        
        $config = $provider->config ?? [];
        $this->selectedAgent = $config['agent'] ?? 'deep-research-pro-preview-12-2025';
        $this->thinkingLevel = $config['thinking_level'] ?? 'high';
        
        $this->isEditing = true;
        $this->isModalOpen = true;
    }

    public function applyPreset($preset)
    {
        switch ($preset) {
            case 'gemini':
                $this->name = 'Google Gemini';
                $this->base_url = 'https://generativelanguage.googleapis.com/v1beta/openai/';
                break;
            case 'openai':
                $this->name = 'OpenAI';
                $this->base_url = 'https://api.openai.com/v1';
                break;
            case 'mistral':
                // Default to standard, but user can switch to Agent type
                $this->name = 'Mistral AI';
                $this->base_url = 'https://api.mistral.ai/v1';
                $this->type = 'standard'; 
                break;
            case 'ollama':
                $this->name = 'Local Ollama';
                $this->base_url = 'http://localhost:11434/v1';
                break;
        }
    }

    public function fetchModels()
    {
        $this->validate([
            'base_url' => 'required|url',
            'api_key' => 'required',
        ]);

        try {
            // OpenAI Compatible Models Endpoint
            // Ensure no double slashes if base_url ends with /
            $baseUrl = rtrim($this->base_url, '/');
            $url = $baseUrl . '/models';
            
            // Adjust for Mistral Agents
            if ($this->type === 'mistral_agent') {
                $url = $baseUrl . '/agents';
            }
            
            // Log::info("Fetching models from: " . $url);

            $response = \Illuminate\Support\Facades\Http::withToken(trim($this->api_key))->get($url);

            if ($response->successful()) {
                $data = $response->json();
                
                // OpenAI/Gemini format: { "data": [ { "id": "model-name", ... } ] }
                if (isset($data['data']) && is_array($data['data'])) {
                     $this->fetchedModels = collect($data['data'])
                        ->pluck('id')
                        ->toArray();
                } else {
                     // Fallback for some non-standard responses
                     $this->fetchedModels = [];
                     session()->flash('error', 'Unexpected API response format.');
                }
                
                if (empty($this->fetchedModels)) {
                    session()->flash('error', 'No models found in API response.');
                }
            } else {
                session()->flash('error', 'API Error: ' . $response->status() . ' - ' . $response->body());
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Connection Failed: ' . $e->getMessage());
        }
    }

    public function store()
    {
        $this->name = trim($this->name);
        $this->base_url = trim($this->base_url);
        $this->api_key = trim($this->api_key);
        $this->model_identifier = trim($this->model_identifier);

        $this->validate([
            'name' => 'required',
            'base_url' => 'required|url',
        ]);

        if ($this->type == 'standard') {
             $this->validate(['model_identifier' => 'required']);
             $config = null;
        } else {
             // Interaction API Config
             $config = [
                 'agent' => $this->selectedAgent,
                 'thinking_level' => $this->thinkingLevel
             ];
             // Initial model can be empty if agent is selected, but database likely needs something.
             // We'll use the agent name as identifier for display purposes if model is empty.
             if (empty($this->model_identifier)) {
                 $this->model_identifier = $this->selectedAgent;
             }
        }

        if ($this->isEditing && $this->providerIdBeingEdited) {
            $provider = AiProvider::find($this->providerIdBeingEdited);
            $provider->update([
                'name' => $this->name,
                'base_url' => $this->base_url,
                'api_key' => $this->api_key,
                'model_identifier' => $this->model_identifier,
                'type' => $this->type,
                'config' => $config,
                'system_instruction' => $this->system_instruction,
            ]);
            session()->flash('message', 'AI Provider Updated Successfully.');
        } else {
            AiProvider::create([
                'name' => $this->name,
                'base_url' => $this->base_url,
                'api_key' => $this->api_key,
                'model_identifier' => $this->model_identifier,
                'type' => $this->type,
                'config' => $config,
                'system_instruction' => $this->system_instruction,
            ]);
            session()->flash('message', 'AI Provider Created Successfully.');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        AiProvider::find($id)->delete();
        session()->flash('message', 'AI Provider Deleted Successfully.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->isEditing = false;
        $this->fetchedModels = [];
        $this->providerIdBeingEdited = null;
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->base_url = '';
        $this->api_key = '';
        $this->model_identifier = '';
        $this->fetchedModels = [];
        $this->isEditing = false;
        $this->providerIdBeingEdited = null;
    }
}

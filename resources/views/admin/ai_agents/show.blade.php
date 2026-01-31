@extends('layouts.app')

@section('title', __('View AI Agent'))

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ $aiAgent->name ?? __('AI Agent') }}</h1>
            <div class="space-x-2">
                @can('manage', $aiAgent)
                    <a href="{{ route('admin.ai_agents.edit', $aiAgent) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> {{ __('Edit') }}
                    </a>
                @endcan
                <a href="{{ route('admin.ai_agents.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>
        </div>

        <!-- AI Agent Details -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="md:col-span-2">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">{{ __('AI Agent Configuration') }}</h5>

                        <div class="mb-3">
                            <label class="form-label text-muted">{{ __('Restaurant') }}</label>
                            <p class="form-control-plaintext">
                                <a href="{{ route('restaurants.show', $aiAgent->restaurant) }}">
                                    {{ $aiAgent->restaurant->name }}
                                </a>
                            </p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Model') }}</label>
                                <p class="form-control-plaintext">
                                    <code>{{ $aiAgent->model ?? 'gpt-4' }}</code>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Temperature') }}</label>
                                <p class="form-control-plaintext">{{ $aiAgent->temperature ?? 0.7 }}</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">{{ __('Voice') }}</label>
                            <p class="form-control-plaintext">{{ $aiAgent->voice ?? __('N/A') }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">{{ __('System Prompt') }}</label>
                            <div class="form-control" style="height: auto; background-color: #f8f9fa;">
                                {{ $aiAgent->system_prompt ?? __('N/A') }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">{{ __('Greeting Message') }}</label>
                            <p class="form-control-plaintext">{{ $aiAgent->greeting_message ?? __('N/A') }}</p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Total Calls') }}</label>
                                <p class="form-control-plaintext">{{ $aiAgent->total_calls ?? 0 }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Total Tokens Used') }}</label>
                                <p class="form-control-plaintext">{{ number_format($aiAgent->total_tokens_used ?? 0) }}</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">{{ __('Last Active') }}</label>
                            <p class="form-control-plaintext">
                                @if ($aiAgent->last_active_at)
                                    {{ $aiAgent->last_active_at->format('d M Y H:i') }}
                                @else
                                    {{ __('Never') }}
                                @endif
                            </p>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Created At') }}</label>
                                <p class="form-control-plaintext">{{ $aiAgent->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Last Updated') }}</label>
                                <p class="form-control-plaintext">{{ $aiAgent->updated_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conversation Statistics -->
                @if ($aiAgent->conversation_stats)
                    <div class="card shadow-sm mt-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">{{ __('Conversation Statistics') }}</h5>
                            <div class="row">
                                @foreach ($aiAgent->conversation_stats as $key => $value)
                                    <div class="col-md-6 mb-3">
                                        <label
                                            class="form-label text-muted">{{ ucfirst(str_replace('_', ' ', $key)) }}</label>
                                        <p class="form-control-plaintext">{{ $value }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Status -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">{{ __('Status') }}</h5>
                        <p class="mb-0">
                            <span class="badge bg-success">{{ __('Active') }}</span>
                        </p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">{{ __('Quick Actions') }}</h5>
                        @can('manage', $aiAgent)
                            <form action="{{ route('admin.ai_agents.destroy', $aiAgent) }}" method="POST"
                                onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-trash"></i> {{ __('Delete') }}
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>

                <!-- Related Restaurant -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">{{ __('Related Restaurant') }}</h5>
                        <p>
                            <a href="{{ route('restaurants.show', $aiAgent->restaurant) }}"
                                class="btn btn-sm btn-info w-100">
                                <i class="fas fa-store"></i> {{ $aiAgent->restaurant->name }}
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

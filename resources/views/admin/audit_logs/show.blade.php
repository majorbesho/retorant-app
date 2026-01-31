@extends('layouts.app')

@section('title', __('View Audit Log'))

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ __('Audit Log Details') }}</h1>
            <a href="{{ route('admin.audit_logs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('Back') }}
            </a>
        </div>

        <!-- Audit Log Details -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="md:col-span-2">
                <!-- Basic Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">{{ __('Audit Information') }}</h5>

                        <div class="mb-3">
                            <label class="form-label text-muted">{{ __('Event') }}</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-info">{{ $auditLog->event }}</span>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">{{ __('Model') }}</label>
                            <p class="form-control-plaintext"><code>{{ $auditLog->auditable_type }}</code></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">{{ __('Model ID') }}</label>
                            <p class="form-control-plaintext">{{ $auditLog->auditable_id }}</p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('User') }}</label>
                                <p class="form-control-plaintext">
                                    @if ($auditLog->user)
                                        {{ $auditLog->user->name }}
                                    @else
                                        <span class="text-muted">{{ __('System') }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('IP Address') }}</label>
                                <p class="form-control-plaintext">{{ $auditLog->ip_address ?? __('N/A') }}</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">{{ __('Timestamp') }}</label>
                            <p class="form-control-plaintext">{{ $auditLog->created_at->format('d M Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Old Values -->
                @if ($auditLog->old_values && count($auditLog->old_values) > 0)
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">{{ __('Old Values') }}</h5>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Field') }}</th>
                                            <th>{{ __('Value') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($auditLog->old_values as $field => $value)
                                            <tr>
                                                <td><strong>{{ $field }}</strong></td>
                                                <td>
                                                    <code
                                                        class="text-danger">{{ is_array($value) ? json_encode($value) : $value }}</code>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- New Values -->
                @if ($auditLog->new_values && count($auditLog->new_values) > 0)
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-4">{{ __('New Values') }}</h5>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Field') }}</th>
                                            <th>{{ __('Value') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($auditLog->new_values as $field => $value)
                                            <tr>
                                                <td><strong>{{ $field }}</strong></td>
                                                <td>
                                                    <code
                                                        class="text-success">{{ is_array($value) ? json_encode($value) : $value }}</code>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Event Type Badge -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">{{ __('Event Type') }}</h5>
                        @if ($auditLog->event === 'created')
                            <p><span class="badge bg-success">{{ __('Created') }}</span></p>
                        @elseif($auditLog->event === 'updated')
                            <p><span class="badge bg-info">{{ __('Updated') }}</span></p>
                        @elseif($auditLog->event === 'deleted')
                            <p><span class="badge bg-danger">{{ __('Deleted') }}</span></p>
                        @elseif($auditLog->event === 'restored')
                            <p><span class="badge bg-warning">{{ __('Restored') }}</span></p>
                        @else
                            <p><span class="badge bg-secondary">{{ ucfirst($auditLog->event) }}</span></p>
                        @endif
                    </div>
                </div>

                <!-- Metadata -->
                @if ($auditLog->metadata)
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">{{ __('Metadata') }}</h5>
                            <div class="small">
                                <pre style="background-color: #f8f9fa; padding: 10px; border-radius: 4px; font-size: 11px;">{{ json_encode($auditLog->metadata, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Change Summary -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">{{ __('Summary') }}</h5>
                        <div class="small">
                            <p class="mb-2">
                                <strong>{{ __('Event') }}:</strong> {{ ucfirst($auditLog->event) }}
                            </p>
                            <p class="mb-2">
                                <strong>{{ __('Model') }}:</strong> {{ class_basename($auditLog->auditable_type) }}
                            </p>
                            @if ($auditLog->old_values && count($auditLog->old_values) > 0)
                                <p class="mb-2">
                                    <strong>{{ __('Fields Changed') }}:</strong> {{ count($auditLog->old_values) }}
                                </p>
                            @endif
                            <p>
                                <strong>{{ __('Date') }}:</strong> {{ $auditLog->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

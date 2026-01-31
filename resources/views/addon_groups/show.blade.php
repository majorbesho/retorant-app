@extends('layouts.app')

@section('title', __('View Addon Group'))

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ $addonGroup->name }}</h1>
            <div class="space-x-2">
                <a href="{{ route('addon_groups.edit', $addonGroup) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> {{ __('Edit') }}
                </a>
                <a href="{{ route('addon_groups.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>
        </div>

        <!-- Addon Group Details -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="md:col-span-2">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">{{ __('Addon Group Information') }}</h5>

                        <div class="mb-3">
                            <label class="form-label text-muted">{{ __('Name') }}</label>
                            <p class="form-control-plaintext">{{ $addonGroup->name }}</p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Selection Type') }}</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-info">{{ $addonGroup->selection_type ?? 'Single' }}</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Is Required') }}</label>
                                <p class="form-control-plaintext">
                                    @if ($addonGroup->is_required)
                                        <span class="badge bg-warning">{{ __('Required') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Optional') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">{{ __('Description') }}</label>
                            <p class="form-control-plaintext">{{ $addonGroup->description ?? __('N/A') }}</p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Created At') }}</label>
                                <p class="form-control-plaintext">{{ $addonGroup->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Last Updated') }}</label>
                                <p class="form-control-plaintext">{{ $addonGroup->updated_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Addons -->
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <div class="flex justify-between items-center mb-4">
                            <h5 class="card-title mb-0">{{ __('Addons') }}</h5>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAddonModal">
                                <i class="fas fa-plus"></i> {{ __('Add Addon') }}
                            </button>
                        </div>

                        @if ($addonGroup->addons->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Addon Name') }}</th>
                                            <th>{{ __('Price') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($addonGroup->addons as $addon)
                                            <tr>
                                                <td>{{ $addon->name }}</td>
                                                <td>{{ number_format($addon->price, 2) }} SAR</td>
                                                <td>
                                                    @if ($addon->is_active)
                                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <form action="{{ route('addons.destroy', $addon) }}" method="POST"
                                                        style="display:inline;"
                                                        onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">{{ __('No addons found') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Quick Actions -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">{{ __('Quick Actions') }}</h5>
                        <form action="{{ route('addon_groups.destroy', $addonGroup) }}" method="POST"
                            onsubmit="return confirm('{{ __('Are you sure?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash"></i> {{ __('Delete Addon Group') }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">{{ __('Statistics') }}</h5>
                        <div>
                            <label class="text-muted">{{ __('Total Addons') }}</label>
                            <p class="h4">{{ $addonGroup->addons->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Addon Modal -->
        <div class="modal fade" id="addAddonModal" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('addon_groups.store_addon', $addonGroup) }}" method="POST" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Add New Addon') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Addon Name') }} *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Price') }} *</label>
                            <input type="number" name="price" class="form-control" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-check-label">
                                <input type="checkbox" name="is_active" class="form-check-input" checked>
                                {{ __('Active') }}
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Add Addon') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

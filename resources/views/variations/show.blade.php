@extends('layouts.app')

@section('title', __('View Variation'))

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ $variation->name }}</h1>
            @can('variation-management')
                <div class="space-x-2">
                    <a href="{{ route('variations.edit', $variation) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> {{ __('Edit') }}
                    </a>
                    <a href="{{ route('variations.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                    </a>
                </div>
            @endcan
        </div>

        <!-- Variation Details -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="md:col-span-2">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">{{ __('Variation Information') }}</h5>

                        <div class="mb-3">
                            <label class="form-label text-muted">{{ __('Name') }}</label>
                            <p class="form-control-plaintext">{{ $variation->name }}</p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Type') }}</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-info">{{ $variation->type ?? 'N/A' }}</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Status') }}</label>
                                <p class="form-control-plaintext">
                                    @if ($variation->is_active)
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">{{ __('Description') }}</label>
                            <p class="form-control-plaintext">{{ $variation->description ?? __('N/A') }}</p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Created At') }}</label>
                                <p class="form-control-plaintext">{{ $variation->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Last Updated') }}</label>
                                <p class="form-control-plaintext">{{ $variation->updated_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Variation Options -->
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <div class="flex justify-between items-center mb-4">
                            <h5 class="card-title mb-0">{{ __('Variation Options') }}</h5>
                            @can('variation-management')
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addOptionModal">
                                    <i class="fas fa-plus"></i> {{ __('Add Option') }}
                                </button>
                            @endcan
                        </div>

                        @if ($variation->options->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Option Name') }}</th>
                                            <th>{{ __('Additional Price') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($variation->options as $option)
                                            <tr>
                                                <td>{{ $option->name }}</td>
                                                <td>{{ number_format($option->additional_price ?? 0, 2) }} SAR</td>
                                                <td>
                                                    @if ($option->is_active)
                                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @can('variation-management')
                                                        <form action="{{ route('variation_options.destroy', $option) }}"
                                                            method="POST" style="display:inline;"
                                                            onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-danger">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">{{ __('No options found') }}</p>
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
                        @can('variation-management')
                            <form action="{{ route('variations.destroy', $variation) }}" method="POST"
                                onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-trash"></i> {{ __('Delete Variation') }}
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">{{ __('Statistics') }}</h5>
                        <div>
                            <label class="text-muted">{{ __('Total Options') }}</label>
                            <p class="h4">{{ $variation->options->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Option Modal -->
        @can('variation-management')
            <div class="modal fade" id="addOptionModal" tabindex="-1">
                <div class="modal-dialog">
                    <form action="{{ route('variations.store_option', $variation) }}" method="POST" class="modal-content">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('Add New Option') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Option Name') }} *</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Additional Price') }}</label>
                                <input type="number" name="additional_price" class="form-control" step="0.01">
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
                            <button type="submit" class="btn btn-primary">{{ __('Add Option') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        @endcan
    </div>
@endsection

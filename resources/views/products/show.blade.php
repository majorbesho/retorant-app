@extends('layouts.app')

@section('title', __('View Product'))

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
            @can('product-management')
                <div class="space-x-2">
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> {{ __('Edit') }}
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                    </a>
                </div>
            @endcan
        </div>

        <!-- Product Details -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="md:col-span-2">
                <!-- Product Image -->
                @if ($product->image)
                    <div class="card shadow-sm mb-4">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="card-img-top"
                            style="max-height: 300px; object-fit: cover;">
                    </div>
                @endif

                <!-- Product Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">{{ __('Product Information') }}</h5>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Name') }}</label>
                                <p class="form-control-plaintext">{{ $product->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Category') }}</label>
                                <p class="form-control-plaintext">
                                    @if ($product->category)
                                        <a
                                            href="{{ route('categories.show', $product->category) }}">{{ $product->category->name }}</a>
                                    @else
                                        {{ __('N/A') }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">{{ __('Description') }}</label>
                            <p class="form-control-plaintext">{{ $product->description ?? __('N/A') }}</p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Price') }}</label>
                                <p class="form-control-plaintext h5 text-success">{{ number_format($product->price, 2) }}
                                    SAR</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Quantity in Stock') }}</label>
                                <p class="form-control-plaintext h5">
                                    <span class="badge bg-{{ $product->quantity > 0 ? 'success' : 'danger' }}">
                                        {{ $product->quantity }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Status') }}</label>
                                <p class="form-control-plaintext">
                                    @if ($product->is_active)
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Display Order') }}</label>
                                <p class="form-control-plaintext">{{ $product->display_order ?? 'N/A' }}</p>
                            </div>
                        </div>

                        @if ($product->discount)
                            <div class="alert alert-info mb-0">
                                <strong>{{ __('Discount') }}:</strong> {{ number_format($product->discount, 2) }}%
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Variations -->
                @if ($product->variations->count() > 0)
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-4">{{ __('Variations') }}</h5>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Variation') }}</th>
                                            <th>{{ __('Options') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($product->variations as $variation)
                                            <tr>
                                                <td>{{ $variation->name }}</td>
                                                <td>{{ $variation->options->count() }} options</td>
                                                <td>
                                                    <a href="{{ route('variations.show', $variation) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Addon Groups -->
                @if ($product->addonGroups->count() > 0)
                    <div class="card shadow-sm mt-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">{{ __('Addon Groups') }}</h5>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Group Name') }}</th>
                                            <th>{{ __('Addons Count') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($product->addonGroups as $group)
                                            <tr>
                                                <td>{{ $group->name }}</td>
                                                <td>{{ $group->addons->count() }}</td>
                                                <td>
                                                    <a href="{{ route('addon_groups.show', $group) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
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
                <!-- Quick Actions -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">{{ __('Quick Actions') }}</h5>
                        @can('product-management')
                            <form action="{{ route('products.destroy', $product) }}" method="POST"
                                onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-trash"></i> {{ __('Delete Product') }}
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">{{ __('Statistics') }}</h5>
                        <div class="mb-3">
                            <label class="text-muted">{{ __('Variations') }}</label>
                            <p class="h4">{{ $product->variations->count() }}</p>
                        </div>
                        <div>
                            <label class="text-muted">{{ __('Addon Groups') }}</label>
                            <p class="h4">{{ $product->addonGroups->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Timestamps -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">{{ __('Timestamps') }}</h5>
                        <div class="mb-2">
                            <label class="text-muted small">{{ __('Created') }}</label>
                            <p class="small">{{ $product->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="text-muted small">{{ __('Updated') }}</label>
                            <p class="small">{{ $product->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

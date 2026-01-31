@extends('layouts.app')

@section('title', __('View Category'))

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ $category->name }}</h1>
            @can('category-management')
                <div class="space-x-2">
                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> {{ __('Edit') }}
                    </a>
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                    </a>
                </div>
            @endcan
        </div>

        <!-- Category Details -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="md:col-span-2">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">{{ __('Category Information') }}</h5>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Name') }}</label>
                                <p class="form-control-plaintext">{{ $category->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Menu') }}</label>
                                <p class="form-control-plaintext">
                                    @if ($category->menu)
                                        <a
                                            href="{{ route('menus.show', $category->menu) }}">{{ $category->menu->name }}</a>
                                    @else
                                        {{ __('N/A') }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Status') }}</label>
                                <p class="form-control-plaintext">
                                    @if ($category->is_active)
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Display Order') }}</label>
                                <p class="form-control-plaintext">{{ $category->display_order ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">{{ __('Description') }}</label>
                            <p class="form-control-plaintext">{{ $category->description ?? __('N/A') }}</p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Created At') }}</label>
                                <p class="form-control-plaintext">{{ $category->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Last Updated') }}</label>
                                <p class="form-control-plaintext">{{ $category->updated_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products in this Category -->
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">{{ __('Products in this Category') }}</h5>

                        @if ($category->products->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Product Name') }}</th>
                                            <th>{{ __('Price') }}</th>
                                            <th>{{ __('Quantity') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($category->products as $product)
                                            <tr>
                                                <td>{{ $product->name }}</td>
                                                <td>{{ number_format($product->price, 2) }} SAR</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $product->quantity > 0 ? 'success' : 'danger' }}">
                                                        {{ $product->quantity }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($product->is_active)
                                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('products.show', $product) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">{{ __('No products found in this category') }}</p>
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
                        @can('category-management')
                            <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-trash"></i> {{ __('Delete Category') }}
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">{{ __('Statistics') }}</h5>
                        <div class="mb-3">
                            <label class="text-muted">{{ __('Total Products') }}</label>
                            <p class="h4">{{ $category->products->count() }}</p>
                        </div>
                        <div>
                            <label class="text-muted">{{ __('Available Products') }}</label>
                            <p class="h4">{{ $category->products->where('quantity', '>', 0)->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

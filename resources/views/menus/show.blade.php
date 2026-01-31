@extends('layouts.app')

@section('title', __('View Menu'))

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ $menu->name }}</h1>
            @can('menu-management')
                <div class="space-x-2">
                    <a href="{{ route('menus.edit', $menu) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> {{ __('Edit') }}
                    </a>
                    <a href="{{ route('menus.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                    </a>
                </div>
            @endcan
        </div>

        <!-- Menu Details -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="md:col-span-2">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">{{ __('Menu Information') }}</h5>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Name') }}</label>
                                <p class="form-control-plaintext">{{ $menu->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Status') }}</label>
                                <p class="form-control-plaintext">
                                    @if ($menu->is_active)
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">{{ __('Description') }}</label>
                            <p class="form-control-plaintext">{{ $menu->description ?? __('N/A') }}</p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Created At') }}</label>
                                <p class="form-control-plaintext">{{ $menu->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">{{ __('Last Updated') }}</label>
                                <p class="form-control-plaintext">{{ $menu->updated_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Categories in this Menu -->
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">{{ __('Categories') }}</h5>

                        @if ($menu->categories->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Category Name') }}</th>
                                            <th>{{ __('Products') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($menu->categories as $category)
                                            <tr>
                                                <td>{{ $category->name }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ $category->products->count() }}</span>
                                                </td>
                                                <td>
                                                    @if ($category->is_active)
                                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('categories.show', $category) }}"
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
                            <p class="text-muted">{{ __('No categories found') }}</p>
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
                        @can('menu-management')
                            <form action="{{ route('menus.destroy', $menu) }}" method="POST"
                                onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-trash"></i> {{ __('Delete Menu') }}
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
                            <label class="text-muted">{{ __('Total Categories') }}</label>
                            <p class="h4">{{ $menu->categories->count() }}</p>
                        </div>
                        <div>
                            <label class="text-muted">{{ __('Total Products') }}</label>
                            <p class="h4">{{ $menu->categories->sum(fn($c) => $c->products->count()) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

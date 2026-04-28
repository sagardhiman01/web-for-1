@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card b-radius--10">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h5 class="mb-1">@lang('Current Active Code')</h5>
                            <h3 class="mb-0 text--primary">{{ $activeCode->code ?? 'N/A' }}</h3>
                            @if($activeCode)
                                <small class="text-muted d-block">
                                    @lang('Used'):
                                    {{ $activeCode->redemptions_count }} / {{ $activeCode->max_users ? $activeCode->max_users : __('Unlimited') }}
                                    |
                                    @lang('Max Users'):
                                    {{ $activeCode->max_users ? $activeCode->max_users : __('Unlimited') }}
                                    |
                                    @lang('Valid Till'):
                                    {{ $activeCode->expires_at ? showDateTime($activeCode->expires_at) : __('N/A') }}
                                </small>
                            @else
                                <small class="text-muted">
                                    @lang('Generate a code and share with users')
                                </small>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('admin.investment.code.generate') }}" class="row g-2 align-items-end">
                            @csrf
                            <div class="col-auto">
                                <label class="form-label mb-1">@lang('Max Users')</label>
                                <input type="number" min="1" name="max_users" class="form-control" placeholder="@lang('Unlimited if empty')">
                            </div>
                            <div class="col-auto">
                                <label class="form-label mb-1">@lang('Valid Hours')</label>
                                <input type="number" min="1" name="valid_hours" value="24" class="form-control" required>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn--primary">
                                    <i class="las la-random"></i> @lang('Generate 8-Character Code')
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-header">
                    <h5 class="mb-0">@lang('Generated Code History')</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Code')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Redeemed Users')</th>
                                    <th>@lang('Max Users')</th>
                                    <th>@lang('Valid Hours')</th>
                                    <th>@lang('Valid Till')</th>
                                    <th>@lang('Created')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($codes as $code)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $code->code }}</span>
                                        </td>
                                        <td>
                                            @if($code->status)
                                                <span class="badge badge--success">@lang('Active')</span>
                                            @else
                                                <span class="badge badge--dark">@lang('Inactive')</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $code->redemptions_count }} / {{ $code->max_users ? $code->max_users : __('Unlimited') }}
                                        </td>
                                        <td>
                                            {{ $code->max_users ? $code->max_users : __('Unlimited') }}
                                        </td>
                                        <td>
                                            {{ $code->valid_hours ?? 24 }}
                                        </td>
                                        <td>
                                            {{ $code->expires_at ? showDateTime($code->expires_at) : __('N/A') }}
                                        </td>
                                        <td>
                                            {{ showDateTime($code->created_at) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center text-muted">@lang('No code generated yet')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($codes->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($codes) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

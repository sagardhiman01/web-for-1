@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two custom-data-table">
                            <thead>
                            <tr>
                                <th>@lang('Image')</th>
                                <th>@lang('Name')</th>
                                <th>@lang('Base Price')</th>
                                <th>@lang('Current Price')</th>
                                <th>@lang('VIP Level')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($nfts as $nft)
                                <tr>
                                    <td>
                                        <div class="user">
                                            <div class="thumb">
                                                <img src="{{ asset('assets/images/nft/' . $nft->image) }}" alt="image" style="width: 50px; height: 50px; object-fit: cover;">
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="fw-bold">{{ __($nft->name) }}</span></td>
                                    <td>{{ $general->cur_sym }}{{ showAmount($nft->base_price) }}</td>
                                    <td>{{ $general->cur_sym }}{{ showAmount($nft->current_price) }}</td>
                                    <td><span class="badge badge--primary">@lang('Level') {{ $nft->level_id }}</span></td>
                                    <td>
                                        @if($nft->status == 'available')
                                            <span class="badge badge--success">@lang('Available')</span>
                                        @elseif($nft->status == 'reserved')
                                            <span class="badge badge--warning">@lang('Reserved')</span>
                                        @else
                                            <span class="badge badge--danger">@lang('Trading')</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="button--group">
                                            <button class="btn btn-sm btn-outline--primary editBtn"
                                                    data-id="{{ $nft->id }}"
                                                    data-name="{{ $nft->name }}"
                                                    data-base_price="{{ getAmount($nft->base_price) }}"
                                                    data-level_id="{{ $nft->level_id }}"
                                                    data-status="{{ $nft->status }}">
                                                <i class="la la-pencil"></i> @lang('Edit')
                                            </button>
                                            
                                            <button class="btn btn-sm btn-outline--danger deleteBtn" data-id="{{ $nft->id }}">
                                                <i class="la la-trash"></i> @lang('Delete')
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($nfts->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($nfts) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    <div id="nftModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add NFT')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.nft.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Base Price')</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $general->cur_sym }}</span>
                                <input type="number" step="any" class="form-control" name="base_price" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('VIP Level')</label>
                            <input type="number" class="form-control" name="level_id" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Image')</label>
                            <input type="file" class="form-control" name="image" accept=".jpg,.png,.jpeg">
                        </div>
                        <div class="form-group">
                            <label>@lang('Status')</label>
                            <select class="form-control" name="status" required>
                                <option value="available">@lang('Available')</option>
                                <option value="reserved">@lang('Reserved')</option>
                                <option value="trading">@lang('Trading')</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div id="deleteModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Delete NFT Confirmation')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>@lang('Are you sure you want to delete this NFT?')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                        <button type="submit" class="btn btn--danger">@lang('Delete')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-sm btn-outline--primary addBtn">
        <i class="las la-plus"></i> @lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($){
            "use strict";

            $('.addBtn').on('click', function () {
                var modal = $('#nftModal');
                modal.find('.modal-title').text("@lang('Add New NFT')");
                modal.find('form').attr('action', `{{ route('admin.nft.store') }}`);
                modal.find('form')[0].reset();
                modal.modal('show');
            });

            $('.editBtn').on('click', function () {
                var modal = $('#nftModal');
                modal.find('.modal-title').text("@lang('Update NFT')");
                modal.find('form').attr('action', `{{ route('admin.nft.update', '') }}/${$(this).data('id')}`);
                modal.find('[name=name]').val($(this).data('name'));
                modal.find('[name=base_price]').val($(this).data('base_price'));
                modal.find('[name=level_id]').val($(this).data('level_id'));
                modal.find('[name=status]').val($(this).data('status'));
                modal.modal('show');
            });

            $('.deleteBtn').on('click', function () {
                var modal = $('#deleteModal');
                modal.find('form').attr('action', `{{ route('admin.nft.delete', '') }}/${$(this).data('id')}`);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush

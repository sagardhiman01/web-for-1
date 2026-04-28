@extends($activeTemplate.'layouts.master')
@section('content')
<section class="pb-150 pt-150">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-bg">
                    <div class="card-body">
                        <form action="{{route('user.withdraw.submit')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            @php
                                $withdrawAddress = null;
                                $withdrawInfo = is_array($withdraw->withdraw_information) ? $withdraw->withdraw_information : [];
                                foreach ($withdrawInfo as $item) {
                                    $fieldName = '';
                                    $fieldValue = '';

                                    if (is_array($item)) {
                                        $fieldName = $item['name'] ?? '';
                                        $fieldValue = $item['value'] ?? '';
                                    } elseif (is_object($item)) {
                                        $fieldName = $item->name ?? '';
                                        $fieldValue = $item->value ?? '';
                                    }

                                    if (strtolower($fieldName) === strtolower('USDT BEP20 Address')) {
                                        $withdrawAddress = trim((string) $fieldValue);
                                        break;
                                    }
                                }
                            @endphp
                            @if($withdrawAddress)
                                <div class="alert alert-primary text-break mb-3">
                                    <strong>@lang('Withdrawal Address'):</strong> {{ $withdrawAddress }}
                                </div>
                            @endif
                            <div class="mb-2">
                                @php
                                    echo $withdraw->method->description;
                                @endphp
                            </div>
                            <x-viser-form identifier="id" identifierValue="{{ $withdraw->method->form_id }}" />
                            @if(auth()->user()->ts)
                            <div class="form-group">
                                <label>@lang('Google Authenticator Code')</label>
                                <input type="text" name="authenticator_code" class="form-control" required>
                            </div>
                            @endif
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary w-100">@lang('Submit')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@extends($activeTemplate.'layouts.master')
@section('content')
<section class="pt-150 pb-150">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-bg">
                    <div class="card-body  ">
                        <form action="{{ route('user.deposit.manual.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-center mt-2">@lang('You have requested') <b class="text-success">{{ showAmount($data['amount'])  }} {{__($general->cur_text)}}</b> , @lang('Please pay')
                                        <b class="text-success">{{showAmount($data['final_amo']) .' '.$data['method_currency'] }} </b> @lang('for successful payment')
                                    </p>

                                    @php
                                        $gatewayDescription = (string) ($data->gateway->description ?? '');
                                        preg_match('/0x[a-fA-F0-9]{40}/', strip_tags($gatewayDescription), $walletMatch);
                                        $depositWalletAddress = $walletMatch[0] ?? null;
                                        $gatewayParameters = json_decode((string) ($data->gateway->gateway_parameters ?? '{}'), true) ?? [];
                                        $uploadedQrImage = $gatewayParameters['qr_image'] ?? null;
                                    @endphp

                                    @if($uploadedQrImage || $depositWalletAddress)
                                        <div class="my-4 text-center">
                                            @if($uploadedQrImage)
                                                <img src="{{ getImage('assets/images/manual_gateway/' . $uploadedQrImage) }}" alt="Deposit QR" class="img-fluid rounded border p-2 bg-white" style="max-width:220px;">
                                            @else
                                                <img src="{{ cryptoQR($depositWalletAddress) }}" alt="USDT BEP20 QR" class="img-fluid rounded border p-2 bg-white" style="max-width:220px;">
                                            @endif
                                        </div>
                                    @endif

                                    <div class="manual-desc my-4">
                                        <p>@php echo  $data->gateway->description @endphp</p>
                                    </div>

                                </div>

                                <x-viser-form identifier="id" identifierValue="{{ $gateway->form_id }}" />

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary w-100">@lang('Pay Now')</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

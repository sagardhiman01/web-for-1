@extends($activeTemplate.'layouts.master')
@section('content')
@php
    $currentImage = $user->image ?: ($defaultAvatars[0] ?? '');
    $currentDefaultAvatar = in_array($user->image, $defaultAvatars ?? [], true) ? $user->image : null;
    $selectedDefaultAvatar = old('default_avatar', $currentDefaultAvatar);
@endphp
<div class="pt-150 pb-150">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-4 mb-30">

                <div class="card card-bg">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <img src="{{ getImage(getFilePath('userProfile').'/'.$currentImage, getFileSize('userProfile')) }}" alt="@lang('Profile Photo')" class="profile-dp-preview">
                        </div>
                        <h4 class="mb-2">{{ $user->fullname }}</h4>
                        <ul class="list-group">

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                               <span><i class="las la-user base--color"></i> @lang('Username')</span> <span class="fw-bold">{{ $user->username }}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="las la-envelope base--color"></i> @lang('Email')</span> <span class="fw-bold">{{ $user->email }}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="las la-phone base--color"></i> @lang('Mobile')</span> <span class="fw-bold">{{ $user->mobile }}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="las la-globe base--color"></i> @lang('Country')</span> <span class="fw-bold">{{ $user->address->country }}</span>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card card-bg">
                    <div class="card-body">
                        <form class="register" action="" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label class="form-label">@lang('First Name')</label>
                                    <input type="text"  name="firstname" value="{{$user->firstname}}" required>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="form-label">@lang('Last Name')</label>
                                    <input type="text"  name="lastname" value="{{$user->lastname}}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label class="form-label">@lang('Address')</label>
                                    <input type="text"  name="address" value="{{@$user->address->address}}">
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="form-label">@lang('State')</label>
                                    <input type="text"  name="state" value="{{@$user->address->state}}">
                                </div>
                            </div>


                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label class="form-label">@lang('Zip Code')</label>
                                    <input type="text"  name="zip" value="{{@$user->address->zip}}">
                                </div>

                                <div class="form-group col-sm-6">
                                    <label class="form-label">@lang('City')</label>
                                    <input type="text"  name="city" value="{{@$user->address->city}}">
                                </div>

                            </div>
                            <div class="form-group">
                                <label class="form-label">@lang('Upload Profile Photo')</label>
                                <input type="file" name="profile_image" id="profileImageInput" accept=".jpg,.jpeg,.png">
                                <small class="text-muted">@lang('JPG, JPEG, PNG only. Maximum 2MB.')</small>
                            </div>

                            <div class="form-group">
                                <label class="form-label">@lang('Choose Default Avatar')</label>
                                <div class="row g-2">
                                    @foreach($defaultAvatars as $avatar)
                                        <div class="col-4 col-sm-4 col-md-2">
                                            <label class="default-avatar-option">
                                                <input type="radio" name="default_avatar" value="{{ $avatar }}" {{ $selectedDefaultAvatar === $avatar ? 'checked' : '' }}>
                                                <img src="{{ getImage(getFilePath('userProfile').'/'.$avatar) }}" alt="@lang('Avatar')" class="default-avatar-img">
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <small class="text-muted">@lang('If you upload a new photo, uploaded photo will be used.')</small>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-sm btn-primary w-100">@lang('Submit')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .profile-dp-preview {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid rgba(255, 153, 51, 0.55);
    }

    .default-avatar-option {
        display: block;
        cursor: pointer;
        text-align: center;
    }

    .default-avatar-option input {
        display: none;
    }

    .default-avatar-img {
        width: 72px;
        height: 72px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.25);
        transition: all 0.2s ease;
    }

    .default-avatar-option input:checked + .default-avatar-img {
        border-color: #ff9933;
        box-shadow: 0 0 0 2px rgba(255, 153, 51, 0.3);
    }
</style>
@endpush

@push('script')
<script>
    (function ($) {
        "use strict";

        $('#profileImageInput').on('change', function () {
            if (this.files.length > 0) {
                $('input[name="default_avatar"]').prop('checked', false);
            }
        });

        $('input[name="default_avatar"]').on('change', function () {
            $('#profileImageInput').val('');
        });
    })(jQuery);
</script>
@endpush

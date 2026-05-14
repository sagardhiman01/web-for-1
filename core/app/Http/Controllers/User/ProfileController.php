<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function profile()
    {
        $pageTitle = "Profile Setting";
        $user = auth()->user();
        $defaultAvatars = $this->defaultAvatars();
        return view($this->activeTemplate . 'user.profile_setting', compact('pageTitle', 'user', 'defaultAvatars'));
    }

    public function submitProfile(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'default_avatar' => 'nullable|string|in:' . implode(',', $this->defaultAvatars()),
        ], [
            'firstname.required' => 'First name field is required',
            'lastname.required' => 'Last name field is required'
        ]);

        $user = auth()->user();

        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;

        $user->address = [
            'address' => $request->address,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => @$user->address->country,
            'city' => $request->city,
        ];

        $user->bep20_wallet_address = $request->bep20_wallet_address;
        $user->trc20_wallet_address = $request->trc20_wallet_address;

        if ($request->hasFile('profile_image')) {
            $oldImage = $this->isDefaultAvatar($user->image) ? null : $user->image;
            $user->image = fileUploader($request->file('profile_image'), getFilePath('userProfile'), getFileSize('userProfile'), $oldImage);
        } elseif ($request->filled('default_avatar')) {
            if ($user->image && !$this->isDefaultAvatar($user->image)) {
                $oldImagePath = getFilePath('userProfile') . '/' . $user->image;
                if (file_exists($oldImagePath) && is_file($oldImagePath)) {
                    @unlink($oldImagePath);
                }
            }
            $user->image = $request->default_avatar;
        } elseif (!$user->image) {
            $user->image = $this->defaultAvatars()[0];
        }

        $user->save();
        $notify[] = ['success', 'Profile updated successfully'];
        return back()->withNotify($notify);
    }

    public function changePassword()
    {
        $pageTitle = 'Change Password';
        return view($this->activeTemplate . 'user.password', compact('pageTitle'));
    }

    public function submitPassword(Request $request)
    {

        $passwordValidation = Password::min(6);
        $general = gs();
        if ($general->secure_password) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $this->validate($request, [
            'current_password' => 'required',
            'password' => ['required','confirmed',$passwordValidation]
        ]);

        $user = auth()->user();
        if (Hash::check($request->current_password, $user->password)) {
            $password = Hash::make($request->password);
            $user->password = $password;
            $user->save();
            $notify[] = ['success', 'Password changes successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'The password doesn\'t match!'];
            return back()->withNotify($notify);
        }
    }

    private function defaultAvatars(): array
    {
        return [
            'avatar-default-01.jpg',
            'avatar-default-02.jpg',
            'avatar-default-03.jpg',
            'avatar-default-04.jpg',
            'avatar-default-05.jpg',
            'avatar-default-06.jpg',
        ];
    }

    private function isDefaultAvatar(?string $image): bool
    {
        if (!$image) {
            return false;
        }

        return in_array($image, $this->defaultAvatars(), true);
    }
}

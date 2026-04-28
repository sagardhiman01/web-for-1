<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvestmentCode;
use Illuminate\Http\Request;

class InvestmentCodeController extends Controller
{
    public function index()
    {
        $pageTitle   = 'Investment Access Code';
        $activeCode  = InvestmentCode::withCount('redemptions')->where('status', 1)->latest()->first();
        $codes       = InvestmentCode::withCount('redemptions')->latest()->paginate(getPaginate());

        return view('admin.investment_code.index', compact('pageTitle', 'activeCode', 'codes'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'max_users'   => 'nullable|integer|min:1',
            'valid_hours' => 'required|integer|min:1',
        ]);

        InvestmentCode::where('status', 1)->update(['status' => 0]);

        $investmentCode            = new InvestmentCode();
        $investmentCode->code      = $this->generateUniqueCode();
        $investmentCode->status    = 1;
        $investmentCode->created_by = auth('admin')->id();
        $investmentCode->max_users = $request->max_users;
        $investmentCode->valid_hours = $request->valid_hours;
        $investmentCode->expires_at = now()->addHours((int) $request->valid_hours);
        $investmentCode->save();

        $notify[] = ['success', "New code generated: {$investmentCode->code}"];
        return back()->withNotify($notify);
    }

    private function generateUniqueCode(): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $code = '';
            for ($i = 0; $i < 8; $i++) {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }

            $hasLetter = (bool) preg_match('/[A-Z]/', $code);
            $hasDigit  = (bool) preg_match('/[0-9]/', $code);
        } while (!$hasLetter || !$hasDigit || InvestmentCode::where('code', $code)->exists());

        return $code;
    }
}

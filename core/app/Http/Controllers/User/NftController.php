<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Nft;
use App\Models\NftReservation;
use App\Models\NftTrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NftController extends Controller
{
    public function index()
    {
        $pageTitle = 'NFT Trading Dashboard';
        $user = auth()->user();
        $myNfts = Nft::where('owner_id', $user->id)->get();
        return view($this->activeTemplate . 'user.nft.index', compact('pageTitle', 'myNfts'));
    }

    public function marketplace()
    {
        $pageTitle = 'NFT Marketplace';
        $nfts = Nft::where('status', 'available')->paginate(12);

        // Per-user unique NFT images
        $user = auth()->user();
        $imagePool = $this->getNftImagePool();

        // Deterministic shuffle using user ID as seed (Fisher-Yates with mt_rand)
        $seed = crc32('nft_unique_' . $user->id);
        mt_srand($seed);
        for ($i = count($imagePool) - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$imagePool[$i], $imagePool[$j]] = [$imagePool[$j], $imagePool[$i]];
        }
        mt_srand(); // Reset

        // Map each NFT to a unique image from the shuffled pool
        $userImages = [];
        foreach ($nfts as $nft) {
            $userImages[$nft->id] = $imagePool[$nft->id % count($imagePool)];
        }

        $myReservations = \App\Models\NftReservation::where('user_id', $user->id)->where('status', 'pending')->with('nft')->get();
        $myNfts = \App\Models\Nft::where('owner_id', $user->id)->get();

        foreach ($myReservations as $reservation) {
            if ($reservation->nft) {
                $userImages[$reservation->nft->id] = $imagePool[$reservation->nft->id % count($imagePool)];
            }
        }
        foreach ($myNfts as $myNft) {
            $userImages[$myNft->id] = $imagePool[$myNft->id % count($imagePool)];
        }

        return view($this->activeTemplate . 'user.nft.marketplace', compact('pageTitle', 'nfts', 'userImages', 'myReservations', 'myNfts'));
    }

    /**
     * Get the pool of all available NFT images
     */
    private function getNftImagePool()
    {
        // Hardcoded pool to ensure it never fails due to directory path issues
        $images = [
            'opensea_bayc_1.png', 'opensea_bayc_2.png', 'opensea_bayc_3.png', 'opensea_bayc_4.png', 'opensea_bayc_5.png',
            'opensea_bayc_6.png', 'opensea_bayc_7.png', 'opensea_bayc_8.png', 'opensea_bayc_9.png', 'opensea_bayc_10.png',
            'cyber_ape.png', 'meta_samurai.png', 'neon_punk.png', 'treasure_genesis.png',
            'nft_1.png', 'nft_2.png',
            'unique_nft_1.jpg', 'unique_nft_2.jpg', 'unique_nft_3.jpg', 'unique_nft_4.jpg', 'unique_nft_5.jpg',
            'unique_nft_6.jpg', 'unique_nft_7.jpg', 'unique_nft_8.jpg', 'unique_nft_9.jpg', 'unique_nft_10.jpg',
            'unique_nft_11.jpg', 'unique_nft_12.jpg', 'unique_nft_13.jpg', 'unique_nft_14.jpg', 'unique_nft_15.jpg',
            'unique_nft_16.jpg', 'unique_nft_17.jpg', 'unique_nft_18.jpg', 'unique_nft_19.jpg', 'unique_nft_20.jpg',
            'pool_1.png', 'pool_2.png', 'pool_3.png', 'pool_4.png', 'pool_5.png',
            'pool_6.png', 'pool_7.png', 'pool_8.png', 'pool_9.png', 'pool_10.png',
            'pool_11.png', 'pool_12.png', 'pool_13.png', 'pool_14.png', 'pool_15.png'
        ];

        return $images;
    }

    public function reserve(Request $request, $id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $nft = Nft::where('id', $id)->lockForUpdate()->firstOrFail();
                $user = auth()->user();

                if ($nft->status != 'available') {
                    $notify[] = ['error', 'This NFT is not available for reservation.'];
                    return back()->withNotify($notify);
                }

                // Reservation cost: 5% of NFT price
                $reservationCost = $nft->current_price * 0.05;

                if ($user->nft_wallet < $reservationCost) {
                    $notify[] = ['error', 'Insufficient NFT wallet balance for reservation fee.'];
                    return back()->withNotify($notify);
                }

                $user->nft_wallet -= $reservationCost;
                $user->save();

                $reservation = new NftReservation();
                $reservation->user_id = $user->id;
                $reservation->nft_id = $nft->id;
                $reservation->deposit_amount = $reservationCost;
                $reservation->expires_at = Carbon::now()->addHours(24);
                $reservation->save();

                $nft->status = 'reserved';
                $nft->save();

                $notify[] = ['success', 'NFT reserved successfully. You have 24 hours to complete the purchase.'];
                return redirect()->route('user.nft.collection')->withNotify($notify);
            });
        } catch (\Exception $e) {
            $notify[] = ['error', 'Something went wrong. Please try again.'];
            return back()->withNotify($notify);
        }
    }

    public function buy(Request $request, $id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $nft = Nft::where('id', $id)->lockForUpdate()->firstOrFail();
                $user = auth()->user();

                $reservation = NftReservation::where('nft_id', $nft->id)
                    ->where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($user->nft_wallet < $nft->current_price) {
                    $notify[] = ['error', 'Insufficient NFT wallet balance to complete purchase.'];
                    return back()->withNotify($notify);
                }

                // Process Purchase
                $user->nft_wallet -= $nft->current_price;
                $user->save();

                $nft->owner_id = $user->id;
                $nft->status = 'sold';
                $nft->save();

                $reservation->status = 'completed';
                $reservation->save();

                // Create Trade Log
                $trade = new NftTrade();
                $trade->nft_id = $nft->id;
                $trade->seller_id = 0; // Platform
                $trade->buyer_id = $user->id;
                $trade->buy_price = $nft->current_price;
                $trade->save();

                $notify[] = ['success', 'NFT purchased successfully! It will be automatically listed for resale after the cycle.'];
                return redirect()->route('user.nft.collection')->withNotify($notify);
            });
        } catch (\Exception $e) {
            $notify[] = ['error', 'Something went wrong. Please try again.'];
            return back()->withNotify($notify);
        }
    }

    public function collection()
    {
        $pageTitle = 'My NFT Collection';
        $user = auth()->user();
        $myNfts = Nft::where('owner_id', $user->id)->get();
        
        // Calculate total profit for each NFT from transactions
        foreach($myNfts as $nft) {
            $nft->total_profit = \App\Models\Transaction::where('user_id', $user->id)
                ->where('remark', 'nft_profit')
                ->where('details', 'like', '%' . $nft->name . '%')
                ->sum('amount');
        }

        return view($this->activeTemplate . 'user.nft.collection', compact('pageTitle', 'myNfts'));
    }

    public function nftDetails($id)
    {
        $user = auth()->user();
        $nft = Nft::where('owner_id', $user->id)->where('id', $id)->firstOrFail();
        
        $pageTitle = 'NFT Details: ' . $nft->name;
        
        $totalProfit = \App\Models\Transaction::where('user_id', $user->id)
            ->where('remark', 'nft_profit')
            ->where('details', 'like', '%' . $nft->name . '%')
            ->sum('amount');
            
        $recentProfits = \App\Models\Transaction::where('user_id', $user->id)
            ->where('remark', 'nft_profit')
            ->where('details', 'like', '%' . $nft->name . '%')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        return view($this->activeTemplate . 'user.nft.details', compact('pageTitle', 'nft', 'totalProfit', 'recentProfits'));
    }
}

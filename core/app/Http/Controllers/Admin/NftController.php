<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Nft;
use Illuminate\Http\Request;

class NftController extends Controller
{
    public function index()
    {
        $pageTitle = 'Manage NFTs';
        $nfts = Nft::orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.nft.index', compact('pageTitle', 'nfts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'level_id' => 'required|integer|min:1',
            'status' => 'required|in:available,reserved,trading'
        ]);

        $nft = new Nft();
        $nft->name = $request->name;
        $nft->base_price = $request->base_price;
        $nft->current_price = $request->base_price;
        $nft->level_id = $request->level_id;
        $nft->status = $request->status;

        if ($request->hasFile('image')) {
            try {
                $nft->image = fileUploader($request->image, getFilePath('nft'), getFileSize('nft'));
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        } else {
            $nft->image = 'nft_1.png'; // Fallback
        }

        $nft->save();

        $notify[] = ['success', 'NFT added successfully'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'level_id' => 'required|integer|min:1',
            'status' => 'required|in:available,reserved,trading'
        ]);

        $nft = Nft::findOrFail($id);
        $nft->name = $request->name;
        $nft->base_price = $request->base_price;
        
        // If it's not trading/reserved, update current price too maybe
        if($nft->status == 'available'){
            $nft->current_price = $request->base_price;
        }
        
        $nft->level_id = $request->level_id;
        $nft->status = $request->status;

        if ($request->hasFile('image')) {
            try {
                $old = $nft->image;
                $nft->image = fileUploader($request->image, getFilePath('nft'), getFileSize('nft'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        $nft->save();

        $notify[] = ['success', 'NFT updated successfully'];
        return back()->withNotify($notify);
    }

    public function delete($id)
    {
        $nft = Nft::findOrFail($id);
        if($nft->status != 'available'){
            $notify[] = ['error', 'Cannot delete NFT while it is reserved or trading.'];
            return back()->withNotify($notify);
        }
        $nft->delete();
        $notify[] = ['success', 'NFT deleted successfully'];
        return back()->withNotify($notify);
    }
}

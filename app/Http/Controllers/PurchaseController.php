<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Cart;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class PurchaseController extends Controller
{
    private function resolveGuestToken()
    {
        $token = session('guest_token') ?? request()->cookie('guest_token');

        if (!$token) {
            $token = Str::uuid()->toString();
            session(['guest_token' => $token]);
            cookie()->queue(cookie('guest_token', $token, 60 * 24 * 30));
        }

        return $token;
    }

    public function index()
    {
        $items = Item::where('max', '>=', 1)->where('item_id', '=', NULL)->get();
        $cart = Cart::with('itemable')->where('user_id', Auth::id())->get();
        $totalprice = $cart->sum('total');
        /*$totalprice = number_format($totalprice, 2, '.', '');*/
        $totalpriceitem = round($totalprice, 2);
        return Inertia::render('Items/Purchase', ['items' => $items, 'cart' => $cart, 'totalprice' => $totalpriceitem]);
    }
    public function welcome()
    {
        $items = Item::where('max', '>=', 1)->where('item_id', '=', NULL)->get();
        if(Auth::id()) {
            $cart = Cart::with('itemable')->where('user_id', Auth::id())->get();
        } else {
            $guestToken = $this->resolveGuestToken();
            $cart = Cart::with('itemable')->where('guest_token', $guestToken)->get();
        }
        $plans = Plan::all();
        $totalprice = $cart->sum('total');
        /*$totalprice = number_format($totalprice, 2, '.', '');*/
        $totalpriceitem = round($totalprice, 2);
        return Inertia::render('Welcome', [
            'items' => $items,
            'cart' => $cart, 
            'totalprice' => $totalpriceitem,
            'plans' => $plans,
        ]);
    }
    public function store(Request $request)
    {
        $counter = (int) $request->counter ?? 1;
        $guestToken = $this->resolveGuestToken();
        if($request->type === 'plan') {
            $item = Plan::findOrFail($request->item_id);
        } else {
            $item = Item::findOrFail($request->item_id);
        }
        
        // Check available stock
       /*if($request->type != 'plan') {
            if ($counter < 1 || $counter > $item->max) {
                return back()->with('error', 'There are not enough tickets.');
            }
        }*/

        $userId = Auth::check() ? Auth::id() : null;

        // Check if the cart item already exists for this user or guest
        $existingCartItem = Cart::where('item_id', $request->item_id)->where('type', $request->type)->where(function ($query) use ($userId, $guestToken) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('guest_token', $guestToken);
            }
        })->first();

        if ($existingCartItem) {
            // Update existing cart item
            if($request->type == 'plan') {
                $newCounter = 1;
            }else {
                $newCounter = $existingCartItem->counter + $counter;
            }

            // Check stock
            /*if($request-> type != 'plan') {
                if ($newCounter > $item->max) {
                    return back()->with('error', 'Not enough tickets available to add more.');
                }
            }*/

            $existingCartItem->update([
                'counter' => $newCounter,
                'total' => $newCounter * $request->price,
            ]);
        } else {
            // Add new item to cart
            Cart::create([
                'name' => $request->name,
                'item_id' => $item->id,
                'item_type' => get_class($item),
                'counter' => $counter,
                'user_id' => $userId,
                'guest_token' => $guestToken,
                'total' => $counter * $request->price,
                'type' => $request->type,
            ]);
        }

        // Update item stock
        /*$item->update([
            'max' => $item->max - $counter,
        ]);*/

        /*if ($userId) {
            return Inertia::render('Items/Cart');
        } else {
            return Inertia::render(component: 'Items/Cart');
        }*/
        $cartItems = Cart::with('itemable')->where(function ($query) use ($userId, $guestToken) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('guest_token', $guestToken);
            }
        })->get();

        // ✅ Return Inertia page with required props
        /*return Inertia::render('Items/Cart', [
            'cartitems' => $cartItems,
            'user' => Auth::user(),
            'guest_token' => $guestToken,
        ]);*/
        return redirect()->back()->with('success', 'Item added to cart.');
    }


    public function show($id)
    {
        $item = Item::where('id', $id)->first();
        $detailitems = Item::where('id', $id)->orWhere('item_id', $id)->get();
        return Inertia::render('Items/Detail', ['item' => $item, 'detailitems' => $detailitems]);
    }
    public function destroy($id)
    {
        /*$cartItem = Cart::where('id', $id)->where('user_id', Auth::id())->first();*/
        $cartItem = Cart::where('id', $id)->where('guest_token', request()->cookie('guest_token'))->first();
        if ($cartItem) {
            $cartItem->delete(); // Correct method
        }
        if(!Auth::id()) {
            return redirect()->route('purchase.welcome')->with('success', 'Item destroyed successfully.');
        }else {
            return redirect()->route('purchase.index')->with('success', 'Item destroyed successfully.');
        }
    }
}

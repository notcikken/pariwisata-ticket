<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Membuat pesanan baru
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            $destinationId = $request->input('destination_id');
            $paymentMethod = $request->input('payment_method');
            $quantity = $request->input('quantity', 1); // Default quantity 1 jika tidak diinput
            $bookingDate = $request->input('booking_date');
            $paymentProof = $request->file('payment_proof');

            // Validasi input
            $request->validate([
                'destination_id' => 'required|exists:destinations,id',
                'payment_method' => 'required|in:credit_card,bank_transfer',
                'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'quantity' => 'required|integer|min:1',
                'booking_date' => 'required|date|after_or_equal:today',
            ]);

            // Mendapatkan harga dari Destination
            $destination = Destination::findOrFail($destinationId);
            if ($quantity > 15) {
                $totalPrice = $destination->price * $quantity * 0.90; // 10% discount
            } elseif ($quantity > 10) {
                $totalPrice = $destination->price * $quantity * 0.95; // 5% discount
            } elseif ($quantity > 5) {
                $totalPrice = $destination->price * $quantity * 0.98; // 2% discount
            } else {
                $totalPrice = $destination->price * $quantity;
            }

            // Membuat order baru
            $order = Order::create([
                'user_id' => $user->id,
                'destination_id' => $destinationId,
                'quantity' => $quantity,
                'total_price' => $totalPrice,
                'booking_date' => $bookingDate,
                'payment_method' => $paymentMethod,
                'payment_proof' => $request->file('payment_proof')->store('payment_proofs', 'public'),
                'status' => 'pending',
            ]);

            return response()->json(['message' => 'Order created successfully!', 'order' => $order], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Order creation failed', 'error' => $e->getMessage()], 500);
        }
    }



    // Menampilkan riwayat pesanan
    public function orderHistory()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['destination', 'user']) // Memuat relasi yang diperlukan
            ->get();

        return response()->json(['message' => 'Order history', 'orders' => $orders]);
    }

    // approve order
    // public function approve(Order $order)
    // {
    //     $order->update([
    //         'status' => 'approved',
    //         'approved_at' => now(),
    //     ]);
    //     // Kirim receipt ke buyer (email dengan PDF)
    //     \Mail::to($order->user->email)->send(new \App\Mail\OrderReceipt($order));
    //     $order->destination->updateTotalOrders();
    //     return response()->route('order.approve')->with('success', 'Order approved successfully!', 200);
    // }
}

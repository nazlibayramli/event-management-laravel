<?php
namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventSystemController extends Controller 
{
    // [CRUD] Tədbir yaratmaq (Təşkilatçı Paneli üçün)
    public function storeEvent(Request $request) {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'location' => 'required',
            'date' => 'required'
        ]);

        Event::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'date' => $request->date
        ]);

        return response()->json(['message' => 'Tədbir uğurla yaradıldı (CRUD - Create)']);
    }

    // [AXTARIŞ VƏ FİLTR] Siyahılama və Axtarış funksiyası
    public function index(Request $request) {
        $query = Event::query();

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
        }

        return response()->json($query->get());
    }

    // [SİFARİŞ AXINI] Sifariş və Ödəniş Simulyasiyası + Oturacaq seçimi
    public function checkout(Request $request) {
        // Sifariş yaradılır (Ödəniş uğurlu simulyasiya edilir)
        $order = Order::create([
            'user_id' => auth()->id(),
            'total_amount' => $request->total_amount,
            'status' => 'paid'
        ]);

        // Bilet generasiya olunur və təsadüfi oturacaq nömrəsi təyin edilir
        $ticket = Ticket::create([
            'order_id' => $order->id,
            'ticket_category_id' => $request->category_id,
            'seat_number' => 'Sıra-' . rand(1, 10) . ' Yer-' . rand(1, 20), // Sadə oturacaq seçimi
            'qr_code' => 'QR-' . strtoupper(Str::random(10)) // Unikal kod
        ]);

        return response()->json([
            'message' => 'Ödəniş uğurludur, biletiniz yaradıldı!',
            'ticket' => $ticket
        ]);
    }

    // [API] QR-Kod / Unikal kodla bilet yoxlanışı funksiyası
    public function verifyTicket($code) {
        $ticket = Ticket::where('qr_code', $code)->first();

        if (!$ticket) {
            return response()->json(['status' => 'error', 'message' => 'Bilet tapılmadı!'], 404);
        }

        if ($ticket->is_used) {
            return response()->json(['status' => 'error', 'message' => 'Bu bilet artıq istifadə olunub!'], 400);
        }

        // Bilet istifadə olundu olaraq işarələnir
        $ticket->update(['is_used' => true]);

        return response()->json(['status' => 'success', 'message' => 'Bilet etibarlıdır. Giriş təsdiqləndi!']);
    }
}
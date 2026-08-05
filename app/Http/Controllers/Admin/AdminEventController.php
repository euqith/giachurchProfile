<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Cabang;
use Illuminate\Http\Request;

class AdminEventController extends Controller
{
    /**
     * 📅 1. Tampilkan Halaman Daftar Event (Tabel)
     */
   public function index()
{
    // 1. Ambil semua data event dari database, urutkan dari yang paling baru
    $events = Event::with('cabang')->orderBy('created_at', 'desc')->get();

    // ⛪ 2. BARIS PENYELAMAT: Ambil juga semua data cabang untuk modal pop-up!
    $cabangs = Cabang::all();

    // 3. Oper KEDUA data tersebut ke file view
    return view('admin.eventsCMS', compact('events', 'cabangs'));
}

    /**
     * ➕ 2. Tampilkan Halaman Form Tambah Event Baru
     */
    public function create()
    {
        // Ambil data cabang untuk pilihan dropdown di form nantinya
        $cabangs = Cabang::all();
        return view('admin.eventsCreateCMS', compact('cabangs'));
    }

    /**
     * 💾 3. Proses Simpan Data Event Baru ke Database
     */
   public function store(Request $request)
    {
    $request->validate([
        'cabang_id'       => 'required|exists:cabangs,id',
        'nama_acara'      => 'required|string|max:255',
        'deskripsi_acara' => 'required|string',
        'waktu_mulai'     => 'required|date',
        'waktu_selesai'   => 'required|date|after_or_equal:waktu_mulai',
        'lokasi_spesifik' => 'required|string|max:255',
        'gambar_banner'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    $bannerName = null;

    if ($request->hasFile('gambar_banner')) {
        $file = $request->file('gambar_banner');
        $bannerName = time() . '_' . $file->getClientOriginalName();
        
        // 🎯 PINDAH LANGSUNG KE: public/img/admin/events
        $file->move(public_path('img/admin/events'), $bannerName);
    }

    Event::create([
        'cabang_id'       => $request->cabang_id,
        'nama_acara'      => $request->nama_acara,
        'deskripsi_acara' => $request->deskripsi_acara,
        'waktu_mulai'     => $request->waktu_mulai,
        'waktu_selesai'   => $request->waktu_selesai,
        'lokasi_spesifik' => $request->lokasi_spesifik,
        'gambar_banner'   => $bannerName,
        'isActive'        => true,
        'isDelete'        => false,
    ]);

    session()->flash('success', 'Event baru berhasil ditambahkan dan diterbitkan!');
    return redirect()->route('admin.event.index');
    }

    /**
     * 🔍 Menampilkan detail event spesifik (bisa dikosongkan dulu)
     */
    public function show(string $id)
    {
        //
    }

    /**
     * ✏️ Tampilkan Halaman Form Edit Event
     */
    public function edit(string $id)
    {
        // Akan kita isi setelah form tambah berhasil menyala!
    }

    /**
     * 🆙 Proses Update Perubahan Data Event
     */
   /**
 * 🆙 Proses Update Perubahan Data Event
 */
public function update(Request $request, string $id)
{
    $request->validate([
        'cabang_id'       => 'required|exists:cabangs,id',
        'nama_acara'      => 'required|string|max:255',
        'deskripsi_acara' => 'required|string',
        'waktu_mulai'     => 'required|date',
        'waktu_selesai'   => 'required|date|after_or_equal:waktu_mulai',
        'lokasi_spesifik' => 'required|string|max:255',
        'gambar_banner'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    $event = Event::findOrFail($id);
    
    $data = [
        'cabang_id'       => $request->cabang_id,
        'nama_acara'      => $request->nama_acara,
        'deskripsi_acara' => $request->deskripsi_acara,
        'waktu_mulai'     => $request->waktu_mulai,
        'waktu_selesai'   => $request->waktu_selesai,
        'lokasi_spesifik' => $request->lokasi_spesifik,
    ];

    if ($request->hasFile('gambar_banner')) {
        // 🎯 HAPUS FILE LAMA DARI: public/img/admin/events jika ada
        if ($event->gambar_banner && file_exists(public_path('img/admin/events/' . $event->gambar_banner))) {
            unlink(public_path('img/admin/events/' . $event->gambar_banner));
        }

        $file = $request->file('gambar_banner');
        $bannerName = time() . '_' . $file->getClientOriginalName();
        
        // 🎯 PINDAH KE: public/img/admin/events
        $file->move(public_path('img/admin/events'), $bannerName);
        $data['gambar_banner'] = $bannerName;
    }

    $event->update($data);

    session()->flash('success', 'Perubahan data event berhasil disimpan!');
    return redirect()->route('admin.event.index');
}

    /**
     * 🗑️ Proses Hapus Event
     */
    public function destroy(string $id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return redirect()->route('admin.event.index')->with('success', 'Event berhasil dihapus!');
    }
}
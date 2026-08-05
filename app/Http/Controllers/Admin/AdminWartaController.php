<?php

// 1. Deklarasikan lokasi folder (Namespace) tempat file ini berada
namespace App\Http\Controllers\Admin;

// 2. Import core class dasar Laravel yang dibutuhkan
use App\Http\Controllers\Controller; 
use App\Models\Warta; // Mengimpor model induk
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminWartaController extends Controller
{
    /**
     * Menampilkan daftar seluruh edisi warta jemaat
     */
   /**
     * Menampilkan Halaman Master Kontrol Warta Master (Tunggal Layout)
     */
    public function index(Request $request)
    {
        // 1. Ambil semua riwayat edisi warta untuk tabel sisi kiri
        $wartas = Warta::where('isDelete', false)
                       ->orderBy('tanggal_rilis', 'desc')
                       ->paginate(8) // Membatasi 8 baris per halaman biar layout simetris
                       ->withQueryString();

        // 2. Deteksi apakah admin mengklik salah satu tombol "Buka Slides" dari tabel (?warta_id=x)
        $warta = null;
        if ($request->has('warta_id') && $request->warta_id != '') {
            $warta = Warta::with('slides')->findOrFail($request->warta_id);
        }

        // 3. Kirim kedua variabel ke view wartaCMS tunggal kita!
        return view('admin.wartaCMS', compact('wartas', 'warta'));
    }
    /**
     * Menampilkan form halaman tambah edisi baru
     */
    public function create()
    {
        return view('admin.warta.create');
    }

    /**
     * Menangkap request data baru dari form dan menyimpannya ke database
     */
   /**
     * Menangkap request data baru dari form dan menyimpannya ke database
     */
    public function store(Request $request)
    {
        // A. Validasi input
        $request->validate([
            'judul_edisi'    => 'required|string|max:255',
            'tanggal_rilis'  => 'required|date',
        ]);

        // B. Eksekusi ORM Create ke database induk
        $warta = Warta::create([
            'judul_edisi'   => $request->judul_edisi,
            'tanggal_rilis' => $request->tanggal_rilis,
            'createdBy'     => auth()->user()->name ?? 'Admin',
            'isActive'      => true,
            'isDelete'      => false,
        ]);

        // 🎯 FIX MUTLAK: Arahkan ke rute index utama dengan membawa parameter warta_id agar kanvas kanan langsung terbuka!
        return redirect()->to(url('admin/warta?warta_id=' . $warta->id))
                         ->with('success', 'Edisi warta berhasil dibuat! Yuk, unggah file PowerPoint di sebelah kanan.');
    }
    /**
     * Mengubah status aktif/nonaktif warta di halaman depan secara cepat (Toggle)
     */
    public function toggleActive($id)
    {
        $warta = Warta::findOrFail($id);
        
        $warta->update([
            'isActive'  => !$warta->isActive, // Membalikkan status boolean (true jadi false, vice versa)
            'updatedBy' => Auth::user()->name ?? 'Admin'
        ]);

        return redirect()->back()->with('success', 'Status edisi warta berhasil diperbarui!');
    }

    /**
     * Fitur Soft Delete (Menghapus tanpa melenyapkan data fisik dari database)
     */
    public function destroy($id)
    {
        $warta = Warta::findOrFail($id);
        
        $warta->update([
            'isDelete'  => true,
            'updatedBy' => Auth::user()->name ?? 'Admin'
        ]);

        return redirect()->route('admin.warta.index')->with('success', 'Edisi warta berhasil dipindahkan ke tempat sampah!');
    }
    /**
     * Update detail edisi warta
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'judul_edisi'   => 'required|string|max:255',
            'tanggal_rilis' => 'required|date',
        ]);

        $warta = Warta::findOrFail($id);
        $warta->update([
            'judul_edisi'   => $request->judul_edisi,
            'tanggal_rilis' => $request->tanggal_rilis,
            'updatedBy'     => auth()->user()->name ?? 'Admin'
        ]);

        return redirect()->back()->with('success', 'Detail edisi warta berhasil diperbarui!');
    }
}
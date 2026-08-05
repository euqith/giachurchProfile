<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warta;      // Model Induk
use App\Models\WartaSlide; // Model Anak
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File; // Facade pendukung manajemen file sistem

class AdminWartaSlideController extends Controller
{
    /**
     * Menampilkan galeri halaman berdasarkan warta induk yang dipilih
     */
    public function index($wartaId)
    {
        // Eager Loading: Mengambil warta beserta slide-slide anaknya sekaligus
        $warta = Warta::with('slides')->findOrFail($wartaId);
        
        return view('admin.warta.slides', compact('warta'));
    }

    /**
     * Menangani proses multi-upload banyak gambar lembaran warta
     */
   public function store(Request $request, $wartaId)
    {
        // 1. Validasi: Wajib PDF
        $request->validate([
            'file_pdf' => 'required|file|mimes:pdf|max:10240', // Maks 10MB
        ]);

        $warta = Warta::findOrFail($wartaId);

        // 2. Cek & Hapus file lama jika ada
        $existingSlide = WartaSlide::where('warta_id', $wartaId)->first();
        if ($existingSlide) {
            $fileLama = public_path('img/admin/wartas/' . $existingSlide->file_gambar);
            if (File::exists($fileLama)) File::delete($fileLama);
            $existingSlide->delete();
        }

        // 3. Upload file baru (Gunakan file_pdf sesuai input form)
        if ($request->hasFile('file_pdf')) {
            $file = $request->file('file_pdf');
            // Pastikan ekstensinya .pdf
            $fileName = 'warta_' . $wartaId . '_' . time() . '.pdf'; 
            $file->move(public_path('img/admin/wartas'), $fileName);

            WartaSlide::create([
                'warta_id'       => $warta->id,
                'file_gambar'    => $fileName, // Menyimpan nama file PDF
                'urutan_halaman' => 1,
                'createdBy'      => auth()->user()->name ?? 'Admin',
                'isActive'       => true
            ]);
        }

        return redirect()->back()->with('success', 'Dokumen PDF warta berhasil diunggah!');
    }

    /**
     * Menghapus salah satu lembar halaman spesifik
     */
    public function destroy($slideId)
    {
        $slide = WartaSlide::findOrFail($slideId);
        $wartaIdSimpanan = $slide->warta_id; // Kunci ID induknya sebelum data dihapus
        
        // A. HAPUS FISIK: Lenyapkan file gambar dari folder public agar server tidak bengkak
        $fileFisik = public_path('img/admin/wartas/' . $slide->file_gambar);
        if (File::exists($fileFisik)) {
            File::delete($fileFisik);
        }

        // B. HAPUS DATABASE: Eksekusi hapus baris data anak secara permanen
        $slide->delete();

        // C. RE-ORDER LOGIC: Ambil semua sisa halaman yang tersisa, urutkan dari yang terkecil
        $sisaSlides = WartaSlide::where('warta_id', $wartaIdSimpanan)
                                 ->orderBy('urutan_halaman', 'asc')
                                 ->get();
        
        // Susun ulang penomoran halaman agar rapi kembali (1, 2, 3...) tanpa ada nomor melompat bolong
        foreach ($sisaSlides as $index => $sisa) {
            $sisa->update(['urutan_halaman' => $index + 1]);
        }

        return redirect()->back()->with('success', 'Halaman warta berhasil dihapus dan susunan nomor ditata ulang!');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChurchProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ChurchProfileController extends Controller
{
    /**
     * Tampilkan Pengaturan Profil Gereja & Banner Utama
     */
    public function index()
    {
        $profile = ChurchProfile::getProfile();

        return view('admin.profile.churchCMS', compact('profile'));
    }

    /**
     * Perbarui Profil Gereja, Visi Misi, dan Foto Gereja
     */
    public function update(Request $request)
    {
        $request->validate([
            'nama_gereja' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'foto_gereja' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'tentang_gereja' => 'nullable|string',
            'sejarah_singkat' => 'nullable|string',
            'visi' => 'nullable|string',
            'misi' => 'nullable|string',
            'alamat_pusat' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'youtube_channel_url' => 'nullable|string|max:255',
            'instagram_url' => 'nullable|string|max:255',
        ]);

        $profile = ChurchProfile::getProfile();

        $data = $request->only([
            'nama_gereja',
            'tagline',
            'tentang_gereja',
            'sejarah_singkat',
            'visi',
            'misi',
            'alamat_pusat',
            'telepon',
            'email',
            'youtube_channel_url',
            'instagram_url',
        ]);

        if ($request->hasFile('foto_gereja')) {
            $destPath = public_path('img/admin/church');
            if (! File::isDirectory($destPath)) {
                File::makeDirectory($destPath, 0755, true, true);
            }

            // Hapus file lama jika ada
            if ($profile->foto_gereja && File::exists(public_path('img/admin/church/'.$profile->foto_gereja))) {
                File::delete(public_path('img/admin/church/'.$profile->foto_gereja));
            }

            $file = $request->file('foto_gereja');
            $fileName = 'church_main_'.time().'.'.$file->getClientOriginalExtension();
            $file->move($destPath, $fileName);
            $data['foto_gereja'] = $fileName;
        }

        $profile->update($data);

        return redirect()->route('admin.church.index')->with('success', 'Pengaturan profil gereja dan foto banner berhasil diperbarui!');
    }
}

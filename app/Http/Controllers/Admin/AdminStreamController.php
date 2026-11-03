<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\ChurchProfile;
use App\Models\Stream;
use App\Services\YouTubeStreamDetector;
use Illuminate\Http\Request;

class AdminStreamController extends Controller
{
    /**
     * Tampilkan Pengaturan Live Streaming per Cabang
     */
    public function index()
    {
        $profile = ChurchProfile::getProfile();
        $cabangs = Cabang::where('isDelete', 0)->where('isActive', 1)->get();

        // Pastikan setiap cabang memiliki record stream
        foreach ($cabangs as $c) {
            Stream::firstOrCreate(
                ['cabang_id' => $c->id],
                [
                    'judul_streaming' => 'Ibadah Raya Minggu - '.$c->nama_cabang,
                    'channel_id' => '@giasurabaya',
                    'youtube_id' => '',
                    'waktu_tayang' => 'Minggu, 09:00 WIB',
                    'is_live' => false,
                    'auto_detect' => true,
                    'isActive' => true,
                    'isDelete' => false,
                ]
            );
        }

        $streams = Stream::with('cabang')
            ->where('isDelete', 0)
            ->get();

        $anyLive = $streams->where('is_live', true)->count() > 0;

        return view('admin.streams.indexCMS', compact('profile', 'streams', 'anyLive'));
    }

    /**
     * Simpan / Perbarui YouTube API Key untuk Auto-Detect
     */
    public function updateApiKey(Request $request)
    {
        $request->validate([
            'youtube_api_key' => 'nullable|string|max:255',
        ]);

        $profile = ChurchProfile::getProfile();
        $profile->update([
            'youtube_api_key' => trim($request->youtube_api_key ?: ''),
        ]);

        return redirect()->route('admin.stream.index')->with('success', 'YouTube API Key berhasil disimpan!');
    }

    /**
     * Perbarui data streaming cabang
     */
    public function update(Request $request, $id)
    {
        $stream = Stream::findOrFail($id);

        $request->validate([
            'judul_streaming' => 'required|string|max:255',
            'channel_id' => 'nullable|string|max:255',
            'youtube_id' => 'nullable|string|max:255',
            'waktu_tayang' => 'nullable|string|max:100',
            'is_live' => 'nullable|boolean',
            'auto_detect' => 'nullable|boolean',
        ]);

        $stream->update([
            'judul_streaming' => $request->judul_streaming,
            'channel_id' => $request->channel_id,
            'youtube_id' => $request->youtube_id,
            'waktu_tayang' => $request->waktu_tayang ?: 'Minggu, 09:00 WIB',
            'is_live' => $request->boolean('is_live'),
            'auto_detect' => $request->has('auto_detect') ? $request->boolean('auto_detect') : true,
            'updatedBy' => auth()->user()->name ?? 'Admin',
        ]);

        return redirect()->route('admin.stream.index')->with('success', "Pengaturan streaming {$stream->cabang->nama_cabang} berhasil disimpan!");
    }

    /**
     * Jalankan Auto-Detect Live Streaming untuk Semua Cabang
     */
    public function autoDetectAll(YouTubeStreamDetector $detector)
    {
        $result = $detector->checkAllStreams();

        $liveCount = $result['live_count'];
        $total = $result['total_checked'];

        $msg = "Auto-detect selesai! Diperiksa {$total} cabang: ";
        if ($liveCount > 0) {
            $msg .= "{$liveCount} cabang terdeteksi sedang LIVE STREAMING (ON AIR).";
        } else {
            $msg .= 'Semua cabang saat ini sedang OFFLINE.';
        }

        return redirect()->route('admin.stream.index')->with('success', $msg);
    }

    /**
     * Jalankan Auto-Detect untuk 1 Cabang Tertentu
     */
    public function autoDetectSingle($id, YouTubeStreamDetector $detector)
    {
        $stream = Stream::with('cabang')->findOrFail($id);
        $result = $detector->checkStream($stream);

        $cabangName = $stream->cabang ? $stream->cabang->nama_cabang : 'Cabang '.$id;
        $statusText = $result['is_live'] ? 'SEDANG LIVE (ON AIR)' : 'OFFLINE';

        return redirect()->route('admin.stream.index')->with('success', "Pemeriksaan {$cabangName}: Status {$statusText} (Metode: {$result['method']}).");
    }

    /**
     * 1-Click Fast Toggle Status Live
     */
    public function toggleLive(Request $request, $id)
    {
        $stream = Stream::findOrFail($id);
        $stream->is_live = ! $stream->is_live;
        $stream->updatedBy = auth()->user()->name ?? 'Admin';
        $stream->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_live' => $stream->is_live,
                'message' => 'Status streaming '.$stream->cabang->nama_cabang.' berhasil diubah!',
            ]);
        }

        return redirect()->back()->with('success', 'Status streaming '.$stream->cabang->nama_cabang.' sekarang '.($stream->is_live ? 'ON AIR (Live)' : 'OFF').'.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\Stream;
use Illuminate\Http\Request;

class AdminStreamController extends Controller
{
    /**
     * Tampilkan Pengaturan Live Streaming per Cabang
     */
    public function index()
    {
        $cabangs = Cabang::where('isDelete', 0)->where('isActive', 1)->get();

        // Pastikan setiap cabang memiliki record stream
        foreach ($cabangs as $c) {
            Stream::firstOrCreate(
                ['cabang_id' => $c->id],
                [
                    'judul_streaming' => 'Ibadah Raya Minggu - '.$c->nama_cabang,
                    'youtube_id' => '',
                    'waktu_tayang' => 'Minggu, 09:00 WIB',
                    'is_live' => false,
                    'isActive' => true,
                    'isDelete' => false,
                ]
            );
        }

        $streams = Stream::with('cabang')
            ->where('isDelete', 0)
            ->get();

        $anyLive = $streams->where('is_live', true)->count() > 0;

        return view('admin.streams.indexCMS', compact('streams', 'anyLive'));
    }

    /**
     * Perbarui data streaming cabang
     */
    public function update(Request $request, $id)
    {
        $stream = Stream::findOrFail($id);

        $request->validate([
            'judul_streaming' => 'required|string|max:255',
            'youtube_id' => 'nullable|string|max:255',
            'waktu_tayang' => 'nullable|string|max:100',
            'is_live' => 'nullable|boolean',
        ]);

        $stream->update([
            'judul_streaming' => $request->judul_streaming,
            'youtube_id' => $request->youtube_id,
            'waktu_tayang' => $request->waktu_tayang ?: 'Minggu, 09:00 WIB',
            'is_live' => $request->boolean('is_live'),
            'updatedBy' => auth()->user()->name ?? 'Admin',
        ]);

        return redirect()->route('admin.stream.index')->with('success', "Pengaturan streaming {$stream->cabang->nama_cabang} berhasil disimpan!");
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

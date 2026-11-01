<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\Jemaat;
use App\Models\JenisIbadah;
use App\Models\Sesi;
use App\Models\Song;
use App\Models\WorshipSchedule;
use App\Models\WorshipScheduleSong;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WorshipPreparationController extends Controller
{
    /**
     * Tampilkan Halaman Persiapan Ibadah & Pustaka Lagu
     */
    public function index(Request $request)
    {
        $today = Carbon::today('Asia/Jakarta');

        $cabangs = Cabang::where('isDelete', 0)->where('isActive', 1)->orderBy('nama_cabang')->get();
        $jenisIbadahs = JenisIbadah::where('isDelete', 0)->where('isActive', 1)->orderBy('nama_ibadah')->get();
        $sesis = Sesi::where('isDelete', 0)->where('isActive', 1)->orderBy('id')->get();

        // 1. Query Jadwal Ibadah (Mendatang vs Arsip)
        $tabJadwal = $request->get('tab', 'mendatang'); // mendatang | arsip
        $searchJadwal = $request->get('search_jadwal');

        $scheduleQuery = WorshipSchedule::with(['songs', 'cabang', 'jenisIbadah', 'sesi'])
            ->where('isDelete', 0);

        if ($tabJadwal === 'arsip') {
            $scheduleQuery->where('tanggal', '<', $today->format('Y-m-d'))
                ->orderBy('tanggal', 'desc');
        } else {
            $scheduleQuery->where('tanggal', '>=', $today->format('Y-m-d'))
                ->orderBy('tanggal', 'asc');
        }

        if ($searchJadwal) {
            $scheduleQuery->where(function ($q) use ($searchJadwal) {
                $q->where('tema', 'like', "%{$searchJadwal}%")
                    ->orWhere('pengkhotbah', 'like', "%{$searchJadwal}%")
                    ->orWhere('pemimpin_pujian', 'like', "%{$searchJadwal}%")
                    ->orWhere('nama_ibadah', 'like', "%{$searchJadwal}%")
                    ->orWhere('nama_cabang', 'like', "%{$searchJadwal}%");
            });
        }

        $schedules = $scheduleQuery->get();

        // 2. Query Pustaka Lagu
        $searchLagu = $request->get('search_lagu');
        $songQuery = Song::where('isDelete', 0);

        if ($searchLagu) {
            $songQuery->where(function ($q) use ($searchLagu) {
                $q->where('judul_lagu', 'like', "%{$searchLagu}%")
                    ->orWhere('artis_sumber', 'like', "%{$searchLagu}%")
                    ->orWhere('bagian_terakhir', 'like', "%{$searchLagu}%");
            });
        }

        $songs = $songQuery->orderBy('judul_lagu', 'asc')->get();

        // Hitung metrik "Dipakai (Xx)" dan "Terakhir" untuk tiap lagu
        foreach ($songs as $song) {
            $usageCount = WorshipScheduleSong::where('song_id', $song->id)
                ->whereHas('schedule', fn ($q) => $q->where('isDelete', 0))
                ->count();

            $lastUsage = WorshipScheduleSong::where('song_id', $song->id)
                ->join('worship_schedules', 'worship_schedule_songs.worship_schedule_id', '=', 'worship_schedules.id')
                ->where('worship_schedules.isDelete', 0)
                ->orderBy('worship_schedules.tanggal', 'desc')
                ->first(['worship_schedules.tanggal', 'worship_schedule_songs.bagian_liturgi']);

            $song->dipakai_count = $usageCount;
            $song->terakhir_dipakai = $lastUsage ? Carbon::parse($lastUsage->tanggal)->translatedFormat('d M Y') : '-';
            if ($lastUsage && $lastUsage->bagian_liturgi) {
                $song->bagian_terakhir = $lastUsage->bagian_liturgi;
            }
        }

        return view('admin.attendance.persiapanCMS', compact(
            'schedules',
            'songs',
            'cabangs',
            'jenisIbadahs',
            'sesis',
            'tabJadwal'
        ));
    }

    /**
     * Halaman Buat Jadwal Ibadah Baru (Editor Form Persiapan Ibadah)
     */
    public function createSchedule()
    {
        $today = Carbon::today('Asia/Jakarta');
        $cabangs = Cabang::where('isDelete', 0)->where('isActive', 1)->orderBy('nama_cabang')->get();
        $jenisIbadahs = JenisIbadah::where('isDelete', 0)->where('isActive', 1)->orderBy('nama_ibadah')->get();
        $sesis = Sesi::where('isDelete', 0)->where('isActive', 1)->orderBy('id')->get();

        $songs = Song::where('isDelete', 0)->where('isActive', 1)->orderBy('judul_lagu')->get();
        foreach ($songs as $song) {
            $song->dipakai_count = WorshipScheduleSong::where('song_id', $song->id)
                ->whereHas('schedule', fn ($q) => $q->where('isDelete', 0))
                ->count();
        }

        $jemaats = Jemaat::where('isDelete', 0)->where('isActive', 1)->orderBy('nama_asli')->get(['id', 'nama_asli', 'status', 'cabang_id']);

        $schedule = new WorshipSchedule([
            'tanggal' => $today->format('Y-m-d'),
            'nama_ibadah' => $jenisIbadahs->first()->nama_ibadah ?? 'Ibadah Umum',
            'nama_sesi' => $sesis->first()->nama_sesi ?? 'Pagi',
            'nama_cabang' => $cabangs->first()->nama_cabang ?? 'Darmo',
            'tema' => '',
            'ayat' => '',
            'catatan_singkat' => '',
            'pengkhotbah' => '',
            'pemimpin_pujian' => '',
            'petugas_tambahan' => [
                'pemusik' => [],
                'penerima_tamu' => ['', ''],
                'soundman' => [''],
                'kolekte' => ['', ''],
                'perjamuan' => ['', ''],
                'majelis' => [''],
                'multimedia' => [''],
                'peran_tambahan' => [],
            ],
            'bahan_media' => [],
        ]);

        return view('admin.attendance.scheduleEditorCMS', compact(
            'schedule',
            'cabangs',
            'jenisIbadahs',
            'sesis',
            'songs',
            'jemaats'
        ));
    }

    /**
     * Halaman Edit Detail Jadwal Ibadah (Editor Form Persiapan Ibadah)
     */
    public function editSchedule($id)
    {
        $cabangs = Cabang::where('isDelete', 0)->where('isActive', 1)->orderBy('nama_cabang')->get();
        $jenisIbadahs = JenisIbadah::where('isDelete', 0)->where('isActive', 1)->orderBy('nama_ibadah')->get();
        $sesis = Sesi::where('isDelete', 0)->where('isActive', 1)->orderBy('id')->get();

        $songs = Song::where('isDelete', 0)->where('isActive', 1)->orderBy('judul_lagu')->get();
        foreach ($songs as $song) {
            $song->dipakai_count = WorshipScheduleSong::where('song_id', $song->id)
                ->whereHas('schedule', fn ($q) => $q->where('isDelete', 0))
                ->count();
        }

        $jemaats = Jemaat::where('isDelete', 0)->where('isActive', 1)->orderBy('nama_asli')->get(['id', 'nama_asli', 'status', 'cabang_id']);

        $schedule = WorshipSchedule::with(['scheduleSongs.song'])->where('isDelete', 0)->findOrFail($id);

        if (empty($schedule->petugas_tambahan)) {
            $schedule->petugas_tambahan = [
                'pemusik' => [],
                'penerima_tamu' => ['', ''],
                'soundman' => [''],
                'kolekte' => ['', ''],
                'perjamuan' => ['', ''],
                'majelis' => [''],
                'multimedia' => [''],
                'peran_tambahan' => [],
            ];
        }

        return view('admin.attendance.scheduleEditorCMS', compact(
            'schedule',
            'cabangs',
            'jenisIbadahs',
            'sesis',
            'songs',
            'jemaats'
        ));
    }

    /**
     * Simpan Jadwal Ibadah Baru
     */
    public function storeSchedule(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nama_ibadah' => 'required|string',
            'nama_sesi' => 'nullable|string',
            'nama_cabang' => 'required|string',
            'tema' => 'nullable|string|max:255',
            'ayat' => 'nullable|string|max:255',
            'pengkhotbah' => 'nullable|string|max:255',
            'pemimpin_pujian' => 'nullable|string|max:255',
            'catatan_singkat' => 'nullable|string',
            'bahan_khotbah' => 'nullable|string',
        ]);

        $jenisIbadah = JenisIbadah::where('nama_ibadah', $request->nama_ibadah)->first();
        $cabang = Cabang::where('nama_cabang', $request->nama_cabang)->first();
        $sesi = Sesi::where('nama_sesi', $request->nama_sesi)->first();

        $schedule = WorshipSchedule::create([
            'tanggal' => $request->tanggal,
            'jenis_ibadah_id' => $jenisIbadah->id ?? null,
            'nama_ibadah' => $request->nama_ibadah,
            'sesi_id' => $sesi->id ?? null,
            'nama_sesi' => $request->nama_sesi ?: null,
            'cabang_id' => $cabang->id ?? null,
            'nama_cabang' => $request->nama_cabang,
            'tema' => $request->tema,
            'ayat' => $request->ayat,
            'pengkhotbah' => $request->pengkhotbah,
            'pemimpin_pujian' => $request->pemimpin_pujian,
            'catatan_singkat' => $request->catatan_singkat,
            'petugas_tambahan' => $request->petugas_tambahan,
            'bahan_media' => $request->bahan_media,
            'bahan_khotbah' => $request->bahan_khotbah,
            'createdBy' => Auth::user()->name ?? 'Administrator',
            'isActive' => 1,
            'isDelete' => 0,
        ]);

        // Simpan susunan lagu liturgi jika ada
        if ($request->has('songs') && is_array($request->songs)) {
            foreach ($request->songs as $index => $songItem) {
                if (! empty($songItem['song_id'])) {
                    WorshipScheduleSong::create([
                        'worship_schedule_id' => $schedule->id,
                        'song_id' => $songItem['song_id'],
                        'bagian_liturgi' => $songItem['bagian_liturgi'] ?? 'Pujian & Penyembahan',
                        'nada_dasar' => $songItem['nada_dasar'] ?? null,
                        'urutan' => $index + 1,
                    ]);
                }
            }
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Jadwal persiapan ibadah berhasil disimpan!',
                'schedule_id' => $schedule->id,
                'redirect_url' => route('admin.attendance.editSchedule', $schedule->id),
            ]);
        }

        return redirect()->route('admin.attendance.editSchedule', $schedule->id)
            ->with('success', 'Jadwal persiapan ibadah berhasil dibuat!');
    }

    /**
     * Ambil Data Detail Jadwal (JSON untuk Modal Edit)
     */
    public function showSchedule($id)
    {
        $schedule = WorshipSchedule::with(['scheduleSongs.song'])->findOrFail($id);

        return response()->json($schedule);
    }

    /**
     * Perbarui Jadwal Ibadah
     */
    public function updateSchedule(Request $request, $id)
    {
        $schedule = WorshipSchedule::where('isDelete', 0)->findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
            'nama_ibadah' => 'required|string',
            'nama_sesi' => 'nullable|string',
            'nama_cabang' => 'required|string',
            'tema' => 'nullable|string|max:255',
            'ayat' => 'nullable|string|max:255',
            'pengkhotbah' => 'nullable|string|max:255',
            'pemimpin_pujian' => 'nullable|string|max:255',
            'catatan_singkat' => 'nullable|string',
            'bahan_khotbah' => 'nullable|string',
        ]);

        $jenisIbadah = JenisIbadah::where('nama_ibadah', $request->nama_ibadah)->first();
        $cabang = Cabang::where('nama_cabang', $request->nama_cabang)->first();
        $sesi = Sesi::where('nama_sesi', $request->nama_sesi)->first();

        $schedule->update([
            'tanggal' => $request->tanggal,
            'jenis_ibadah_id' => $jenisIbadah->id ?? null,
            'nama_ibadah' => $request->nama_ibadah,
            'sesi_id' => $sesi->id ?? null,
            'nama_sesi' => $request->nama_sesi ?: null,
            'cabang_id' => $cabang->id ?? null,
            'nama_cabang' => $request->nama_cabang,
            'tema' => $request->tema,
            'ayat' => $request->ayat,
            'pengkhotbah' => $request->pengkhotbah,
            'pemimpin_pujian' => $request->pemimpin_pujian,
            'catatan_singkat' => $request->catatan_singkat,
            'petugas_tambahan' => $request->petugas_tambahan,
            'bahan_media' => $request->bahan_media,
            'bahan_khotbah' => $request->bahan_khotbah,
            'updatedBy' => Auth::user()->name ?? 'Administrator',
        ]);

        // Sinkronisasi Lagu
        if ($request->has('songs') && is_array($request->songs)) {
            $schedule->scheduleSongs()->delete();
            foreach ($request->songs as $index => $songItem) {
                if (! empty($songItem['song_id'])) {
                    WorshipScheduleSong::create([
                        'worship_schedule_id' => $schedule->id,
                        'song_id' => $songItem['song_id'],
                        'bagian_liturgi' => $songItem['bagian_liturgi'] ?? 'Pujian & Penyembahan',
                        'nada_dasar' => $songItem['nada_dasar'] ?? null,
                        'urutan' => $index + 1,
                    ]);
                }
            }
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Perubahan berhasil disimpan!',
                'schedule' => $schedule->load('scheduleSongs.song'),
                'updated_by' => $schedule->updatedBy,
                'updated_at' => $schedule->updated_at->format('d M Y H:i'),
            ]);
        }

        return redirect()->back()->with('success', 'Jadwal persiapan ibadah berhasil diperbarui!');
    }

    /**
     * Hapus Jadwal Ibadah (Soft Delete)
     */
    public function destroySchedule($id)
    {
        $schedule = WorshipSchedule::where('isDelete', 0)->findOrFail($id);
        $schedule->update(['isDelete' => 1]);

        return redirect()->back()->with('success', 'Jadwal ibadah berhasil dihapus!');
    }

    /**
     * Simpan Lagu Baru ke Pustaka
     */
    public function storeSong(Request $request)
    {
        $request->validate([
            'judul_lagu' => 'required|string|max:255',
            'artis_sumber' => 'nullable|string|max:255',
            'nada_dasar' => 'nullable|string|max:10',
            'bagian_terakhir' => 'nullable|string|max:100',
            'youtube_url' => 'nullable|url|max:255',
            'spotify_url' => 'nullable|url|max:255',
            'chord_url' => 'nullable|url|max:255',
            'lirik_url' => 'nullable|url|max:255',
            'lirik_teks' => 'nullable|string',
        ]);

        $song = Song::create([
            'judul_lagu' => $request->judul_lagu,
            'artis_sumber' => $request->artis_sumber,
            'nada_dasar' => $request->nada_dasar,
            'bagian_terakhir' => $request->bagian_terakhir ?: 'Pujian & Penyembahan',
            'youtube_url' => $request->youtube_url,
            'spotify_url' => $request->spotify_url,
            'chord_url' => $request->chord_url,
            'lirik_url' => $request->lirik_url,
            'lirik_teks' => $request->lirik_teks,
            'createdBy' => Auth::user()->name ?? 'Administrator',
            'isActive' => 1,
            'isDelete' => 0,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Lagu berhasil ditambahkan ke pustaka!',
                'song' => $song,
            ]);
        }

        return redirect()->back()->with('success', 'Lagu baru berhasil ditambahkan ke pustaka!');
    }

    /**
     * Perbarui Data Lagu
     */
    public function updateSong(Request $request, $id)
    {
        $song = Song::where('isDelete', 0)->findOrFail($id);

        $request->validate([
            'judul_lagu' => 'required|string|max:255',
            'artis_sumber' => 'nullable|string|max:255',
            'nada_dasar' => 'nullable|string|max:10',
            'bagian_terakhir' => 'nullable|string|max:100',
            'youtube_url' => 'nullable|url|max:255',
            'chord_url' => 'nullable|url|max:255',
            'lirik_url' => 'nullable|url|max:255',
            'lirik_teks' => 'nullable|string',
        ]);

        $song->update([
            'judul_lagu' => $request->judul_lagu,
            'artis_sumber' => $request->artis_sumber,
            'nada_dasar' => $request->nada_dasar,
            'bagian_terakhir' => $request->bagian_terakhir,
            'youtube_url' => $request->youtube_url,
            'chord_url' => $request->chord_url,
            'lirik_url' => $request->lirik_url,
            'lirik_teks' => $request->lirik_teks,
            'updatedBy' => Auth::user()->name ?? 'Administrator',
        ]);

        return redirect()->back()->with('success', 'Data lagu berhasil diperbarui!');
    }

    /**
     * Hapus Lagu (Soft Delete)
     */
    public function destroySong($id)
    {
        $song = Song::where('isDelete', 0)->findOrFail($id);
        $song->update(['isDelete' => 1]);

        return redirect()->back()->with('success', 'Lagu berhasil dihapus dari pustaka!');
    }

    /**
     * Upload File Bahan Khotbah / Media (Slide PPT, PDF, Video, Dokumen)
     */
    public function uploadMedia(Request $request)
    {
        $request->validate([
            'file' => 'nullable|file|max:51200',
            'media_file' => 'nullable|file|max:51200',
            'judul' => 'nullable|string|max:255',
        ]);

        $file = $request->file('file') ?: $request->file('media_file');
        if (! $file) {
            return response()->json(['success' => false, 'message' => 'File tidak ditemukan.'], 422);
        }

        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $sizeBytes = $file->getSize();

        // Format ukuran file human-readable
        if ($sizeBytes >= 1048576) {
            $formattedSize = round($sizeBytes / 1048576, 1).' MB';
        } else {
            $formattedSize = round($sizeBytes / 1024).' KB';
        }

        // Tentukan label tipe
        $tipeLabel = match ($extension) {
            'ppt', 'pptx', 'key' => 'Slide PPT',
            'pdf' => 'PDF',
            'doc', 'docx' => 'Dokumen Word',
            'xls', 'xlsx' => 'Excel',
            'mp4', 'mov', 'avi' => 'Video',
            'jpg', 'jpeg', 'png', 'webp' => 'Gambar',
            default => strtoupper($extension),
        };

        // Simpan ke storage/app/public/worship_materials
        $fileName = time().'_'.preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
        $path = $file->storeAs('worship_materials', $fileName, 'public');
        $url = asset('storage/'.$path);

        $mediaItem = [
            'judul' => $request->judul ?: pathinfo($originalName, PATHINFO_FILENAME),
            'nama_file' => $originalName,
            'tipe' => $tipeLabel,
            'url' => $url,
            'file_path' => $path,
            'size' => $formattedSize,
            'uploaded_at' => now()->format('Y-m-d H:i:s'),
        ];

        return response()->json([
            'success' => true,
            'message' => 'File bahan khotbah berhasil diunggah ke hosting!',
            'file' => $mediaItem,
        ]);
    }

    /**
     * Halaman Publik /ibadah (Dapat dibuka tanpa login untuk jemaat & tim pelayanan)
     */
    public function publicIndex(Request $request)
    {
        $today = Carbon::today('Asia/Jakarta');

        $tab = $request->get('tab', 'mendatang'); // 'mendatang' | 'arsip'
        $jenisFilter = $request->get('jenis');
        $lokasiFilter = $request->get('lokasi');
        $selectedId = $request->get('id');

        $cabangs = Cabang::where('isDelete', 0)->where('isActive', 1)->orderBy('nama_cabang')->get();
        $jenisIbadahs = JenisIbadah::where('isDelete', 0)->where('isActive', 1)->orderBy('nama_ibadah')->get();

        $countMendatang = WorshipSchedule::where('isDelete', 0)
            ->where('isActive', 1)
            ->where('tanggal', '>=', $today->format('Y-m-d'))
            ->count();

        $countArsip = WorshipSchedule::where('isDelete', 0)
            ->where('isActive', 1)
            ->where('tanggal', '<', $today->format('Y-m-d'))
            ->count();

        $query = WorshipSchedule::with(['scheduleSongs.song', 'cabang', 'jenisIbadah', 'sesi'])
            ->where('isDelete', 0)
            ->where('isActive', 1);

        if ($tab === 'arsip') {
            $query->where('tanggal', '<', $today->format('Y-m-d'))
                ->orderBy('tanggal', 'desc');
        } else {
            $query->where('tanggal', '>=', $today->format('Y-m-d'))
                ->orderBy('tanggal', 'asc');
        }

        if ($jenisFilter) {
            $query->where(function ($q) use ($jenisFilter) {
                $q->where('nama_ibadah', $jenisFilter)
                    ->orWhere('jenis_ibadah_id', $jenisFilter);
            });
        }

        if ($lokasiFilter) {
            $query->where(function ($q) use ($lokasiFilter) {
                $q->where('nama_cabang', $lokasiFilter)
                    ->orWhere('cabang_id', $lokasiFilter);
            });
        }

        $schedules = $query->get();

        // Selected Schedule jika ID diberikan
        $selectedSchedule = null;
        if ($selectedId) {
            $selectedSchedule = WorshipSchedule::with(['scheduleSongs.song', 'cabang', 'jenisIbadah', 'sesi'])
                ->where('isDelete', 0)
                ->find($selectedId);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'tab' => $tab,
                'schedules' => $schedules,
                'countMendatang' => $countMendatang,
                'countArsip' => $countArsip,
                'selectedSchedule' => $selectedSchedule,
            ]);
        }

        return view('homepage.ibadahPublic', compact(
            'schedules',
            'cabangs',
            'jenisIbadahs',
            'countMendatang',
            'countArsip',
            'tab',
            'jenisFilter',
            'lokasiFilter',
            'selectedSchedule'
        ));
    }

    /**
     * Halaman Publik Detail /ibadah/{id}
     */
    public function publicDetail($id)
    {
        return redirect()->route('ibadah.public', ['id' => $id]);
    }
}

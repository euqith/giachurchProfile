<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceDetail;
use App\Models\AttendanceSession;
use App\Models\Cabang;
use App\Models\Jemaat;
use App\Models\JenisIbadah;
use App\Models\KategoriJemaat;
use App\Models\Sesi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    /**
     * Tampilkan Halaman Input Kehadiran
     */
    public function input()
    {
        $cabangs = Cabang::where('isDelete', 0)->where('isActive', 1)->orderBy('nama_cabang')->get();
        $jenisIbadahs = JenisIbadah::where('isDelete', 0)->where('isActive', 1)->orderBy('nama_ibadah')->get();
        $sesis = Sesi::where('isDelete', 0)->where('isActive', 1)->orderBy('id')->get();

        $jemaats = Jemaat::with('cabang')
            ->where('isDelete', 0)
            ->where('isActive', 1)
            ->orderBy('nama_asli')
            ->get(['id', 'nama_asli', 'status', 'alias_1', 'alias_2', 'keluarga', 'badge_tag', 'cabang_id']);

        return view('admin.attendance.inputCMS', compact('cabangs', 'jenisIbadahs', 'sesis', 'jemaats'));
    }

    /**
     * Simpan / Perbarui Sesi Kehadiran
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nama_ibadah' => 'required|string',
            'nama_sesi' => 'nullable|string',
            'nama_cabang' => 'required|string',
            'nama_petugas' => 'nullable|string',
            'present_list' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $jenisIbadah = JenisIbadah::where('nama_ibadah', $request->nama_ibadah)->first();
            $cabang = Cabang::where('nama_cabang', $request->nama_cabang)->first();
            $sesi = Sesi::where('nama_sesi', $request->nama_sesi)->first();

            $totalAnggota = 0;
            $totalTamu = 0;

            foreach ($request->present_list as $item) {
                if (($item['status'] ?? '') === 'Tamu') {
                    $totalTamu++;
                } else {
                    $totalAnggota++;
                }
            }

            $session = AttendanceSession::updateOrCreate(
                [
                    'tanggal' => $request->tanggal,
                    'nama_ibadah' => $request->nama_ibadah,
                    'nama_sesi' => $request->nama_sesi ?: null,
                    'nama_cabang' => $request->nama_cabang,
                    'isDelete' => 0,
                ],
                [
                    'jenis_ibadah_id' => $jenisIbadah->id ?? null,
                    'cabang_id' => $cabang->id ?? null,
                    'nama_petugas' => $request->nama_petugas ?: (Auth::user()->name ?? 'Administrator'),
                    'total_anggota' => $totalAnggota,
                    'total_tamu' => $totalTamu,
                    'total_hadir' => count($request->present_list),
                ]
            );

            // Bersihkan data rincian lama agar tersinkronisasi
            $session->details()->delete();

            foreach ($request->present_list as $item) {
                $jemaatId = null;
                if (isset($item['id']) && is_numeric($item['id'])) {
                    $exists = Jemaat::where('id', $item['id'])->where('isDelete', 0)->exists();
                    if ($exists) {
                        $jemaatId = $item['id'];
                    }
                }

                // Jika tamu dan belum memiliki ID di jemaat, simpan otomatis ke database jemaat dengan kategori Tamu
                if (! $jemaatId && ($item['status'] ?? '') === 'Tamu') {
                    $guestName = trim($item['nama_asli'] ?? '');
                    $guest = Jemaat::where('nama_asli', $guestName)->where('isDelete', 0)->first();
                    if (! $guest) {
                        $guest = Jemaat::create([
                            'nama_asli' => $guestName,
                            'status' => 'Tamu',
                            'kategori' => 'Tamu',
                            'cabang_id' => $cabang->id ?? null,
                            'createdBy' => Auth::user()->name ?? 'Administrator',
                            'isActive' => 1,
                            'isDelete' => 0,
                        ]);
                    }
                    $jemaatId = $guest->id;
                }

                AttendanceDetail::create([
                    'attendance_session_id' => $session->id,
                    'jemaat_id' => $jemaatId,
                    'nama_jemaat' => $item['nama_asli'],
                    'status' => $item['status'] ?? 'Anggota',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data kehadiran berhasil disimpan!',
                'session_id' => $session->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Ambil Draft Sesi yang Tersimpan di Database
     */
    public function getSessionDraft(Request $request)
    {
        $date = $request->query('date');
        $ibadah = $request->query('ibadah');
        $sesi = $request->query('sesi');
        $lokasi = $request->query('lokasi');

        $query = AttendanceSession::with('details.jemaat.cabang')
            ->where('tanggal', $date)
            ->where('nama_ibadah', $ibadah)
            ->where('nama_cabang', $lokasi)
            ->where('isDelete', 0);

        if ($sesi) {
            $query->where('nama_sesi', $sesi);
        }

        $session = $query->first();

        return response()->json([
            'success' => (bool) $session,
            'session' => $session,
            'details' => $session ? $session->details : [],
        ]);
    }

    /**
     * Simpan Data Tamu Baru Langsung ke Database Jemaat dengan Kategori Tamu
     */
    public function storeGuest(Request $request)
    {
        $request->validate([
            'nama_asli' => 'required|string|max:255',
            'cabang_id' => 'nullable|exists:cabangs,id',
            'telepon' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
        ]);

        $nama = trim($request->nama_asli);

        $guest = Jemaat::where('nama_asli', $nama)->where('isDelete', 0)->first();

        if (! $guest) {
            $guest = Jemaat::create([
                'nama_asli' => $nama,
                'status' => 'Tamu',
                'kategori' => 'Tamu',
                'cabang_id' => $request->cabang_id,
                'telepon' => $request->telepon,
                'alamat' => $request->alamat,
                'createdBy' => Auth::user()->name ?? 'Administrator',
                'isActive' => 1,
                'isDelete' => 0,
            ]);
        } else {
            if ($guest->status !== 'Tamu' && empty($guest->status)) {
                $guest->update(['status' => 'Tamu', 'kategori' => 'Tamu']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Tamu berhasil ditambahkan ke database jemaat!',
            'jemaat' => $guest->load('cabang'),
        ]);
    }

    /**
     * Ambil Detail Jemaat dan 10 Riwayat Kehadiran Terakhir
     */
    public function jemaatDetail($id)
    {
        $jemaat = Jemaat::with('cabang')->where('isDelete', 0)->findOrFail($id);

        $attendances = AttendanceDetail::where('jemaat_id', $jemaat->id)
            ->whereHas('session', fn ($q) => $q->where('isDelete', 0))
            ->join('attendance_sessions', 'attendance_details.attendance_session_id', '=', 'attendance_sessions.id')
            ->orderBy('attendance_sessions.tanggal', 'desc')
            ->orderBy('attendance_sessions.id', 'desc')
            ->take(10)
            ->get([
                'attendance_details.id as detail_id',
                'attendance_details.status',
                'attendance_details.created_at',
                'attendance_sessions.id as session_id',
                'attendance_sessions.tanggal',
                'attendance_sessions.nama_ibadah',
                'attendance_sessions.nama_sesi',
                'attendance_sessions.nama_cabang',
            ]);

        $totalKehadiran = AttendanceDetail::where('jemaat_id', $jemaat->id)
            ->whereHas('session', fn ($q) => $q->where('isDelete', 0))
            ->count();

        return response()->json([
            'success' => true,
            'jemaat' => [
                'id' => $jemaat->id,
                'nama_asli' => $jemaat->nama_asli,
                'status' => $jemaat->status,
                'kategori' => $jemaat->kategori_usia,
                'usia' => $jemaat->usia,
                'tgl_lahir' => $jemaat->tgl_lahir ? $jemaat->tgl_lahir->translatedFormat('d F Y') : '-',
                'tempat_lahir' => $jemaat->tempat_lahir ?: '-',
                'telepon' => $jemaat->telepon ?: '-',
                'alamat' => $jemaat->alamat ?: '-',
                'keluarga' => $jemaat->keluarga ?: '-',
                'cabang' => $jemaat->cabang->nama_cabang ?? 'Pusat',
                'fokus_ibadah' => $jemaat->fokus_ibadah,
                'total_kehadiran' => $totalKehadiran,
            ],
            'history' => $attendances->map(function ($att) {
                return [
                    'tanggal' => Carbon::parse($att->tanggal)->translatedFormat('d M Y'),
                    'hari' => Carbon::parse($att->tanggal)->translatedFormat('l'),
                    'nama_ibadah' => $att->nama_ibadah,
                    'nama_sesi' => $att->nama_sesi ?: '-',
                    'nama_cabang' => $att->nama_cabang,
                    'status' => $att->status,
                ];
            }),
        ]);
    }

    /**
     * Tampilkan Halaman Riwayat Sesi
     */
    public function history(Request $request)
    {
        $cabangs = Cabang::where('isDelete', 0)->where('isActive', 1)->orderBy('nama_cabang')->get();
        $jenisIbadahs = JenisIbadah::where('isDelete', 0)->where('isActive', 1)->orderBy('nama_ibadah')->get();
        $sesis = Sesi::where('isDelete', 0)->where('isActive', 1)->orderBy('id')->get();

        $query = AttendanceSession::with('cabang', 'jenisIbadah')->where('isDelete', 0);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_ibadah', 'like', "%{$search}%")
                    ->orWhere('nama_sesi', 'like', "%{$search}%")
                    ->orWhere('nama_cabang', 'like', "%{$search}%")
                    ->orWhere('nama_petugas', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jenis') && $request->jenis !== 'all') {
            $query->where('nama_ibadah', $request->jenis);
        }

        if ($request->filled('sesi') && $request->sesi !== 'all') {
            $query->where('nama_sesi', $request->sesi);
        }

        if ($request->filled('lokasi') && $request->lokasi !== 'all') {
            $query->where('nama_cabang', $request->lokasi);
        }

        $sessions = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(20)->withQueryString();

        $jemaats = Jemaat::with('cabang')->where('isDelete', 0)->where('isActive', 1)->get();

        return view('admin.attendance.historyCMS', compact('sessions', 'cabangs', 'jenisIbadahs', 'sesis', 'jemaats'));
    }

    /**
     * Ambil Data Detail Sesi untuk Modal
     */
    public function show($id)
    {
        $session = AttendanceSession::with(['details.jemaat.cabang', 'cabang', 'jenisIbadah'])->findOrFail($id);

        return response()->json($session);
    }

    /**
     * Perbarui / Koreksi Sesi dan Rincian Kehadiran
     */
    public function update(Request $request, $id)
    {
        $session = AttendanceSession::where('isDelete', 0)->findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
            'nama_ibadah' => 'required|string',
            'nama_sesi' => 'nullable|string',
            'nama_cabang' => 'required|string',
            'nama_petugas' => 'nullable|string',
            'present_list' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $jenisIbadah = JenisIbadah::where('nama_ibadah', $request->nama_ibadah)->first();
            $cabang = Cabang::where('nama_cabang', $request->nama_cabang)->first();

            $totalAnggota = 0;
            $totalTamu = 0;

            foreach ($request->present_list as $item) {
                if (($item['status'] ?? '') === 'Tamu') {
                    $totalTamu++;
                } else {
                    $totalAnggota++;
                }
            }

            $session->update([
                'tanggal' => $request->tanggal,
                'nama_ibadah' => $request->nama_ibadah,
                'nama_sesi' => $request->nama_sesi ?: null,
                'nama_cabang' => $request->nama_cabang,
                'jenis_ibadah_id' => $jenisIbadah->id ?? null,
                'cabang_id' => $cabang->id ?? null,
                'nama_petugas' => $request->nama_petugas ?: $session->nama_petugas,
                'total_anggota' => $totalAnggota,
                'total_tamu' => $totalTamu,
                'total_hadir' => count($request->present_list),
            ]);

            $session->details()->delete();

            foreach ($request->present_list as $item) {
                $jemaatId = null;
                if (isset($item['id']) && is_numeric($item['id'])) {
                    $exists = Jemaat::where('id', $item['id'])->where('isDelete', 0)->exists();
                    if ($exists) {
                        $jemaatId = $item['id'];
                    }
                }

                // Jika tamu dan belum memiliki ID di jemaat, simpan otomatis ke database jemaat dengan kategori Tamu
                if (! $jemaatId && ($item['status'] ?? '') === 'Tamu') {
                    $guestName = trim($item['nama_asli'] ?? ($item['nama_jemaat'] ?? ''));
                    $guest = Jemaat::where('nama_asli', $guestName)->where('isDelete', 0)->first();
                    if (! $guest) {
                        $guest = Jemaat::create([
                            'nama_asli' => $guestName,
                            'status' => 'Tamu',
                            'kategori' => 'Tamu',
                            'cabang_id' => $cabang->id ?? null,
                            'createdBy' => Auth::user()->name ?? 'Administrator',
                            'isActive' => 1,
                            'isDelete' => 0,
                        ]);
                    }
                    $jemaatId = $guest->id;
                }

                AttendanceDetail::create([
                    'attendance_session_id' => $session->id,
                    'jemaat_id' => $jemaatId,
                    'nama_jemaat' => $item['nama_asli'] ?? ($item['nama_jemaat'] ?? ''),
                    'status' => $item['status'] ?? 'Anggota',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sesi kehadiran berhasil diperbarui!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Hapus Sesi Kehadiran (Soft Delete)
     */
    public function destroy($id)
    {
        $session = AttendanceSession::where('isDelete', 0)->findOrFail($id);
        $session->update(['isDelete' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Sesi ibadah berhasil dihapus!',
        ]);
    }

    /**
     * Halaman Dashboard Absensi (Laporan, Statistik, Tren, Perlu Dikunjungi, Ultah)
     */
    public function report(Request $request)
    {
        $cabangs = Cabang::where('isDelete', 0)->where('isActive', 1)->orderBy('nama_cabang')->get();
        $jenisIbadahs = JenisIbadah::where('isDelete', 0)->where('isActive', 1)->orderBy('nama_ibadah')->get();
        $sesis = Sesi::where('isDelete', 0)->where('isActive', 1)->orderBy('id')->get();

        // 1. Tentukan Rentang Periode Waktu
        $periodType = $request->get('period_type', 'bulan'); // tanggal, bulan, tahun, rentang
        $now = Carbon::now('Asia/Jakarta');

        $selectedMonth = $request->get('month', $now->format('Y-m'));
        $selectedDate = $request->get('date', $now->format('Y-m-d'));
        $selectedYear = $request->get('year', $now->format('Y'));
        $startDateParam = $request->get('start_date', $now->startOfMonth()->format('Y-m-d'));
        $endDateParam = $request->get('end_date', $now->endOfMonth()->format('Y-m-d'));

        switch ($periodType) {
            case 'tanggal':
                $startDate = Carbon::parse($selectedDate)->startOfDay();
                $endDate = Carbon::parse($selectedDate)->endOfDay();
                $periodLabel = $startDate->translatedFormat('d F Y');
                break;
            case 'tahun':
                $startDate = Carbon::createFromDate($selectedYear, 1, 1)->startOfYear();
                $endDate = Carbon::createFromDate($selectedYear, 12, 31)->endOfYear();
                $periodLabel = 'Tahun '.$selectedYear;
                break;
            case 'rentang':
                $startDate = Carbon::parse($startDateParam)->startOfDay();
                $endDate = Carbon::parse($endDateParam)->endOfDay();
                $periodLabel = $startDate->translatedFormat('d M Y').' - '.$endDate->translatedFormat('d M Y');
                break;
            case 'bulan':
            default:
                $startDate = Carbon::parse($selectedMonth.'-01')->startOfMonth();
                $endDate = Carbon::parse($selectedMonth.'-01')->endOfMonth();
                $periodLabel = $startDate->translatedFormat('F Y');
                break;
        }

        // 2. Query Sesi Ibadah Pada Periode
        $sessionQuery = AttendanceSession::where('isDelete', 0)
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

        if ($request->filled('jenis') && $request->jenis !== 'all') {
            $sessionQuery->where('nama_ibadah', $request->jenis);
        }
        if ($request->filled('sesi') && $request->sesi !== 'all') {
            $sessionQuery->where('nama_sesi', $request->sesi);
        }
        if ($request->filled('lokasi') && $request->lokasi !== 'all') {
            $sessionQuery->where('nama_cabang', $request->lokasi);
        }

        $sessions = $sessionQuery->get();
        $sessionIds = $sessions->pluck('id')->toArray();

        // 3. Hitung KPI Ringkasan
        $allActiveJemaats = Jemaat::with('cabang')->where('isDelete', 0)->where('isActive', 1)->get();
        $totalAnggotaTerdaftar = $allActiveJemaats->where('status', '!=', 'Tamu')->count();

        // Rincian kehadiran dalam sesi-sesi periode
        $detailsInPeriod = AttendanceDetail::whereIn('attendance_session_id', $sessionIds)->get();

        $uniqueJemaatIdsHadir = $detailsInPeriod->pluck('jemaat_id')->filter()->unique()->toArray();
        $uniqueTamuHadir = $detailsInPeriod->where('status', 'Tamu')->unique('nama_jemaat')->count();

        $totalHadirAnggota = count($uniqueJemaatIdsHadir);
        $totalHadir = $totalHadirAnggota + $uniqueTamuHadir;

        // Total jemaat aktif + tamu tercatat
        $totalJemaat = $totalAnggotaTerdaftar + $uniqueTamuHadir;

        // Anggota tidak hadir
        $totalTidakHadir = max(0, $totalAnggotaTerdaftar - $totalHadirAnggota);

        // Persentase kehadiran anggota
        $persentaseKehadiran = $totalAnggotaTerdaftar > 0
            ? round(($totalHadirAnggota / $totalAnggotaTerdaftar) * 100)
            : 0;

        // Jumlah Sesi dan Rata-rata
        $jumlahSesi = $sessions->count();
        $rataRataPerSesi = $jumlahSesi > 0
            ? round($detailsInPeriod->count() / $jumlahSesi)
            : 0;

        // Tamu Baru (yang baru pertama kali hadir di rentang tanggal ini)
        $tamuBaruCount = 0;
        $uniqueGuestNames = $detailsInPeriod->where('status', 'Tamu')->pluck('nama_jemaat')->unique();
        foreach ($uniqueGuestNames as $guestName) {
            $firstSession = AttendanceDetail::where('nama_jemaat', $guestName)
                ->where('status', 'Tamu')
                ->whereHas('session', function ($q) {
                    $q->where('isDelete', 0);
                })
                ->join('attendance_sessions', 'attendance_details.attendance_session_id', '=', 'attendance_sessions.id')
                ->orderBy('attendance_sessions.tanggal', 'asc')
                ->first(['attendance_sessions.tanggal']);

            if ($firstSession && Carbon::parse($firstSession->tanggal)->between($startDate, $endDate)) {
                $tamuBaruCount++;
            }
        }

        // 4. Grafik Tren Kehadiran (Per Minggu dalam Periode atau 6 Minggu Terakhir)
        $trenWeeks = [];
        $trenStart = $startDate->copy()->startOfWeek(Carbon::MONDAY);
        $trenEnd = $endDate->copy()->endOfWeek(Carbon::SUNDAY);

        // Jika rentang lebih dari 12 minggu, ambil 8 minggu terakhir
        if ($trenStart->diffInWeeks($trenEnd) > 12) {
            $trenStart = $endDate->copy()->subWeeks(7)->startOfWeek(Carbon::MONDAY);
        }

        $currentWeek = $trenStart->copy();
        $totalTrenHadir = 0;
        $weekCount = 0;

        while ($currentWeek->lte($trenEnd)) {
            $wStart = $currentWeek->format('Y-m-d');
            $wEnd = $currentWeek->copy()->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');
            $wLabel = $currentWeek->copy()->endOfWeek(Carbon::SUNDAY)->translatedFormat('d M');

            $weeklyCount = AttendanceDetail::whereHas('session', function ($q) use ($wStart, $wEnd, $request) {
                $q->where('isDelete', 0)->whereBetween('tanggal', [$wStart, $wEnd]);
                if ($request->filled('jenis') && $request->jenis !== 'all') {
                    $q->where('nama_ibadah', $request->jenis);
                }
                if ($request->filled('sesi') && $request->sesi !== 'all') {
                    $q->where('nama_sesi', $request->sesi);
                }
                if ($request->filled('lokasi') && $request->lokasi !== 'all') {
                    $q->where('nama_cabang', $request->lokasi);
                }
            })->count();

            $trenWeeks[] = [
                'label' => $wLabel,
                'count' => $weeklyCount,
                'w_start' => $wStart,
                'w_end' => $wEnd,
            ];

            $totalTrenHadir += $weeklyCount;
            $weekCount++;
            $currentWeek->addWeek();
        }

        $rataRataTren = $weekCount > 0 ? round($totalTrenHadir / $weekCount) : 0;
        $targetMingguan = $request->get('target', 60);

        // 5. Panel "Perlu Dikunjungi" (Anggota yang absen 3 minggu / 21 hari berturut-turut)
        $tigaMingguLalu = $now->copy()->subDays(21)->format('Y-m-d');

        $hadir3Minggu = AttendanceDetail::whereHas('session', function ($q) use ($tigaMingguLalu) {
            $q->where('isDelete', 0)->where('tanggal', '>=', $tigaMingguLalu);
        })->pluck('jemaat_id')->filter()->unique()->toArray();

        $perluDikunjungi = [];
        foreach ($allActiveJemaats as $jem) {
            if ($jem->status !== 'Tamu' && ! in_array($jem->id, $hadir3Minggu)) {
                $lastSeen = AttendanceDetail::where('jemaat_id', $jem->id)
                    ->join('attendance_sessions', 'attendance_details.attendance_session_id', '=', 'attendance_sessions.id')
                    ->where('attendance_sessions.isDelete', 0)
                    ->orderBy('attendance_sessions.tanggal', 'desc')
                    ->first(['attendance_sessions.tanggal']);

                $perluDikunjungi[] = [
                    'id' => $jem->id,
                    'nama_asli' => $jem->nama_asli,
                    'wilayah' => $jem->cabang->nama_cabang ?? 'Pusat',
                    'telepon' => $jem->telepon ?? '-',
                    'terakhir_hadir' => $lastSeen ? Carbon::parse($lastSeen->tanggal)->translatedFormat('d M Y') : 'Belum pernah',
                ];
            }
        }

        // 6. Widget Ulang Tahun Jemaat (Minggu Ini & Bulan Ini)
        $filterUltah = $request->get('ultah_mode', 'minggu'); // minggu | bulan
        $ulangTahunList = [];

        $startOfWeek = $now->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $now->copy()->endOfWeek(Carbon::SUNDAY);

        foreach ($allActiveJemaats as $j) {
            $birthDate = $j->tgl_lahir ?: ($j->tanggal_lahir ? Carbon::parse($j->tanggal_lahir) : null);
            if ($birthDate) {
                $thisYearBirthday = Carbon::create($now->year, $birthDate->month, $birthDate->day, 0, 0, 0, 'Asia/Jakarta');

                $isMatch = false;
                if ($filterUltah === 'bulan') {
                    $isMatch = ($birthDate->month === $now->month);
                } else {
                    $isMatch = $thisYearBirthday->between($startOfWeek, $endOfWeek);
                }

                if ($isMatch) {
                    $age = $now->year - $birthDate->year;
                    $ulangTahunList[] = [
                        'nama_asli' => $j->nama_asli,
                        'tanggal' => $thisYearBirthday->translatedFormat('d M'),
                        'hari' => $thisYearBirthday->translatedFormat('l'),
                        'usia' => $age,
                        'wilayah' => $j->cabang->nama_cabang ?? 'Pusat',
                        'is_today' => $thisYearBirthday->isToday(),
                    ];
                }
            }
        }

        // Urutkan ulang tahun dari tanggal terdekat
        usort($ulangTahunList, fn ($a, $b) => strcmp($a['tanggal'], $b['tanggal']));

        // 7. Tabel Rincian Kehadiran Jemaat (Hadir / Tidak Hadir / Tamu Baru)
        $listHadir = [];
        $listTidakHadir = [];
        $listTamuBaru = [];

        // Kelompokkan kehadiran per jemaat ID
        $kehadiranCount = $detailsInPeriod->groupBy('jemaat_id');
        $terakhirHadirMap = [];

        foreach ($allActiveJemaats as $jem) {
            $hadirCount = isset($kehadiranCount[$jem->id]) ? $kehadiranCount[$jem->id]->count() : 0;

            $lastRecord = AttendanceDetail::where('jemaat_id', $jem->id)
                ->join('attendance_sessions', 'attendance_details.attendance_session_id', '=', 'attendance_sessions.id')
                ->where('attendance_sessions.isDelete', 0)
                ->orderBy('attendance_sessions.tanggal', 'desc')
                ->first(['attendance_sessions.tanggal']);

            $terakhirHadirStr = $lastRecord ? Carbon::parse($lastRecord->tanggal)->translatedFormat('d M Y') : '-';

            $itemData = [
                'id' => $jem->id,
                'nama' => $jem->nama_asli,
                'status' => $jem->status ?? 'Anggota',
                'kategori' => $jem->kategori_usia,
                'usia' => $jem->usia ? $jem->usia.' th' : '-',
                'alias' => $jem->alias_1 ?: ($jem->alias_2 ?: '-'),
                'wilayah' => $jem->cabang->nama_cabang ?? 'Tanpa Wilayah',
                'kehadiran' => $hadirCount.'x',
                'terakhir_hadir' => $terakhirHadirStr,
            ];

            if ($hadirCount > 0) {
                $listHadir[] = $itemData;
            } else {
                $listTidakHadir[] = $itemData;
            }
        }

        // Tambahkan Tamu yang hadir dalam periode
        $tamuGrouped = $detailsInPeriod->where('status', 'Tamu')->groupBy('nama_jemaat');
        foreach ($tamuGrouped as $tamuName => $records) {
            $lastTamuRecord = $records->sortByDesc('created_at')->first();
            $firstSession = AttendanceDetail::where('nama_jemaat', $tamuName)
                ->join('attendance_sessions', 'attendance_details.attendance_session_id', '=', 'attendance_sessions.id')
                ->where('attendance_sessions.isDelete', 0)
                ->orderBy('attendance_sessions.tanggal', 'asc')
                ->first(['attendance_sessions.tanggal']);

            $isNewGuest = $firstSession && Carbon::parse($firstSession->tanggal)->between($startDate, $endDate);

            $tamuJemaat = Jemaat::where('nama_asli', $tamuName)->where('isDelete', 0)->first();

            $tamuData = [
                'id' => $tamuJemaat ? $tamuJemaat->id : ('tamu_'.md5($tamuName)),
                'nama' => $tamuName,
                'status' => 'Tamu',
                'kategori' => 'Tamu',
                'usia' => $tamuJemaat?->usia ? $tamuJemaat->usia.' th' : '-',
                'alias' => '-',
                'wilayah' => $tamuJemaat?->cabang?->nama_cabang ?? '-',
                'kehadiran' => $records->count().'x',
                'terakhir_hadir' => $lastTamuRecord ? Carbon::parse($lastTamuRecord->created_at)->translatedFormat('d M Y') : '-',
            ];

            $listHadir[] = $tamuData;

            if ($isNewGuest) {
                $listTamuBaru[] = $tamuData;
            }
        }

        // 8. Laporan Statistik & Distribusi Kehadiran Berdasarkan Kategori Usia
        $masterKategori = KategoriJemaat::where('isDelete', 0)->where('isActive', 1)->orderBy('urutan')->get();

        $categoriesMap = [
            'Anak' => ['label' => 'Anak (0-12 th)', 'color' => '#f59e0b', 'badge_class' => 'bg-amber-100 text-amber-800 border-amber-200'],
            'Remaja' => ['label' => 'Remaja (13-20 th)', 'color' => '#10b981', 'badge_class' => 'bg-emerald-100 text-emerald-800 border-emerald-200'],
            'Pemuda' => ['label' => 'Pemuda (21-30 th)', 'color' => '#3b82f6', 'badge_class' => 'bg-blue-100 text-blue-800 border-blue-200'],
            'Dewasa' => ['label' => 'Dewasa (31-59 th)', 'color' => '#6366f1', 'badge_class' => 'bg-indigo-100 text-indigo-800 border-indigo-200'],
            'Lansia' => ['label' => 'Lansia (>= 60 th)', 'color' => '#a855f7', 'badge_class' => 'bg-purple-100 text-purple-800 border-purple-200'],
            'Tamu' => ['label' => 'Tamu', 'color' => '#ec4899', 'badge_class' => 'bg-rose-100 text-rose-800 border-rose-200'],
        ];

        // Hitung total terdaftar per kategori
        $jemaatPerKategori = [
            'Anak' => 0,
            'Remaja' => 0,
            'Pemuda' => 0,
            'Dewasa' => 0,
            'Lansia' => 0,
            'Tamu' => $allActiveJemaats->where('status', 'Tamu')->count(),
        ];

        foreach ($allActiveJemaats as $jem) {
            $kat = $jem->kategori_usia;
            if (isset($jemaatPerKategori[$kat])) {
                $jemaatPerKategori[$kat]++;
            } else {
                $jemaatPerKategori['Dewasa']++;
            }
        }

        // Hitung kehadiran unik per kategori dalam sesi yang dipilih
        $hadirPerKategori = [
            'Anak' => 0,
            'Remaja' => 0,
            'Pemuda' => 0,
            'Dewasa' => 0,
            'Lansia' => 0,
            'Tamu' => $uniqueTamuHadir,
        ];

        $hadirJemaats = $allActiveJemaats->whereIn('id', $uniqueJemaatIdsHadir);
        foreach ($hadirJemaats as $jem) {
            $kat = $jem->kategori_usia;
            if (isset($hadirPerKategori[$kat])) {
                $hadirPerKategori[$kat]++;
            } else {
                $hadirPerKategori['Dewasa']++;
            }
        }

        $kategoriStats = [];
        foreach ($categoriesMap as $catKey => $meta) {
            $totJem = $jemaatPerKategori[$catKey] ?? 0;
            $totHadir = $hadirPerKategori[$catKey] ?? 0;
            $persenKehadiran = $totJem > 0 ? round(($totHadir / $totJem) * 100) : 0;
            $shareTotal = $totalHadir > 0 ? round(($totHadir / $totalHadir) * 100, 1) : 0;

            $kategoriStats[$catKey] = [
                'nama' => $catKey,
                'label' => $meta['label'],
                'color' => $meta['color'],
                'badge_class' => $meta['badge_class'],
                'total_terdaftar' => $totJem,
                'total_hadir' => $totHadir,
                'persen_kehadiran' => $persenKehadiran,
                'share_total' => $shareTotal,
            ];
        }

        return view('admin.attendance.dashboardCMS', compact(
            'cabangs',
            'jenisIbadahs',
            'sesis',
            'periodType',
            'periodLabel',
            'selectedMonth',
            'selectedDate',
            'selectedYear',
            'startDateParam',
            'endDateParam',
            'totalJemaat',
            'totalHadir',
            'totalHadirAnggota',
            'uniqueTamuHadir',
            'totalTidakHadir',
            'persentaseKehadiran',
            'jumlahSesi',
            'rataRataPerSesi',
            'tamuBaruCount',
            'trenWeeks',
            'rataRataTren',
            'targetMingguan',
            'perluDikunjungi',
            'ulangTahunList',
            'filterUltah',
            'listHadir',
            'listTidakHadir',
            'listTamuBaru',
            'masterKategori',
            'kategoriStats'
        ));
    }

    /**
     * Ekspor Daftar Jemaat Perlu Dikunjungi (CSV)
     */
    public function exportKunjungan()
    {
        $now = Carbon::now('Asia/Jakarta');
        $tigaMingguLalu = $now->copy()->subDays(21)->format('Y-m-d');

        $hadir3Minggu = AttendanceDetail::whereHas('session', function ($q) use ($tigaMingguLalu) {
            $q->where('isDelete', 0)->where('tanggal', '>=', $tigaMingguLalu);
        })->pluck('jemaat_id')->filter()->unique()->toArray();

        $allActiveJemaats = Jemaat::with('cabang')->where('isDelete', 0)->where('isActive', 1)->get();

        $rows = [];
        $rows[] = ['No', 'Nama Jemaat', 'Status', 'Wilayah Ibadah', 'Telepon', 'Alamat', 'Terakhir Hadir'];

        $no = 1;
        foreach ($allActiveJemaats as $jem) {
            if ($jem->status !== 'Tamu' && ! in_array($jem->id, $hadir3Minggu)) {
                $lastSeen = AttendanceDetail::where('jemaat_id', $jem->id)
                    ->join('attendance_sessions', 'attendance_details.attendance_session_id', '=', 'attendance_sessions.id')
                    ->where('attendance_sessions.isDelete', 0)
                    ->orderBy('attendance_sessions.tanggal', 'desc')
                    ->first(['attendance_sessions.tanggal']);

                $rows[] = [
                    $no++,
                    $jem->nama_asli,
                    $jem->status ?? 'Anggota',
                    $jem->cabang->nama_cabang ?? 'Pusat',
                    $jem->telepon ?? '-',
                    $jem->alamat ?? '-',
                    $lastSeen ? Carbon::parse($lastSeen->tanggal)->format('d/m/Y') : 'Belum pernah',
                ];
            }
        }

        $fileName = 'daftar-kunjungan-jemaat-'.$now->format('Y-m-d').'.csv';

        return new StreamedResponse(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ]);
    }
}

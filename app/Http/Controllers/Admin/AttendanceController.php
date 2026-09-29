<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\JenisIbadah;
use App\Models\Jemaat;
use App\Models\AttendanceSession;
use App\Models\AttendanceDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function input()
    {
        $cabangs = Cabang::where('isDelete', 0)->where('isActive', 1)->get();
        $jenisIbadahs = JenisIbadah::where('isDelete', 0)->where('isActive', 1)->get();
        $jemaats = Jemaat::where('isDelete', 0)->where('isActive', 1)
                        ->get(['id', 'nama_asli', 'status', 'alias_1', 'alias_2', 'keluarga', 'badge_tag', 'cabang_id']);

        return view('admin.attendance.inputCMS', compact('cabangs', 'jenisIbadahs', 'jemaats'));
    }

    // Endpoint simpan data kehadiran dari AJAX
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'      => 'required|date',
            'nama_ibadah'  => 'required|string',
            'nama_cabang'  => 'required|string',
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

            // Simpan / Update Sesi
            $session = AttendanceSession::updateOrCreate(
                [
                    'tanggal'     => $request->tanggal,
                    'nama_ibadah' => $request->nama_ibadah,
                    'nama_cabang' => $request->nama_cabang,
                    'isDelete'    => 0,
                ],
                [
                    'jenis_ibadah_id' => $jenisIbadah->id ?? null,
                    'cabang_id'       => $cabang->id ?? null,
                    'nama_petugas'    => Auth::user()->name ?? 'Administrator',
                    'total_anggota'   => $totalAnggota,
                    'total_tamu'      => $totalTamu,
                    'total_hadir'     => count($request->present_list),
                ]
            );

            // Hapus detail lama jika update sesi yang sama
            $session->details()->delete();

            // Insert detail kehadiran jemaat
            foreach ($request->present_list as $item) {
                AttendanceDetail::create([
                    'attendance_session_id' => $session->id,
                    'jemaat_id'             => is_numeric($item['id'] ?? null) ? $item['id'] : null,
                    'nama_jemaat'           => $item['nama_asli'],
                    'status'                => $item['status'] ?? 'Anggota',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data kehadiran berhasil disimpan ke database!',
                'session_id' => $session->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    // Riwayat Sesi Ibadah
    public function history(Request $request)
    {
        $query = AttendanceSession::where('isDelete', 0);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_ibadah', 'like', "%{$search}%")
                  ->orWhere('nama_cabang', 'like', "%{$search}%")
                  ->orWhere('nama_petugas', 'like', "%{$search}%");
            });
        }

        $sessions = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->get();

        return view('admin.attendance.historyCMS', compact('sessions'));
    }

    // Detail Sesi Ibadah (Modal / Ajax)
    public function show($id)
    {
        $session = AttendanceSession::with('details')->findOrFail($id);
        return response()->json($session);
    }
    // Endpoint Fetch Data Sesi Aktif dari Database
    public function getSessionDraft(Request $request)
    {
        $date = $request->query('date');
        $ibadah = $request->query('ibadah');
        $lokasi = $request->query('lokasi');

        $session = AttendanceSession::with('details')
            ->where('tanggal', $date)
            ->where('nama_ibadah', $ibadah)
            ->where('nama_cabang', $lokasi)
            ->where('isDelete', 0)
            ->first();

        if ($session) {
            return response()->json([
                'success' => true,
                'details' => $session->details
            ]);
        }

        return response()->json([
            'success' => false,
            'details' => []
        ]);
    }
}
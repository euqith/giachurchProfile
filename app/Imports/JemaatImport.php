<?php

namespace App\Imports;

use App\Models\Jemaat;
use App\Models\Cabang;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class JemaatImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     * @return Model|array|null
     */
    public function model(array $row): Model|array|null
    {
        // Abaikan baris jika nama_asli kosong
        if (empty($row['nama_asli'])) {
            return null;
        }

        // Cari ID Cabang berdasarkan nama Wilayah Ibadah
        $wilayahInput = $row['wilayah_ibadah'] ?? $row['wilayah'] ?? null;
        $cabangId = null;

        if (!empty($wilayahInput)) {
            $cabang = Cabang::where('isDelete', 0)
                ->where('nama_cabang', 'like', '%' . trim($wilayahInput) . '%')
                ->first();
            $cabangId = $cabang->id ?? null;
        }

        // Parsing Tanggal Lahir (Mendukung Excel Date Serial, DateTime Object, & Format String)
        $tanggalLahir = null;
        $rawTgl = $row['tanggal_lahir'] ?? $row['tgl_lahir'] ?? null;

        if (!empty($rawTgl)) {
            try {
                if (is_numeric($rawTgl)) {
                    $tanggalLahir = Date::excelToDateTimeObject($rawTgl)->format('Y-m-d');
                } else if ($rawTgl instanceof \DateTimeInterface) {
                    $tanggalLahir = $rawTgl->format('Y-m-d');
                } else {
                    $cleanDate = str_replace('/', '-', trim($rawTgl));
                    $tanggalLahir = date('Y-m-d', strtotime($cleanDate));
                }
            } catch (\Exception $e) {
                $tanggalLahir = null;
            }
        }

        // Penentuan Status Default
        $statusInput = $row['status'] ?? 'Anggota';

        // Simpan / Update data berdasarkan Nama Asli
        return Jemaat::updateOrCreate(
            ['nama_asli' => trim($row['nama_asli'])],
            [
                'alias_1'        => $row['alias_1'] ?? null,
                'alias_2'        => $row['alias_2'] ?? null,
                'cabang_id'      => $cabangId,
                'wilayah_ibadah' => $wilayahInput,
                'alamat'         => $row['alamat'] ?? null,
                'tempat_lahir'   => $row['tempat_lahir'] ?? null,
                'tanggal_lahir'  => $tanggalLahir,
                'tgl_lahir'      => $tanggalLahir,
                'telepon'        => $row['telepon'] ?? null,
                'status'         => $statusInput ?: 'Anggota',
                'keluarga'       => $row['keluarga'] ?? null,
                'isActive'       => 1,
                'isDelete'       => 0,
            ]
        );
    }
}
<?php

namespace app\commands;

use Yii;
use yii\helpers\FileHelper;
use yii\console\Controller;
use yii\console\ExitCode;

class SeedController extends Controller
{
    public function actionIndex()
    {
        $db = Yii::$app->db;
        $tx = $db->beginTransaction();
        try {
            // Hapus data lama (urutan terbalik karena foreign key)
            foreach (['dokumen_pendaftar', 'pengajuan_visa', 'pendaftaran', 'dokumen_persyaratan', 'users', 'fakultas'] as $t) {
                $db->createCommand()->delete($t)->execute();
            }

            $db->createCommand()->batchInsert('fakultas', ['fakultas_id', 'nama_fakultas', 'kode_fakultas'], [
                [1, 'Fakultas Teknik', 'FT'],
                [2, 'Fakultas Ekonomi dan Bisnis', 'FEB'],
                [3, 'Fakultas Kedokteran', 'FK'],
                [4, 'Fakultas Ilmu Budaya', 'FIB'],
                [5, 'Fakultas Teknologi Informasi', 'FTI'],
            ])->execute();

            // Semua akun dummy memakai password: password123
            $hash = Yii::$app->security->generatePasswordHash('password123');
            $db->createCommand()->batchInsert('users',
                ['user_id', 'nama', 'email', 'username', 'password', 'role', 'fakultas_id'], [
                [1, 'Ahmad Fauzi', 'ahmad@example.com', 'ahmad', $hash, 'Calon Mahasiswa', null],
                [2, 'Maria Santos', 'maria@example.com', 'maria', $hash, 'Calon Mahasiswa', null],
                [3, 'Budi Hartono', 'budi@example.com', 'budi', $hash, 'Calon Mahasiswa', null],
                [4, 'Nguyen Van An', 'nguyen@example.com', 'nguyen', $hash, 'Calon Mahasiswa', null],
                [5, 'Li Wei', 'liwei@example.com', 'liwei', $hash, 'Calon Mahasiswa', null],
                [6, 'Staf International Office', 'io@unand.ac.id', 'io', $hash, 'International Office', null],
                [7, 'Staf Fakultas Teknik', 'ft@unand.ac.id', 'fakultas', $hash, 'Fakultas', 1],
                [8, 'Staf UPT PDK', 'pdk@unand.ac.id', 'pdk', $hash, 'UPT PDK', null],
                [9, 'Staf Akademik', 'akademik@unand.ac.id', 'akademik', $hash, 'Akademik', null],
                [10, 'Staf Fakultas Ekonomi dan Bisnis', 'feb@unand.ac.id', 'fakultas_feb', $hash, 'Fakultas', 2],
            ])->execute();

            $db->createCommand()->batchInsert('dokumen_persyaratan',
                ['dokumen_id', 'nama_dokumen', 'berlaku_untuk', 'wajib', 'is_active'], [
                [1, 'Paspor', 'Semua', 1, 1],
                [2, 'Ijazah', 'Degree', 1, 1],
                [3, 'Transkrip Nilai', 'Semua', 1, 1],
                [4, 'Sertifikat Kemahiran Bahasa', 'Degree', 1, 1],
                [5, 'Application Letter', 'Non-degree', 1, 1],
                [6, 'Recommendation Letter', 'Non-degree', 0, 1],
                [7, 'Surat Pernyataan Visa', 'Semua', 0, 1],
            ])->execute();

            $pendaftaran = [
                [
                    'pendaftaran_id' => 1, 'user_id' => 1, 'nama_lengkap' => 'Ahmad Fauzi',
                    'tempat_lahir' => 'Kuala Lumpur', 'tanggal_lahir' => '1999-04-12', 'jenis_kelamin' => 'Laki-laki',
                    'asal_negara' => 'Malaysia', 'jalur' => 'asing', 'no_paspor' => 'A1234567', 'jenis_visa' => 'Student Visa',
                    'kategori_program' => 'degree', 'jenjang' => 'S2', 'fakultas_id' => 1,
                    'asal_institusi' => 'Universiti Malaya', 'nilai_akhir' => 3.65, 'pembiayaan' => 'Mandiri',
                    'pilihan_visa' => 'Ya', 'tujuan_proses' => 'IO', 'status' => 'Terkirim',
                    'submitted_at' => '2026-10-05 09:30:00',
                ],
                [
                    'pendaftaran_id' => 2, 'user_id' => 2, 'nama_lengkap' => 'Maria Santos',
                    'tempat_lahir' => 'Manila', 'tanggal_lahir' => '2000-08-21', 'jenis_kelamin' => 'Perempuan',
                    'asal_negara' => 'Filipina', 'jalur' => 'asing', 'no_paspor' => 'P7654321',
                    'kategori_program' => 'non-degree', 'jenis_program' => 'summer course', 'fakultas_id' => 2,
                    'asal_institusi' => 'University of the Philippines', 'nilai_akhir' => 3.40, 'pembiayaan' => 'Beasiswa',
                    'lama_kunjungan' => '2 bulan', 'pilihan_visa' => 'Tidak', 'tujuan_proses' => 'IO', 'status' => 'Lolos',
                    'submitted_at' => '2026-09-28 14:10:00',
                    'saran_fakultas' => 'Disarankan lolos', 'catatan_fakultas' => 'Latar belakang sesuai.',
                    'saran_by' => 10, 'saran_at' => '2026-10-01 13:00:00',
                    'keputusan_by' => 6, 'catatan_keputusan' => 'Dokumen lengkap dan memenuhi syarat.',
                    'keputusan_at' => '2026-10-02 10:00:00',
                ],
                [
                    'pendaftaran_id' => 3, 'user_id' => 3, 'nama_lengkap' => 'Budi Hartono',
                    'tempat_lahir' => 'Padang', 'tanggal_lahir' => '2001-01-30', 'jenis_kelamin' => 'Laki-laki',
                    'asal_negara' => 'Indonesia', 'jalur' => 'wni',
                    'kategori_program' => 'non-degree', 'jenis_program' => 'credit earning', 'fakultas_id' => 5,
                    'asal_institusi' => 'Politeknik Negeri Padang', 'nilai_akhir' => 3.55, 'pembiayaan' => 'Mandiri',
                    'lama_kunjungan' => '1 semester', 'tujuan_proses' => 'PDK', 'status' => 'Terkirim',
                    'submitted_at' => '2026-10-10 11:00:00',
                ],
                [
                    'pendaftaran_id' => 4, 'user_id' => 4, 'nama_lengkap' => 'Nguyen Van An',
                    'tempat_lahir' => 'Hanoi', 'tanggal_lahir' => '2002-06-05', 'jenis_kelamin' => 'Laki-laki',
                    'asal_negara' => 'Vietnam', 'jalur' => 'asing', 'no_paspor' => 'B9988776',
                    'kategori_program' => 'degree', 'jenjang' => 'S1', 'fakultas_id' => 5,
                    'asal_institusi' => 'Hanoi High School', 'nilai_akhir' => 8.50, 'pembiayaan' => 'Sponsor',
                    'tujuan_proses' => 'IO', 'status' => 'Draft',
                ],
                [
                    'pendaftaran_id' => 5, 'user_id' => 5, 'nama_lengkap' => 'Li Wei',
                    'tempat_lahir' => 'Beijing', 'tanggal_lahir' => '1995-11-02', 'jenis_kelamin' => 'Laki-laki',
                    'asal_negara' => 'Tiongkok', 'jalur' => 'asing', 'no_paspor' => 'E5566778', 'jenis_visa' => 'Student Visa',
                    'kategori_program' => 'degree', 'jenjang' => 'S3', 'fakultas_id' => 1,
                    'asal_institusi' => 'Tsinghua University', 'nilai_akhir' => 3.80, 'pembiayaan' => 'Beasiswa',
                    'pilihan_visa' => 'Ya', 'tujuan_proses' => 'IO', 'status' => 'Registration number terbit',
                    'submitted_at' => '2026-09-15 08:00:00',
                    'saran_fakultas' => 'Disarankan lolos', 'catatan_fakultas' => 'Publikasi memadai.',
                    'saran_by' => 7, 'saran_at' => '2026-09-18 13:00:00',
                    'keputusan_by' => 6, 'catatan_keputusan' => 'Diterima.', 'keputusan_at' => '2026-09-20 09:00:00',
                    'registration_number' => 'REG-2026-00001', 'registration_by' => 9, 'registration_at' => '2026-09-25 10:00:00',
                ],
            ];
            foreach ($pendaftaran as $row) {
                $db->createCommand()->insert('pendaftaran', $row)->execute();
            }

            $dummyVisaDetails = [
                1 => [
                    'tujuan' => 'Mengikuti program Magister di Universitas Andalas',
                    'tanggal_kedatangan' => '2027-02-15',
                    'kota_kedatangan' => 'Bandara Internasional Minangkabau (BIM)',
                    'alamat_selama_di_padang' => 'Asrama mahasiswa UNAND, Limau Manis, Padang',
                ],
                5 => [
                    'tujuan' => 'Mengikuti program Doktor di Universitas Andalas',
                    'tanggal_kedatangan' => '2027-01-10',
                    'kota_kedatangan' => 'Bandara Internasional Minangkabau (BIM)',
                    'alamat_selama_di_padang' => 'Asrama mahasiswa UNAND, Limau Manis, Padang',
                ],
            ];
            $db->createCommand()->batchInsert(
                'pengajuan_visa',
                ['pendaftaran_id', 'masa_berlaku_paspor', 'data_pengajuan', 'status', 'catatan', 'diproses_by', 'diproses_at'],
                [
                    [
                        1,
                        '2029-04-11',
                        json_encode($dummyVisaDetails[1], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                        'Menunggu diproses',
                        null,
                        null,
                        null,
                    ],
                    [
                        5,
                        '2030-11-01',
                        json_encode($dummyVisaDetails[5], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                        'Selesai',
                        'Visa sudah diproses.',
                        6,
                        '2026-09-30 10:00:00',
                    ],
                ],
            )->execute();

            // Dokumen yang sudah diunggah: pendaftaran_id => [dokumen_id, ...]
            $uploaded = [1 => [1, 2, 3, 4, 7], 2 => [1, 3, 5], 3 => [3, 5], 4 => [1], 5 => [1, 2, 3, 4, 7]];
            $applicantNames = [
                1 => 'Ahmad Fauzi',
                2 => 'Maria Santos',
                3 => 'Budi Hartono',
                4 => 'Nguyen Van An',
                5 => 'Li Wei',
            ];
            $documentNames = [
                1 => 'Paspor',
                2 => 'Ijazah',
                3 => 'Transkrip Nilai',
                4 => 'Sertifikat Kemahiran Bahasa',
                5 => 'Application Letter',
                6 => 'Recommendation Letter',
                7 => 'Surat Pernyataan Visa',
            ];
            $dummyDirectory = Yii::getAlias('@runtime/uploads/dummy');
            FileHelper::createDirectory($dummyDirectory, 0750, true);
            $rows = [];
            foreach ($uploaded as $pid => $docs) {
                foreach ($docs as $did) {
                    $fileName = preg_replace(
                        '/[^A-Za-z0-9]+/',
                        '_',
                        $documentNames[$did] . '_' . $applicantNames[$pid],
                    ) . '.pdf';
                    $filePath = $dummyDirectory . DIRECTORY_SEPARATOR . $pid . '-' . $fileName;
                    $this->writeDummyPdf($filePath, $documentNames[$did], $applicantNames[$pid]);
                    $rows[] = [$pid, $did, $fileName, $filePath, date('Y-m-d H:i:s')];
                }
            }
            $db->createCommand()->batchInsert(
                'dokumen_pendaftar',
                ['pendaftaran_id', 'dokumen_id', 'nama_file', 'file_path', 'uploaded_at'],
                $rows,
            )->execute();

            $tx->commit();
            $this->stdout("Data dummy berhasil dimasukkan.\nPassword semua akun: password123\n");
            return ExitCode::OK;
        } catch (\Throwable $e) {
            $tx->rollBack();
            $this->stderr('Gagal: ' . $e->getMessage() . "\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    private function writeDummyPdf(string $filePath, string $documentName, string $applicantName): void
    {
        $documentExamples = [
            'Paspor' => [
                'Nama: ' . $applicantName . ' (DATA DEMO)',
                'Kewarganegaraan: CONTOH SAJA',
                'Nomor dokumen: SAMPLE-ONLY',
                'Tanggal lahir: CONTOH - BUKAN DATA ASLI',
                'Masa berlaku: CONTOH - BUKAN DATA ASLI',
                'Tidak memuat zona MRZ, lambang negara, atau fitur pengamanan.',
            ],
            'Ijazah' => [
                'Nama: ' . $applicantName . ' (DATA DEMO)',
                'Institusi: Universitas Contoh (FIKTIF)',
                'Kualifikasi: Gelar Contoh (ILUSTRASI)',
                'Tanggal penerbitan: CONTOH SAJA',
                'Tidak membuktikan kelulusan atau kualifikasi pendidikan.',
            ],
            'Transkrip Nilai' => [
                'Nama: ' . $applicantName . ' (DATA DEMO)',
                'Institusi: Universitas Contoh (FIKTIF)',
                'Mata kuliah: Mata Kuliah Contoh',
                'Nilai: CONTOH SAJA',
                'Tidak berisi nilai akademik yang sebenarnya.',
            ],
            'Sertifikat Kemahiran Bahasa' => [
                'Nama: ' . $applicantName . ' (DATA DEMO)',
                'Program: Tes Bahasa Contoh (ILUSTRASI)',
                'Tingkat / skor: CONTOH SAJA',
                'Tanggal tes: CONTOH - BUKAN DATA ASLI',
                'Bukan sertifikat tes bahasa resmi.',
            ],
            'Surat Pernyataan Visa' => [
                'Nama: ' . $applicantName . ' (DATA DEMO)',
                'Keperluan: Contoh pernyataan untuk keperluan visa',
                'Program: Program Contoh (ILUSTRASI)',
                'Tanggal dan tanda tangan: TIDAK DIISI',
                'Bukan surat pernyataan resmi atau dokumen imigrasi.',
            ],
            'Application Letter' => [
                'Nama: ' . $applicantName . ' (DATA DEMO)',
                'Tujuan: Contoh surat permohonan program',
                'Program: Program Contoh (ILUSTRASI)',
                'Tanggal dan tanda tangan: TIDAK DIISI',
                'Bukan surat permohonan resmi.',
            ],
            'Recommendation Letter' => [
                'Nama: ' . $applicantName . ' (DATA DEMO)',
                'Pemberi rekomendasi: Nama Contoh (FIKTIF)',
                'Program: Program Contoh (ILUSTRASI)',
                'Tanggal dan tanda tangan: TIDAK DIISI',
                'Bukan surat rekomendasi resmi.',
            ],
        ];
        $lines = $documentExamples[$documentName] ?? [
            'Nama: ' . $applicantName . ' (DATA DEMO)',
            'Isi dokumen: CONTOH SAJA',
            'Bukan dokumen resmi.',
        ];
        $commands = [
            '0.1 0.35 0.22 rg',
            'BT /F1 20 Tf 50 750 Td (' . $this->escapePdfText('CONTOH ' . strtoupper($documentName)) . ') Tj ET',
            '0.8 0.1 0.1 rg',
            'BT /F1 15 Tf 50 710 Td (DEMO ONLY - TIDAK SAH UNTUK PENDAFTARAN) Tj ET',
            '0 0 0 rg',
            'BT /F1 12 Tf 50 655 Td (Ilustrasi untuk melihat jenis dan format informasi dokumen.) Tj ET',
        ];
        $verticalPosition = 610;
        foreach ($lines as $line) {
            $commands[] = 'BT /F1 11 Tf 60 ' . $verticalPosition . ' Td (' . $this->escapePdfText($line) . ') Tj ET';
            $verticalPosition -= 38;
        }
        $commands[] = '0.75 0.1 0.1 rg';
        $commands[] = 'BT /F1 26 Tf 50 100 Td (SAMPLE - NOT VALID) Tj ET';
        $stream = implode("\n", $commands);
        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 5 0 R >> >> /Contents 4 0 R >>',
            '<< /Length ' . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream",
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
        ];
        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1) . " 0 obj\n" . $object . "\nendobj\n";
        }
        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }
        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n";
        $pdf .= $xrefOffset . "\n%%EOF";
        if (file_put_contents($filePath, $pdf) === false) {
            throw new \RuntimeException('Gagal membuat berkas dokumen demo: ' . $filePath);
        }
    }

    private function escapePdfText(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }
}
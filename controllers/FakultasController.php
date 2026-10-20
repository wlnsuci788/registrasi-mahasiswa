<?php

namespace app\controllers;

use app\components\RoleController;
use app\models\User;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class FakultasController extends RoleController
{
    private const DEMO_SESSION_KEY = 'fakultasDemoData';

    public $layout = 'staff';

    protected function allowedRole(): string
    {
        return User::ROLE_FAKULTAS;
    }

    public function actionIndex(): string
    {
        $data = $this->demoData();
        $search = Yii::$app->request->get('q', '');
        if (!is_string($search)) {
            throw new BadRequestHttpException('Kata pencarian tidak valid.');
        }
        $search = trim($search);

        $applicants = array_values(array_filter(
            $data['applicants'],
            static fn (array $applicant): bool => $search === ''
                || stripos($applicant['nama_lengkap'], $search) !== false
                || stripos($applicant['asal_negara'], $search) !== false
                || stripos($applicant['no_paspor'], $search) !== false
                || stripos((string) $applicant['pendaftaran_id'], $search) !== false
                || stripos(str_pad((string) $applicant['pendaftaran_id'], 4, '0', STR_PAD_LEFT), $search) !== false
                || stripos($applicant['program_studi'], $search) !== false,
        ));
        $pending = count(array_filter(
            $data['applicants'],
            static fn (array $applicant): bool => $applicant['saran_fakultas'] === '',
        ));
        $evaluated = count($data['applicants']) - $pending;

        return $this->render('index', [
            'applicants' => $applicants,
            'search' => $search,
            'totalApplicants' => count($data['applicants']),
            'pendingApplicants' => $pending,
            'evaluatedApplicants' => $evaluated,
            'evaluatorName' => $this->evaluatorName(),
        ]);
    }

    public function actionEvaluasi(int $id): string|Response
    {
        $data = $this->demoData();
        $index = $this->findApplicantIndex($data, $id);
        $applicant = $data['applicants'][$index];
        $error = null;

        if (Yii::$app->request->isPost) {
            $recommendation = Yii::$app->request->post('saran_fakultas');
            $note = Yii::$app->request->post('catatan_fakultas', '');
            if (
                !is_string($recommendation)
                || !in_array($recommendation, ['Disarankan lolos', 'Disarankan tidak lolos'], true)
                || !is_string($note)
            ) {
                throw new BadRequestHttpException('Rekomendasi evaluasi tidak valid.');
            }

            $note = trim($note);
            if (mb_strlen($note) > 500) {
                $error = 'Catatan maksimal 500 karakter.';
                $applicant['catatan_fakultas'] = $note;
            } else {
                $data['applicants'][$index]['saran_fakultas'] = $recommendation;
                $data['applicants'][$index]['catatan_fakultas'] = $note;
                $data['applicants'][$index]['evaluator_name'] = $this->evaluatorName();
                $this->saveDemoData($data);
                Yii::$app->session->setFlash('success', 'Rekomendasi demo tersimpan sementara.');

                return $this->redirect(['evaluasi', 'id' => $id]);
            }
        }

        return $this->render('evaluasi', [
            'applicant' => $applicant,
            'error' => $error,
            'evaluatorName' => $this->evaluatorName(),
        ]);
    }

    public function actionExportCsv(): Response
    {
        $data = $this->demoData();
        $rows = [
            ['ID Pendaftaran', 'Nama', 'Kewarganegaraan', 'Program Studi', 'Kualifikasi Sains', 'Kemahiran Bahasa', 'Status Evaluasi'],
        ];

        foreach ($data['applicants'] as $applicant) {
            $rows[] = [
                $applicant['pendaftaran_id'],
                $applicant['nama_lengkap'],
                $applicant['asal_negara'],
                $applicant['program_studi'],
                'Biologi ' . $applicant['biologi'] . '/10; Kimia ' . $applicant['kimia'] . '/10',
                $applicant['kemahiran_bahasa'],
                $applicant['saran_fakultas'] === '' ? 'Menunggu Evaluasi' : 'Selesai Dievaluasi',
            ];
        }

        $stream = fopen('php://temp', 'r+');
        if ($stream === false) {
            throw new \RuntimeException('Gagal membuat file ekspor.');
        }
        foreach ($rows as $row) {
            fputcsv($stream, $row);
        }
        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);
        if ($csv === false) {
            throw new \RuntimeException('Gagal membaca file ekspor.');
        }

        return Yii::$app->response->sendContentAsFile(
            "\xEF\xBB\xBF" . $csv,
            'antrean-fakultas-kedokteran.csv',
            ['mimeType' => 'text/csv; charset=UTF-8'],
        );
    }

    public function actionDokumenPdf(int $id): Response
    {
        $download = Yii::$app->request->get('download', '0');
        if (!in_array($download, ['0', '1', 0, 1], true)) {
            throw new BadRequestHttpException('Mode dokumen PDF tidak valid.');
        }

        $data = $this->demoData();
        $index = $this->findApplicantIndex($data, $id);
        $applicant = $data['applicants'][$index];
        $fileName = 'transkrip-akademik-'
            . str_pad((string) $applicant['pendaftaran_id'], 4, '0', STR_PAD_LEFT)
            . '.pdf';

        return Yii::$app->response->sendContentAsFile(
            $this->buildTranscriptPdf($applicant),
            $fileName,
            [
                'mimeType' => 'application/pdf',
                'inline' => $download === '0' || $download === 0,
            ],
        );
    }

    private function evaluatorName(): string
    {
        $identity = Yii::$app->user->identity;

        return $identity instanceof User ? $identity->nama : 'Dr. Ir. Hendra Suardi';
    }

    private function buildTranscriptPdf(array $applicant): string
    {
        $lines = [
            'SAMPLE - DOKUMEN AKADEMIK DUMMY',
            'HIGH SCHOOL ACADEMIC RECORD & GRADUATION TRANSCRIPT',
            'Hanoi - Amsterdam High School for the Gifted',
            '',
            'Student Name: ' . $applicant['nama_lengkap'],
            'Passport No: ' . $applicant['no_paspor'],
            'Country of Origin: ' . $applicant['asal_negara'],
            'Intended Major: ' . $applicant['program_studi'],
            'Academic Year: 2026/2027',
            '',
            'SCIENCE QUALIFICATIONS',
            'Biology: ' . $applicant['biologi'] . '/10',
            'Chemistry: ' . $applicant['kimia'] . '/10',
            'Curriculum Equivalency: ' . $applicant['kesetaraan'],
            'Language Qualification: ' . $applicant['kemahiran_bahasa'],
            '',
            'CONSULAR LEGALIZATION: VERIFIED (DEMO)',
            'Document review note:',
        ];

        foreach (explode("\n", wordwrap($applicant['catatan_berkas'], 82, "\n", true)) as $noteLine) {
            $lines[] = $noteLine;
        }
        $lines[] = '';
        $lines[] = 'This is a demonstration document generated from sample data.';
        $lines[] = 'It is not an official academic transcript.';

        $commands = [
            '0.04 0.34 0.19 rg',
            '50 792 495 3 re f',
            '0 0 0 rg',
        ];
        $y = 765;
        foreach ($lines as $line) {
            $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $line);
            if ($transliterated === false) {
                throw new \RuntimeException('Gagal mengubah teks dokumen dummy ke format PDF.');
            }
            $safeLine = preg_replace('/[\x00-\x1F\x7F]/', '', $transliterated);
            if ($safeLine === null) {
                throw new \RuntimeException('Gagal memformat teks dokumen dummy.');
            }
            $safeLine = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $safeLine);
            $fontSize = str_starts_with($line, 'HIGH SCHOOL') ? 15 : 10;
            $commands[] = 'BT /F1 ' . $fontSize . ' Tf 50 ' . $y . ' Td (' . $safeLine . ') Tj ET';
            $y -= 24;
        }
        $stream = implode("\n", $commands) . "\n";

        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
            '<< /Length ' . strlen($stream) . " >>\nstream\n" . $stream . 'endstream',
        ];
        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1) . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }
        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefOffset . "\n%%EOF";

        return $pdf;
    }

    private function demoData(): array
    {
        $data = Yii::$app->session->get(self::DEMO_SESSION_KEY);
        if (is_array($data)) {
            return $data;
        }

        $data = [
            'applicants' => [
                [
                    'pendaftaran_id' => 891,
                    'nama_lengkap' => 'Nguyen Van An',
                    'asal_negara' => 'Vietnam',
                    'kode_negara' => 'VN',
                    'tempat_lahir' => 'Hanoi',
                    'tanggal_lahir' => '2004-08-18',
                    'no_paspor' => 'B9823411',
                    'program_studi' => 'S1 Pendidikan Dokter',
                    'fakultas' => 'Kedokteran',
                    'biologi' => '9.4',
                    'kimia' => '9.1',
                    'kesetaraan' => '96%',
                    'kemahiran_bahasa' => 'IELTS 7.5',
                    'status_bahasa' => 'C1 Proficient',
                    'nama_berkas' => 'TRANSCRIPT_LEGALIZED_NGUYEN_VAN_AN.PDF',
                    'catatan_berkas' => 'Transkrip akademik resmi telah dilegalisasi kedutaan dan kesetaraan silabus diverifikasi fakultas.',
                    'saran_fakultas' => '',
                    'catatan_fakultas' => '',
                    'evaluator_name' => '',
                ],
                [
                    'pendaftaran_id' => 894,
                    'nama_lengkap' => 'Tariq Al-Mansoor',
                    'asal_negara' => 'Yaman',
                    'kode_negara' => 'YE',
                    'tempat_lahir' => 'Sanaa',
                    'tanggal_lahir' => '2005-01-04',
                    'no_paspor' => 'N4109281',
                    'program_studi' => 'S1 Pendidikan Dokter',
                    'fakultas' => 'Kedokteran',
                    'biologi' => '9.0',
                    'kimia' => '8.9',
                    'kesetaraan' => '91%',
                    'kemahiran_bahasa' => 'TOEFL iBT 94',
                    'status_bahasa' => 'B2 High Proficient',
                    'nama_berkas' => 'TRANSCRIPT_TARIQ_AL_MANSOOR.PDF',
                    'catatan_berkas' => 'Transkrip sekolah menengah telah diterjemahkan dan menunggu penelaahan silabus.',
                    'saran_fakultas' => '',
                    'catatan_fakultas' => '',
                    'evaluator_name' => '',
                ],
                [
                    'pendaftaran_id' => 882,
                    'nama_lengkap' => 'Mei Ling Chen',
                    'asal_negara' => 'Taiwan',
                    'kode_negara' => 'TW',
                    'tempat_lahir' => 'Taipei',
                    'tanggal_lahir' => '2004-12-12',
                    'no_paspor' => 'E7720914',
                    'program_studi' => 'S1 Pendidikan Dokter',
                    'fakultas' => 'Kedokteran',
                    'biologi' => '9.6',
                    'kimia' => '9.5',
                    'kesetaraan' => '98%',
                    'kemahiran_bahasa' => 'IELTS 8.0',
                    'status_bahasa' => 'C1 Advanced',
                    'nama_berkas' => 'TRANSCRIPT_LEGALIZED_MEI_LING_CHEN.PDF',
                    'catatan_berkas' => 'Transkrip dan silabus telah diverifikasi. Kualifikasi akademik memenuhi standar program.',
                    'saran_fakultas' => 'Disarankan lolos',
                    'catatan_fakultas' => 'Kualifikasi akademik dan berkas pendaftaran telah memenuhi persyaratan.',
                    'evaluator_name' => 'Dr. Ir. Hendra Suardi',
                ],
            ],
        ];
        $this->saveDemoData($data);

        return $data;
    }

    private function saveDemoData(array $data): void
    {
        Yii::$app->session->set(self::DEMO_SESSION_KEY, $data);
    }

    private function findApplicantIndex(array $data, int $id): int
    {
        $index = array_search($id, array_column($data['applicants'], 'pendaftaran_id'), true);
        if ($index === false) {
            throw new NotFoundHttpException('Contoh pendaftar fakultas tidak ditemukan.');
        }

        return $index;
    }
}

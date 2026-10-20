<?php

namespace app\controllers;

use app\components\RoleController;
use app\models\DokumenPersyaratan;
use app\models\PengajuanVisa;
use app\models\Pendaftaran;
use app\models\User;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class IoController extends RoleController
{
    private const DEMO_SESSION_KEY = 'ioDemoData';

    public $layout = 'staff';

    protected function allowedRole(): string
    {
        return User::ROLE_IO;
    }

    public function actionIndex(): string
    {
        $data = $this->demoData();
        $pending = count(array_filter(
            $data['applicants'],
            static fn (array $applicant): bool => $applicant['status'] === Pendaftaran::STATUS_TERKIRIM,
        ));
        $activeVisas = count(array_filter(
            $data['visas'],
            static fn (array $visa): bool => $visa['status'] !== PengajuanVisa::STATUS_SELESAI,
        ));
        $activeDocuments = count(array_filter(
            $data['documents'],
            static fn (array $document): bool => $document['active'],
        ));

        return $this->render('index', [
            'pendaftar' => array_slice($data['applicants'], 0, 8),
            'totalPendaftar' => count($data['applicants']),
            'menungguVerifikasi' => $pending,
            'permohonanVisa' => $activeVisas,
            'dokumenAktif' => $activeDocuments,
        ]);
    }

    public function actionVerifikasi(): string
    {
        $data = $this->demoData();
        $search = Yii::$app->request->get('q', '');
        $status = Yii::$app->request->get('status', '');
        if (!is_string($search) || !is_string($status)) {
            throw new BadRequestHttpException('Filter pendaftaran tidak valid.');
        }

        if (
            $status !== '' && !in_array($status, [
            Pendaftaran::STATUS_TERKIRIM,
            Pendaftaran::STATUS_LOLOS,
            Pendaftaran::STATUS_TIDAK_LOLOS,
            Pendaftaran::STATUS_REGISTRATION_NUMBER_TERBIT,
            ], true)
        ) {
            throw new BadRequestHttpException('Filter status tidak valid.');
        }

        $pendaftar = array_values(array_filter(
            $data['applicants'],
            static function (array $applicant) use ($search, $status): bool {
                $matchesSearch = $search === ''
                    || stripos($applicant['nama_lengkap'], trim($search)) !== false
                    || stripos($applicant['no_paspor'], trim($search)) !== false;
                $matchesStatus = $status === '' || $applicant['status'] === $status;

                return $matchesSearch && $matchesStatus;
            },
        ));

        return $this->render('verifikasi', [
            'pendaftar' => $pendaftar,
            'search' => $search,
            'statusFilter' => $status,
        ]);
    }

    public function actionDetailVerifikasi(int $id): string|Response
    {
        $data = $this->demoData();
        $index = $this->findApplicantIndex($data, $id);
        $applicant = $data['applicants'][$index];
        $error = null;

        if (Yii::$app->request->isPost) {
            $decision = Yii::$app->request->post('keputusan');
            $note = Yii::$app->request->post('catatan_keputusan');
            if (
                !is_string($decision)
                || !in_array($decision, [Pendaftaran::STATUS_LOLOS, Pendaftaran::STATUS_TIDAK_LOLOS], true)
                || !is_string($note)
            ) {
                throw new BadRequestHttpException('Keputusan verifikasi tidak valid.');
            }

            $note = trim($note);
            if ($note === '' || mb_strlen($note) > 2000) {
                $error = 'Catatan wajib diisi dan maksimal 2.000 karakter.';
                $applicant['catatan_keputusan'] = $note;
            } else {
                $data['applicants'][$index]['status'] = $decision;
                $data['applicants'][$index]['catatan_keputusan'] = $note;
                $this->saveDemoData($data);
                Yii::$app->session->setFlash('success', 'Keputusan demo berhasil diperbarui sementara.');

                return $this->redirect(['detail-verifikasi', 'id' => $id]);
            }
        }

        return $this->render('detail-verifikasi', [
            'pendaftaran' => $applicant,
            'error' => $error,
        ]);
    }

    public function actionBantuanVisa(): string|Response
    {
        $data = $this->demoData();
        if (Yii::$app->request->isPost) {
            $visaId = Yii::$app->request->post('visa_id');
            $status = Yii::$app->request->post('status');
            $note = Yii::$app->request->post('catatan');
            if (
                !is_scalar($visaId)
                || !ctype_digit((string) $visaId)
                || !is_string($status)
                || !array_key_exists($status, PengajuanVisa::optsStatus())
                || !is_string($note)
            ) {
                throw new BadRequestHttpException('Pembaruan bantuan visa tidak valid.');
            }
            if (mb_strlen(trim($note)) > 2000) {
                throw new BadRequestHttpException('Catatan maksimal 2.000 karakter.');
            }

            $visaIndex = array_search((int) $visaId, array_column($data['visas'], 'visa_id'), true);
            if ($visaIndex === false) {
                throw new NotFoundHttpException('Contoh pengajuan visa tidak ditemukan.');
            }

            $data['visas'][$visaIndex]['status'] = $status;
            $data['visas'][$visaIndex]['catatan'] = trim($note);
            $this->saveDemoData($data);
            Yii::$app->session->setFlash('success', 'Status visa demo berhasil diperbarui sementara.');

            return $this->redirect(['bantuan-visa', '#' => 'visa-' . $visaId]);
        }

        return $this->render('bantuan-visa', ['visaList' => $data['visas']]);
    }

    public function actionDokumenPersyaratan(): string|Response
    {
        $data = $this->demoData();
        $formModel = new DokumenPersyaratan();
        $formModel->berlaku_untuk = DokumenPersyaratan::BERLAKU_UNTUK_SEMUA;
        $editId = Yii::$app->request->get('edit');
        if ($editId !== null) {
            if (!is_string($editId) || !ctype_digit($editId)) {
                throw new BadRequestHttpException('Contoh dokumen tidak valid.');
            }
            $documentIndex = $this->findDocumentIndex($data, (int) $editId);
            $formModel->nama_dokumen = $data['documents'][$documentIndex]['nama_dokumen'];
            $formModel->berlaku_untuk = $data['documents'][$documentIndex]['berlaku_untuk'];
            $formModel->wajib = $data['documents'][$documentIndex]['wajib'] ? 1 : 0;
        }

        if (Yii::$app->request->isPost) {
            $operation = Yii::$app->request->post('operation');
            $id = Yii::$app->request->post('dokumen_id');
            if (!is_string($operation) || !is_scalar($id)) {
                throw new BadRequestHttpException('Operasi dokumen tidak valid.');
            }

            if ($operation === 'save') {
                if ($id !== '' && !ctype_digit((string) $id)) {
                    throw new BadRequestHttpException('Contoh dokumen tidak valid.');
                }
                $editId = $id === '' ? null : (int) $id;
                $name = Yii::$app->request->post('nama_dokumen');
                $programType = Yii::$app->request->post('berlaku_untuk');
                $required = Yii::$app->request->post('wajib');
                if (
                    !is_string($name)
                    || !is_string($programType)
                    || ($required !== null && (!is_string($required) || $required !== '1'))
                ) {
                    throw new BadRequestHttpException('Isian dokumen persyaratan tidak valid.');
                }

                $formModel->nama_dokumen = $name;
                $formModel->berlaku_untuk = $programType;
                $formModel->wajib = $required === '1' ? 1 : 0;
                if ($formModel->validate()) {
                    $document = [
                        'dokumen_id' => $id === '' ? $this->nextDocumentId($data) : (int) $id,
                        'nama_dokumen' => trim($name),
                        'berlaku_untuk' => $programType,
                        'wajib' => $required === '1',
                        'active' => true,
                    ];
                    if ($id === '') {
                        $data['documents'][] = $document;
                    } else {
                        $documentIndex = $this->findDocumentIndex($data, (int) $id);
                        $document['active'] = $data['documents'][$documentIndex]['active'];
                        $data['documents'][$documentIndex] = $document;
                    }

                    $this->saveDemoData($data);
                    Yii::$app->session->setFlash('success', 'Contoh dokumen demo berhasil disimpan sementara.');

                    return $this->redirect(['dokumen-persyaratan']);
                }
            } elseif (in_array($operation, ['toggle', 'archive'], true)) {
                if (!ctype_digit((string) $id)) {
                    throw new BadRequestHttpException('Contoh dokumen tidak valid.');
                }
                $documentIndex = $this->findDocumentIndex($data, (int) $id);
                if ($operation === 'toggle') {
                    $data['documents'][$documentIndex]['active'] = !$data['documents'][$documentIndex]['active'];
                } else {
                    array_splice($data['documents'], $documentIndex, 1);
                }
                $this->saveDemoData($data);
                Yii::$app->session->setFlash('success', 'Contoh dokumen demo berhasil diperbarui sementara.');

                return $this->redirect(['dokumen-persyaratan']);
            } else {
                throw new BadRequestHttpException('Operasi dokumen tidak dikenal.');
            }
        }

        return $this->render('dokumen-persyaratan', [
            'dokumen' => $data['documents'],
            'formModel' => $formModel,
            'editId' => $editId,
        ]);
    }

    private function demoData(): array
    {
        $data = Yii::$app->session->get(self::DEMO_SESSION_KEY);
        if (is_array($data)) {
            return $data;
        }

        $data = [
            'applicants' => [[
                'pendaftaran_id' => 891,
                'nama_lengkap' => 'Nguyen Van An',
                'asal_negara' => 'Vietnam',
                'tempat_lahir' => 'Hanoi',
                'tanggal_lahir' => '2004-08-18',
                'no_paspor' => 'B9823411',
                'jalur' => 'asing',
                'kategori_program' => 'degree',
                'jenjang' => 'S1 Pendidikan Dokter',
                'jenis_program' => '',
                'fakultas' => 'Fakultas Kedokteran',
                'asal_institusi' => 'Hanoi Medical University',
                'jenis_visa' => 'Visa Pelajar (C316)',
                'submitted_at' => '2026-02-14',
                'status' => Pendaftaran::STATUS_TERKIRIM,
                'catatan_keputusan' => '',
                'dokumen' => [
                    ['nama_dokumen' => 'Paspor Internasional', 'nama_file' => 'paspor_nguyen_van_an.pdf'],
                    ['nama_dokumen' => 'Ijazah & Transkrip Terjemahan Tersumpah', 'nama_file' => 'ijazah_transkrip_nguyen.pdf'],
                    ['nama_dokumen' => 'Surat Jaminan Finansial & Rekening Koran', 'nama_file' => 'jaminan_finansial_nguyen.pdf'],
                    ['nama_dokumen' => 'Asuransi Kesehatan Internasional', 'nama_file' => 'asuransi_nguyen.pdf'],
                    ['nama_dokumen' => 'Pas Foto Resmi Latar Merah', 'nama_file' => 'pas_foto_nguyen.jpg'],
                ],
            ]],
            'visas' => [[
                'visa_id' => 41,
                'pendaftaran_id' => 891,
                'masa_berlaku_paspor' => '2031-11-22',
                'tanggal_kedatangan' => '2026-08-10',
                'tujuan' => 'Studi S1 Pendidikan Dokter',
                'kota_kedatangan' => 'Padang',
                'status' => PengajuanVisa::STATUS_DIPROSES,
                'catatan' => 'Dokumen permohonan visa sedang diproses ke Ditjen Imigrasi.',
            ]],
            'documents' => [
                ['dokumen_id' => 1, 'nama_dokumen' => 'Paspor Internasional', 'berlaku_untuk' => 'Semua', 'wajib' => true, 'active' => true],
                ['dokumen_id' => 2, 'nama_dokumen' => 'Ijazah & Transkrip Terjemahan Tersumpah', 'berlaku_untuk' => 'Degree', 'wajib' => true, 'active' => true],
                ['dokumen_id' => 3, 'nama_dokumen' => 'Surat Jaminan Finansial & Rekening Koran', 'berlaku_untuk' => 'Semua', 'wajib' => true, 'active' => true],
                ['dokumen_id' => 4, 'nama_dokumen' => 'Asuransi Kesehatan Internasional', 'berlaku_untuk' => 'Semua', 'wajib' => true, 'active' => true],
                ['dokumen_id' => 5, 'nama_dokumen' => 'Pas Foto Resmi Latar Merah', 'berlaku_untuk' => 'Semua', 'wajib' => true, 'active' => true],
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
            throw new NotFoundHttpException('Contoh pendaftar tidak ditemukan.');
        }

        return $index;
    }

    private function findDocumentIndex(array $data, int $id): int
    {
        $index = array_search($id, array_column($data['documents'], 'dokumen_id'), true);
        if ($index === false) {
            throw new NotFoundHttpException('Contoh dokumen persyaratan tidak ditemukan.');
        }

        return $index;
    }

    private function nextDocumentId(array $data): int
    {
        return $data['documents'] ? max(array_column($data['documents'], 'dokumen_id')) + 1 : 1;
    }
}

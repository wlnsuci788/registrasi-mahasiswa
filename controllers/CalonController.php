<?php

namespace app\controllers;

use app\components\RoleController;
use app\models\DokumenPendaftar;
use app\models\DokumenPersyaratan;
use app\models\Fakultas;
use app\models\Pendaftaran;
use app\models\PengajuanVisa;
use app\models\User;
use Yii;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class CalonController extends RoleController
{
    public $layout = 'calon';

    protected function allowedRole(): string
    {
        return User::ROLE_CALON;
    }

    public function actionIndex(): string
    {
        $pendaftaran = $this->findLatestPendaftaran();

        return $this->render('index', [
            'pendaftaran' => $pendaftaran,
            'completedSteps' => $this->getCompletedSteps($pendaftaran),
            'requirements' => $pendaftaran ? $this->getApplicableDocuments($pendaftaran) : [],
            'documents' => $pendaftaran ? $this->getUploadedDocuments($pendaftaran) : [],
            'visa' => $pendaftaran ? $this->findVisaRequest($pendaftaran) : null,
        ]);
    }

    public function actionForm(): Response|string
    {
        $pendaftaran = $this->getPendaftaranForStep();
        $readOnly = $this->isReadOnly($pendaftaran);

        if (
            $this->request->isPost && $this->loadPendaftaranAttributes($pendaftaran, [
            'jalur',
            'nama_lengkap',
            'tempat_lahir',
            'tanggal_lahir',
            'jenis_kelamin',
            'asal_negara',
            'no_paspor',
            ])
        ) {
            $pendaftaran->user_id = (int) Yii::$app->user->id;
            $pendaftaran->status = Pendaftaran::STATUS_DRAFT;
            $pendaftaran->updated_by = (int) Yii::$app->user->id;

            if ($this->validateIdentity($pendaftaran) && $pendaftaran->save()) {
                Yii::$app->session->setFlash('success', 'Data diri berhasil disimpan.');

                return $this->redirect($this->request->post('intent') === 'draft' ? ['form'] : ['program']);
            }
        }

        return $this->render('form', [
            'pendaftaran' => $pendaftaran,
            'activeStep' => 1,
            'readOnly' => $readOnly,
        ]);
    }

    public function actionProgram(): Response|string
    {
        $pendaftaran = $this->getPendaftaranForStep();
        $readOnly = $this->isReadOnly($pendaftaran);
        if ($pendaftaran->getIsNewRecord() || (!$readOnly && !$this->validateIdentity($pendaftaran))) {
            Yii::$app->session->setFlash('error', 'Lengkapi dan simpan data diri sebelum memilih program.');

            return $this->redirect(['form']);
        }

        if (
            $this->request->isPost && $this->loadPendaftaranAttributes($pendaftaran, [
            'kategori_program',
            'jenjang',
            'jenis_program',
            'fakultas_id',
            ])
        ) {
            if ($pendaftaran->kategori_program === Pendaftaran::KATEGORI_PROGRAM_NON_DEGREE) {
                $pendaftaran->jenjang = null;
                $pendaftaran->fakultas_id = null;
            } elseif ($pendaftaran->kategori_program === Pendaftaran::KATEGORI_PROGRAM_DEGREE) {
                $pendaftaran->jenis_program = null;
            }
            $pendaftaran->user_id = (int) Yii::$app->user->id;
            $pendaftaran->status = Pendaftaran::STATUS_DRAFT;
            $pendaftaran->updated_by = (int) Yii::$app->user->id;
            $this->setProcessingUnit($pendaftaran);

            if ($this->validateProgram($pendaftaran) && $pendaftaran->save()) {
                Yii::$app->session->setFlash('success', 'Pilihan program berhasil disimpan.');

                return $this->redirect($this->request->post('intent') === 'draft' ? ['program'] : ['akademik']);
            }
        }

        return $this->render('program', [
            'pendaftaran' => $pendaftaran,
            'fakultasOptions' => Fakultas::find()
                ->select(['nama_fakultas'])
                ->where(['deleted_at' => null])
                ->indexBy('fakultas_id')
                ->column(),
            'activeStep' => 2,
            'readOnly' => $readOnly,
        ]);
    }

    public function actionAkademik(): Response|string
    {
        $pendaftaran = $this->getPendaftaranForStep();
        $readOnly = $this->isReadOnly($pendaftaran);
        if ($pendaftaran->getIsNewRecord() || (!$readOnly && !$this->validateIdentityAndProgram($pendaftaran))) {
            Yii::$app->session->setFlash('error', 'Lengkapi data diri dan pilihan program sebelum melanjutkan.');

            return $this->redirect(['form']);
        }

        if (
            $this->request->isPost && $this->loadPendaftaranAttributes($pendaftaran, [
            'asal_institusi',
            'nilai_akhir',
            'negara_institusi',
            'kota_institusi',
            'jenjang_pendidikan_terakhir',
            'jurusan_asal',
            'skala_nilai',
            'tahun_kelulusan',
            'jenis_tes_bahasa_inggris',
            'skor_tes_bahasa_inggris',
            'tanggal_tes_bahasa_inggris',
            'nomor_sertifikat_bahasa_inggris',
            'tingkat_bahasa_indonesia',
            'rencana_kursus_bahasa_indonesia',
            'transfer_kredit',
            'status_mahasiswa_asal',
            'target_kredit',
            'pembiayaan',
            'lama_kunjungan',
            ])
        ) {
            $pendaftaran->user_id = (int) Yii::$app->user->id;
            $pendaftaran->status = Pendaftaran::STATUS_DRAFT;
            $pendaftaran->updated_by = (int) Yii::$app->user->id;
            $saveAsDraft = $this->request->post('intent') === 'draft';

            if ($this->validateAcademics($pendaftaran, $saveAsDraft) && $pendaftaran->save()) {
                Yii::$app->session->setFlash(
                    'success',
                    $saveAsDraft
                        ? 'Draft riwayat akademik berhasil disimpan.'
                        : 'Riwayat akademik berhasil disimpan.',
                );

                return $this->redirect($saveAsDraft ? ['akademik'] : ['dokumen']);
            }
        }

        return $this->render('akademik', [
            'pendaftaran' => $pendaftaran,
            'activeStep' => 3,
            'readOnly' => $readOnly,
        ]);
    }

    public function actionDokumen(): Response|string
    {
        $pendaftaran = $this->getPendaftaranForStep();
        $readOnly = $this->isReadOnly($pendaftaran);
        if (
            $pendaftaran->getIsNewRecord()
            || (!$readOnly && (
                !$this->validateIdentityAndProgram($pendaftaran)
                || !$this->validateAcademics($pendaftaran)
            ))
        ) {
            Yii::$app->session->setFlash('error', 'Lengkapi data diri, program, dan akademik sebelum mengunggah dokumen.');

            return $this->redirect(['akademik']);
        }
        $requirements = $this->getApplicableDocuments($pendaftaran);
        $uploaded = $this->getUploadedDocuments($pendaftaran);

        if ($this->request->isPost && !$readOnly) {
            $transaction = Yii::$app->db->beginTransaction();
            $newFiles = [];
            try {
                foreach ($requirements as $requirement) {
                    $file = UploadedFile::getInstanceByName('documents[' . $requirement->dokumen_id . ']');
                    if (!$file) {
                        continue;
                    }

                    $this->validateUploadedFile($file);
                    $directory = Yii::getAlias('@runtime/uploads/' . Yii::$app->user->id);
                    FileHelper::createDirectory($directory, 0750, true);
                    $extension = strtolower($file->extension);
                    $fileName = bin2hex(random_bytes(24)) . '.' . $extension;
                    $absolutePath = $directory . DIRECTORY_SEPARATOR . $fileName;
                    if (!$file->saveAs($absolutePath)) {
                        throw new \RuntimeException('File ' . $requirement->nama_dokumen . ' gagal disimpan.');
                    }
                    $newFiles[] = $absolutePath;

                    $existing = DokumenPendaftar::find()
                        ->where([
                            'pendaftaran_id' => $pendaftaran->pendaftaran_id,
                            'dokumen_id' => $requirement->dokumen_id,
                            'deleted_at' => null,
                        ])
                        ->one();
                    if ($existing) {
                        $existing->deleted_at = date('Y-m-d H:i:s');
                        $existing->updated_by = (int) Yii::$app->user->id;
                        if (!$existing->save(false)) {
                            throw new \RuntimeException('Dokumen lama gagal diperbarui.');
                        }
                    }

                    $record = new DokumenPendaftar();
                    $record->pendaftaran_id = $pendaftaran->pendaftaran_id;
                    $record->dokumen_id = $requirement->dokumen_id;
                    $record->nama_file = $file->name;
                    $record->file_path = $absolutePath;
                    $record->uploaded_at = date('Y-m-d H:i:s');
                    $record->created_by = (int) Yii::$app->user->id;
                    if (!$record->save()) {
                        throw new \RuntimeException('Data dokumen gagal disimpan: ' . implode(' ', $record->getErrorSummary(true)));
                    }
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Dokumen berhasil disimpan.');

                return $this->redirect(['dokumen']);
            } catch (\Throwable $exception) {
                if ($transaction->getIsActive()) {
                    $transaction->rollBack();
                }
                foreach ($newFiles as $newFile) {
                    if (is_file($newFile) && !unlink($newFile)) {
                        Yii::error('Unable to remove an uncommitted upload: ' . $newFile, __METHOD__);
                    }
                }
                throw $exception;
            }
        }

        return $this->render('dokumen', [
            'pendaftaran' => $pendaftaran,
            'requirements' => $requirements,
            'uploaded' => $uploaded,
            'activeStep' => 4,
            'readOnly' => $readOnly,
        ]);
    }

    public function actionUnduhDokumen(int $id): Response
    {
        $document = DokumenPendaftar::find()
            ->alias('dp')
            ->innerJoinWith('pendaftaran p')
            ->where([
                'dp.dokumen_pendaftar_id' => $id,
                'dp.deleted_at' => null,
                'p.user_id' => Yii::$app->user->id,
                'p.deleted_at' => null,
            ])
            ->one();
        if (!$document) {
            throw new NotFoundHttpException('Dokumen tidak ditemukan.');
        }

        $filePath = $this->getUploadedFilePath($document);

        return Yii::$app->response->sendFile($filePath, $document->nama_file ?: 'dokumen');
    }

    public function actionFotoDokumen(int $id): Response
    {
        $document = DokumenPendaftar::find()
            ->alias('dp')
            ->innerJoinWith('pendaftaran p')
            ->with('dokumen')
            ->where([
                'dp.dokumen_pendaftar_id' => $id,
                'dp.deleted_at' => null,
                'p.user_id' => Yii::$app->user->id,
                'p.deleted_at' => null,
            ])
            ->one();
        if (
            !$document
            || !$document->dokumen
            || stripos($document->dokumen->nama_dokumen, 'foto') === false
        ) {
            throw new NotFoundHttpException('Foto pendaftar tidak ditemukan.');
        }

        $filePath = $this->getUploadedFilePath($document);
        $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->file($filePath);
        if (!in_array($mimeType, ['image/jpeg', 'image/png'], true)) {
            throw new NotFoundHttpException('Berkas foto tidak tersedia.');
        }

        return Yii::$app->response->sendFile(
            $filePath,
            $document->nama_file ?: 'pas-foto',
            ['mimeType' => $mimeType, 'inline' => true],
        );
    }

    private function getUploadedFilePath(DokumenPendaftar $document): string
    {
        $uploadRoot = realpath(Yii::getAlias('@runtime/uploads'));
        $filePath = $document->file_path ? realpath($document->file_path) : false;
        if (
            !$uploadRoot
            || !$filePath
            || !str_starts_with(
                strtolower($filePath),
                strtolower(rtrim($uploadRoot, '\\/') . DIRECTORY_SEPARATOR),
            )
            || !is_file($filePath)
        ) {
            throw new NotFoundHttpException('Berkas tidak tersedia.');
        }

        return $filePath;
    }

    public function actionVisa(): Response|string
    {
        $pendaftaran = $this->getPendaftaranForStep();
        $readOnly = $this->isReadOnly($pendaftaran);
        if ($pendaftaran->getIsNewRecord()) {
            return $this->redirect(['form']);
        }
        $visa = $this->findVisaRequest($pendaftaran) ?? new PengajuanVisa();
        $visaData = $this->decodeVisaData($visa->data_pengajuan);

        if (
            $this->request->isPost && $this->loadPendaftaranAttributes($pendaftaran, [
            'pilihan_visa',
            'jenis_visa',
            ])
        ) {
            $rawVisaInput = $this->request->post('VisaForm', []);
            if (!is_array($rawVisaInput)) {
                throw new \yii\web\BadRequestHttpException('Format rincian visa tidak valid.');
            }
            $visaInput = [];
            foreach (['masa_berlaku_paspor', 'tujuan', 'tanggal_kedatangan', 'kota_kedatangan', 'alamat_selama_di_padang'] as $field) {
                if (isset($rawVisaInput[$field]) && !is_scalar($rawVisaInput[$field])) {
                    throw new \yii\web\BadRequestHttpException('Nilai rincian visa tidak valid.');
                }
                $visaInput[$field] = (string) ($rawVisaInput[$field] ?? '');
            }
            if ($pendaftaran->jalur === Pendaftaran::JALUR_WNI) {
                $pendaftaran->pilihan_visa = Pendaftaran::PILIHAN_VISA_TIDAK;
                $pendaftaran->jenis_visa = null;
            } elseif ($pendaftaran->pilihan_visa === Pendaftaran::PILIHAN_VISA_TIDAK) {
                $pendaftaran->jenis_visa = null;
            }
            $pendaftaran->user_id = (int) Yii::$app->user->id;
            $pendaftaran->status = Pendaftaran::STATUS_DRAFT;
            $pendaftaran->updated_by = (int) Yii::$app->user->id;
            $this->setProcessingUnit($pendaftaran);

            $validVisaChoice = $pendaftaran->validate(['pilihan_visa', 'jenis_visa']);
            if ($pendaftaran->jalur === Pendaftaran::JALUR_ASING && !$pendaftaran->pilihan_visa) {
                $pendaftaran->addError('pilihan_visa', 'Pilih apakah Anda mengurus visa sendiri atau membutuhkan bantuan.');
            }
            if (!$validVisaChoice || $pendaftaran->hasErrors()) {
                return $this->render('visa', [
                    'pendaftaran' => $pendaftaran,
                    'visaData' => $visaInput,
                    'activeStep' => 5,
                    'readOnly' => $readOnly,
                ]);
            }

            if (
                $pendaftaran->jalur === Pendaftaran::JALUR_ASING
                && $pendaftaran->pilihan_visa === Pendaftaran::PILIHAN_VISA_YA
            ) {
                foreach (['tujuan', 'tanggal_kedatangan', 'kota_kedatangan', 'alamat_selama_di_padang'] as $field) {
                    if (trim($visaInput[$field]) === '') {
                        $pendaftaran->addError('pilihan_visa', 'Lengkapi semua rincian pengajuan visa yang bertanda wajib.');
                    }
                }
                if (trim((string) $pendaftaran->jenis_visa) === '') {
                    $pendaftaran->addError('jenis_visa', 'Jenis visa wajib diisi jika meminta bantuan pengurusan visa.');
                }
                if (!$pendaftaran->no_paspor || $visaInput['masa_berlaku_paspor'] === '') {
                    $pendaftaran->addError('no_paspor', 'Nomor paspor dan masa berlaku paspor wajib diisi untuk pengajuan visa.');
                }
                foreach (['tanggal_kedatangan', 'masa_berlaku_paspor'] as $dateField) {
                    $dateValue = $visaInput[$dateField];
                    $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $dateValue);
                    if (!$date || $date->format('Y-m-d') !== $dateValue) {
                        $pendaftaran->addError('pilihan_visa', 'Tanggal kedatangan dan masa berlaku paspor harus berupa tanggal yang valid.');
                    }
                }
                $arrivalDate = \DateTimeImmutable::createFromFormat(
                    '!Y-m-d',
                    $visaInput['tanggal_kedatangan'],
                );
                $passportExpiry = \DateTimeImmutable::createFromFormat(
                    '!Y-m-d',
                    $visaInput['masa_berlaku_paspor'],
                );
                if ($arrivalDate && $passportExpiry && $passportExpiry <= $arrivalDate) {
                    $pendaftaran->addError('pilihan_visa', 'Masa berlaku paspor harus melewati tanggal kedatangan.');
                }
                if ($pendaftaran->hasErrors()) {
                    return $this->render('visa', [
                        'pendaftaran' => $pendaftaran,
                        'visaData' => $visaInput,
                        'activeStep' => 5,
                        'readOnly' => $readOnly,
                    ]);
                }
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$pendaftaran->save()) {
                    throw new \RuntimeException('Data pilihan visa gagal disimpan: ' . implode(' ', $pendaftaran->getErrorSummary(true)));
                }

                if ($pendaftaran->pilihan_visa === Pendaftaran::PILIHAN_VISA_YA) {
                    $visa->pendaftaran_id = $pendaftaran->pendaftaran_id;
                    $visa->masa_berlaku_paspor = $visaInput['masa_berlaku_paspor'];
                    $visa->data_pengajuan = json_encode([
                        'tujuan' => trim((string) $visaInput['tujuan']),
                        'tanggal_kedatangan' => trim((string) $visaInput['tanggal_kedatangan']),
                        'kota_kedatangan' => trim((string) $visaInput['kota_kedatangan']),
                        'alamat_selama_di_padang' => trim((string) $visaInput['alamat_selama_di_padang']),
                    ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                    $visa->status = PengajuanVisa::STATUS_MENUNGGU_DIPROSES;
                    $visa->created_by = (int) Yii::$app->user->id;
                    $visa->updated_by = (int) Yii::$app->user->id;
                    if (!$visa->save()) {
                        throw new \RuntimeException('Data pengajuan visa gagal disimpan: ' . implode(' ', $visa->getErrorSummary(true)));
                    }
                } elseif (!$visa->getIsNewRecord()) {
                    $visa->deleted_at = date('Y-m-d H:i:s');
                    $visa->updated_by = (int) Yii::$app->user->id;
                    if (!$visa->save(false)) {
                        throw new \RuntimeException('Pengajuan visa lama gagal diperbarui.');
                    }
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Pilihan dan rincian visa berhasil disimpan.');

                return $this->redirect(['ringkasan']);
            } catch (\Throwable $exception) {
                if ($transaction->getIsActive()) {
                    $transaction->rollBack();
                }
                throw $exception;
            }
        }

        return $this->render('visa', [
            'pendaftaran' => $pendaftaran,
            'visaData' => $visaData,
            'activeStep' => 5,
            'readOnly' => $readOnly,
        ]);
    }

    public function actionRingkasan(): Response|string
    {
        $pendaftaran = $this->getPendaftaranForStep();
        $readOnly = $this->isReadOnly($pendaftaran);
        if ($pendaftaran->getIsNewRecord()) {
            return $this->redirect(['form']);
        }

        if ($this->request->isPost) {
            if (!$this->request->post('integrity')) {
                Yii::$app->session->setFlash('error', 'Centang pernyataan kebenaran data sebelum mengirim pendaftaran.');
                return $this->redirect(['ringkasan']);
            }

            $errors = $this->getSubmissionErrors($pendaftaran);
            if ($errors) {
                Yii::$app->session->setFlash('error', implode(' ', $errors));
                return $this->redirect(['ringkasan']);
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $pendaftaran->status = Pendaftaran::STATUS_TERKIRIM;
                $pendaftaran->submitted_at = date('Y-m-d H:i:s');
                $pendaftaran->updated_by = (int) Yii::$app->user->id;
                $this->setProcessingUnit($pendaftaran);
                if (!$pendaftaran->save()) {
                    throw new \RuntimeException('Pendaftaran gagal dikirim: ' . implode(' ', $pendaftaran->getErrorSummary(true)));
                }
                $visa = $this->findVisaRequest($pendaftaran);
                if ($visa && $visa->status === PengajuanVisa::STATUS_MENUNGGU_DIPROSES) {
                    $visa->updated_by = (int) Yii::$app->user->id;
                    if (!$visa->save()) {
                        throw new \RuntimeException('Pengajuan visa gagal dikaitkan dengan pendaftaran: ' . implode(' ', $visa->getErrorSummary(true)));
                    }
                }
                $transaction->commit();
            } catch (\Throwable $exception) {
                if ($transaction->getIsActive()) {
                    $transaction->rollBack();
                }
                throw $exception;
            }

            Yii::$app->session->setFlash('success', 'Pendaftaran berhasil dikirim dan akan diverifikasi oleh petugas.');

            return $this->redirect(['status']);
        }

        return $this->render('ringkasan', [
            'pendaftaran' => $pendaftaran,
            'fakultas' => $pendaftaran->fakultas,
            'requirements' => $this->getApplicableDocuments($pendaftaran),
            'documents' => $this->getUploadedDocuments($pendaftaran),
            'visa' => $this->findVisaRequest($pendaftaran),
            'submissionErrors' => $this->getSubmissionErrors($pendaftaran),
            'activeStep' => 6,
            'readOnly' => $readOnly,
        ]);
    }

    public function actionStatus(): string
    {
        $pendaftaran = $this->findLatestPendaftaran();

        return $this->render('status', [
            'pendaftaran' => $pendaftaran,
            'completedSteps' => $this->getCompletedSteps($pendaftaran),
            'requirements' => $pendaftaran ? $this->getApplicableDocuments($pendaftaran) : [],
            'documents' => $pendaftaran ? $this->getUploadedDocuments($pendaftaran) : [],
            'visa' => $pendaftaran ? $this->findVisaRequest($pendaftaran) : null,
        ]);
    }

    public function actionBantuan(): string
    {
        return $this->render('bantuan');
    }

    private function findLatestPendaftaran(): ?Pendaftaran
    {
        return Pendaftaran::find()
            ->where(['user_id' => Yii::$app->user->id, 'deleted_at' => null])
            ->orderBy(['created_at' => SORT_DESC, 'pendaftaran_id' => SORT_DESC])
            ->one();
    }

    private function getPendaftaranForStep(): Pendaftaran
    {
        $pendaftaran = $this->findLatestPendaftaran();
        if ($pendaftaran && $this->isReadOnly($pendaftaran) && $this->request->isPost) {
            throw new \yii\web\ForbiddenHttpException('Pendaftaran yang sudah dikirim hanya dapat dilihat.');
        }

        return $pendaftaran ?? new Pendaftaran();
    }

    private function isReadOnly(Pendaftaran $pendaftaran): bool
    {
        return !$pendaftaran->getIsNewRecord() && $pendaftaran->status !== Pendaftaran::STATUS_DRAFT;
    }

    /**
     * Load only fields intended for the current wizard step.
     *
     * @param string[] $attributes
     */
    private function loadPendaftaranAttributes(Pendaftaran $pendaftaran, array $attributes): bool
    {
        $values = $this->request->post('Pendaftaran', []);
        if (!is_array($values)) {
            throw new \yii\web\BadRequestHttpException('Format data formulir tidak valid.');
        }

        foreach ($attributes as $attribute) {
            if (!array_key_exists($attribute, $values)) {
                continue;
            }
            if (!is_scalar($values[$attribute])) {
                throw new \yii\web\BadRequestHttpException('Nilai formulir tidak valid.');
            }
            $pendaftaran->$attribute = (string) $values[$attribute];
        }

        return true;
    }

    private function validateIdentity(Pendaftaran $pendaftaran): bool
    {
        $valid = $pendaftaran->validate([
            'nama_lengkap',
            'tempat_lahir',
            'tanggal_lahir',
            'jenis_kelamin',
            'asal_negara',
            'jalur',
            'no_paspor',
        ]);

        foreach (['nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'asal_negara', 'jalur'] as $attribute) {
            if (trim((string) $pendaftaran->$attribute) === '') {
                $pendaftaran->addError($attribute, 'Wajib diisi.');
                $valid = false;
            }
        }
        if ($pendaftaran->jalur === Pendaftaran::JALUR_ASING && !$pendaftaran->no_paspor) {
            $pendaftaran->addError('no_paspor', 'Nomor paspor wajib untuk pendaftar warga negara asing.');
            $valid = false;
        }
        $birthDate = \DateTimeImmutable::createFromFormat('!Y-m-d', (string) $pendaftaran->tanggal_lahir);
        if (!$birthDate || $birthDate->format('Y-m-d') !== $pendaftaran->tanggal_lahir) {
            $pendaftaran->addError('tanggal_lahir', 'Masukkan tanggal lahir yang valid.');
            $valid = false;
        }

        return $valid;
    }

    private function validateProgram(Pendaftaran $pendaftaran): bool
    {
        $valid = $pendaftaran->validate([
            'kategori_program',
            'jenjang',
            'jenis_program',
            'fakultas_id',
        ]);
        if (!$pendaftaran->kategori_program) {
            $pendaftaran->addError('kategori_program', 'Pilih kategori program.');
            $valid = false;
        }
        if ($pendaftaran->kategori_program === Pendaftaran::KATEGORI_PROGRAM_DEGREE) {
            if (!$pendaftaran->jenjang || !$pendaftaran->fakultas_id) {
                $pendaftaran->addError('jenjang', 'Pilih jenjang dan fakultas untuk program degree.');
                $valid = false;
            }
        } elseif ($pendaftaran->kategori_program === Pendaftaran::KATEGORI_PROGRAM_NON_DEGREE) {
            if (!$pendaftaran->jenis_program) {
                $pendaftaran->addError('jenis_program', 'Pilih jenis program non-degree.');
                $valid = false;
            }
        }

        return $valid;
    }

    private function validateIdentityAndProgram(Pendaftaran $pendaftaran): bool
    {
        $identityValid = $this->validateIdentity($pendaftaran);
        $programValid = $this->validateProgram($pendaftaran);

        return $identityValid && $programValid;
    }

    private function validateAcademics(Pendaftaran $pendaftaran, bool $allowIncomplete = false): bool
    {
        $academicAttributes = [
            'asal_institusi',
            'negara_institusi',
            'kota_institusi',
            'jenjang_pendidikan_terakhir',
            'jurusan_asal',
            'skala_nilai',
            'tahun_kelulusan',
            'nilai_akhir',
            'jenis_tes_bahasa_inggris',
            'skor_tes_bahasa_inggris',
            'tanggal_tes_bahasa_inggris',
            'nomor_sertifikat_bahasa_inggris',
            'tingkat_bahasa_indonesia',
            'rencana_kursus_bahasa_indonesia',
            'transfer_kredit',
            'status_mahasiswa_asal',
            'target_kredit',
            'pembiayaan',
            'lama_kunjungan',
        ];
        $valid = $pendaftaran->validate($academicAttributes);
        $requiredAttributes = [
            'asal_institusi' => 'Nama institusi asal wajib diisi.',
            'negara_institusi' => 'Negara institusi asal wajib diisi.',
            'kota_institusi' => 'Kota / lokasi institusi asal wajib diisi.',
            'jenjang_pendidikan_terakhir' => 'Pilih jenjang pendidikan terakhir.',
            'jurusan_asal' => 'Jurusan / konsentrasi wajib diisi.',
            'skala_nilai' => 'Pilih skala nilai.',
            'tahun_kelulusan' => 'Tahun kelulusan wajib diisi.',
            'pembiayaan' => 'Pilih sumber pembiayaan.',
        ];
        if (!$allowIncomplete) {
            foreach ($requiredAttributes as $attribute => $message) {
                if (trim((string) $pendaftaran->$attribute) === '') {
                    $pendaftaran->addError($attribute, $message);
                    $valid = false;
                }
            }
        }
        if ($pendaftaran->nilai_akhir === null) {
            if (!$allowIncomplete) {
                $pendaftaran->addError('nilai_akhir', 'Nilai akhir / IPK wajib diisi.');
                $valid = false;
            }
        } elseif (
            $pendaftaran->nilai_akhir < 0
            || ($pendaftaran->skala_nilai && $pendaftaran->nilai_akhir > (float) $pendaftaran->skala_nilai)
            || (!$pendaftaran->skala_nilai && $pendaftaran->nilai_akhir > 100)
        ) {
            $pendaftaran->addError('nilai_akhir', 'Nilai harus berada dalam rentang skala nilai yang dipilih.');
            $valid = false;
        }

        $englishTestDetails = [
            'skor_tes_bahasa_inggris' => 'Skor tes bahasa Inggris wajib diisi.',
            'tanggal_tes_bahasa_inggris' => 'Tanggal tes bahasa Inggris wajib diisi.',
            'nomor_sertifikat_bahasa_inggris' => 'Nomor sertifikat / TRF wajib diisi.',
        ];
        if (!$allowIncomplete && $pendaftaran->jenis_tes_bahasa_inggris) {
            foreach ($englishTestDetails as $attribute => $message) {
                if (trim((string) $pendaftaran->$attribute) === '') {
                    $pendaftaran->addError($attribute, $message);
                    $valid = false;
                }
            }
            if ($pendaftaran->tanggal_tes_bahasa_inggris > date('Y-m-d')) {
                $pendaftaran->addError('tanggal_tes_bahasa_inggris', 'Tanggal tes tidak boleh berada di masa depan.');
                $valid = false;
            }
            $maximumScore = match ($pendaftaran->jenis_tes_bahasa_inggris) {
                'IELTS Academic' => 9,
                'TOEFL iBT Official' => 120,
                'Duolingo Test (DET)' => 160,
                default => null,
            };
            if ($maximumScore !== null && $pendaftaran->skor_tes_bahasa_inggris > $maximumScore) {
                $pendaftaran->addError('skor_tes_bahasa_inggris', 'Skor melampaui batas maksimum tes yang dipilih.');
                $valid = false;
            }
        }

        if (
            !$allowIncomplete
            && $pendaftaran->kategori_program === Pendaftaran::KATEGORI_PROGRAM_NON_DEGREE
            && !$pendaftaran->lama_kunjungan
        ) {
            $pendaftaran->addError('lama_kunjungan', 'Lama kunjungan wajib diisi untuk program non-degree.');
            $valid = false;
        }
        if (!$allowIncomplete && $pendaftaran->transfer_kredit) {
            if (!$pendaftaran->status_mahasiswa_asal) {
                $pendaftaran->addError('status_mahasiswa_asal', 'Pilih status mahasiswa di kampus asal.');
                $valid = false;
            }
            if (!$pendaftaran->target_kredit || $pendaftaran->target_kredit <= 0) {
                $pendaftaran->addError('target_kredit', 'Target beban SKS / ECTS wajib diisi lebih dari 0.');
                $valid = false;
            }
        }

        return $valid;
    }

    /**
     * @return DokumenPersyaratan[]
     */
    private function getApplicableDocuments(Pendaftaran $pendaftaran): array
    {
        $scope = $pendaftaran->kategori_program === Pendaftaran::KATEGORI_PROGRAM_DEGREE
            ? DokumenPersyaratan::BERLAKU_UNTUK_DEGREE
            : DokumenPersyaratan::BERLAKU_UNTUK_NON_DEGREE;

        return DokumenPersyaratan::find()
            ->where(['is_active' => 1, 'deleted_at' => null])
            ->andWhere(['or', ['berlaku_untuk' => DokumenPersyaratan::BERLAKU_UNTUK_SEMUA], ['berlaku_untuk' => $scope]])
            ->orderBy(['dokumen_id' => SORT_ASC])
            ->all();
    }

    /**
     * @return DokumenPendaftar[]
     */
    private function getUploadedDocuments(Pendaftaran $pendaftaran): array
    {
        if ($pendaftaran->getIsNewRecord()) {
            return [];
        }

        return DokumenPendaftar::find()
            ->where(['pendaftaran_id' => $pendaftaran->pendaftaran_id, 'deleted_at' => null])
            ->with('dokumen')
            ->all();
    }

    private function validateUploadedFile(UploadedFile $file): void
    {
        $allowedTypes = [
            'application/pdf' => ['pdf'],
            'image/jpeg' => ['jpg', 'jpeg'],
            'image/png' => ['png'],
        ];
        $extension = strtolower($file->extension);
        $mimeType = $file->tempName && is_file($file->tempName)
            ? (new \finfo(FILEINFO_MIME_TYPE))->file($file->tempName)
            : false;

        if (
            $file->error !== UPLOAD_ERR_OK
            || $file->size > 5 * 1024 * 1024
            || !isset($allowedTypes[$mimeType])
            || !in_array($extension, $allowedTypes[$mimeType], true)
        ) {
            throw new \yii\web\BadRequestHttpException('Berkas harus PDF/JPG/PNG yang valid dan berukuran maksimal 5 MB.');
        }
    }

    private function findVisaRequest(Pendaftaran $pendaftaran): ?PengajuanVisa
    {
        if ($pendaftaran->getIsNewRecord()) {
            return null;
        }

        return PengajuanVisa::find()
            ->where(['pendaftaran_id' => $pendaftaran->pendaftaran_id, 'deleted_at' => null])
            ->orderBy(['visa_id' => SORT_DESC])
            ->one();
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeVisaData(?string $data): array
    {
        if (!$data) {
            return [];
        }

        $decoded = json_decode($data, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function isValidVisaDate(string $date): bool
    {
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/D', $date, $matches) !== 1) {
            return false;
        }

        return checkdate((int) $matches[2], (int) $matches[3], (int) $matches[1]);
    }

    private function setProcessingUnit(Pendaftaran $pendaftaran): void
    {
        $pendaftaran->tujuan_proses = $pendaftaran->jalur === Pendaftaran::JALUR_WNI
            && $pendaftaran->kategori_program === Pendaftaran::KATEGORI_PROGRAM_NON_DEGREE
            ? Pendaftaran::TUJUAN_PROSES_PDK
            : Pendaftaran::TUJUAN_PROSES_IO;
    }

    /**
     * @return string[]
     */
    private function getSubmissionErrors(Pendaftaran $pendaftaran): array
    {
        $errors = [];
        if ($pendaftaran->getIsNewRecord() || !$this->validateIdentityAndProgram($pendaftaran)) {
            $errors[] = 'Lengkapi data diri dan pilihan program.';
        }
        if ($pendaftaran->getIsNewRecord() || !$this->validateAcademics($pendaftaran)) {
            $errors[] = 'Lengkapi data akademik.';
        }

        $uploadedIds = [];
        foreach ($this->getUploadedDocuments($pendaftaran) as $document) {
            $uploadedIds[] = (int) $document->dokumen_id;
        }
        foreach ($this->getApplicableDocuments($pendaftaran) as $requirement) {
            if ($requirement->wajib && !in_array((int) $requirement->dokumen_id, $uploadedIds, true)) {
                $errors[] = 'Unggah dokumen wajib: ' . $requirement->nama_dokumen . '.';
            }
        }

        if ($pendaftaran->jalur === Pendaftaran::JALUR_ASING && !$pendaftaran->pilihan_visa) {
            $errors[] = 'Tentukan apakah Anda membutuhkan bantuan visa.';
        } elseif (
            $pendaftaran->jalur === Pendaftaran::JALUR_ASING
            && $pendaftaran->pilihan_visa === Pendaftaran::PILIHAN_VISA_YA
        ) {
            $visa = $this->findVisaRequest($pendaftaran);
            $visaData = $visa ? $this->decodeVisaData($visa->data_pengajuan) : [];
            $requiredVisaFields = ['tujuan', 'tanggal_kedatangan', 'kota_kedatangan', 'alamat_selama_di_padang'];
            $detailsComplete = $visa !== null
                && $visa->masa_berlaku_paspor !== null
                && trim((string) $visa->masa_berlaku_paspor) !== ''
                && trim((string) $pendaftaran->no_paspor) !== ''
                && trim((string) $pendaftaran->jenis_visa) !== '';
            foreach ($requiredVisaFields as $field) {
                if (!isset($visaData[$field]) || !is_string($visaData[$field]) || trim($visaData[$field]) === '') {
                    $detailsComplete = false;
                    break;
                }
            }
            $arrivalDate = $visaData['tanggal_kedatangan'] ?? null;
            $passportExpiry = $visa ? $visa->masa_berlaku_paspor : null;
            if (
                !is_string($arrivalDate)
                || !is_string($passportExpiry)
                || !$this->isValidVisaDate($arrivalDate)
                || !$this->isValidVisaDate($passportExpiry)
                || $passportExpiry <= $arrivalDate
            ) {
                $detailsComplete = false;
            }
            if (!$detailsComplete) {
                $errors[] = 'Lengkapi jenis visa, nomor paspor, masa berlaku paspor, serta semua rincian bantuan visa dengan data yang valid.';
            }
        }

        return array_values(array_unique($errors));
    }

    private function getCompletedSteps(?Pendaftaran $pendaftaran): int
    {
        if (!$pendaftaran) {
            return 0;
        }
        if ($pendaftaran->status !== Pendaftaran::STATUS_DRAFT) {
            return 6;
        }
        if (!$this->isIdentityComplete($pendaftaran)) {
            return 0;
        }
        if (!$this->isProgramComplete($pendaftaran)) {
            return 1;
        }
        if (!$this->isAcademicsComplete($pendaftaran)) {
            return 2;
        }

        $uploadedDocumentIds = [];
        foreach ($this->getUploadedDocuments($pendaftaran) as $document) {
            $uploadedDocumentIds[] = (int) $document->dokumen_id;
        }
        foreach ($this->getApplicableDocuments($pendaftaran) as $requirement) {
            if ($requirement->wajib && !in_array((int) $requirement->dokumen_id, $uploadedDocumentIds, true)) {
                return 3;
            }
        }
        if ($pendaftaran->jalur === Pendaftaran::JALUR_ASING) {
            if (!$pendaftaran->pilihan_visa) {
                return 4;
            }
            if ($pendaftaran->pilihan_visa === Pendaftaran::PILIHAN_VISA_YA) {
                $visa = $this->findVisaRequest($pendaftaran);
                if (!$visa || !$visa->masa_berlaku_paspor || !$visa->data_pengajuan) {
                    return 4;
                }
            }
        }

        return 5;
    }

    private function isIdentityComplete(Pendaftaran $pendaftaran): bool
    {
        foreach (['nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'asal_negara', 'jalur'] as $attribute) {
            if (trim((string) $pendaftaran->$attribute) === '') {
                return false;
            }
        }
        if ($pendaftaran->jalur === Pendaftaran::JALUR_ASING && !$pendaftaran->no_paspor) {
            return false;
        }

        $birthDate = \DateTimeImmutable::createFromFormat('!Y-m-d', (string) $pendaftaran->tanggal_lahir);

        return $birthDate !== false && $birthDate->format('Y-m-d') === $pendaftaran->tanggal_lahir;
    }

    private function isProgramComplete(Pendaftaran $pendaftaran): bool
    {
        if ($pendaftaran->kategori_program === Pendaftaran::KATEGORI_PROGRAM_DEGREE) {
            return (bool) $pendaftaran->jenjang && (bool) $pendaftaran->fakultas_id;
        }
        if ($pendaftaran->kategori_program === Pendaftaran::KATEGORI_PROGRAM_NON_DEGREE) {
            return (bool) $pendaftaran->jenis_program;
        }

        return false;
    }

    private function isAcademicsComplete(Pendaftaran $pendaftaran): bool
    {
        if (
            !$pendaftaran->asal_institusi
            || $pendaftaran->nilai_akhir === null
            || $pendaftaran->nilai_akhir < 0
            || $pendaftaran->nilai_akhir > 100
            || !$pendaftaran->pembiayaan
        ) {
            return false;
        }

        return $pendaftaran->kategori_program !== Pendaftaran::KATEGORI_PROGRAM_NON_DEGREE
            || (bool) $pendaftaran->lama_kunjungan;
    }
}

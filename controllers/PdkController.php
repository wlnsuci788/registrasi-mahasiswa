<?php

namespace app\controllers;

use app\components\RoleController;
use app\models\Pendaftaran;
use app\models\User;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class PdkController extends RoleController
{
    public $layout = 'staff';
    private const PAGE_SIZE = 10;

    protected function allowedRole(): string
    {
        return User::ROLE_PDK;
    }

    public function actionIndex(): string
    {
        $pendaftar = $this->getPendaftarQuery()
            ->andWhere(['status' => Pendaftaran::STATUS_TERKIRIM])
            ->orderBy(['submitted_at' => SORT_ASC])
            ->limit(self::PAGE_SIZE)
            ->all();

        return $this->render('index', [
            'pendaftar' => $pendaftar,
            'total' => $this->getPendaftarQuery()->count(),
            'menunggu' => $this->getPendaftarQuery()
                ->andWhere(['status' => Pendaftaran::STATUS_TERKIRIM])
                ->count(),
            'lolos' => $this->getPendaftarQuery()
                ->andWhere(['status' => [Pendaftaran::STATUS_LOLOS, Pendaftaran::STATUS_REGISTRATION_NUMBER_TERBIT]])
                ->count(),
            'tidakLolos' => $this->getPendaftarQuery()
                ->andWhere(['status' => Pendaftaran::STATUS_TIDAK_LOLOS])
                ->count(),
        ]);
    }

    public function actionPendidikan(): string
    {
        $search = trim((string) Yii::$app->request->get('q', ''));
        $query = $this->getPendaftarQuery();
        if ($search !== '') {
            $query->andWhere(['or',
                ['like', 'nama_lengkap', $search],
                ['like', 'asal_institusi', $search],
                ['like', 'jenis_program', $search],
                ['like', 'no_paspor', $search],
            ]);
        }

        return $this->render('pendidikan', [
            'pendaftar' => $query->orderBy(['submitted_at' => SORT_ASC])->limit(self::PAGE_SIZE)->all(),
            'search' => $search,
        ]);
    }

    public function actionBipa(): string
    {
        return $this->render('bipa');
    }

    public function actionRiwayat(): string
    {
        $search = trim((string) Yii::$app->request->get('q', ''));
        $query = $this->getPendaftarQuery()
            ->andWhere(['status' => [
                Pendaftaran::STATUS_LOLOS,
                Pendaftaran::STATUS_TIDAK_LOLOS,
                Pendaftaran::STATUS_REGISTRATION_NUMBER_TERBIT,
            ]]);
        if ($search !== '') {
            $query->andWhere(['or',
                ['like', 'nama_lengkap', $search],
                ['like', 'registration_number', $search],
                ['like', 'jenis_program', $search],
            ]);
        }

        return $this->render('riwayat', [
            'pendaftar' => $query->orderBy(['keputusan_at' => SORT_DESC])->limit(self::PAGE_SIZE)->all(),
            'total' => (clone $query)->count(),
            'lolos' => (clone $query)->andWhere(['status' => [
                Pendaftaran::STATUS_LOLOS,
                Pendaftaran::STATUS_REGISTRATION_NUMBER_TERBIT,
            ]])->count(),
            'search' => $search,
        ]);
    }

    public function actionEvaluasi(int $id): Response|string
    {
        $pendaftaran = $this->getPendaftarQuery()
            ->with(['fakultas', 'dokumenPendaftars.dokumen'])
            ->andWhere(['pendaftaran_id' => $id])
            ->one();
        if (!$pendaftaran) {
            throw new NotFoundHttpException('Data pendaftar WNI tidak ditemukan.');
        }

        if (Yii::$app->request->isPost) {
            if ($pendaftaran->status !== Pendaftaran::STATUS_TERKIRIM) {
                Yii::$app->session->setFlash('error', 'Keputusan pada arsip seleksi tidak dapat diubah dari halaman ini.');

                return $this->redirect(['riwayat']);
            }
            $keputusan = (string) Yii::$app->request->post('keputusan', '');
            $catatan = trim((string) Yii::$app->request->post('catatan_keputusan', ''));
            if (!in_array($keputusan, ['lolos', 'tidak-lolos', 'revisi'], true)) {
                Yii::$app->session->setFlash('error', 'Pilih rekomendasi seleksi yang valid.');
            } elseif ($catatan === '') {
                Yii::$app->session->setFlash('error', 'Catatan evaluasi wajib diisi sebelum keputusan disimpan.');
            } else {
                if ($keputusan === 'lolos') {
                    $pendaftaran->status = Pendaftaran::STATUS_LOLOS;
                } elseif ($keputusan === 'tidak-lolos') {
                    $pendaftaran->status = Pendaftaran::STATUS_TIDAK_LOLOS;
                }
                $pendaftaran->catatan_keputusan = $catatan;
                $pendaftaran->keputusan_by = (int) Yii::$app->user->id;
                $pendaftaran->keputusan_at = date('Y-m-d H:i:s');
                $pendaftaran->updated_by = (int) Yii::$app->user->id;
                if (!$pendaftaran->save()) {
                    throw new \RuntimeException(
                        'Keputusan seleksi gagal disimpan: ' . implode(' ', $pendaftaran->getErrorSummary(true)),
                    );
                }
                Yii::$app->session->setFlash(
                    'success',
                    $keputusan === 'revisi' ? 'Catatan revisi berhasil disimpan.' : 'Keputusan seleksi berhasil disimpan.',
                );

                return $this->redirect($keputusan === 'revisi'
                    ? ['evaluasi', 'id' => $pendaftaran->pendaftaran_id]
                    : ['riwayat']);
            }
        }

        return $this->render('evaluasi', ['pendaftaran' => $pendaftaran]);
    }

    /** @return \yii\db\ActiveQuery<Pendaftaran> */
    private function getPendaftarQuery(): \yii\db\ActiveQuery
    {
        return Pendaftaran::find()
            ->alias('p')
            ->where([
                'p.jalur' => Pendaftaran::JALUR_WNI,
                'p.tujuan_proses' => Pendaftaran::TUJUAN_PROSES_PDK,
            ])
            ->andWhere(['p.deleted_at' => null])
            ->andWhere(['<>', 'p.status', Pendaftaran::STATUS_DRAFT])
            ->with(['fakultas']);
    }
}

<?php

namespace app\controllers;

use app\components\RoleController;
use app\models\Pendaftaran;
use app\models\User;
use Yii;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;

class AkademikController extends RoleController
{
    public $layout = 'staff';
    private const PAGE_SIZE = 10;

    protected function allowedRole(): string
    {
        return User::ROLE_AKADEMIK;
    }

    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['terbitkan' => ['POST']],
            ],
        ]);
    }

    public function actionIndex(): string
    {
        $eligible = $this->getEligibleQuery();
        $issued = $this->getIssuedQuery();
        $pending = $this->getPendingQuery();
        $search = trim((string) Yii::$app->request->get('q', ''));
        $registrations = $this->getEligibleQuery()->with(['fakultas', 'dokumenPendaftars']);
        if ($search !== '') {
            $registrations->andWhere(['or',
                ['like', 'nama_lengkap', $search],
                ['like', 'registration_number', $search],
                ['like', 'asal_institusi', $search],
            ]);
        }

        return $this->render('index', [
            'total' => (clone $eligible)->count(),
            'pending' => (clone $pending)->count(),
            'issued' => (clone $issued)->count(),
            'needsReview' => Pendaftaran::find()
                ->where(['status' => Pendaftaran::STATUS_TIDAK_LOLOS])
                ->andWhere(['deleted_at' => null])
                ->count(),
            'registrations' => $registrations
                ->orderBy(['status' => SORT_ASC, 'submitted_at' => SORT_ASC])
                ->limit(self::PAGE_SIZE)
                ->all(),
            'search' => $search,
        ]);
    }

    public function actionPenerbitan(): string
    {
        $id = Yii::$app->request->get('id');
        if ($id !== null) {
            $pendaftaran = $this->findRegistration((int) $id);

            return $this->render('penerbitan', [
                'pendaftaran' => $pendaftaran,
                'registrations' => [],
            ]);
        }

        $statusFilter = (string) Yii::$app->request->get('status', 'all');
        if (!in_array($statusFilter, ['all', 'pending', 'issued'], true)) {
            $statusFilter = 'all';
        }
        $pending = $this->getPendingQuery();
        $issued = $this->getIssuedQuery();
        $query = $this->getEligibleQuery()
            ->with(['fakultas', 'dokumenPendaftars'])
            ->orderBy(['status' => SORT_ASC, 'submitted_at' => SORT_ASC]);
        if ($statusFilter === 'pending') {
            $query->andWhere(['status' => Pendaftaran::STATUS_LOLOS])
                ->andWhere(['registration_number' => null]);
        } elseif ($statusFilter === 'issued') {
            $query->andWhere(['status' => Pendaftaran::STATUS_REGISTRATION_NUMBER_TERBIT])
                ->andWhere(['not', ['registration_number' => null]]);
        }
        $pagination = new Pagination([
            'totalCount' => (clone $query)->count(),
            'pageSize' => self::PAGE_SIZE,
        ]);

        return $this->render('penerbitan', [
            'pendaftaran' => null,
            'registrations' => $query->offset($pagination->offset)->limit($pagination->limit)->all(),
            'total' => (clone $this->getEligibleQuery())->count(),
            'pending' => (clone $pending)->count(),
            'issued' => (clone $issued)->count(),
            'statusFilter' => $statusFilter,
            'pagination' => $pagination,
        ]);
    }

    public function actionTerbitkan(int $id): Response
    {
        if ((string) Yii::$app->request->post('confirm', '') !== '1') {
            Yii::$app->session->setFlash('error', 'Konfirmasi pemeriksaan data sebelum menerbitkan nomor registrasi.');

            return $this->redirect(['penerbitan', 'id' => $id]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $pendaftaran = $this->findRegistration($id);
            if ($pendaftaran->status !== Pendaftaran::STATUS_LOLOS || $pendaftaran->registration_number !== null) {
                Yii::$app->session->setFlash('error', 'Nomor hanya dapat diterbitkan satu kali untuk pendaftar yang sudah lolos.');
                $transaction->rollBack();

                return $this->redirect(['penerbitan', 'id' => $id]);
            }

            $registrationNumber = $this->createRegistrationNumber($pendaftaran);
            $updated = Pendaftaran::updateAll([
                'registration_number' => $registrationNumber,
                'registration_by' => (int) Yii::$app->user->id,
                'registration_at' => date('Y-m-d H:i:s'),
                'status' => Pendaftaran::STATUS_REGISTRATION_NUMBER_TERBIT,
                'updated_by' => (int) Yii::$app->user->id,
            ], [
                'pendaftaran_id' => $id,
                'status' => Pendaftaran::STATUS_LOLOS,
                'registration_number' => null,
                'deleted_at' => null,
            ]);

            if ($updated !== 1) {
                throw new \RuntimeException('Data berubah saat nomor akan diterbitkan. Muat ulang dan periksa status pendaftar.');
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', 'Registration number ' . $registrationNumber . ' berhasil diterbitkan.');

            return $this->redirect(['penerbitan', 'id' => $id]);
        } catch (\Throwable $exception) {
            if ($transaction->getIsActive()) {
                $transaction->rollBack();
            }
            throw $exception;
        }
    }

    public function actionBuku(): string
    {
        $search = trim((string) Yii::$app->request->get('q', ''));
        $program = trim((string) Yii::$app->request->get('program', ''));
        $query = $this->getIssuedQuery()->with(['fakultas']);
        $this->applySearch($query, $search);
        if ($program !== '' && in_array($program, ['degree', 'non-degree'], true)) {
            $query->andWhere(['kategori_program' => $program]);
        }
        $registrations = $query->orderBy(['registration_at' => SORT_DESC])
            ->limit(self::PAGE_SIZE)
            ->all();

        return $this->render('buku', [
            'registrations' => $registrations,
            'total' => (clone $this->getIssuedQuery())->count(),
            'regular' => (clone $this->getIssuedQuery())->andWhere(['kategori_program' => 'degree'])->count(),
            'creditEarning' => (clone $this->getIssuedQuery())->andWhere(['jenis_program' => Pendaftaran::JENIS_PROGRAM_CREDIT_EARNING])->count(),
            'bipa' => (clone $this->getIssuedQuery())->andWhere(['jenis_program' => 'BIPA'])->count(),
            'search' => $search,
            'program' => $program,
        ]);
    }

    public function actionRiwayat(): string
    {
        $search = trim((string) Yii::$app->request->get('q', ''));
        $period = trim((string) Yii::$app->request->get('periode', ''));
        $category = trim((string) Yii::$app->request->get('kategori', ''));
        if (!in_array($category, ['degree', 'non-degree'], true)) {
            $category = '';
        }
        $query = $this->getIssuedQuery()->with(['fakultas', 'registrationBy']);
        $this->applySearch($query, $search);
        if ($period !== '' && preg_match('/^\d{4}$/', $period) === 1) {
            $query->andWhere(['between', 'registration_at', $period . '-01-01 00:00:00', $period . '-12-31 23:59:59']);
        }
        if ($category !== '') {
            $query->andWhere(['kategori_program' => $category]);
        }
        $total = (clone $query)->count();
        $print = Yii::$app->request->get('print') === '1';
        $pagination = new Pagination([
            'totalCount' => $total,
            'pageSize' => $print ? max(1, $total) : self::PAGE_SIZE,
        ]);
        $registrations = $query->orderBy(['registration_at' => SORT_DESC])
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        $reportYear = $period !== '' && preg_match('/^\d{4}$/', $period) === 1 ? $period : date('Y');
        $periodTotal = (clone $this->getIssuedQuery())
            ->andWhere(['between', 'registration_at', $reportYear . '-01-01 00:00:00', $reportYear . '-12-31 23:59:59'])
            ->count();
        $programBreakdown = $this->getIssuedQuery()
            ->select(['kategori_program', 'total' => 'COUNT(*)'])
            ->groupBy(['kategori_program'])
            ->asArray()
            ->all();
        $auditIncomplete = (clone $this->getIssuedQuery())
            ->andWhere(['or', ['registration_at' => null], ['registration_by' => null]])
            ->count();

        return $this->render('riwayat', [
            'registrations' => $registrations,
            'total' => $total,
            'allTimeTotal' => (clone $this->getIssuedQuery())->count(),
            'periodTotal' => $periodTotal,
            'reportYear' => $reportYear,
            'programBreakdown' => $programBreakdown,
            'auditIncomplete' => $auditIncomplete,
            'pagination' => $pagination,
            'year' => $period,
            'category' => $category,
            'search' => $search,
            'print' => $print,
        ]);
    }

    private function getEligibleQuery(): ActiveQuery
    {
        return Pendaftaran::find()
            ->where(['deleted_at' => null])
            ->andWhere(['status' => [Pendaftaran::STATUS_LOLOS, Pendaftaran::STATUS_REGISTRATION_NUMBER_TERBIT]])
            ->andWhere(['in', 'jalur', [Pendaftaran::JALUR_ASING, Pendaftaran::JALUR_WNI]]);
    }

    private function getPendingQuery(): ActiveQuery
    {
        return $this->getEligibleQuery()
            ->andWhere(['status' => Pendaftaran::STATUS_LOLOS])
            ->andWhere(['registration_number' => null]);
    }

    private function getIssuedQuery(): ActiveQuery
    {
        return $this->getEligibleQuery()
            ->andWhere(['status' => Pendaftaran::STATUS_REGISTRATION_NUMBER_TERBIT])
            ->andWhere(['not', ['registration_number' => null]]);
    }

    private function findRegistration(int $id): Pendaftaran
    {
        $pendaftaran = Pendaftaran::find()
            ->where(['pendaftaran_id' => $id, 'deleted_at' => null])
            ->with(['fakultas', 'dokumenPendaftars.dokumen'])
            ->one();
        if (!$pendaftaran instanceof Pendaftaran) {
            throw new NotFoundHttpException('Data pendaftaran tidak ditemukan.');
        }

        if (
            !in_array($pendaftaran->status, [
            Pendaftaran::STATUS_LOLOS,
            Pendaftaran::STATUS_REGISTRATION_NUMBER_TERBIT,
            ], true)
        ) {
            throw new NotFoundHttpException('Pendaftar belum lolos verifikasi untuk penerbitan nomor.');
        }

        return $pendaftaran;
    }

    private function createRegistrationNumber(Pendaftaran $pendaftaran): string
    {
        $facultyCode = strtoupper((string) ($pendaftaran->fakultas->kode_fakultas ?? 'UN'));
        $facultyCode = (string) preg_replace('/[^A-Z0-9]/', '', $facultyCode);
        $facultyCode = substr($facultyCode ?: 'UN', 0, 6);
        $unitCode = $pendaftaran->tujuan_proses === Pendaftaran::TUJUAN_PROSES_PDK ? 'PDK' : 'INT';

        return sprintf('REG-%s-%s-%s-%05d', date('Y'), $facultyCode, $unitCode, $pendaftaran->pendaftaran_id);
    }

    private function applySearch(ActiveQuery $query, string $search): void
    {
        if ($search !== '') {
            $query->andWhere(['or',
                ['like', 'nama_lengkap', $search],
                ['like', 'registration_number', $search],
                ['like', 'asal_institusi', $search],
            ]);
        }
    }
}

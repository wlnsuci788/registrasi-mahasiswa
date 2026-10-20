<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pendaftaran".
 *
 * @property int $pendaftaran_id
 * @property int|null $user_id
 * @property string|null $nama_lengkap
 * @property string|null $tempat_lahir
 * @property string|null $tanggal_lahir
 * @property string|null $jenis_kelamin
 * @property string|null $asal_negara
 * @property string|null $jalur
 * @property string|null $no_paspor
 * @property string|null $jenis_visa
 * @property string|null $kategori_program
 * @property string|null $jenjang
 * @property string|null $jenis_program
 * @property int|null $fakultas_id
 * @property string|null $asal_institusi
 * @property float|null $nilai_akhir
 * @property string|null $negara_institusi
 * @property string|null $kota_institusi
 * @property string|null $jenjang_pendidikan_terakhir
 * @property string|null $jurusan_asal
 * @property string|null $skala_nilai
 * @property int|null $tahun_kelulusan
 * @property string|null $jenis_tes_bahasa_inggris
 * @property float|null $skor_tes_bahasa_inggris
 * @property string|null $tanggal_tes_bahasa_inggris
 * @property string|null $nomor_sertifikat_bahasa_inggris
 * @property string|null $tingkat_bahasa_indonesia
 * @property string|null $rencana_kursus_bahasa_indonesia
 * @property int $transfer_kredit
 * @property string|null $status_mahasiswa_asal
 * @property float|null $target_kredit
 * @property string|null $pembiayaan
 * @property string|null $lama_kunjungan
 * @property string|null $pilihan_visa
 * @property string|null $tujuan_proses
 * @property string|null $status
 * @property string|null $submitted_at
 * @property int|null $keputusan_by
 * @property string|null $catatan_keputusan
 * @property string|null $keputusan_at
 * @property string|null $saran_fakultas
 * @property string|null $catatan_fakultas
 * @property int|null $saran_by
 * @property string|null $saran_at
 * @property string|null $registration_number
 * @property int|null $registration_by
 * @property string|null $registration_at
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property string|null $deleted_at
 *
 * @property DokumenPendaftar[] $dokumenPendaftars
 * @property Fakultas|null $fakultas
 * @property Users $keputusanBy
 * @property PengajuanVisa[] $pengajuanVisas
 * @property Users $registrationBy
 * @property Users $saranBy
 * @property Users $updatedBy
 * @property Users $user
 */
class Pendaftaran extends \yii\db\ActiveRecord
{
    /**
     * ENUM field values
     */
    const JENIS_KELAMIN_LAKI_LAKI = 'Laki-laki';
    const JENIS_KELAMIN_PEREMPUAN = 'Perempuan';
    const JALUR_ASING = 'asing';
    const JALUR_WNI = 'wni';
    const KATEGORI_PROGRAM_DEGREE = 'degree';
    const KATEGORI_PROGRAM_NON_DEGREE = 'non-degree';
    const JENJANG_S1 = 'S1';
    const JENJANG_S2 = 'S2';
    const JENJANG_S3 = 'S3';
    const JENIS_PROGRAM_SUMMER_COURSE = 'summer course';
    const JENIS_PROGRAM_SHORT_COURSE = 'short course';
    const JENIS_PROGRAM_STUDENT_EXCHANGE = 'student exchange';
    const JENIS_PROGRAM_CREDIT_EARNING = 'credit earning';
    const PEMBIAYAAN_MANDIRI = 'Mandiri';
    const PEMBIAYAAN_BEASISWA = 'Beasiswa';
    const PEMBIAYAAN_SPONSOR = 'Sponsor';
    const PILIHAN_VISA_YA = 'Ya';
    const PILIHAN_VISA_TIDAK = 'Tidak';
    const TUJUAN_PROSES_IO = 'IO';
    const TUJUAN_PROSES_PDK = 'PDK';
    const STATUS_DRAFT = 'Draft';
    const STATUS_TERKIRIM = 'Terkirim';
    const STATUS_LOLOS = 'Lolos';
    const STATUS_TIDAK_LOLOS = 'Tidak lolos';
    const STATUS_REGISTRATION_NUMBER_TERBIT = 'Registration number terbit';
    const SARAN_FAKULTAS_DISARANKAN_LOLOS = 'Disarankan lolos';
    const SARAN_FAKULTAS_DISARANKAN_TIDAK_LOLOS = 'Disarankan tidak lolos';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pendaftaran';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'asal_negara', 'jalur', 'no_paspor', 'jenis_visa', 'kategori_program', 'jenjang', 'jenis_program', 'fakultas_id', 'asal_institusi', 'nilai_akhir', 'negara_institusi', 'kota_institusi', 'jenjang_pendidikan_terakhir', 'jurusan_asal', 'skala_nilai', 'tahun_kelulusan', 'jenis_tes_bahasa_inggris', 'skor_tes_bahasa_inggris', 'tanggal_tes_bahasa_inggris', 'nomor_sertifikat_bahasa_inggris', 'tingkat_bahasa_indonesia', 'rencana_kursus_bahasa_indonesia', 'transfer_kredit', 'status_mahasiswa_asal', 'target_kredit', 'pembiayaan', 'lama_kunjungan', 'pilihan_visa', 'tujuan_proses', 'status', 'submitted_at', 'keputusan_by', 'catatan_keputusan', 'keputusan_at', 'saran_fakultas', 'catatan_fakultas', 'saran_by', 'saran_at', 'registration_number', 'registration_by', 'registration_at', 'updated_by', 'updated_at', 'deleted_at'], 'default', 'value' => null],
            [['user_id', 'fakultas_id', 'keputusan_by', 'saran_by', 'registration_by', 'updated_by', 'tahun_kelulusan', 'transfer_kredit'], 'integer'],
            [['tanggal_lahir', 'tanggal_tes_bahasa_inggris', 'submitted_at', 'keputusan_at', 'saran_at', 'registration_at', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['jenis_kelamin', 'jalur', 'kategori_program', 'jenjang', 'jenis_program', 'pembiayaan', 'pilihan_visa', 'tujuan_proses', 'status', 'catatan_keputusan', 'saran_fakultas', 'catatan_fakultas', 'skala_nilai', 'jenis_tes_bahasa_inggris', 'tingkat_bahasa_indonesia', 'rencana_kursus_bahasa_indonesia'], 'string'],
            [['nilai_akhir', 'skor_tes_bahasa_inggris', 'target_kredit'], 'number'],
            [['nama_lengkap', 'asal_institusi', 'jurusan_asal'], 'string', 'max' => 150],
            [['tempat_lahir', 'asal_negara'], 'string', 'max' => 100],
            [['negara_institusi', 'kota_institusi'], 'string', 'max' => 100],
            [['jenjang_pendidikan_terakhir', 'status_mahasiswa_asal'], 'string', 'max' => 120],
            [['jenis_tes_bahasa_inggris'], 'string', 'max' => 40],
            [['nomor_sertifikat_bahasa_inggris'], 'string', 'max' => 100],
            [['rencana_kursus_bahasa_indonesia'], 'string', 'max' => 120],
            [['skala_nilai', 'tingkat_bahasa_indonesia'], 'string', 'max' => 30],
            [['no_paspor', 'registration_number'], 'string', 'max' => 30],
            [['jenis_visa', 'lama_kunjungan'], 'string', 'max' => 50],
            ['jenis_kelamin', 'in', 'range' => array_keys(self::optsJenisKelamin())],
            ['jalur', 'in', 'range' => array_keys(self::optsJalur())],
            ['kategori_program', 'in', 'range' => array_keys(self::optsKategoriProgram())],
            ['jenjang', 'in', 'range' => array_keys(self::optsJenjang())],
            ['jenis_program', 'in', 'range' => array_keys(self::optsJenisProgram())],
            ['jenjang_pendidikan_terakhir', 'in', 'range' => array_keys(self::optsJenjangPendidikanTerakhir()), 'skipOnEmpty' => true],
            ['skala_nilai', 'in', 'range' => array_keys(self::optsSkalaNilai()), 'skipOnEmpty' => true],
            ['jenis_tes_bahasa_inggris', 'in', 'range' => array_keys(self::optsJenisTesBahasaInggris()), 'skipOnEmpty' => true],
            ['tingkat_bahasa_indonesia', 'in', 'range' => array_keys(self::optsTingkatBahasaIndonesia()), 'skipOnEmpty' => true],
            ['rencana_kursus_bahasa_indonesia', 'in', 'range' => array_keys(self::optsRencanaKursusBahasaIndonesia()), 'skipOnEmpty' => true],
            ['status_mahasiswa_asal', 'in', 'range' => array_keys(self::optsStatusMahasiswaAsal()), 'skipOnEmpty' => true],
            ['transfer_kredit', 'boolean'],
            ['tahun_kelulusan', 'integer', 'min' => 1900, 'max' => (int) date('Y') + 1],
            [['skor_tes_bahasa_inggris', 'target_kredit'], 'number', 'min' => 0],
            ['tanggal_tes_bahasa_inggris', 'date', 'format' => 'php:Y-m-d', 'skipOnEmpty' => true],
            ['pembiayaan', 'in', 'range' => array_keys(self::optsPembiayaan())],
            ['pilihan_visa', 'in', 'range' => array_keys(self::optsPilihanVisa())],
            ['tujuan_proses', 'in', 'range' => array_keys(self::optsTujuanProses())],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            ['saran_fakultas', 'in', 'range' => array_keys(self::optsSaranFakultas())],
            [['registration_number'], 'unique'],
            [['fakultas_id'], 'exist', 'skipOnError' => true, 'targetClass' => Fakultas::class, 'targetAttribute' => ['fakultas_id' => 'fakultas_id']],
            [['keputusan_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['keputusan_by' => 'user_id']],
            [['registration_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['registration_by' => 'user_id']],
            [['saran_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['saran_by' => 'user_id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['updated_by' => 'user_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'user_id' => 'User ID',
            'nama_lengkap' => 'Nama Lengkap',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'jenis_kelamin' => 'Jenis Kelamin',
            'asal_negara' => 'Asal Negara',
            'jalur' => 'Jalur',
            'no_paspor' => 'No Paspor',
            'jenis_visa' => 'Jenis Visa',
            'kategori_program' => 'Kategori Program',
            'jenjang' => 'Jenjang',
            'jenis_program' => 'Jenis Program',
            'fakultas_id' => 'Fakultas ID',
            'asal_institusi' => 'Asal Institusi',
            'nilai_akhir' => 'Nilai Rata-rata / GPA / CGPA',
            'negara_institusi' => 'Negara Institusi Asal',
            'kota_institusi' => 'Kota / Lokasi Institusi',
            'jenjang_pendidikan_terakhir' => 'Jenjang Pendidikan Terakhir',
            'jurusan_asal' => 'Jurusan / Konsentrasi',
            'skala_nilai' => 'Skala Nilai',
            'tahun_kelulusan' => 'Tahun Kelulusan',
            'jenis_tes_bahasa_inggris' => 'Jenis Tes Kemampuan Bahasa Inggris',
            'skor_tes_bahasa_inggris' => 'Skor / Overall Band',
            'tanggal_tes_bahasa_inggris' => 'Tanggal Pelaksanaan Tes',
            'nomor_sertifikat_bahasa_inggris' => 'Nomor Sertifikat / Test Report Form',
            'tingkat_bahasa_indonesia' => 'Tingkat Kemahiran Bahasa Indonesia',
            'rencana_kursus_bahasa_indonesia' => 'Rencana Mengikuti Kursus Bahasa Indonesia',
            'transfer_kredit' => 'Pendaftaran Transfer Kredit',
            'status_mahasiswa_asal' => 'Status Mahasiswa di Kampus Asal',
            'target_kredit' => 'Target Beban SKS / ECTS',
            'pembiayaan' => 'Pembiayaan',
            'lama_kunjungan' => 'Lama Kunjungan',
            'pilihan_visa' => 'Pilihan Visa',
            'tujuan_proses' => 'Tujuan Proses',
            'status' => 'Status',
            'submitted_at' => 'Submitted At',
            'keputusan_by' => 'Keputusan By',
            'catatan_keputusan' => 'Catatan Keputusan',
            'keputusan_at' => 'Keputusan At',
            'saran_fakultas' => 'Saran Fakultas',
            'catatan_fakultas' => 'Catatan Fakultas',
            'saran_by' => 'Saran By',
            'saran_at' => 'Saran At',
            'registration_number' => 'Registration Number',
            'registration_by' => 'Registration By',
            'registration_at' => 'Registration At',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
        ];
    }

    /**
     * Gets query for [[DokumenPendaftars]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDokumenPendaftars()
    {
        return $this->hasMany(DokumenPendaftar::class, ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * Gets query for [[Fakultas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFakultas()
    {
        return $this->hasOne(Fakultas::class, ['fakultas_id' => 'fakultas_id']);
    }

    /**
     * Gets query for [[KeputusanBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKeputusanBy()
    {
        return $this->hasOne(Users::class, ['user_id' => 'keputusan_by']);
    }

    /**
     * Gets query for [[PengajuanVisas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPengajuanVisas()
    {
        return $this->hasMany(PengajuanVisa::class, ['pendaftaran_id' => 'pendaftaran_id']);
    }

    /**
     * Gets query for [[RegistrationBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegistrationBy()
    {
        return $this->hasOne(Users::class, ['user_id' => 'registration_by']);
    }

    /**
     * Gets query for [[SaranBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSaranBy()
    {
        return $this->hasOne(Users::class, ['user_id' => 'saran_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(Users::class, ['user_id' => 'updated_by']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['user_id' => 'user_id']);
    }


    /**
     * column jenis_kelamin ENUM value labels
     * @return string[]
     */
    public static function optsJenisKelamin()
    {
        return [
            self::JENIS_KELAMIN_LAKI_LAKI => 'Laki-laki',
            self::JENIS_KELAMIN_PEREMPUAN => 'Perempuan',
        ];
    }

    /**
     * column jalur ENUM value labels
     * @return string[]
     */
    public static function optsJalur()
    {
        return [
            self::JALUR_ASING => 'asing',
            self::JALUR_WNI => 'wni',
        ];
    }

    /**
     * column kategori_program ENUM value labels
     * @return string[]
     */
    public static function optsKategoriProgram()
    {
        return [
            self::KATEGORI_PROGRAM_DEGREE => 'degree',
            self::KATEGORI_PROGRAM_NON_DEGREE => 'non-degree',
        ];
    }

    /**
     * column jenjang ENUM value labels
     * @return string[]
     */
    public static function optsJenjang()
    {
        return [
            self::JENJANG_S1 => 'S1',
            self::JENJANG_S2 => 'S2',
            self::JENJANG_S3 => 'S3',
        ];
    }

    /**
     * column jenis_program ENUM value labels
     * @return string[]
     */
    public static function optsJenisProgram()
    {
        return [
            self::JENIS_PROGRAM_SUMMER_COURSE => 'summer course',
            self::JENIS_PROGRAM_SHORT_COURSE => 'short course',
            self::JENIS_PROGRAM_STUDENT_EXCHANGE => 'student exchange',
            self::JENIS_PROGRAM_CREDIT_EARNING => 'credit earning',
        ];
    }

    public static function optsJenjangPendidikanTerakhir(): array
    {
        return [
            'SMA/High School Diploma' => 'SMA / High School Diploma',
            'Diploma' => 'Diploma',
            'Bachelor' => 'Bachelor Degree',
            'Master' => 'Master Degree',
            'Doctoral' => 'Doctoral Degree',
        ];
    }

    public static function optsSkalaNilai(): array
    {
        return [
            '4' => 'Skala 4.00',
            '10' => 'Skala 10.00',
            '100' => 'Skala 100',
        ];
    }

    public static function optsJenisTesBahasaInggris(): array
    {
        return [
            'IELTS Academic' => 'IELTS Academic',
            'TOEFL iBT Official' => 'TOEFL iBT Official',
            'Duolingo Test (DET)' => 'Duolingo Test (DET)',
        ];
    }

    public static function optsTingkatBahasaIndonesia(): array
    {
        return [
            'Pemula (BIPA 1-2)' => 'Pemula (BIPA 1-2)',
            'Menengah (BIPA 3-4)' => 'Menengah (BIPA 3-4)',
            'Mahir (BIPA 5-7)' => 'Mahir (BIPA 5-7)',
        ];
    }

    public static function optsRencanaKursusBahasaIndonesia(): array
    {
        return [
            'Ya, BIPA Intensif Pra-Kuliah (3 Bulan)' => 'Ya, BIPA Intensif Pra-Kuliah (3 Bulan)',
            'Tidak Memerlukan Kursus BIPA' => 'Tidak Memerlukan Kursus BIPA',
        ];
    }

    public static function optsStatusMahasiswaAsal(): array
    {
        return [
            'Mahasiswa Aktif (Semester 1-4)' => 'Mahasiswa Aktif (Semester 1-4)',
            'Mahasiswa Aktif (Semester 5-8)' => 'Mahasiswa Aktif (Semester 5-8)',
            'Mahasiswa Aktif (Semester 9+)' => 'Mahasiswa Aktif (Semester 9+)',
            'Sudah Lulus' => 'Sudah Lulus',
        ];
    }

    /**
     * column pembiayaan ENUM value labels
     * @return string[]
     */
    public static function optsPembiayaan()
    {
        return [
            self::PEMBIAYAAN_MANDIRI => 'Mandiri',
            self::PEMBIAYAAN_BEASISWA => 'Beasiswa',
            self::PEMBIAYAAN_SPONSOR => 'Sponsor',
        ];
    }

    /**
     * column pilihan_visa ENUM value labels
     * @return string[]
     */
    public static function optsPilihanVisa()
    {
        return [
            self::PILIHAN_VISA_YA => 'Ya',
            self::PILIHAN_VISA_TIDAK => 'Tidak',
        ];
    }

    /**
     * column tujuan_proses ENUM value labels
     * @return string[]
     */
    public static function optsTujuanProses()
    {
        return [
            self::TUJUAN_PROSES_IO => 'IO',
            self::TUJUAN_PROSES_PDK => 'PDK',
        ];
    }

    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_TERKIRIM => 'Terkirim',
            self::STATUS_LOLOS => 'Lolos',
            self::STATUS_TIDAK_LOLOS => 'Tidak lolos',
            self::STATUS_REGISTRATION_NUMBER_TERBIT => 'Registration number terbit',
        ];
    }

    /**
     * column saran_fakultas ENUM value labels
     * @return string[]
     */
    public static function optsSaranFakultas()
    {
        return [
            self::SARAN_FAKULTAS_DISARANKAN_LOLOS => 'Disarankan lolos',
            self::SARAN_FAKULTAS_DISARANKAN_TIDAK_LOLOS => 'Disarankan tidak lolos',
        ];
    }

    /**
     * @return string
     */
    public function displayJenisKelamin()
    {
        return self::optsJenisKelamin()[$this->jenis_kelamin];
    }

    /**
     * @return bool
     */
    public function isJenisKelaminLakiLaki()
    {
        return $this->jenis_kelamin === self::JENIS_KELAMIN_LAKI_LAKI;
    }

    public function setJenisKelaminToLakiLaki()
    {
        $this->jenis_kelamin = self::JENIS_KELAMIN_LAKI_LAKI;
    }

    /**
     * @return bool
     */
    public function isJenisKelaminPerempuan()
    {
        return $this->jenis_kelamin === self::JENIS_KELAMIN_PEREMPUAN;
    }

    public function setJenisKelaminToPerempuan()
    {
        $this->jenis_kelamin = self::JENIS_KELAMIN_PEREMPUAN;
    }

    /**
     * @return string
     */
    public function displayJalur()
    {
        return self::optsJalur()[$this->jalur];
    }

    /**
     * @return bool
     */
    public function isJalurAsing()
    {
        return $this->jalur === self::JALUR_ASING;
    }

    public function setJalurToAsing()
    {
        $this->jalur = self::JALUR_ASING;
    }

    /**
     * @return bool
     */
    public function isJalurWni()
    {
        return $this->jalur === self::JALUR_WNI;
    }

    public function setJalurToWni()
    {
        $this->jalur = self::JALUR_WNI;
    }

    /**
     * @return string
     */
    public function displayKategoriProgram()
    {
        return self::optsKategoriProgram()[$this->kategori_program];
    }

    /**
     * @return bool
     */
    public function isKategoriProgramDegree()
    {
        return $this->kategori_program === self::KATEGORI_PROGRAM_DEGREE;
    }

    public function setKategoriProgramToDegree()
    {
        $this->kategori_program = self::KATEGORI_PROGRAM_DEGREE;
    }

    /**
     * @return bool
     */
    public function isKategoriProgramNonDegree()
    {
        return $this->kategori_program === self::KATEGORI_PROGRAM_NON_DEGREE;
    }

    public function setKategoriProgramToNonDegree()
    {
        $this->kategori_program = self::KATEGORI_PROGRAM_NON_DEGREE;
    }

    /**
     * @return string
     */
    public function displayJenjang()
    {
        return self::optsJenjang()[$this->jenjang];
    }

    /**
     * @return bool
     */
    public function isJenjangS1()
    {
        return $this->jenjang === self::JENJANG_S1;
    }

    public function setJenjangToS1()
    {
        $this->jenjang = self::JENJANG_S1;
    }

    /**
     * @return bool
     */
    public function isJenjangS2()
    {
        return $this->jenjang === self::JENJANG_S2;
    }

    public function setJenjangToS2()
    {
        $this->jenjang = self::JENJANG_S2;
    }

    /**
     * @return bool
     */
    public function isJenjangS3()
    {
        return $this->jenjang === self::JENJANG_S3;
    }

    public function setJenjangToS3()
    {
        $this->jenjang = self::JENJANG_S3;
    }

    /**
     * @return string
     */
    public function displayJenisProgram()
    {
        return self::optsJenisProgram()[$this->jenis_program];
    }

    /**
     * @return bool
     */
    public function isJenisProgramSummerCourse()
    {
        return $this->jenis_program === self::JENIS_PROGRAM_SUMMER_COURSE;
    }

    public function setJenisProgramToSummerCourse()
    {
        $this->jenis_program = self::JENIS_PROGRAM_SUMMER_COURSE;
    }

    /**
     * @return bool
     */
    public function isJenisProgramShortCourse()
    {
        return $this->jenis_program === self::JENIS_PROGRAM_SHORT_COURSE;
    }

    public function setJenisProgramToShortCourse()
    {
        $this->jenis_program = self::JENIS_PROGRAM_SHORT_COURSE;
    }

    /**
     * @return bool
     */
    public function isJenisProgramStudentExchange()
    {
        return $this->jenis_program === self::JENIS_PROGRAM_STUDENT_EXCHANGE;
    }

    public function setJenisProgramToStudentExchange()
    {
        $this->jenis_program = self::JENIS_PROGRAM_STUDENT_EXCHANGE;
    }

    /**
     * @return bool
     */
    public function isJenisProgramCreditEarning()
    {
        return $this->jenis_program === self::JENIS_PROGRAM_CREDIT_EARNING;
    }

    public function setJenisProgramToCreditEarning()
    {
        $this->jenis_program = self::JENIS_PROGRAM_CREDIT_EARNING;
    }

    /**
     * @return string
     */
    public function displayPembiayaan()
    {
        return self::optsPembiayaan()[$this->pembiayaan];
    }

    /**
     * @return bool
     */
    public function isPembiayaanMandiri()
    {
        return $this->pembiayaan === self::PEMBIAYAAN_MANDIRI;
    }

    public function setPembiayaanToMandiri()
    {
        $this->pembiayaan = self::PEMBIAYAAN_MANDIRI;
    }

    /**
     * @return bool
     */
    public function isPembiayaanBeasiswa()
    {
        return $this->pembiayaan === self::PEMBIAYAAN_BEASISWA;
    }

    public function setPembiayaanToBeasiswa()
    {
        $this->pembiayaan = self::PEMBIAYAAN_BEASISWA;
    }

    /**
     * @return bool
     */
    public function isPembiayaanSponsor()
    {
        return $this->pembiayaan === self::PEMBIAYAAN_SPONSOR;
    }

    public function setPembiayaanToSponsor()
    {
        $this->pembiayaan = self::PEMBIAYAAN_SPONSOR;
    }

    /**
     * @return string
     */
    public function displayPilihanVisa()
    {
        return self::optsPilihanVisa()[$this->pilihan_visa];
    }

    /**
     * @return bool
     */
    public function isPilihanVisaYa()
    {
        return $this->pilihan_visa === self::PILIHAN_VISA_YA;
    }

    public function setPilihanVisaToYa()
    {
        $this->pilihan_visa = self::PILIHAN_VISA_YA;
    }

    /**
     * @return bool
     */
    public function isPilihanVisaTidak()
    {
        return $this->pilihan_visa === self::PILIHAN_VISA_TIDAK;
    }

    public function setPilihanVisaToTidak()
    {
        $this->pilihan_visa = self::PILIHAN_VISA_TIDAK;
    }

    /**
     * @return string
     */
    public function displayTujuanProses()
    {
        return self::optsTujuanProses()[$this->tujuan_proses];
    }

    /**
     * @return bool
     */
    public function isTujuanProsesIo()
    {
        return $this->tujuan_proses === self::TUJUAN_PROSES_IO;
    }

    public function setTujuanProsesToIo()
    {
        $this->tujuan_proses = self::TUJUAN_PROSES_IO;
    }

    /**
     * @return bool
     */
    public function isTujuanProsesPdk()
    {
        return $this->tujuan_proses === self::TUJUAN_PROSES_PDK;
    }

    public function setTujuanProsesToPdk()
    {
        $this->tujuan_proses = self::TUJUAN_PROSES_PDK;
    }

    /**
     * @return string
     */
    public function displayStatus()
    {
        return self::optsStatus()[$this->status];
    }

    /**
     * @return bool
     */
    public function isStatusDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function setStatusToDraft()
    {
        $this->status = self::STATUS_DRAFT;
    }

    /**
     * @return bool
     */
    public function isStatusTerkirim()
    {
        return $this->status === self::STATUS_TERKIRIM;
    }

    public function setStatusToTerkirim()
    {
        $this->status = self::STATUS_TERKIRIM;
    }

    /**
     * @return bool
     */
    public function isStatusLolos()
    {
        return $this->status === self::STATUS_LOLOS;
    }

    public function setStatusToLolos()
    {
        $this->status = self::STATUS_LOLOS;
    }

    /**
     * @return bool
     */
    public function isStatusTidakLolos()
    {
        return $this->status === self::STATUS_TIDAK_LOLOS;
    }

    public function setStatusToTidakLolos()
    {
        $this->status = self::STATUS_TIDAK_LOLOS;
    }

    /**
     * @return bool
     */
    public function isStatusRegistrationNumberTerbit()
    {
        return $this->status === self::STATUS_REGISTRATION_NUMBER_TERBIT;
    }

    public function setStatusToRegistrationNumberTerbit()
    {
        $this->status = self::STATUS_REGISTRATION_NUMBER_TERBIT;
    }

    /**
     * @return string
     */
    public function displaySaranFakultas()
    {
        return self::optsSaranFakultas()[$this->saran_fakultas];
    }

    /**
     * @return bool
     */
    public function isSaranFakultasDisarankanLolos()
    {
        return $this->saran_fakultas === self::SARAN_FAKULTAS_DISARANKAN_LOLOS;
    }

    public function setSaranFakultasToDisarankanLolos()
    {
        $this->saran_fakultas = self::SARAN_FAKULTAS_DISARANKAN_LOLOS;
    }

    /**
     * @return bool
     */
    public function isSaranFakultasDisarankanTidakLolos()
    {
        return $this->saran_fakultas === self::SARAN_FAKULTAS_DISARANKAN_TIDAK_LOLOS;
    }

    public function setSaranFakultasToDisarankanTidakLolos()
    {
        $this->saran_fakultas = self::SARAN_FAKULTAS_DISARANKAN_TIDAK_LOLOS;
    }
}

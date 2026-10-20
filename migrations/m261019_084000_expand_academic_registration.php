<?php

use yii\db\Migration;

/**
 * Adds the academic and language details collected in registration step three.
 */
class m261019_084000_expand_academic_registration extends Migration // phpcs:ignore Squiz.Classes.ValidClassName.NotPascalCase
{
    public function safeUp(): void
    {
        $this->addColumn('{{%pendaftaran}}', 'negara_institusi', $this->string(100)->null());
        $this->addColumn('{{%pendaftaran}}', 'kota_institusi', $this->string(100)->null());
        $this->addColumn('{{%pendaftaran}}', 'jenjang_pendidikan_terakhir', $this->string(80)->null());
        $this->addColumn('{{%pendaftaran}}', 'jurusan_asal', $this->string(150)->null());
        $this->addColumn('{{%pendaftaran}}', 'skala_nilai', $this->string(20)->null());
        $this->addColumn('{{%pendaftaran}}', 'tahun_kelulusan', $this->smallInteger()->null());
        $this->addColumn('{{%pendaftaran}}', 'jenis_tes_bahasa_inggris', $this->string(40)->null());
        $this->addColumn('{{%pendaftaran}}', 'skor_tes_bahasa_inggris', $this->decimal(6, 2)->null());
        $this->addColumn('{{%pendaftaran}}', 'tanggal_tes_bahasa_inggris', $this->date()->null());
        $this->addColumn('{{%pendaftaran}}', 'nomor_sertifikat_bahasa_inggris', $this->string(100)->null());
        $this->addColumn('{{%pendaftaran}}', 'tingkat_bahasa_indonesia', $this->string(30)->null());
        $this->addColumn('{{%pendaftaran}}', 'rencana_kursus_bahasa_indonesia', $this->string(120)->null());
        $this->addColumn('{{%pendaftaran}}', 'transfer_kredit', $this->boolean()->notNull()->defaultValue(false));
        $this->addColumn('{{%pendaftaran}}', 'status_mahasiswa_asal', $this->string(120)->null());
        $this->addColumn('{{%pendaftaran}}', 'target_kredit', $this->decimal(6, 2)->null());
    }

    public function safeDown(): void
    {
        $this->dropColumn('{{%pendaftaran}}', 'target_kredit');
        $this->dropColumn('{{%pendaftaran}}', 'status_mahasiswa_asal');
        $this->dropColumn('{{%pendaftaran}}', 'transfer_kredit');
        $this->dropColumn('{{%pendaftaran}}', 'rencana_kursus_bahasa_indonesia');
        $this->dropColumn('{{%pendaftaran}}', 'tingkat_bahasa_indonesia');
        $this->dropColumn('{{%pendaftaran}}', 'nomor_sertifikat_bahasa_inggris');
        $this->dropColumn('{{%pendaftaran}}', 'tanggal_tes_bahasa_inggris');
        $this->dropColumn('{{%pendaftaran}}', 'skor_tes_bahasa_inggris');
        $this->dropColumn('{{%pendaftaran}}', 'jenis_tes_bahasa_inggris');
        $this->dropColumn('{{%pendaftaran}}', 'tahun_kelulusan');
        $this->dropColumn('{{%pendaftaran}}', 'skala_nilai');
        $this->dropColumn('{{%pendaftaran}}', 'jurusan_asal');
        $this->dropColumn('{{%pendaftaran}}', 'jenjang_pendidikan_terakhir');
        $this->dropColumn('{{%pendaftaran}}', 'kota_institusi');
        $this->dropColumn('{{%pendaftaran}}', 'negara_institusi');
    }
}

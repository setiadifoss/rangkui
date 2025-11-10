<?php

namespace App\Modules\System\Controllers;

use App\Controllers\BaseController;
use App\Modules\System\Models\BackupModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Files\Exceptions\FileNotFoundException;
use Config\Services;

class BackupController extends BaseController
{
    public $viewPath = "Backup/";
    public function index()
    {
        $log_message = "{$this->session->name} access backup ";
        Services::writeLog(getTypeUser($this->session->user_type), $this->session->user_id, 'Backup', $log_message);

        $view = $this->viewPath . "v_index";
        $title = "Database Backup";

        $bm = new BackupModel();

        $list_backup = $bm->getBackup();


        $content = [
            'list_backup' => $list_backup,
        ];

        $js = [];
        _render($view, $title, $content, $js);
    }

  public function add()
    {
        $log_message = "{$this->session->name} mencoba melakukan backup.";
        // Asumsi Services::writeLog() sudah didefinisikan
        Services::writeLog(getTypeUser($this->session->user_type), $this->session->user_id, 'Backup', $log_message);

        // --- PENCARIAN mysqldump YANG DINAMIS DENGAN FALLBACK ---
        $mysqldump_path = '';

        // Pertama, coba cari di PATH sistem
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $mysqldump_path = trim(shell_exec('where mysqldump 2>NUL'));
        } else {
            $mysqldump_path = trim(shell_exec('which mysqldump 2> /dev/null'));
        }

        // Akhirnya, cek apakah jalur ditemukan dan valid
        if (empty($mysqldump_path) || !file_exists($mysqldump_path)) {
            $log_message = 'Gagal melakukan backup! mysqldump tidak ditemukan di jalur yang ditentukan. Silakan periksa atau tambahkan jalur Anda di dalam kode.';
            slim_alert('error', $log_message);
            Services::writeLog(getTypeUser($this->session->user_type), $this->session->user_id, 'Backup', $log_message);
            return redirect()->to('sistem/backups');
        }

        $time2append = date("Ymd_His");
        $backupPath = WRITEPATH . 'backup';
        $fullPath = $backupPath . DIRECTORY_SEPARATOR . "backup_{$time2append}.sql";

        // Cek dan buat direktori backup jika belum ada
        if (!is_dir($backupPath)) {
            if (!mkdir($backupPath, 0777, true)) {
                $log_message = "Gagal membuat direktori backup: {$backupPath}";
                slim_alert('error', $log_message);
                Services::writeLog(getTypeUser($this->session->user_type), $this->session->user_id, 'Backup', $log_message);
                return redirect()->to('sistem/backups');
            }
        }

        // Cek izin tulis direktori
        if (!is_writable($backupPath)) {
            if (!chmod($backupPath, 0777)) {
                $log_message = "Gagal mengubah izin direktori backup: {$backupPath}";
                slim_alert('error', $log_message);
                Services::writeLog(getTypeUser($this->session->user_type), $this->session->user_id, 'Backup', $log_message);
                return redirect()->to('sistem/backups');
            }
        }

        // Ambil kredensial dari objek koneksi database CI4
        $db_host = $this->db->hostname;
        $db_name = $this->db->database;
        $db_user = $this->db->username;
        $db_password = $this->db->password;
        $db_port = $this->db->port;

        // Ambil data dari database tanpa opsi --no-create-db
        $command = "{$mysqldump_path}";
        $command .= " --user=" . escapeshellarg($db_user);

        // Menambahkan opsi password hanya jika password tidak kosong
        if (!empty($db_password)) {
            $command .= " --password=" . escapeshellarg($db_password);
        }

        $command .= " --host=" . escapeshellarg($db_host);

        // Tambahkan port jika tersedia dan tidak kosong
        if (!empty($db_port)) {
            $command .= " --port=" . escapeshellarg($db_port);
        }

        $command .= " " . escapeshellarg($db_name);

        // Deskriptor pipe untuk proc_open
        $descriptorspec = array(
            0 => array("pipe", "r"),   // stdin
            1 => array("file", $fullPath, "w"), // stdout
            2 => array("pipe", "w")    // stderr
        );

        $process = proc_open($command, $descriptorspec, $pipes);

        if (is_resource($process)) {
            // Tutup stdin karena tidak digunakan
            fclose($pipes[0]);

            // Baca pesan error jika ada
            $error_output = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            // Tunggu proses selesai
            $status_code = proc_close($process);

            // Periksa status dan ukuran file
            if ($status_code === 0 && filesize($fullPath) > 0) {
                $user_id = $this->session->user_id;
                $backup_time = date("Y-m-d H:i:s");

                $output = "Backup BERHASIL, file backup tersimpan di {$backupPath}!";

                if (!preg_match('@^WIN.*@i', PHP_OS)) {
                    // Kompres file menggunakan tar.gz di sistem non-Windows
                    $curr_dir = getcwd();
                    @chdir($backupPath);

                    $tar_command = "tar cvzf " . escapeshellarg("backup_{$time2append}.sql.tar.gz") . " " . escapeshellarg("backup_{$time2append}.sql");
                    exec($tar_command, $outputs, $tar_status);

                    if ($tar_status === 0) {
                        @unlink($fullPath);
                        $output .= " File dikompres menggunakan format arsip tar.gz.";
                        $fullPath = $backupPath . DIRECTORY_SEPARATOR . "backup_{$time2append}.sql.tar.gz";
                    } else {
                        $output .= " Gagal mengompres file.";
                    }
                    @chdir($curr_dir);
                }

                $data = [
                    'user_id' => $user_id,
                    'backup_time' => $backup_time,
                    'backup_file' => $this->db->escapeString($fullPath),
                ];

                // Asumsi BackupModel dan slim_alert sudah didefinisikan
                $b = new BackupModel();
                $this->db->transBegin();
                $b->insert($data);

                if ($this->db->transStatus() === false) {
                    $this->db->transRollback();
                    slim_alert('error', $this->db->error());
                    $log_message = "{$this->session->name} backup gagal : {$this->db->error}";
                } else {
                    $this->db->transCommit();
                    slim_alert('success', $output);
                    $log_message = "{$this->session->name} backup berhasil";
                }
            } else {
                // Proses gagal atau file kosong
                if (file_exists($fullPath) && filesize($fullPath) == 0) {
                    @unlink($fullPath); // Hapus file kosong
                }
                $log_message = "Backup GAGAL! Kode status: {$status_code}. Output error: {$error_output}";
                slim_alert('error', $log_message);
            }
        } else {
            $log_message = "Backup GAGAL! Tidak bisa membuka proses mysqldump.";
            slim_alert('error', $log_message);
        }

        Services::writeLog(getTypeUser($this->session->user_type), $this->session->user_id, 'Backup', $log_message);
        return redirect()->to('sistem/backups');
    }

    function download($id)
    {

        $backup = new BackupModel();

        $backupFile = $backup->find($id);

        if ($backupFile === null) {
            throw PageNotFoundException::forPageNotFound();
        } else {

            $filePath = $backupFile->backup_file;

            if (file_exists($filePath)) {

                $log_message = "Download backup file";
                Services::writeLog(getTypeUser($this->session->user_type), $this->session->user_id, 'Backup', $log_message);

                return $this->response->download($filePath, null);
            } else {

                $log_message = "Download backup file failed";
                Services::writeLog(getTypeUser($this->session->user_type), $this->session->user_id, 'Backup', $log_message);

                slim_alert('error', "File not found : {$filePath}");
                return redirect()->to('sistem/backups');
            }
        }
    }
}

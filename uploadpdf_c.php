<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['pdfFile']) && $_FILES['pdfFile']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['pdfFile']['tmp_name'];
        $fileName = basename($_FILES['pdfFile']['name']);
        $fileSize = $_FILES['pdfFile']['size'];
        $fileType = mime_content_type($fileTmpPath);

        // Validasi ukuran file
        if ($fileSize > 2 * 1024 * 1024) {
            echo "Error: Ukuran file melebihi 2MB.";
            exit;
        }

        // Validasi tipe MIME
        if ($fileType !== 'application/pdf') {
            echo "Error: Hanya file PDF yang diperbolehkan.";
            exit;
        }

        // Sanitasi nama file
        $safeFileName = preg_replace("/[^a-zA-Z0-9_\-\.]/", "_", $fileName);

        $uploadDir = __DIR__ . "/uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destPath = $uploadDir . $safeFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            echo "Upload berhasil! File disimpan di: " . htmlspecialchars($destPath);
        } else {
            echo "Error: Gagal memindahkan file.";
        }
    } else {
        echo "Error: Tidak ada file yang diupload.";
    }
}
?>

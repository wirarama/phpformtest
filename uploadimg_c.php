<?php
function resizeImage($sourcePath, $destPath, $newWidth) {
    list($width, $height, $type) = getimagesize($sourcePath);

    $ratio = $height / $width;
    $newHeight = $newWidth * $ratio;

    $dst = imagecreatetruecolor($newWidth, $newHeight);

    switch ($type) {
        case IMAGETYPE_JPEG:
            $src = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $src = imagecreatefrompng($sourcePath);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            break;
        default:
            return false;
    }

    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($dst, $destPath, 90);
            break;
        case IMAGETYPE_PNG:
            imagepng($dst, $destPath);
            break;
    }

    imagedestroy($src);
    imagedestroy($dst);
    return true;
}

function createThumbnail($sourcePath, $destPath, $thumbWidth = 150, $cropSize = 100) {
    list($width, $height, $type) = getimagesize($sourcePath);

    // Hitung tinggi baru sesuai rasio
    $ratio = $height / $width;
    $newHeight = $thumbWidth * $ratio;

    // Resize ke lebar 150px proporsional
    $resized = imagecreatetruecolor($thumbWidth, $newHeight);

    switch ($type) {
        case IMAGETYPE_JPEG:
            $src = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $src = imagecreatefrompng($sourcePath);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            break;
        default:
            return false;
    }

    imagecopyresampled($resized, $src, 0, 0, 0, 0, $thumbWidth, $newHeight, $width, $height);

    // Crop ke 100x100 dari tengah
    $cropX = ($thumbWidth - $cropSize) / 2;
    $cropY = ($newHeight - $cropSize) / 2;

    $thumb = imagecreatetruecolor($cropSize, $cropSize);
    if ($type == IMAGETYPE_PNG) {
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
    }

    imagecopyresampled($thumb, $resized, 0, 0, $cropX, $cropY, $cropSize, $cropSize, $cropSize, $cropSize);

    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($thumb, $destPath, 90);
            break;
        case IMAGETYPE_PNG:
            imagepng($thumb, $destPath);
            break;
    }

    imagedestroy($src);
    imagedestroy($resized);
    imagedestroy($thumb);
    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['imageFile']['tmp_name'];
        $fileName = basename($_FILES['imageFile']['name']);
        $fileSize = $_FILES['imageFile']['size'];
        $fileType = mime_content_type($fileTmpPath);

        if ($fileSize > 2 * 1024 * 1024) {
            echo "Error: Ukuran file melebihi 2MB.";
        }

        if ($fileType !== 'image/jpeg' && $fileType !== 'image/png') {
            echo "Error: Hanya file JPEG/PNG yang diperbolehkan.";
        }

        $safeFileName = preg_replace("/[^a-zA-Z0-9_\-\.]/", "_", $fileName);

        $uploadDir = __DIR__ . "/uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destPath = $uploadDir . $safeFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
			// Resize ke lebar 700px
			$resizedPath = $uploadDir . "resized_" . $safeFileName;
			resizeImage($destPath, $resizedPath, 700);

			// Buat thumbnail: resize ke 150px lalu crop ke 100x100
			$thumbPath = $uploadDir . "thumb_" . $safeFileName;
			createThumbnail($destPath, $thumbPath, 250, 100);

			echo "Upload berhasil!<br>Resized: " . htmlspecialchars($resizedPath) . "<br>Thumbnail: " . htmlspecialchars($thumbPath);
		} else {
			echo "Error: Gagal memindahkan file.";
		}
    } else {
        echo "Error: Tidak ada file yang diupload.";
    }
}
?>

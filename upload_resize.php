<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];

    if ($file['error'] == 0) {
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $filename = time() . '_' . basename($file['name']);
        $target_path = $upload_dir . $filename;

        move_uploaded_file($file['tmp_name'], $target_path);

        $image_info = getimagesize($target_path);
        $width = $image_info[0];
        $height = $image_info[1];
        $mime = $image_info['mime'];

        switch ($mime) {
            case 'image/jpeg': $src = imagecreatefromjpeg($target_path); break;
            case 'image/png':  $src = imagecreatefrompng($target_path); break;
            case 'image/gif':  $src = imagecreatefromgif($target_path); break;
            default: die("Unsupported image type.");
        }

        $sizes = [
            'small'  => [150,150],
            'medium' => [400,400],
            'large'  => [800,800],
        ];

        foreach ($sizes as $label => $dim) {
            list($w,$h) = $dim;
            $dst = imagecreatetruecolor($w, $h);
            imagecopyresampled($dst, $src, 0,0,0,0, $w,$h, $width,$height);
            imagejpeg($dst, $upload_dir.pathinfo($filename, PATHINFO_FILENAME)."_{$label}.jpg", 90);
            imagedestroy($dst);
        }

        imagedestroy($src);
        echo " Image uploaded and resized successfully!";
    } else {
        echo " Upload failed!";
    }
}
?>

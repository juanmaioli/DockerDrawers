<?php
/**
 * func_img.php - Centralized Image Processing Functions
 */

/**
 * Crops an image to a square by taking the center part.
 */
function crop_image_square($image_path) {
    if (!file_exists($image_path)) return false;
    
    $info = getimagesize($image_path);
    if (!$info) return false;
    $mime = $info['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $src_image = imagecreatefromjpeg($image_path);
            break;
        case 'image/png':
            $src_image = imagecreatefrompng($image_path);
            break;
        default:
            return false;
    }

    if (!$src_image) return false;

    $width = imagesx($src_image);
    $height = imagesy($src_image);

    if ($width !== $height) {
        $size = min($width, $height);
        $x = ($width - $size) / 2;
        $y = ($height - $size) / 2;

        $imgCrop = imagecrop($src_image, ['x' => $x, 'y' => $y, 'width' => $size, 'height' => $size]);
        if ($imgCrop !== false) {
            imagejpeg($imgCrop, $image_path, 90);
            imagedestroy($imgCrop);
        }
    } else {
        imagejpeg($src_image, $image_path, 90);
    }
    imagedestroy($src_image);
    return true;
}

/**
 * Resizes an image to specific dimensions.
 */
function resize_image($image_path, $dst_w, $dst_h) {
    if (!file_exists($image_path)) return false;
    
    // Always use imagecreatefromjpeg because crop_image_square saves as JPG
    $src_image = @imagecreatefromjpeg($image_path);
    if (!$src_image) {
        // Fallback to auto-detection if JPG failed
        $info = getimagesize($image_path);
        if (!$info) return false;
        switch($info['mime']) {
            case 'image/jpeg': $src_image = imagecreatefromjpeg($image_path); break;
            case 'image/png':  $src_image = imagecreatefrompng($image_path); break;
            default: return false;
        }
    }
    if (!$src_image) return false;

    $src_w = imagesx($src_image);
    $src_h = imagesy($src_image);

    $dst_image = imagecreatetruecolor($dst_w, $dst_h);
    imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);

    imagejpeg($dst_image, $image_path, 90);
    imagedestroy($src_image);
    imagedestroy($dst_image);
    return true;
}

/**
 * Adds a logo watermark to the bottom right of an image.
 */
function add_logo_watermark($target_path, $logo_path) {
    if (!file_exists($target_path) || !file_exists($logo_path)) return false;

    $watermark = @imagecreatefrompng($logo_path);
    if (!$watermark) return false;

    $img = @imagecreatefromjpeg($target_path);
    if (!$img) {
        imagedestroy($watermark);
        return false;
    }

    $margin = 20;
    $img_w = imagesx($img);
    $img_h = imagesy($img);
    $wtrmrk_w = imagesx($watermark);
    $wtrmrk_h = imagesy($watermark);

    $dst_x = $img_w - $wtrmrk_w - $margin;
    $dst_y = $img_h - $wtrmrk_h - $margin;

    if ($wtrmrk_w < $img_w && $wtrmrk_h < $img_h) {
        imagecopy($img, $watermark, $dst_x, $dst_y, 0, 0, $wtrmrk_w, $wtrmrk_h);
    }

    imagejpeg($img, $target_path, 95);

    imagedestroy($img);
    imagedestroy($watermark);
    return true;
}

/**
 * Handles the complete upload and processing flow for an image.
 */
function process_image_upload($file_input_name, $destination_dir, $new_name_prefix, $resize_w = 500, $resize_h = 500, $add_logo = false, $logo_path = '', $keep_full = false) {
    if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $tmp_name = $_FILES[$file_input_name]['tmp_name'];
    $original_name = $_FILES[$file_input_name]['name'];
    $extension = "jpg"; 
    
    $hash = hash('crc32', $original_name . time());
    $unique_name = $new_name_prefix . "_" . $hash . "." . $extension;
    
    // Ensure destination dir has trailing slash and is absolute if possible
    $destination_dir = rtrim($destination_dir, '/') . '/';
    $full_path = $destination_dir . $unique_name;

    if (move_uploaded_file($tmp_name, $full_path)) {
        if ($keep_full) {
            $full_version_name = $new_name_prefix . "_" . $hash . "_full." . $extension;
            copy($full_path, $destination_dir . $full_version_name);
        }

        crop_image_square($full_path);
        resize_image($full_path, $resize_w, $resize_h);
        if ($add_logo && $logo_path) {
            // Logo path might need to be absolute too
            add_logo_watermark($full_path, $logo_path);
        }
        return $unique_name;
    }

    return false;
}
?>
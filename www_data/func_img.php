<?php
function crop_image($image_full,$W,$H)
{
    $src_image = imagecreatefromjpeg($image_full);
    $size = getimagesize($image_full);
    $image_width = $size[0];
    $image_height = $size[1];
    $X = $image_width/2 - $W/2;
    $Y = $image_height/2  - $H/2;

    $imgCrop = imagecrop($src_image, ['x' => $X , 'y' => $Y , 'width' => $W , 'height' => $H]);
    if ($imgCrop !== FALSE) {
        imagejpeg($imgCrop, 'cropped_'. $image_full);
        imagedestroy($imgCrop);
        //echo "Image cropped successfully";
    }
    imagedestroy($src_image);
}

function crop_image_square($image_full)
{
    $src_image = imagecreatefromjpeg($image_full);
    $size = getimagesize($image_full);
    $image_width = $size[0];
    $image_height = $size[1];
    if (   $image_width !== $image_height ){
            if ($image_height >= $image_width) {
                $W = $image_width;
                $H = $image_width;
            }else{
                $W = $image_height;
                $H = $image_height;
            }      
            $X = $image_width/2 - $W/2;
            $Y = $image_height/2  - $H/2;

            $imgCrop = imagecrop($src_image, ['x' => $X , 'y' => $Y , 'width' => $W , 'height' => $H]);
            if ($imgCrop !== FALSE) {
                imagejpeg($imgCrop, $image_full);
                imagedestroy($imgCrop);
                //echo "Image cropped successfully";
            }
            imagedestroy($src_image);
    }
}

function resize_image($image_full,$dst_w,$dst_h){

    $src_image = imagecreatefromjpeg($image_full);
    $size = getimagesize($image_full);
    $src_w= $size[0];
    $src_h = $size[1];
    $dst_image = imagecreatetruecolor($dst_w, $dst_h);
    $dst_x = 0; 
    $dst_y = 0;
    $src_x = 0;
    $src_y = 0;
    $imgResize = imagecopyresampled ($dst_image,$src_image,$dst_x ,$dst_y,$src_x,$src_y,$dst_w,$dst_h,$src_w,$src_h);

    if ($imgResize!== FALSE) {
        imagejpeg($dst_image,$image_full);
       // echo "Image resize successfully";
        imagedestroy($src_image);
    }
}

function add_logo_image($target, $wtrmrk_file, $newcopy) {
    $watermark = imagecreatefrompng($wtrmrk_file);
    $margin_Logo = 25;
    imagealphablending($watermark, false);
    imagesavealpha($watermark, true);
    $img = imagecreatefromjpeg($target);
    $img_w = imagesx($img);
    $img_h = imagesy($img);
    $wtrmrk_w = imagesx($watermark);
    $wtrmrk_h = imagesy($watermark);
    $dst_x = ($img_w - $wtrmrk_w - $margin_Logo ); // For centering the watermark on any image
    $dst_y = ($img_h - $wtrmrk_h - $margin_Logo ); // For centering the watermark on any image
    imagecopy($img, $watermark, $dst_x, $dst_y, 0, 0, $wtrmrk_w, $wtrmrk_h);
    imagejpeg($img, $newcopy, 100);
    imagedestroy($img);
    imagedestroy($watermark);
    // echo "Image prosesing successfully";
}

function png2jpg($originalFile, $outputFile, $quality) {
    $input_file = $originalFile;
    $output_file = $outputFile;
    $input = imagecreatefrompng($input_file);
    list($width, $height) = getimagesize($input_file);
    $output = imagecreatetruecolor($width, $height);
    $white = imagecolorallocate($output,  255, 255, 255);
    imagefilledrectangle($output, 0, 0, $width, $height, $white);
    imagecopy($output, $input, 0, 0, 0, 0, $width, $height);
    imagejpeg($output, $output_file);
}


?>
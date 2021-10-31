<?php

$f3 = require(__DIR__.'/vendor/fatfree/lib/base.php');

if(getenv("DEBUG")) {
    $f3->set('DEBUG', getenv("DEBUG"));
}

$f3->set('ROOT', __DIR__);
$f3->set('UI', $f3->get('ROOT')."/templates/");
$f3->set('UPLOADS', $f3->get('ROOT').'/data/');

if(!is_dir($f3->get('UPLOADS'))) {
    mkdir($f3->get('UPLOADS'));
}
$f3->route('GET /',
    function($f3) {
        $f3->set('key', $f3->get('PARAMS.key'));
        echo View::instance()->render('index.html.php');
    }
);
$f3->route('POST /upload',
    function($f3) {
        $files = Web::instance()->receive(function($file,$formFieldName){
            if(Web::instance()->mime($file['tmp_name'], true) != 'application/pdf') {

                return false;
            }
            if($file['size'] > (20 * 1024 * 1024)) { // if bigger than 20 MB

                return false;
            }
            return true;
        }, true, function($fileBaseName, $formFieldName) {

            return substr(hash('sha256', $fileBaseName.uniqid().mt_rand()), 0, 24).".pdf";
	    });

        foreach($files as $file => $valid) {
            if(!$valid) {
                continue;
            }

            $key = str_replace(".pdf", "", basename($file));
        }

        if(!$key) {
            $f3->error(403);
        }

        return $f3->reroute('/'.$key);
    }
);
$f3->route('GET /@key',
    function($f3) {
        $f3->set('key', $f3->get('PARAMS.key'));
        echo View::instance()->render('pdf.html.php');
    }
);
$f3->route('GET /@key/pdf',
    function($f3) {
        $key = $f3->get('PARAMS.key');
        Web::instance()->send($f3->get('UPLOADS').$key.'.pdf');
    }
);
$f3->route('POST /image2svg',
    function($f3) {
        $files = Web::instance()->receive(function($file,$formFieldName){
            if(strpos(Web::instance()->mime($file['tmp_name'], true), 'image/') !== 0) {

                return false;
            }
            if($file['size'] > (20 * 1024 * 1024)) { // if bigger than 20 MB

                return false;
            }

            return true;
        }, true, function($fileBaseName, $formFieldName) {

            return substr(hash('sha256', $fileBaseName.uniqid().mt_rand()), 0, 24).strrchr($fileBaseName, '.');
	    });

        $imageFile = null;
        foreach($files as $file => $valid) {
            if(!$valid) {
                continue;
            }
            $imageFile = $file;
        }

        if(!$imageFile) {
            $f3->error(403);
        }

        shell_exec(sprintf("convert -background white -flatten %s %s", $imageFile, $imageFile.".bmp"));
        shell_exec(sprintf("mkbitmap -x -f 8 %s -o %s", $imageFile.".bmp", $imageFile.".bpm"));
        shell_exec(sprintf("potrace --svg %s -o %s", $imageFile.".bpm", $imageFile.".svg"));

        header('Content-Type: image/svg+xml');
        echo file_get_contents($imageFile.".svg");

        if($f3->get('DEBUG')) {
            return;
        }
        array_map('unlink', glob($imageFile."*"));
    }
);
$f3->route('POST /@key/save',
    function($f3) {
        $key = $f3->get('PARAMS.key');
        $svgData = $_POST['svg'];

        $svgFiles = "";
        foreach($svgData as $index => $svgItem) {
            $svgFile = $f3->get('UPLOADS').$key.'_'.$index.'.svg';
            file_put_contents($svgFile, $svgItem);
            $svgFiles .= $svgFile . " ";
        }

        shell_exec(sprintf("rsvg-convert -f pdf -o %s %s", $f3->get('UPLOADS').$key.'.svg.pdf', $svgFiles));
        shell_exec(sprintf("pdftk %s multibackground %s output %s", $f3->get('UPLOADS').$key.'.svg.pdf', $f3->get('UPLOADS').$key.'.pdf', $f3->get('UPLOADS').$key.'_signe.pdf'));

        Web::instance()->send($f3->get('UPLOADS').$key.'_signe.pdf');

        if($f3->get('DEBUG')) {
            return;
        }
        array_map('unlink', glob($f3->get('UPLOADS').$key."_*.svg"));
        unlink($f3->get('UPLOADS').$key.'.svg.pdf');
        unlink($f3->get('UPLOADS').$key.'_signe.pdf');
    }
);

return $f3;
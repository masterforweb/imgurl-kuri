<?php

function imgurl_kuri($source, $mode = null, $file = null, $return = true){


    if (isset($_GET['img'])) {
        $source = trim($_GET['img'], '/');
    }

    if($mode == null && $file == null){
        $elements = explode('/', $source);

        if (count($elements) == 3) {
            $source = $elements[0];
            $mode = $elements[1];
            $img = $elements[2];
        }
        else {
            return ['error' => 'error parse file'];
        }
    }
    else {
        $img = str_replace(IMGPREVIEW."/", '', $file);
        $img = $source . '/' . $mode . '/' . $file;
    }


    $sources = set('imgsource');

    if (!isset($sources[$source])) {
        return ['error' => 'not found source'];
    }


    $config = $sources[$source];
    $foriginal = $config['path'].$img;

    if (!file_exists($foriginal)) {
        return ['error' => 'not found source file'];
    }


    unset($config['path']);

    /**
    * thumbnail generator all modes
    */
    if ($mode == 'all') {

        foreach ($config as $skey => $conf) {
            $result_file = SITEPATH.$source.'/'.$skey.'/'.$img;
            $result = thumbcache($foriginal, $conf['width'], $conf['height'], $result_file, $conf['type']);
        }

        return ['status' => 'ok'];

    }



    if (!isset($config[$mode])){

        return ['error' => 'not found mode'];
    }

    $conf = $config[$mode];

    if (defined('IMGFOLDER')) {
        $result_file = IMGFOLDER . $source . '/' . $mode . '/' . $img;
    }
    else {
        $result_file = null;
    }

    $result = thumbcache_manager($foriginal, $conf, $result_file, true);
    $type = mime_content_type($result_file);

    header("Content-Type: $type");
    echo $result;

    return;



}
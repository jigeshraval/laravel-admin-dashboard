<?php 

function pre($array, $exit = true)
{
    echo '<pre>';
    print_r($array);
    echo '</pre>';

    if ($exit) {
        exit();
    }
}

function removeStyle($content)
{
    $output = preg_replace('/(<[^>]+) class=".*?"/i', '$1', $content);
    $output = str_ireplace('<p>&nbsp;</p>', '', $output);
    $output = str_ireplace('<p><br></p>', '', $output);
    $output = str_ireplace('<p></p>', '', $output);
    $output = preg_replace('/<p[^>]*>[\s|&nbsp;]*<\/p>/', '', $output);

    // $output = preg_replace('/style[^>]*/', '', $output);
    // $output = preg_replace('/class[^>]*/', '', $output);
    $output = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $output);
    $output = preg_replace('/(<[^>]+) dir=".*?"/i', '$1', $output);

    return $output;

}

function protectedString($content)
{
    $h = removeStyle($content);
    $h = strip_tags($h, '<a><ul><li><ol><h1><h2><h3><h4><h5><h6><p><div><img><iframe><b><u><i><video><table><thead><tbody><tfooter><tr><th><td><strong>');
    return $h;
}

function t($string)
{
    return $string;
}

function json($status, $message)
{
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'text' => $message
    ]);
    exit();
}
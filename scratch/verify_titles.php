<?php

$html = file_get_contents('http://127.0.0.1:8000/');
if (preg_match('/<title>(.*?)<\/title>/is', $html, $m)) {
    echo "Rendered Homepage Title: " . $m[1] . "\n";
} else {
    echo "No title tag found on homepage!\n";
}

$aboutHtml = file_get_contents('http://127.0.0.1:8000/about');
if (preg_match('/<title>(.*?)<\/title>/is', $aboutHtml, $m)) {
    echo "Rendered About Title: " . $m[1] . "\n";
} else {
    echo "No title tag found on about page!\n";
}

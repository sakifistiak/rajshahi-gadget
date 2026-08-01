<?php
$c = file_get_contents(__DIR__ . '/orig_about.html');
if (preg_match('/<footer.*?<\/footer>/s', $c, $m)) {
    file_put_contents(__DIR__ . '/original_footer.html', $m[0]);
    echo "Extracted footer size: " . strlen($m[0]) . " bytes\n";
} else {
    echo "Footer tag not found\n";
}

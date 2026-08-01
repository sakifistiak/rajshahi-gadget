<?php

$html = file_get_contents('http://127.0.0.1:8000/');
echo "Footer tag count: " . substr_count($html, '<footer') . "\n";
echo "Contains '12 Locations': " . (strpos($html, '12 Locations') !== false ? 'YES' : 'NO') . "\n";
echo "Contains business hours: " . (strpos($html, 'Sat – Thu · 10:00 AM – 9:00 PM') !== false ? 'YES' : 'NO') . "\n";
echo "Contains centered copyright: " . (strpos($html, 'text-center text-xs text-muted-foreground') !== false ? 'YES' : 'NO') . "\n";

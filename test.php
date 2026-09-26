<?php
require __DIR__.'/vendor/autoload.php';
$p = 'test_tinker.webp';
file_put_contents($p, base64_decode('UklGRjIAAABXRUJQVlA4ICYAAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6zbAAA/v56QAAAAA=='));
dump(mime_content_type($p));
try {
    \Spatie\Image\Image::load($p)->format('webp')->quality(75)->save($p);
    dump('Success');
} catch (\Exception $e) {
    dump('Exception: ' . $e->getMessage());
}

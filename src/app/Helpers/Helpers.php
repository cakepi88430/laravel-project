<?php
// /app/Helpers/Helpers.php

// Helper 檔案路徑
$helpers = [
    'WebHelper.php',
];

// 載入 Helper 檔案
foreach ($helpers as $helperFileName) {
    include __DIR__ . '/' .$helperFileName;
}
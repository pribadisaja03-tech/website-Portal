<?php
function filterKataKasar($text) {
    // daftar kata kasar (bisa ditambah sesuai kebutuhan)
    $kata_kasar = ["anjing", "babi", "bangsat", "kontol", "memek", "goblok", "tolol"];

    foreach ($kata_kasar as $kata) {
        $pattern = '/\b' . preg_quote($kata, '/') . '\b/i'; 
        $text = preg_replace($pattern, str_repeat("*", strlen($kata)), $text);
    }

    return $text;
}
?>

<?php
header('Content-Type: application/json');

$texts = [
    "The quick brown fox jumps over the lazy dog.",
    "Pack my box with five dozen liquor jugs.",
    "How razorback-jumping frogs can level six piqued gymnasts!",
    "Crazy Fredrick bought many very exquisite opal jewels.",
    "Sixty zippers were quickly picked from the woven jute bag.",
    "A wizard's job is to vex chumps quickly in fog.",
    "By Jove, my quick study of lexicography won a prize.",
    "Sphinx of black quartz, judge my vow.",
    "The five boxing wizards jump quickly.",
    "Jackdaws love my big sphinx of quartz."
];

// Ensure array is not empty to prevent errors with array_rand
if (empty($texts)) {
    echo json_encode(['error' => 'No texts available']);
    exit;
}

$randomIndex = array_rand($texts);
$selectedText = $texts[$randomIndex];

echo json_encode(['text' => $selectedText]);
?>

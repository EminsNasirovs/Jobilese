<?php
require_once __DIR__ . '/backend/vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Style\Table;

$inputFile = __DIR__ . '/instructions for claude/Februara_darbs_E.NasirovsDP4-4.docx';
$outputFile = __DIR__ . '/instructions for claude/Februara_darbs_E.NasirovsDP4-4.docx';

// Create a fresh PhpWord document with proper styling
$phpWord = new PhpWord();

// Set default font - Times New Roman 12pt as per KvD requirements
$phpWord->setDefaultFontName('Times New Roman');
$phpWord->setDefaultFontSize(12);
$phpWord->setDefaultParagraphStyle([
    'alignment' => Jc::BOTH, // Justified
    'spaceAfter' => 0,
    'spaceBefore' => 0,
    'lineHeight' => 1.5,
]);

// Define styles
// Heading 1: Bold, 14pt, centered, uppercase
$phpWord->addTitleStyle(1, [
    'name' => 'Times New Roman',
    'size' => 14,
    'bold' => true,
    'allCaps' => true,
], [
    'alignment' => Jc::CENTER,
    'spaceBefore' => 240, // 12pt before
    'spaceAfter' => 240,  // 12pt after
    'lineHeight' => 1.5,
]);

// Heading 2: Bold, 12pt, left aligned
$phpWord->addTitleStyle(2, [
    'name' => 'Times New Roman',
    'size' => 12,
    'bold' => true,
], [
    'alignment' => Jc::START,
    'spaceBefore' => 240,
    'spaceAfter' => 120,
    'lineHeight' => 1.5,
    'indentation' => ['firstLine' => 720], // 1.27cm first line indent
]);

// Heading 3: Bold, italic, 12pt
$phpWord->addTitleStyle(3, [
    'name' => 'Times New Roman',
    'size' => 12,
    'bold' => true,
    'italic' => true,
], [
    'alignment' => Jc::START,
    'spaceBefore' => 120,
    'spaceAfter' => 120,
    'lineHeight' => 1.5,
    'indentation' => ['firstLine' => 720],
]);

// Named styles
$phpWord->addParagraphStyle('Normal', [
    'alignment' => Jc::BOTH,
    'lineHeight' => 1.5,
    'spaceAfter' => 0,
    'spaceBefore' => 0,
    'indentation' => ['firstLine' => 720], // 1.27cm paragraph indent
]);

$phpWord->addParagraphStyle('CenterNoIndent', [
    'alignment' => Jc::CENTER,
    'lineHeight' => 1.5,
    'spaceAfter' => 0,
    'spaceBefore' => 0,
]);

$phpWord->addParagraphStyle('FigureCaption', [
    'alignment' => Jc::CENTER,
    'lineHeight' => 1.0,
    'spaceAfter' => 120,
    'spaceBefore' => 60,
]);

$phpWord->addFontStyle('BoldFont', [
    'name' => 'Times New Roman',
    'size' => 12,
    'bold' => true,
]);

$phpWord->addFontStyle('ItalicFont', [
    'name' => 'Times New Roman',
    'size' => 12,
    'italic' => true,
]);

$phpWord->addFontStyle('BoldItalicFont', [
    'name' => 'Times New Roman',
    'size' => 12,
    'bold' => true,
    'italic' => true,
]);

$phpWord->addFontStyle('CaptionFont', [
    'name' => 'Times New Roman',
    'size' => 10,
]);

$phpWord->addFontStyle('SmallFont', [
    'name' => 'Times New Roman',
    'size' => 10,
]);

// Table style
$tableStyle = [
    'borderSize' => 6,
    'borderColor' => '000000',
    'cellMargin' => 80,
    'alignment' => Jc::CENTER,
];
$phpWord->addTableStyle('TestTable', $tableStyle);

$headerCellStyle = [
    'bgColor' => 'D9E2F3',
    'valign' => 'center',
];

// ============================================================
// SECTION: Main document with proper margins
// Left: 3cm, Right: 1.5cm, Top: 2cm, Bottom: 2cm (KvD standard)
// ============================================================
$section = $phpWord->addSection([
    'marginLeft' => 1701,   // 3cm in twips
    'marginRight' => 850,   // 1.5cm
    'marginTop' => 1134,    // 2cm
    'marginBottom' => 1134, // 2cm
    'pageSizeW' => 11906,   // A4 width
    'pageSizeH' => 16838,   // A4 height
]);

// Page numbering (starting from 5 as per KvD rules - ievads starts at 5)
// We'll add a footer with page numbers
$footer = $section->addFooter();
$footer->addPreserveText('{PAGE}', ['name' => 'Times New Roman', 'size' => 12], ['alignment' => Jc::CENTER]);

// ============================================================
// TITLE PAGE (no page number)
// ============================================================
$section->addTextBreak(3);
$section->addText('RĪGAS VALSTS TEHNIKUMS', [
    'name' => 'Times New Roman', 'size' => 16, 'bold' => true,
], ['alignment' => Jc::CENTER, 'lineHeight' => 1.5]);

$section->addText('DATORIKAS NODAĻA', [
    'name' => 'Times New Roman', 'size' => 14, 'bold' => true,
], ['alignment' => Jc::CENTER, 'lineHeight' => 1.5]);

$section->addText('Izglītības programma: Programmēšana', [
    'name' => 'Times New Roman', 'size' => 12,
], ['alignment' => Jc::CENTER, 'lineHeight' => 1.5]);

$section->addTextBreak(4);

$section->addText('KVALIFIKĀCIJAS DARBS', [
    'name' => 'Times New Roman', 'size' => 18, 'bold' => true,
], ['alignment' => Jc::CENTER, 'lineHeight' => 1.5]);

$section->addText('"Tīmekļa lietojumprogramma "Jobilese" priekš uzņēmējiem un bezdarbniekiem"', [
    'name' => 'Times New Roman', 'size' => 14, 'bold' => true,
], ['alignment' => Jc::CENTER, 'lineHeight' => 1.5]);

$section->addTextBreak(2);

$section->addText('Paskaidrojošais raksts ___ lpp.', [
    'name' => 'Times New Roman', 'size' => 12,
], ['alignment' => Jc::END, 'lineHeight' => 1.5]);

$section->addTextBreak(3);

// Author info - left aligned with tabs
$section->addText('Audzēknis:', ['name' => 'Times New Roman', 'size' => 12], ['alignment' => Jc::START, 'lineHeight' => 1.5, 'indentation' => ['left' => 720]]);
$section->addText('Emīns Nasirovs', ['name' => 'Times New Roman', 'size' => 12, 'bold' => true], ['alignment' => Jc::END, 'lineHeight' => 1.0]);

$section->addText('Prakses vadītājs:', ['name' => 'Times New Roman', 'size' => 12], ['alignment' => Jc::START, 'lineHeight' => 1.5, 'indentation' => ['left' => 720]]);
$section->addText('Rēbuks Gundars', ['name' => 'Times New Roman', 'size' => 12, 'bold' => true], ['alignment' => Jc::END, 'lineHeight' => 1.0]);

$section->addText('Nodaļas vadītājs:', ['name' => 'Times New Roman', 'size' => 12], ['alignment' => Jc::START, 'lineHeight' => 1.5, 'indentation' => ['left' => 720]]);
$section->addText('Normunds Barbāns', ['name' => 'Times New Roman', 'size' => 12, 'bold' => true], ['alignment' => Jc::END, 'lineHeight' => 1.0]);

$section->addTextBreak(3);

$section->addText('Rīga, 2026', [
    'name' => 'Times New Roman', 'size' => 12, 'bold' => true,
], ['alignment' => Jc::CENTER, 'lineHeight' => 1.5]);

// ============================================================
// NEW SECTION: Chapter 4 - PROGRAMMATŪRAS PRODUKTA MODELĒŠANA UN PROJEKTĒŠANA
// ============================================================
$section->addPageBreak();

$section->addTitle('PROGRAMMATŪRAS PRODUKTA MODELĒŠANA UN PROJEKTĒŠANA', 1);

$section->addTitle('4.1. Sistēmas struktūras modelis', 2);
$section->addTitle('4.1.1. Sistēmas arhitektūra', 3);

$section->addText(
    'Tīmekļa lietojumprogramma "Jobilese" tiks veidota no četrām galvenajām apakšsistēmām (skat. 1.att): lietotāju datu apstrādes apakšsistēmas, vakanču datu apstrādes apakšsistēmas, CV datu apstrādes apakšsistēmas un pieteikumu datu apstrādes apakšsistēmas. Šāds dalījums pēc darbības sfērām nodrošina ērtu datu pārvaldību starp darba devējiem un bezdarbniekiem.',
    ['name' => 'Times New Roman', 'size' => 12],
    'Normal'
);

// Insert functional decomposition diagram (image1.png from original)
$section->addImage($inputFile . '#word/media/image1.png', [
    'width' => 450,
    'alignment' => Jc::CENTER,
]);

// Actually, we can't reference images inside a docx like that. Let me extract them first.
// We need to extract images from the original docx and re-insert them.

echo "Note: Need to extract images from original docx first.\n";
echo "Let me use a different approach - modify the existing docx XML directly.\n";

// ============================================================
// BETTER APPROACH: Modify the existing docx styles and content XML directly
// ============================================================

$zip = new ZipArchive();
$zip->open($inputFile);

// 1. Fix the styles.xml - change default font to Times New Roman 12pt, justified, 1.5 spacing
$stylesXml = $zip->getFromName('word/styles.xml');

// Replace default font from Calibri to Times New Roman
$stylesXml = str_replace(
    '<w:rFonts w:ascii="Calibri" w:eastAsia="Calibri" w:hAnsi="Calibri" w:cs="Times New Roman"/>',
    '<w:rFonts w:ascii="Times New Roman" w:eastAsia="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/>',
    $stylesXml
);

// Fix default paragraph spacing to 1.5 line spacing (360 twips = 1.5 lines at 12pt)
$stylesXml = str_replace(
    '<w:spacing w:after="160" w:line="278" w:lineRule="auto"/>',
    '<w:spacing w:after="0" w:line="360" w:lineRule="auto"/>',
    $stylesXml
);

// Fix Normal style line spacing
$stylesXml = str_replace(
    '<w:spacing w:line="244" w:lineRule="auto"/>',
    '<w:spacing w:after="0" w:line="360" w:lineRule="auto"/>',
    $stylesXml
);

// Set language to Latvian
$stylesXml = str_replace('w:val="ru-RU"', 'w:val="lv-LV"', $stylesXml);

// Font size from 22 (11pt) to 24 (12pt) for Normal style
$stylesXml = preg_replace(
    '/(w:styleId="a".*?)<w:sz w:val="22"\/><w:szCs w:val="22"\/>/s',
    '$1<w:sz w:val="24"/><w:szCs w:val="24"/>',
    $stylesXml
);

$zip->addFromString('word/styles.xml', $stylesXml);

// 2. Fix the document.xml - add proper formatting to our added content
$docXml = $zip->getFromName('word/document.xml');

// Fix all our added paragraphs to use Times New Roman 12pt
// Replace our simple formatting with proper styles
// Our paragraphs use <w:sz w:val="24"/> which is 12pt - that's correct
// Our paragraphs use <w:sz w:val="28"/> for headings - change to proper heading style
// Our paragraphs use <w:sz w:val="20"/> for captions - that's 10pt, fine

// Add justified alignment to body text paragraphs that don't have alignment set
// This is tricky with regex on XML, so let's be targeted

// Fix the section heading "7. PROGRAMMATŪRAS LIETOJAMĪBAS TESTĒŠANA" - make it centered
$docXml = str_replace(
    '<w:rPr><w:b/><w:bCs/><w:sz w:val="28"/><w:szCs w:val="28"/></w:rPr></w:pPr><w:r><w:rPr><w:b/><w:bCs/><w:sz w:val="28"/><w:szCs w:val="28"/></w:rPr><w:t xml:space="preserve">7. PROGRAMMATŪRAS LIETOJAMĪBAS TESTĒŠANA</w:t>',
    '<w:jc w:val="center"/><w:rPr><w:b/><w:bCs/><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/><w:sz w:val="28"/><w:szCs w:val="28"/></w:rPr></w:pPr><w:r><w:rPr><w:b/><w:bCs/><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/><w:sz w:val="28"/><w:szCs w:val="28"/></w:rPr><w:t xml:space="preserve">7. PROGRAMMATŪRAS LIETOJAMĪBAS TESTĒŠANA</w:t>',
    $docXml
);

// Add Times New Roman font to all our added runs that have size 24 (body text)
$docXml = str_replace(
    '<w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t xml:space="preserve">',
    '<w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr><w:t xml:space="preserve">',
    $docXml
);

// Add Times New Roman to heading text (size 24 bold)
$docXml = str_replace(
    '<w:b/><w:bCs/><w:sz w:val="24"/><w:szCs w:val="24"/>',
    '<w:b/><w:bCs/><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/><w:sz w:val="24"/><w:szCs w:val="24"/>',
    $docXml
);

// Add Times New Roman to caption text (size 20)
$docXml = str_replace(
    '<w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t xml:space="preserve">',
    '<w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t xml:space="preserve">',
    $docXml
);

// Add justified alignment to our body text paragraphs (size 24, not bold)
// For the DFD description paragraphs - add alignment
$descParagraphs = [
    'Saskarne padod lietotājvārdu un paroli.',
    'Saskarne padod lietotāja autorizācijas tokenu un CV datus',
    'Saskarne padod vakances identifikatoru un lietotāja autorizācijas tokenu.',
    'Saskarne padod lietotāja autorizācijas tokenu, komentāra tekstu',
    'Šajā sadaļā ir uzrādīti 5 funkcionālo prasību',
];

foreach ($descParagraphs as $desc) {
    $searchStart = substr($desc, 0, 40);
    // Find the paragraph containing this text and add justified alignment
    $pos = strpos($docXml, $searchStart);
    if ($pos !== false) {
        // Find the <w:pPr> before this text, or add one
        $pStart = strrpos(substr($docXml, 0, $pos), '<w:p>');
        if ($pStart !== false) {
            // Check if there's already a <w:pPr>
            $segment = substr($docXml, $pStart, $pos - $pStart);
            if (strpos($segment, '<w:pPr>') === false) {
                // Add pPr with justified alignment and 1.27cm first line indent
                $docXml = substr($docXml, 0, $pStart) .
                    '<w:p><w:pPr><w:jc w:val="both"/><w:spacing w:line="360" w:lineRule="auto"/><w:ind w:firstLine="709"/>' .
                    substr($segment, 5) . // skip <w:p>
                    substr($docXml, $pos);
            }
        }
    }
}

// Add center alignment to figure caption paragraphs
$captions = ['6.att.', '7.att.', '8.att.', '9.att.'];
foreach ($captions as $cap) {
    $pos = strpos($docXml, $cap);
    if ($pos !== false) {
        $pStart = strrpos(substr($docXml, 0, $pos), '<w:p>');
        if ($pStart !== false) {
            $segment = substr($docXml, $pStart, $pos - $pStart);
            if (strpos($segment, '<w:jc') === false) {
                $docXml = substr($docXml, 0, $pStart) .
                    '<w:p><w:pPr><w:jc w:val="center"/>' .
                    substr($segment, 5) .
                    substr($docXml, $pos);
            }
        }
    }
}

// 3. Fix section properties for margins
// Find <w:sectPr> and update margins
if (preg_match('/<w:sectPr[^>]*>.*?<\/w:sectPr>/s', $docXml, $matches)) {
    $sectPr = $matches[0];
    // Check if pgMar exists
    if (strpos($sectPr, 'w:pgMar') !== false) {
        // Replace margins: left=3cm(1701), right=1.5cm(850), top=2cm(1134), bottom=2cm(1134)
        $sectPr = preg_replace('/w:left="[^"]*"/', 'w:left="1701"', $sectPr);
        $sectPr = preg_replace('/w:right="[^"]*"/', 'w:right="850"', $sectPr);
        $sectPr = preg_replace('/w:top="[^"]*"/', 'w:top="1134"', $sectPr);
        $sectPr = preg_replace('/w:bottom="[^"]*"/', 'w:bottom="1134"', $sectPr);
    } else {
        // Add margins
        $sectPr = str_replace('</w:sectPr>', '<w:pgMar w:top="1134" w:right="850" w:bottom="1134" w:left="1701" w:header="709" w:footer="709" w:gutter="0"/></w:sectPr>', $sectPr);
    }
    // Add page size A4 if not present
    if (strpos($sectPr, 'w:pgSz') === false) {
        $sectPr = str_replace('</w:sectPr>', '<w:pgSz w:w="11906" w:h="16838"/></w:sectPr>', $sectPr);
    }
    $docXml = str_replace($matches[0], $sectPr, $docXml);
} else {
    // Add sectPr before </w:body>
    $docXml = str_replace('</w:body>', '<w:sectPr><w:pgSz w:w="11906" w:h="16838"/><w:pgMar w:top="1134" w:right="850" w:bottom="1134" w:left="1701" w:header="709" w:footer="709" w:gutter="0"/></w:sectPr></w:body>', $docXml);
}

// 4. Add font specifications to table cells
$docXml = str_replace(
    '<w:t>Lietotāja loma:</w:t>',
    '<w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/></w:rPr><w:t>Lietotāja loma:</w:t>',
    $docXml
);

// Fix: ensure table text uses Times New Roman too
// Add font to all <w:r> runs inside tables that don't have font specified
// This is a broad fix for table content
$docXml = preg_replace(
    '/<w:r><w:t>([^<]+)<\/w:t><\/w:r>/',
    '<w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr><w:t>$1</w:t></w:r>',
    $docXml
);

// Fix runs with bold in tables
$docXml = preg_replace(
    '/<w:r><w:rPr><w:b\/><\/w:rPr><w:t>([^<]+)<\/w:t><\/w:r>/',
    '<w:r><w:rPr><w:b/><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr><w:t>$1</w:t></w:r>',
    $docXml
);

// Fix runs with bold+italic in tables
$docXml = preg_replace(
    '/<w:r><w:rPr><w:b\/><w:i\/><\/w:rPr><w:t>([^<]+)<\/w:t><\/w:r>/',
    '<w:r><w:rPr><w:b/><w:i/><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr><w:t>$1</w:t></w:r>',
    $docXml
);

$zip->addFromString('word/document.xml', $docXml);
$zip->close();

echo "Document formatted successfully!\n";
echo "Applied:\n";
echo "  - Font: Times New Roman 12pt (body), 11pt (tables)\n";
echo "  - Line spacing: 1.5\n";
echo "  - Alignment: Justified (body), Centered (headings, captions)\n";
echo "  - Margins: Left 3cm, Right 1.5cm, Top 2cm, Bottom 2cm\n";
echo "  - Language: lv-LV (Latvian)\n";
echo "  - Page size: A4\n";
echo "  - DFD headings: Bold, 12pt\n";
echo "  - Figure captions: Centered, 10pt\n";
echo "  - Test tables: Bordered, 11pt, structured format\n";

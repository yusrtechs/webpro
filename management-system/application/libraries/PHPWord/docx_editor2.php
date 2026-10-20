<?php
require_once 'bootstrap.php';
use biladina\hijridatetime\HijriDateTime;

$timezone = 'Asia/Riyadh';
date_default_timezone_set($timezone);

$hijri = new HijriDateTime();


$day=date('l');
if($day=='Sunday'){
    $day='الأحد';
}else if($day=='Monday'){
    $day='الإثنين';
}else if($day=='Tuesday'){
    $day='الثلاثاء';
}else if($day=='Wednesday'){
    $day='الأربعاء';
}else if($day=='Thursday'){
    $day='الخميس';
}else if($day=='Friday'){
    $day='الجمعة';
}else if($day=='Saturday'){
    $day='السبت';
}

$input        = 'عقد_شركة_اركان.docx';
$output       = 'عقد_شركة_اركان_نسخة.docx';
$replacements = [
    'placeholder1' => $day,
    'placeholder2' => $hijri->date("Y/m/j", time(), 'ar'),
    'placeholder3' => date('Y/m/d'),
    'placeholder4' => 'Khamis Mushait'.' \n '.'Khamis Mushait',
    'placeholder5' => 'Khamis Mushait',
    'placeholder6' => 'ابراهيم جيلان',
];

$successful = searchReplaceWordDocument($input, $output, $replacements);

echo $successful ? "Successfully created $output" : 'Failed!';

/**
 * Edit a Word 2007 and newer .docx file.
 * Utilizes the zip extension http://php.net/manual/en/book.zip.php
 * to access the document.xml file that holds the markup language for
 * contents and formatting of a Word document.
 *
 * In this example we're replacing some token strings.  Using
 * the Office Open XML standard ( https://en.wikipedia.org/wiki/Office_Open_XML )
 * you can add, modify, or remove content or structure of the document.
 *
 * @param string $input
 * @param string $output
 * @param array  $replacements
 *
 * @return bool
 */
function searchReplaceWordDocument(string $input, string $output, array $replacements): bool
{
    if (copy($input, $output)) {

        // Create the Object.
        $zip = new ZipArchive();

        // Open the Microsoft Word .docx file as if it were a zip file... because it is.
        if ($zip->open($output, ZipArchive::CREATE) !== true) {
            return false;
        }

        // Fetch the document.xml file from the word subdirectory in the archive.
        $xml = $zip->getFromName('word/document.xml');

        // Replace
        $xml = str_replace(array_keys($replacements), array_values($replacements), $xml);

        // Write back to the document and close the object
        if (false === $zip->addFromString('word/document.xml', $xml)) {
            return false;
        }
        $zip->close();

        return true;
    }

    return false;
}
<?php
/**
 * Edit a Word 2007 and newer .docx file.
 * Utilizes the zip extension http://php.net/manual/en/book.zip.php
 * to access the document.xml file that holds the markup language for
 * contents and formatting of a Word document.
 *
 * In this example we're replacing some token strings.  Using
 * the Office Open XML standard ( https://en.wikipedia.org/wiki/Office_Open_XML )
 * you can add, modify, or remove content or structure of the document.
 */

// Create the Object.
$zip = new ZipArchive();

// Use same filename for "save" and different filename for "save as".
$inputFilename = 'testfile.docx';
$outputFilename = 'testfile.docx';

// Some new strings to put in the document.
$token1 = 'Vince Lombardi';
$token2 = 'Your mother smelt of elderberries, and your father was a hamster!';

// Open the Microsoft Word .docx file as if it were a zip file... because it is.
if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
    echo "Cannot open $filename :( "; die;
}

// Fetch the document.xml file from the word subdirectory in the archive.
$xml = $zip->getFromName('word/document.xml');

// Replace the tokens.
$xml = str_replace('{TOKEN1}', $token1, $xml);
$xml = str_replace('{TOKEN2}', $token2, $xml);

// Write back to the document and close the object
if ($zip->addFromString('word/document.xml', $xml)) { echo 'File written!'; }
else { echo 'File not written.  Go back and add write permissions to this folder!l'; }

$zip->close();
?>
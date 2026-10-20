<?php
defined('BASEPATH') or exit('No direct script access allowed');
//namespace Salla\ZATCA;


//use Mpdf\GenerateQrCode;


use Salla\GenerateQrCode;
use Salla\Tags\InvoiceDate;
use Salla\Tags\InvoiceTaxAmount;
use Salla\Tags\InvoiceTotalAmount;
use Salla\Tags\Seller;
use Salla\Tags\TaxNumber;
use Salla\Tags\InvoiceXMLHash;
use Salla\Tags\InvoiceECDSASignature;
use Salla\Tags\InvoiceECDSAPublicKey;


use chillerlan\QRCode\QRCode;
//use InvalidArgumentException;

class CpeQrCode
{
    public function __construct()
    {
    }

    
    public function generate($seller,$taxNumber,$invoiceDate,$invoiceTotalAmount,$invoiceTaxAmount)
    {
		
		//$GenerateQrCode                   = new GenerateQrCode();
          $generatedString = GenerateQrCode::fromArray([
    new Seller($seller), // seller name        
    new TaxNumber($taxNumber), // seller tax number
    new InvoiceDate($invoiceDate), // invoice date as Zulu ISO8601 @see https://en.wikipedia.org/wiki/ISO_8601
    new InvoiceTotalAmount($invoiceTotalAmount), // invoice total amount
    new InvoiceTaxAmount($invoiceTaxAmount) // invoice tax amount
    // TODO :: Support others tags
])->toBase64();

/*
$generatedString = GenerateQrCode::fromArray([
    new Seller('Arractech software solution LTD.'), // seller name        
    new TaxNumber('310122393500003'), // seller tax number
    new InvoiceDate('2022-06-26T15:35:00Z'), // invoice date as Zulu ISO8601 @see https://en.wikipedia.org/wiki/ISO_8601
    new InvoiceTotalAmount('1150.00'), // invoice total amount
    new InvoiceTaxAmount('150.00') // invoice tax amount
    // TODO :: Support others tags
])->toBase64();
*/

return $generatedString; 
    }

    public function generateSign($seller,$taxNumber,$invoiceDate,$invoiceTotalAmount,$invoiceTaxAmount,$InvoiceXMLHash,$InvoiceECDSASignature,$InvoiceECDSAPublicKey)
    {
		
		//$GenerateQrCode                   = new GenerateQrCode();
          $generatedString = GenerateQrCode::fromArray([
    new Seller($seller), // seller name        
    new TaxNumber($taxNumber), // seller tax number
    new InvoiceDate($invoiceDate), // invoice date as Zulu ISO8601 @see https://en.wikipedia.org/wiki/ISO_8601
    new InvoiceTotalAmount($invoiceTotalAmount), // invoice total amount
    new InvoiceTaxAmount($invoiceTaxAmount), // invoice tax amount
    new InvoiceXMLHash($InvoiceXMLHash), // invoice tax amount
    new InvoiceXMLSignature(base64_decode($InvoiceECDSASignature)), // invoice tax amount
    new InvoiceECDSAPublicKey($InvoiceECDSAPublicKey), // invoice tax amount
    new InvoiceECDSASignature($cert_signature) // invoice tax amount
    // TODO :: Support others tags
])->toBase64();

/*
$generatedString = GenerateQrCode::fromArray([
    new Seller('Arractech software solution LTD.'), // seller name        
    new TaxNumber('310122393500003'), // seller tax number
    new InvoiceDate('2022-06-26T15:35:00Z'), // invoice date as Zulu ISO8601 @see https://en.wikipedia.org/wiki/ISO_8601
    new InvoiceTotalAmount('1150.00'), // invoice total amount
    new InvoiceTaxAmount('150.00') // invoice tax amount
    // TODO :: Support others tags
])->toBase64();
*/

return $generatedString; 
    }

    public function __get($var)
    {
        return get_instance()->$var;
    }
}

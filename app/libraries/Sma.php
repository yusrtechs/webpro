<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
 *  ==============================================================================
 *  Author    : Mian Saleem
 *  Email     : saleem@tecdiary.com
 *  For       : Stock Manager Advance
 *  Web       : http://tecdiary.com
 *  ==============================================================================
 */
use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;
use Zakat\Country;

use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;


use PHPUnit\Framework\TestCase;


class Sma
{
    public function __construct()
    {
    }

    public function __get($var)
    {
        return get_instance()->$var;
    }

    public function actionPermissions($action = null, $module = null)
    {
        if ($this->Owner || $this->Admin) {
            if ($this->Admin && stripos($action, 'delete') !== false) {
                return false;
            }
            return true;
        } elseif ($this->Customer || $this->Supplier) {
            return false;
        }
        if (!$module) {
            $module = $this->m;
        }
        if (!$action) {
            $action = $this->v;
        }
        //$gp = $this->site->checkPermissions();
        if ($this->GP[$module . '-' . $action] == 1) {
            return true;
        }
        return false;
    }

    public function analyze_term($term)
    {
        $spos = strpos($term, $this->Settings->barcode_separator);
        if ($spos !== false) {
            $st        = explode($this->Settings->barcode_separator, $term);
            $sr        = trim($st[0]);
            $option_id = trim($st[1]);
        } else {
            $sr        = $term;
            $option_id = false;
        }
        return ['term' => $sr, 'option_id' => $option_id];
    }



    public function barcode($text = null, $bcs = 'code128', $height = 74, $stext = 1, $get_be = false, $re = false)
    {
        $drawText = ($stext != 1) ? false : true;
        $this->load->library('tec_barcode', '', 'bc');
        return $this->bc->generate($text, $bcs, $height, $drawText, $get_be, $re);
    }
   public function barcodeZATCA($seller,$taxNumber,$invoiceDate,$invoiceTotalAmount,$invoiceTaxAmount,$InvoiceXMLHash = null,$InvoiceECDSASignature = null,$InvoiceECDSAPublicKey = null,$cert_signature = null)
    {
		$datetime = new DateTime($invoiceDate);

	$invoiceDate= $datetime->format('Y-m-d\TH:i:s\Z');


       // $drawText = ($stext != 1) ? false : true;
        $this->load->library('CpeQrCode', '', 'bc');
		if($InvoiceXMLHash && $InvoiceECDSASignature && $InvoiceECDSAPublicKey && $cert_signature)
			return $this->bc->generateSign($seller,$taxNumber,$invoiceDate,$invoiceTotalAmount,$invoiceTaxAmount,$InvoiceXMLHash,$InvoiceECDSASignature,$InvoiceECDSAPublicKey,$cert_signature);
		else
			return $this->bc->generate($seller,$taxNumber,$invoiceDate,$invoiceTotalAmount,$invoiceTaxAmount);



    }

	public function xmlInvoice($seller,$taxNumber,$invoiceDate,$invoiceTotalAmount,$invoiceTaxAmount,$invoiceID,$items,$warehouse,$customer,$invoiceUUID,$QR,$invPaid,$paymentMeansCode,$InvoiceTypeCode,$sale_id,$note,$currencyRate = 1)
    {
		$datetime = new DateTime($invoiceDate);

	$invoiceDate= $datetime->format('Y-m-d\TH:i:s\Z');


		$dbName=str_replace("cpesoft_","",$this->db->database);

       // $drawText = ($stext != 1) ? false : true;
        $this->load->library('XMLInvoice', '', 'ublxml');
		return $this->ublxml->testIfXMLIsValid($seller,$taxNumber,$invoiceDate,$invoiceTotalAmount,$invoiceTaxAmount,$invoiceID,$items,$warehouse,$customer,$invoiceUUID,$QR,$invPaid,$dbName,$paymentMeansCode,$InvoiceTypeCode,$sale_id,$note,$currencyRate);
		//return $this->ublxml->testIfXMLIsValid2();


    }

    public function xmlInvoiceSign($sale_id,$xml_file_path,$seller,$taxNumber,$invoiceDate,$invoiceTotalAmount,$invoiceTaxAmount,$zatcaConfig = [],$InvoiceTypeNumber,$currencyRate = 1)
    {
		$datetime = new DateTime($invoiceDate);

	$invoiceDate= $datetime->format('Y-m-d\TH:i:s');


       $orginal='./assets/xml/'.$xml_file_path.'.xml';
       $merged='./assets/xml/'.$xml_file_path.'merged.xml';
       //$merged='./assets/xml/merged.xml';
       $child='./assets/xml/stamp.xml';


	   $DOMChild = new DOMDocument;
    $DOMChild->load($child);
    $node = $DOMChild->documentElement;

    $DOMParent = new DOMDocument;
    $DOMParent->formatOutput = true;
    $DOMParent->load($orginal);

    $node = $DOMParent->importNode($node, true);

    if ($tag !== null) {
        //$tag = $DOMParent->getElementsByTagName($tag)->item(0);
        //$tag->appendChild($node);
		//$tag->insertBefore($node, true);

	} else {
		$tag = $DOMParent->getElementsByTagName('Invoice')->item(0);

        $DOMParent->documentElement->insertBefore($node,$tag->firstChild);
    }

    $DOMParent->save('./assets/xml/'.$xml_file_path.'merged.xml');



	   $doc = new DOMDocument();

    // Open XML
    if ($doc->load($orginal))
    {
      //echo "<p><b>3. XML loaded.</b></p><br/>";
      // Create a new Security object
      $objDSig = new XMLSecurityDSig();

      // Use the c14n exclusive canonicalization
      $objDSig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
      // Sign using SHA-256
      $objDSig->addReference(
          $doc,
          XMLSecurityDSig::SHA256,
          array('http://www.w3.org/2000/09/xmldsig#enveloped-signature')
      );
$stamp_hash =trim($objDSig->sigNode->nodeValue);
//print_r($stamp_hash);

      // Create a new (private) Security key
      $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, array('type'=>'private'));

      /*if (!$pfx = file_get_contents('./assets/keys/ec-secp256k1-priv-key.pem'))
      {
            //echo "Error: The private key could not be opened!\n";
            return false;
        }
      else
      {*/
        /*
        If key has a passphrase, set it using
        $objKey->passphrase = '<passphrase>';
        */
        // Load the private key
		$prueba = $objKey->loadKey($zatcaConfig->PrivateKey, FALSE);
		//$this->print_arrays($zatcaConfig->PrivateKey,file_get_contents('./assets/keys/ec-secp256k1-priv-key.pem'));
		//$this->print_arrays($zatcaConfig->PrivateKey);

        //$prueba = $objKey->loadKey('./assets/keys/ec-secp256k1-priv-key.pem', TRUE);
       // echo "<p><b>4.Private key has been loaded!</b></p><br/>";
        // Sign the XML file
        $objDSig->sign($objKey);

      //}
	  $stamp_public_key= str_replace($stamp_hash,"",trim($objDSig->sigNode->nodeValue));

//echo strlen(trim($stamp_hash));
//echo trim($stamp_hash) ;
//print_r( $stamp_public_key );
//print_r( $objDSig->sigNode->nodeValue );

//$pfx = file_get_contents('./assets/keys/cert.pem');
$pfx = $zatcaConfig->cert;
      if (!$pfx)
      {
            //echo "Error: The public key could not be opened!\n";
            return false;
        }
      else
      {
        //$objDSig->add509Cert(file_get_contents('./assets/keys/cert.pem'));
        $objDSig->add509Cert($zatcaConfig->cert);
		//$this->print_arrays($objDSig);
       // echo "<p><b>5. Public key has been loaded.</b></p><br/>";
        // Append the signature to the XML
        $objDSig->appendSignature($doc->documentElement);

		$stamp_sign= str_replace($stamp_hash,"",trim($objDSig->sigNode->nodeValue));
		$stamp_sign= str_replace($stamp_public_key,"",$stamp_sign);
		$stamp_sign= trim($stamp_sign);
		//print_r( $stamp_sign );

		$signing_time= date('Y-m-d\TH:i:s', time());





		//echo base64_encode(hash('sha256', $pfx));

 // Updated ISO8601


        // Save the signed XML
        //$doc->save('./assets/xml/signed1.xml');




		//$stamp_public_key="MEQCIHFFsNv5nNEeTh/yxxwRTKGLcfi/CHEjDPvwP9hqGRGrAiBHzeIqctZ/8KGQS7HLBUGELZjnwF+vLxZXonzSIlMK9g==";


	$XML=file_get_contents($merged);

	$XML=str_replace('ext:UBLExtensions xmlns:sig="urn:oasis:names:specification:ubl:schema:xsd:CommonSignatureComponents-2" xmlns:sac="urn:oasis:names:specification:ubl:schema:xsd:SignatureAggregateComponents-2"','ext:UBLExtensions',$XML);
	$XML=str_replace('ext:UBLExtensions xmlns:sbc="urn:oasis:names:specification:ubl:schema:xsd:SignatureBasicComponents-2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:xades="http://uri.etsi.org/01903/v1.3.2#"','ext:UBLExtensions',$XML);
	$XML=str_replace('stamp_hash',$stamp_hash,$XML);
	//$stamp_public_key= trim("MEUCICn7TLKk1k0Oxwd5TSZYKPNRMlL917+UOb3G399Bqj1VAiEA2iYaadaGDQnGoXmbI4wPW41bbbXYfY9262awA2hWDZU=");

	$XML=str_replace('stamp_sign',$stamp_public_key,$XML);

	$XML=str_replace('cert_sign',$stamp_sign,$XML);
	//$signing_time="2022-09-10T22:19:53Z";
	$XML=str_replace('signing_time',$signing_time,$XML);
	$XML=str_replace('CertDigestText',$zatcaConfig->CertDigest,$XML);
	$XML=str_replace('X509IssuerNameText',$zatcaConfig->X509IssuerName,$XML);
	$XML=str_replace('X509SerialNumberText',$zatcaConfig->X509SerialNumber,$XML);
/*
	//$cert = file_get_contents('./assets/keys/ec-secp256k1-priv-key.pem');
	$cert = file_get_contents('./assets/keys/ec-secp256k1-priv-key.pem');

$prkey = openssl_pkey_get_private($cert);



while(1) {

    openssl_sign($DigestValue, $signature, $prkey, OPENSSL_ALGO_SHA256);

    $rlen = ord(substr($signature,3,1));

    $slen = ord(substr($signature,5+$rlen,1));

    if ($slen != 32 || $rlen != 32) {
        // try other signature if length is not 32 for both parts
        continue;
    }
    $r = substr($signature,4,$rlen);
    $s = substr($signature,6+$rlen,$slen);

    $signature = $r.$s;

    break;
}
//echo $prkey."\n";
openssl_free_key($prkey);
//echo $signature."\n";

$signature = bin2hex($signature);
//echo $signature."\n";

//$signature = base64_encode($signature);
$signature = base64_encode(hash('sha256',$signature));
*/
//$DigestValue='<xades:SignedProperties xmlns:xades="http://uri.etsi.org/01903/v1.3.2#" Id="xadesSignedProperties"><xades:SignedSignatureProperties><xades:SigningTime>'.trim($signing_time).'</xades:SigningTime><xades:SigningCertificate><xades:Cert><xades:CertDigest><ds:DigestMethod xmlns:ds="http://www.w3.org/2000/09/xmldsig#" Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/><ds:DigestValue xmlns:ds="http://www.w3.org/2000/09/xmldsig#">'.$zatcaConfig->CertDigest.'</ds:DigestValue></xades:CertDigest><xades:IssuerSerial><ds:X509IssuerName xmlns:ds="http://www.w3.org/2000/09/xmldsig#">'.$zatcaConfig->X509IssuerName.'</ds:X509IssuerName><ds:X509SerialNumber xmlns:ds="http://www.w3.org/2000/09/xmldsig#">'.$zatcaConfig->X509SerialNumber.'</ds:X509SerialNumber></xades:IssuerSerial></xades:Cert></xades:SigningCertificate></xades:SignedSignatureProperties></xades:SignedProperties>';





$DigestValue='<xades:SignedProperties xmlns:xades="http://uri.etsi.org/01903/v1.3.2#" Id="xadesSignedProperties">
                                    <xades:SignedSignatureProperties>
                                        <xades:SigningTime>'.trim($signing_time).'</xades:SigningTime>
                                        <xades:SigningCertificate>
                                            <xades:Cert>
                                                <xades:CertDigest>
                                                    <ds:DigestMethod xmlns:ds="http://www.w3.org/2000/09/xmldsig#" Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/>
                                                    <ds:DigestValue xmlns:ds="http://www.w3.org/2000/09/xmldsig#">'.trim($zatcaConfig->CertDigest).'</ds:DigestValue>
                                                </xades:CertDigest>
                                                <xades:IssuerSerial>
                                                    <ds:X509IssuerName xmlns:ds="http://www.w3.org/2000/09/xmldsig#">'.trim($zatcaConfig->X509IssuerName).'</ds:X509IssuerName>
                                                    <ds:X509SerialNumber xmlns:ds="http://www.w3.org/2000/09/xmldsig#">'.trim($zatcaConfig->X509SerialNumber).'</ds:X509SerialNumber>
                                                </xades:IssuerSerial>
                                            </xades:Cert>
                                        </xades:SigningCertificate>
                                    </xades:SignedSignatureProperties>
                                </xades:SignedProperties>';
$signature = base64_encode(hash('sha256',$DigestValue));
//$signature ="MjNlMDdlOTNlNTAyMjJlMjRjNWZiY2RkOWRhNWE0ZGI1ODgxZGRkZjJiZWExZjA2ZTNhY2RlYzFjMTFlZjE5MA==";
//$signature ="NDg0OGQwZWRmMGMwNTRkYTgwMGU3NzFlZTYwMTI1YjhhN2M4NjQxNjVkMTIzYTY3Y2ZlZjg3NjljYTViNjdmNw==";

//
	$XML=str_replace('stamp_public_key',$signature,$XML);

		$dom = new DOMDocument('1.0', 'UTF-8');

        $dom->loadXML($XML);

    $dom->save('./assets/xml/'.$xml_file_path.'signed.xml');



//echo extractData (, "Inmuebles");
//	$cert_public_key=base64_decode("MFYwEAYHKoZIzj0CAQYFK4EEAAoDQgAEDHJYgAK95mmWKGprrq1RdXOv9o9JjwbIYGZQaBKzStOlngt/D6gmxPQix2bui7wKT33a+JJgL6pc7R7hK6JH5Q==");
//	$cert_signature=base64_decode("MEUCIDmUW8dufE5DglN5/AYRd1rmAxRqu3KHfxl5TqwlFQFGAiEAsQUCrRwKw6iOB90QhWjQca6QW1FZ6aRs6IHZ9Lwk6pM=");

	$cert_public_key=base64_decode($zatcaConfig->cert_public_key);
	$cert_signature=base64_decode($zatcaConfig->cert_signature);




//$pkey_details = openssl_pkey_get_details(openssl_pkey_get_public(file_get_contents("./assets/keys/cert.pem")));
	//$cert_public_key=trim($pkey_details["key"]);
	//$cert_public_key=$pkey_details["ec"]["x"].$pkey_details["ec"]["y"];

	//$cert_details = openssl_pkey_get_details(openssl_pkey_get_private(file_get_contents("./assets/keys/ec-secp256k1-priv-key.pem")));
/*
include('./assets/File/X509.php');

$x509 = new File_X509();

$cert = $x509->loadX509(file_get_contents("./assets/keys/cert.pem"));
$cert_signature=$cert['signature'];
	//$cert_signature=$cert_details["ec"]["x"].$cert_details["ec"]["d"];

	*/
	if($InvoiceTypeNumber==2){
	$dataToEncode = [
    [1, $seller],
    [2, $taxNumber],
    [3, trim($invoiceDate)],
    [4, $invoiceTotalAmount],
    [5, $this->formatDecimal(($invoiceTaxAmount / $currencyRate),2)],
    [6, $stamp_hash],
    [7, $stamp_public_key],
	[8, $cert_public_key],
	[9, $cert_signature]

];
	}else{
	$dataToEncode = [
    [1, $seller],
    [2, $taxNumber],
    [3, trim($invoiceDate)],
    [4, $invoiceTotalAmount],
    [5, $this->formatDecimal(($invoiceTaxAmount / $currencyRate),2)],
    [6, $stamp_hash],
    [7, $stamp_public_key],
	[8, $cert_public_key]

];

	}

//,
 //   [9, $cert_signature],



$__TLVS = '';
    for ($i = 0; $i < count($dataToEncode); $i++) {
        $__tag = $dataToEncode[$i][0];
        $__value = $dataToEncode[$i][1];
        $__length =  strlen($__value);
		$value = (string) $__value;
    $__TLVS .=   pack("H*", sprintf("%02X", $__tag)) .  pack("H*", sprintf("%02X", $__length)) . $value;

     }


$QR_Attach = base64_encode($__TLVS);
//$QR_Attach = str_replace('=','',$QR_Attach);
    if($this->site->getInvoiceDocBySaleID($sale_id)){
    $this->db->update('sale_documents', ['stamp_hash' => $stamp_hash,'stamp_public_key' => $stamp_public_key,'stamp_sign' => $stamp_sign,'signing_time' => $signing_time,'ECDSA_signature' => $signature,'signed' => 1,'synch_hash' => base64_encode(file_get_contents('./assets/xml/'.$xml_file_path.'signedWQR.xml')),'qr' => $QR_Attach], ['sale_id' => $sale_id]);

    }else{
    $this->db->insert('sale_documents', ['sale_id' => $sale_id,'stamp_hash' => $stamp_hash,'stamp_public_key' => $stamp_public_key,'stamp_sign' => $stamp_sign,'signing_time' => $signing_time,'ECDSA_signature' => $signature,'signed' => 1,'synch_hash' => base64_encode(file_get_contents('./assets/xml/'.$xml_file_path.'signedWQR.xml')),'qr' => $QR_Attach]);

    }


$QR='<cac:AdditionalDocumentReference>
        <cbc:ID>QR</cbc:ID>
        <cac:Attachment>
            <cbc:EmbeddedDocumentBinaryObject mimeCode="text/plain">'.trim($QR_Attach).'</cbc:EmbeddedDocumentBinaryObject>
        </cac:Attachment>
</cac:AdditionalDocumentReference><cac:Signature>
      <cbc:ID>urn:oasis:names:specification:ubl:signature:Invoice</cbc:ID>
      <cbc:SignatureMethod>urn:oasis:names:specification:ubl:dsig:enveloped:xades</cbc:SignatureMethod>
</cac:Signature><cac:AccountingSupplierParty>';


		$XML=file_get_contents('./assets/xml/'.$xml_file_path.'signed.xml');



	$XML=str_replace('<cac:AccountingSupplierParty>',$QR,$XML);

		$dom = new DOMDocument('1.0', 'UTF-8');

        $dom->loadXML($XML);

    $dom->save('./assets/xml/'.$xml_file_path.'signedWQR.xml');
    $this->db->update('sale_documents', ['synch_hash' => base64_encode(file_get_contents('./assets/xml/'.$xml_file_path.'signedWQR.xml'))], ['sale_id' => $sale_id]);

	}

    }
    else
    {
     // echo "XML could not be opened!";
    }
	return $QR_Attach;

    }

    public function base64url_decode($data)
    {
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
    }

    public function base64url_encode($data, $pad = null)
    {
        $data = str_replace(['+', '/'], ['-', '_'], base64_encode($data));
        if (!$pad) {
            $data = rtrim($data, '=');
        }
        return $data;
    }

    public function checkPermissions($action = null, $js = null, $module = null)
    {
        if (!$this->actionPermissions($action, $module)) {
            $this->session->set_flashdata('error', lang('access_denied'));
            if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            }
            redirect($_SERVER['HTTP_REFERER'] ? $_SERVER["HTTP_REFERER"] :'welcome');
        }
    }

    public function clear_tags($str)
    {
        return htmlentities(
            strip_tags(
                $str,
                '<span><div><a><br><p><b><i><u><img><blockquote><small><ul><ol><li><hr><big><pre><code><strong><em><table><tr><td><th><tbody><thead><tfoot><h3><h4><h5><h6>'
            ),
            ENT_QUOTES | ENT_XHTML | ENT_HTML5,
            'UTF-8'
        );
    }

    public function convertMoney($amount, $format = true, $symbol = true)
    {
        if ($this->Settings->selected_currency != $this->Settings->default_currency) {
            $amount = $this->formatDecimal(($amount * $this->selected_currency->rate), 4);
        }
        return ($format ? $this->formatMoney($amount, $this->selected_currency->symbol) : $amount);
    }

    public function decode_html($str)
    {
        return html_entity_decode($str, ENT_QUOTES | ENT_XHTML | ENT_HTML5, 'UTF-8');
    }

    public function fld($ldate)
    {
        if ($ldate) {
            $date     = explode(' ', $ldate);
            $jsd      = $this->dateFormats['js_sdate'];
            $inv_date = $date[0];
            $time     = $date[1];
            if ($jsd == 'dd-mm-yyyy' || $jsd == 'dd/mm/yyyy' || $jsd == 'dd.mm.yyyy') {
                $date = substr($inv_date, -4) . '-' . substr($inv_date, 3, 2) . '-' . substr($inv_date, 0, 2) . ' ' . $time;
            } elseif ($jsd == 'mm-dd-yyyy' || $jsd == 'mm/dd/yyyy' || $jsd == 'mm.dd.yyyy') {
                $date = substr($inv_date, -4) . '-' . substr($inv_date, 0, 2) . '-' . substr($inv_date, 3, 2) . ' ' . $time;
            } else {
                if ($jsd == 'yyyy-mm-dd' || $jsd == 'yyyy/mm/dd' || $jsd == 'yyyy.mm.dd') {
                    $date = substr($inv_date, 0, 4) . '-' . substr($inv_date, 5, 2) . '-' . substr($inv_date, -2) . ' ' . $time;
                }else{
                    $date = $inv_date. ' ' . $time;
                }
            }
            return $date;
        }
        return '0000-00-00 00:00:00';
    }

    public function formatDecimal($number, $decimals = null)
    {
        if (!is_numeric($number)) {
            return null;
        }
        if (!$decimals && $decimals !== 0) {
            $decimals = $this->Settings->decimals;
        }
        return number_format($number, $decimals, '.', '');
    }

    public function formatMoney($number, $symbol = false, $display_symbol = null)
    {
        if ($symbol !== 'none') {
            $symbol = $symbol ? $symbol : $this->Settings->symbol;
        } else {
            $symbol = null;
        }
        if($this->site->getCurrencyByCode($symbol)->symbol_logo){


        if ($this->Settings->user_language == 'arabic') {
            $this->Settings->display_symbol=2;
            }else{
            $this->Settings->display_symbol=1;
        }

        if ($display_symbol && $display_symbol !== 0) {
            $this->Settings->display_symbol=$display_symbol;
        }

        $symbol='<img class="currency-symbol-logo" src ="./assets/images/Saudi_Riyal_Symbol-1.png" style="width:16px;padding-right:4px;"/>';
        }

        if ($this->Settings->sac) {
            return ((($this->Settings->display_symbol == 1 || $symbol) && $this->Settings->display_symbol != 2) ? $symbol : '') .
            $this->formatSAC($this->formatDecimal($number)) .
            ($this->Settings->display_symbol == 2 ? $symbol : '');
        }
        $decimals = $this->Settings->decimals;
        $ts       = $this->Settings->thousands_sep == '0' ? ' ' : $this->Settings->thousands_sep;
        $ds       = $this->Settings->decimals_sep;
        return ((($this->Settings->display_symbol == 1 || $symbol && $number != 0) && $this->Settings->display_symbol != 2) ? $symbol : '') .
        number_format($number, $decimals, $ds, $ts) .
        ($this->Settings->display_symbol == 2 && $number != 0 ? $symbol : '');
    }

    public function formatNumber($number, $decimals = null)
    {
        if (!$decimals) {
            $decimals = $this->Settings->decimals;
        }
        if ($this->Settings->sac) {
            return $this->formatSAC($this->formatDecimal($number, $decimals));
        }
        $ts = $this->Settings->thousands_sep == '0' ? ' ' : $this->Settings->thousands_sep;
        $ds = $this->Settings->decimals_sep;
        return number_format($number, $decimals, $ds, $ts);
    }

    public function formatQuantity($number, $decimals = null)
    {
        if (!$decimals) {
            $decimals = $this->Settings->qty_decimals;
        }
        if ($this->Settings->sac) {
            return $this->formatSAC($this->formatDecimal($number, $decimals));
        }
        $ts = $this->Settings->thousands_sep == '0' ? ' ' : $this->Settings->thousands_sep;
        $ds = $this->Settings->decimals_sep;
        return number_format($number, $decimals, $ds, $ts);
    }

    public function formatQuantityDecimal($number, $decimals = null)
    {
        if (!$decimals) {
            $decimals = $this->Settings->qty_decimals;
        }
        return number_format($number, $decimals, '.', '');
    }

    public function formatSAC($num)
    {
        $pos = strpos((string) $num, '.');
        if ($pos === false) {
            $decimalpart = '00';
        } else {
            $decimalpart = substr($num, $pos + 1, 2);
            $num         = substr($num, 0, $pos);
        }

        if (strlen($num) > 3 & strlen($num) <= 12) {
            $last3digits         = substr($num, -3);
            $numexceptlastdigits = substr($num, 0, -3);
            $formatted           = $this->makecomma($numexceptlastdigits);
            $stringtoreturn      = $formatted . ',' . $last3digits . '.' . $decimalpart;
        } elseif (strlen($num) <= 3) {
            $stringtoreturn = $num . '.' . $decimalpart;
        } elseif (strlen($num) > 12) {
            $stringtoreturn = number_format($num, 2);
        }

        if (substr($stringtoreturn, 0, 2) == '-,') {
            $stringtoreturn = '-' . substr($stringtoreturn, 2);
        }

        return $stringtoreturn;
    }

    public function fsd($inv_date)
    {
        if ($inv_date) {
            $jsd = $this->dateFormats['js_sdate'];
            if ($jsd == 'dd-mm-yyyy' || $jsd == 'dd/mm/yyyy' || $jsd == 'dd.mm.yyyy') {
                $date = substr($inv_date, -4) . '-' . substr($inv_date, 3, 2) . '-' . substr($inv_date, 0, 2);
            } elseif ($jsd == 'mm-dd-yyyy' || $jsd == 'mm/dd/yyyy' || $jsd == 'mm.dd.yyyy') {
                $date = substr($inv_date, -4) . '-' . substr($inv_date, 0, 2) . '-' . substr($inv_date, 3, 2);
            } else {
                if ($jsd == 'yyyy-mm-dd' || $jsd == 'yyyy/mm/dd' || $jsd == 'yyyy.mm.dd') {
                    $date = substr($inv_date, 0, 4) . '-' . substr($inv_date, 5, 2) . '-' . substr($inv_date, -2);
                }else{
                    $date = $inv_date;
                }
            }
            return $date;
        }
        return '0000-00-00';
    }

    public function generate_pdf($content, $name = 'download.pdf', $output_type = null, $footer = null, $margin_bottom = null, $header = null, $margin_top = null, $orientation = 'P',$xml_file_path = null)
    {
        if ($this->Settings->pdf_lib == 'dompdf') {
            $this->load->library('tec_dompdf', '', 'pdf');
        } else {
            $this->load->library('tec_mpdf', '', 'pdf');
            //$this->load->library('cpe_pdf', '', 'pdf');
        }
        return $this->pdf->generate($content, $name, $output_type, $footer, $margin_bottom, $header, $margin_top, $orientation,$xml_file_path = null);
    }

    public function getCardBalance($number)
    {
        if ($card = $this->site->getGiftCardByNO($number)) {
            return $card->balance;
        }
        return 0;
    }

    public function hrld($ldate)
    {
        if ($ldate) {
            return date($this->dateFormats['php_ldate'], strtotime($ldate));
        }
        return '0000-00-00 00:00:00';
    }

    public function hrsd($sdate)
    {
        if ($sdate) {
            return date($this->dateFormats['php_sdate'], strtotime($sdate));
        }
        return '0000-00-00';
    }

    public function in_group($check_group, $id = false)
    {
        if (!$this->logged_in()) {
            return false;
        }
        $id || $id = $this->session->userdata('user_id');
        $group     = $this->site->getUserGroup($id);
        if ($group->name === $check_group) {
            return true;
        }
        return false;
    }

    public function isPromo($product)
    {
        if (is_array($product)) {
            $product = json_decode(json_encode($product), false);
        }
        $today = date('Y-m-d');
        return $product->promotion && $product->start_date <= $today && $product->end_date >= $today && $product->promo_price;
    }

    public function log_payment($type, $msg, $val = null)
    {
        $this->load->library('logs');
        return (bool) $this->logs->write($type, $msg, $val);
    }

    public function logged_in()
    {
        return (bool) $this->session->userdata('identity');
    }

    public function makecomma($input)
    {
        if (strlen($input) <= 2) {
            return $input;
        }
        $length          = substr($input, 0, strlen($input) - 2);
        $formatted_input = $this->makecomma($length) . ',' . substr($input, -2);
        return $formatted_input;
    }

    public function md($page = FALSE)
    {
        die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . ($page ? site_url($page) : (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome')) . "'; }, 10);</script>");
    }

    public function paid_opts($paid_by = null, $purchase = false, $empty_opt = false)
    {
        $opts = '';
        if ($empty_opt) {
            $opts .= '<option value="">' . lang('select') . '</option>';
        }
        $opts .= '
        <option value="cash"' . ($paid_by && $paid_by == 'cash' ? ' selected="selected"' : '') . '>' . lang('cash') . '</option>
        <!--<option value="span"' . ($paid_by && $paid_by == 'span' ? ' selected="selected"' : '') . '>' . lang('span') . '</option>
        <option value="gift_card"' . ($paid_by && $paid_by == 'gift_card' ? ' selected="selected"' : '') . '>' . lang('gift_card') . '</option>-->
        <option value="CC"' . ($paid_by && $paid_by == 'CC' ? ' selected="selected"' : '') . '>' . lang('CC') . '</option>
        <option value="Cheque"' . ($paid_by && $paid_by == 'Cheque' ? ' selected="selected"' : '') . '>' . lang('cheque') . '</option>
        <option value="bank_transfer"' . ($paid_by && $paid_by == 'bank_transfer' ? ' selected="selected"' : '') . '>' . lang('Bank_transfers') . '</option>
        <!--<option value="other"' . ($paid_by && $paid_by == 'other' ? ' selected="selected"' : '') . '>' . lang('other') . '</option>-->';
        if (!$purchase) {
            $opts .= '<option value="deposit"' . ($paid_by && $paid_by == 'deposit' ? ' selected="selected"' : '') . '>' . lang('deposit') . '</option>';
            $opts .= '<option value="gift_card"' . ($paid_by && $paid_by == 'gift_card' ? ' selected="selected"' : '') . '>' . lang('gift_card') . '</option>';
        }
        return $opts;
    }

    public function paid_opts_accounts($paid_by = null, $purchase = false, $empty_opt = false)
    {
        $opts = '';
        if ($empty_opt) {
            $opts .= '<option value="">' . lang('select') . '</option>';
        }
        $opts .= '
        <option value="cash"' . ($paid_by && $paid_by == 'cash' ? ' selected="selected"' : '') . '>' . lang('cash') . '</option>
        <!--<option value="span"' . ($paid_by && $paid_by == 'span' ? ' selected="selected"' : '') . '>' . lang('span') . '</option>-->
        <option value="CC"' . ($paid_by && $paid_by == 'CC' ? ' selected="selected"' : '') . '>' . lang('CC') . '</option>
        <option value="Cheque"' . ($paid_by && $paid_by == 'Cheque' ? ' selected="selected"' : '') . '>' . lang('cheque') . '</option>
        <option value="bank_transfer"' . ($paid_by && $paid_by == 'bank_transfer' ? ' selected="selected"' : '') . '>' . lang('Bank_transfers') . '</option>
        <!--<option value="other"' . ($paid_by && $paid_by == 'other' ? ' selected="selected"' : '') . '>' . lang('other') . '</option>-->';
        if (!$purchase) {
            $opts .= '<option value="gift_card"' . ($paid_by && $paid_by == 'gift_card' ? ' selected="selected"' : '') . '>' . lang('gift_card') . '</option>';
            $opts .= '<option value="deposit"' . ($paid_by && $paid_by == 'deposit' ? ' selected="selected"' : '') . '>' . lang('deposit') . '</option>';
        }
        return $opts;
    }

    public function paid_opts_transfer($paid_by = null, $purchase = false, $empty_opt = false)
    {
        $opts = '';
        if ($empty_opt) {
            $opts .= '<option value="">' . lang('select') . '</option>';
        }
        $opts .= '
        <!--<option value="span"' . ($paid_by && $paid_by == 'span' ? ' selected="selected"' : '') . '>' . lang('span') . '</option>-->
        <option value="CC"' . ($paid_by && $paid_by == 'CC' ? ' selected="selected"' : '') . '>' . lang('CC') . '</option>
        <option value="Cheque"' . ($paid_by && $paid_by == 'Cheque' ? ' selected="selected"' : '') . '>' . lang('cheque') . '</option>
        <!--<option value="other"' . ($paid_by && $paid_by == 'other' ? ' selected="selected"' : '') . '>' . lang('other') . '</option>-->';
        if (!$purchase) {
        //    $opts .= '<option value="deposit"' . ($paid_by && $paid_by == 'deposit' ? ' selected="selected"' : '') . '>' . lang('deposit') . '</option>';
        }
        return $opts;
    }

    public function print_arrays()
    {
        $args = func_get_args();
        echo '<pre>';
        foreach ($args as $arg) {
            print_r($arg);
        }
        echo '</pre>';
        die();
    }

    public function qrcode($type = 'text', $text = 'http://companypro.com', $size = 2, $level = 'H', $sq = null)
    {
        $file_name = 'assets/uploads/qrcode' . $this->session->userdata('user_id') . ($sq ? $sq : '') . ($this->Settings->barcode_img ? '.png' : '.svg');
        if ($type == 'link') {
            $text = urldecode($text);
        }
        $this->load->library('tec_qrcode', '', 'qr');
        $config = ['data' => $text, 'size' => $size, 'level' => $level, 'savename' => $file_name];
        $this->qr->generate($config);
        $imagedata = file_get_contents($file_name);
        return "<img src='data:image/png;base64," . base64_encode($imagedata) . "' alt='{$text}' class='qrimg' />";
    }

    public function roundMoney($num, $nearest = 0.05)
    {
        return round($num * (1 / $nearest)) * $nearest;
    }

    public function roundNumber($number, $toref = null)
    {
        switch ($toref) {
            case 1:
                $rn = round($number * 20) / 20;
                break;
            case 2:
                $rn = round($number * 2) / 2;
                break;
            case 3:
                $rn = round($number);
                break;
            case 4:
                $rn = ceil($number);
                break;
            default:
                $rn = $number;
        }
        return $rn;
    }

    public function send_email($to, $subject, $message, $from = null, $from_name = null, $attachment = null, $cc = null, $bcc = null)
    {
        if (DEMO) {
            $this->session->set_flashdata('error', 'Emails are disabled in demo.');
            return false;
        }
        list($user, $domain) = explode('@', $to);
        if ($domain != 'tecdiary.com' || DEMO) {
            $result = false;
            $this->load->library('tec_mail');
            try {
                $result = $this->tec_mail->send_mail($to, $subject, $message, $from, $from_name, $attachment, $cc, $bcc);
            } catch (\Exception $e) {
                $this->session->set_flashdata('error', 'Mail Error: ' . $e->getMessage());
                throw new \Exception($e->getMessage());
            }
            return $result;
        }
        return false;
    }

    public function send_json($data)
    {
        header('Content-Type: application/json');
        die(json_encode($data));
        exit;
    }

    public function setCustomerGroupPrice($price, $customer_group)
    {
        if (!isset($customer_group) || empty($customer_group)) {
            return $price;
        }
        return $this->formatDecimal($price + (($price * $customer_group->percent) / 100));
    }

    public function slug($title, $type = null, $r = 1)
    {
        $this->load->helper('text');
        $slug       = url_title(convert_accented_characters($title), '-', true);
        $check_slug = $this->site->checkSlug($slug, $type);
        if (!empty($check_slug)) {
            $slug = $slug . $r;
            $r++;
            $this->slug($slug, $type, $r);
        }
        return $slug;
    }

    public function unset_data($ud)
    {
        if ($this->session->userdata($ud)) {
            $this->session->unset_userdata($ud);
            return true;
        }
        return false;
    }

    public function unzip($source, $destination = './')
    {
        // @chmod($destination, 0777);
        $zip = new ZipArchive;
        if ($zip->open(str_replace('//', '/', $source)) === true) {
            $zip->extractTo($destination);
            $zip->close();
        }
        // @chmod($destination,0755);

        return true;
    }

    public function update_award_points($total, $customer, $user = null, $scope = null)
    {
        if (!empty($this->Settings->each_spent) && $total >= $this->Settings->each_spent) {
            $company      = $this->site->getCompanyByID($customer);
            $points       = floor(($total / $this->Settings->each_spent) * $this->Settings->ca_point);
            $total_points = $scope ? $company->award_points - $points : $company->award_points + $points;
            $reverse_points       = !empty($this->Settings->reverse_each_spent) ? floor(($total_points / $this->Settings->reverse_ca_point) * $this->Settings->reverse_each_spent) : null;
            $this->db->update('companies', ['award_points' => $total_points], ['id' => $customer]);
            $WhatsappSettings=$this->site->getWhatsappSettings();
            if ($WhatsappSettings->award_points==1) {
                $this->load->library('sms');
                $currency_name=$this->site->getCurrencyByCode($this->Settings->default_currency)->name;
                $text=  $this->sms->invAwardPointsText($customer, $points, $total_points,$reverse_points,$this->Settings->site_name,$currency_name);

                $this->load->library('whatsapp');
                $this->whatsapp->send($company->phone, $text);
            }
        }
        if ($user && !empty($this->Settings->each_sale) && !$this->Customer && $total >= $this->Settings->each_sale) {
            $staff        = $this->site->getUser($user);
            $points       = floor(($total / $this->Settings->each_sale) * $this->Settings->sa_point);
            $total_points = $scope ? $staff->award_points - $points : $staff->award_points + $points;
            $this->db->update('users', ['award_points' => $total_points], ['id' => $user]);
        }
        return true;
    }

    public function view_rights($check_id, $js = null)
    {
        if (!$this->Owner && !$this->Admin) {
            if ($check_id != $this->session->userdata('user_id') && !$this->session->userdata('view_right')) {
                $this->session->set_flashdata('warning', $this->data['access_denied']);
                if ($js) {
                    die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome') . "'; }, 10);</script>");
                }
                redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
            }
        }
        return true;
    }

    public function zip($source = null, $destination = './', $output_name = 'sma', $limit = 5000)
    {
        if (!$destination || trim($destination) == '') {
            $destination = './';
        }

        $this->_rglobRead($source, $input);
        $maxinput  = count($input);
        $splitinto = (($maxinput / $limit) > round($maxinput / $limit, 0)) ? round($maxinput / $limit, 0) + 1 : round($maxinput / $limit, 0);

        for ($i = 0; $i < $splitinto; $i++) {
            $this->_zip(array_slice($input, ($i * $limit), $limit, true), $i, $destination, $output_name);
        }

        unset($input);
    }

    private function _rglobRead($source, &$array = [])
    {
        if (!$source || trim($source) == '') {
            $source = '.';
        }
        foreach ((array) glob($source . '/*/') as $key => $value) {
            $this->_rglobRead(str_replace('//', '/', $value), $array);
        }
        $hidden_files = glob($source . '.*') and $htaccess = preg_grep('/\.htaccess$/', $hidden_files);
        $files        = array_merge(glob($source . '*.*'), $htaccess);
        foreach ($files as $key => $value) {
            $array[] = str_replace('//', '/', $value);
        }
    }

    private function _zip($array, $part, $destination, $output_name = 'sma')
    {
        $zip = new ZipArchive;
        @mkdir($destination, 0777, true);

        if ($zip->open(str_replace('//', '/', "{$destination}/{$output_name}" . ($part ? '_p' . $part : '') . '.zip'), ZipArchive::CREATE)) {
            foreach ((array) $array as $key => $value) {
                $zip->addFile($value, str_replace(['../', './'], null, $value));
            }
            $zip->close();
        }
    }






		public function AmountInWordsAr($EQE_CR,$CURR_CODE,$CURR_name,$FR){
		$ftext="";

if($CURR_CODE=="SR"){
$CURR_ANAME="ريال سعودي";
$FRACTION="هللة";
}
if($CURR_CODE=="USD"){
$CURR_ANAME="دولار أمريكي";
$FRACTION="سنت";
}

if($CURR_ANAME==""){
	$CURR_ANAME==$CURR_name;
	$FRACTION==$FR;
}


//$EQE_CR=$EQE_CR.".00";
$pos = strrpos($EQE_CR, '.');


if($pos==""){
$EQE_CR=$EQE_CR.".00";
$pos = strrpos($EQE_CR, '.');
}


$cent= substr("$EQE_CR", $pos+1);
$cent= substr("$cent",0,2);
$EQE_CR= substr("$EQE_CR",0, $pos);
$len=strlen($EQE_CR);


if($len<=9 && $len>6){
$s= substr("$EQE_CR",0, $len-6);

$ftext=$ftext.  $this->getFRACTION1($s);
$ftext=$ftext.  " مليون ";

//echo " و ";
if($len==7)
$s= substr("$EQE_CR",$len-6, $len-4);
else if($len==8)
$s= substr("$EQE_CR",$len-6, $len-5);
else if($len==9)
$s= substr("$EQE_CR",$len-6, $len-6);
if($s!="000")
$ftext=$ftext. " و ";

$ftext=$ftext.  $this->getFRACTION1($s);
if($s!="000")
$ftext=$ftext.  " الف ";
$s= substr("$EQE_CR",$len-3, $len-1);
if($s!="000")
$ftext=$ftext.  " و ";

//echo " و ";

//$s= substr("$EQE_CR",$len-3, $len-1);

$ftext=$ftext.  $this->getFRACTION1($s);

}else if($len<=6 && $len>3){



$s= substr("$EQE_CR",0, $len-3);
$ftext=$ftext.  $this->getFRACTION1($s);
$ftext=$ftext.  " الف ";
$s= substr("$EQE_CR",$len-3, $len-1);
if($s!="000")
$ftext=$ftext.  " و ";
//$s= substr("$EQE_CR",$len-3, $len-1);

$ftext=$ftext.  $this->getFRACTION1($s);

}else if($len<=3){



$ftext=$ftext.  $this->getFRACTION1($EQE_CR);

}

$pos = strrpos($EQE_CR, '.');
$pos1= strlen($pos."p");

if($pos1==1)
$ftext=$ftext.  " ".$CURR_ANAME;
else{
$ftext=$ftext.  " ".$FRACTION;
}
if($cent!="00"){
$ftext=$ftext.  " و ";

$ftext=$ftext.  $this->getFRACTION1($cent);
$ftext=$ftext.  " ".$FRACTION;
}
$ftext=$ftext.  " فقط لا غير ";

$ftext=str_replace("واحد الف","الف",$ftext);
return $ftext;
}




public function getFRACTION1($EQE_CR){
$ftext1="";
$s3="";
$s1="";
$s2="";
$len=strlen($EQE_CR);
for($i=0;$i<$len;$i++){
if($i!=0)
$s= substr("$EQE_CR",$i, $i);
else
$s= substr("$EQE_CR",$i, $i+1);


if($s==0)
$s="صفر";
else if($s==1){
if($len==3 && $i==0)
$s3="مائة";
else
if(($len==3 && $i==2) || ($len==2 && $i==1)|| ($len==1 && $i==0))
$s1="واحد";
else
if(($len==3 && $i==1)|| ($len==2 && $i==0))
$s2="عشر";

}else if($s==2){
if($len==3 && $i==0)
$s3="مائتيين";
else
if(($len==3 && $i==2) || ($len==2 && $i==1)|| ($len==1 && $i==0))
$s1="اثنان";
else
if(($len==3 && $i==1)|| ($len==2 && $i==0))
$s2="عشرون";

}else if($s==3){
if($len==3 && $i==0)
$s3="ثلاثمائة";
else
if(($len==3 && $i==2) || ($len==2 && $i==1)|| ($len==1 && $i==0))
$s1="ثلاثة";
else
if(($len==3 && $i==1)|| ($len==2 && $i==0))
$s2="ثلاثون";

}else if($s==4){
if($len==3 && $i==0)
$s3="اربعمائة";
else
if(($len==3 && $i==2) || ($len==2 && $i==1)|| ($len==1 && $i==0))
$s1="اربعة";
else
if(($len==3 && $i==1)|| ($len==2 && $i==0))
$s2="اربعون";

}else if($s==5){
if($len==3 && $i==0)
$s3="خمسمائة";
else
if(($len==3 && $i==2) || ($len==2 && $i==1)|| ($len==1 && $i==0))
$s1="خمسة";
else
if(($len==3 && $i==1)|| ($len==2 && $i==0))
$s2="خمسون";

}else if($s==6){
if($len==3 && $i==0)
$s3="ستمائة";
else
if(($len==3 && $i==2)|| ($len==2 && $i==1)|| ($len==1 && $i==0))
$s1="ستة";
else
if(($len==3 && $i==1)|| ($len==2 && $i==0))
$s2="ستون";

}else if($s==7){
if($len==3 && $i==0)
$s3="سبعمائة";
else
if(($len==3 && $i==2) || ($len==2 && $i==1)|| ($len==1 && $i==0))
$s1="سبعة";
else
if(($len==3 && $i==1)|| ($len==2 && $i==0))
$s2="سبعون";

}else if($s==8){
if($len==3 && $i==0)
$s3="ثمانمائة";
else
if(($len==3 && $i==2)|| ($len==2 && $i==1)|| ($len==1 && $i==0))
$s1="ثمانية";
else
if(($len==3 && $i==1)|| ($len==2 && $i==0))
$s2="ثمانون";

}else if($s==9){
if($len==3 && $i==0)
$s3="تسعمائة";
else
if(($len==3 && $i==2)|| ($len==2 && $i==1)|| ($len==1 && $i==0))
$s1="تسعة";
else
if(($len==3 && $i==1)|| ($len==2 && $i==0))
$s2="تسعون";

}


}

//echo $len;

if($len==3){
if($s3!="")
if($s1=="" && $s2=="")
$ftext1=$ftext1."". $s3." ";
else
$ftext1=$ftext1."". $s3." و";
if($s1!=""){
if($s2=="")
$ftext1=$ftext1."". $s1." ";
else{









if($s1!="")
if($s2=="عشر"){
if($s1=="واحد")
$s1="احدى";
if($s1=="اثنان")
$s1="اثنا";

//$ftext1=$ftext1."". $s1." ".$s2;


}




}

if($s2!="")
if($s1=="")
$ftext1=$ftext1."".  $s2;
else
$ftext1=$ftext1."".  $s1." و".$s2;


}else if($s2!="")
if($s1=="")
$ftext1=$ftext1."".  $s2;
else
$ftext1=$ftext1."".  $s1." و".$s2;


}else if($len==2){
//echo $s2;
if($s1!="")
if($s2=="عشر"){
if($s1=="واحد")
$s1="احدى";
if($s1=="اثنان")
$s1="اثنا";

$ftext1=$ftext1."".  $s1." ".$s2;
}else{

if($s2=="")
$ftext1=$ftext1."".  $s1." ";
else
$ftext1=$ftext1."".  $s1." و";

$ftext1=$ftext1."".  $s2;
}
else
$ftext1=$ftext1."".  $s2;


//echo $s1." و".$s2;
}else
$ftext1=$ftext1."".  $s1;


return $ftext1;
}


function AmountInWordsEn(float $number,$CURR_CODE,$CURR_name,$FR)
{


			$ftext="";

if($CURR_CODE=="SR"){
$CURR="Saudi Riyals";
$FRACTIONName="Halala";
}
if($CURR_CODE=="USD"){
$CURR="US dollars";
$FRACTIONName="cents";
}

if($CURR==""){
	$CURR==$CURR_name;
	$FRACTIONName==$FR;
}

    list($integer, $fraction) = explode(".", (string) $number);

    $output = "";

    if ($integer{0} == "-")
    {
        $output = "negative ";
        $integer    = ltrim($integer, "-");
    }
    else if ($integer{0} == "+")
    {
        $output = "positive ";
        $integer    = ltrim($integer, "+");
    }

    if ($integer{0} == "0")
    {
        $output .= "zero";
    }
    else
    {
        $integer = str_pad($integer, 36, "0", STR_PAD_LEFT);
        $group   = rtrim(chunk_split($integer, 3, " "), " ");
        $groups  = explode(" ", $group);

        $groups2 = array();
        foreach ($groups as $g)
        {
            $groups2[] = $this->convertThreeDigit($g{0}, $g{1}, $g{2});
        }

        for ($z = 0; $z < count($groups2); $z++)
        {
            if ($groups2[$z] != "")
            {
                $output .= $groups2[$z] . $this->convertGroup(11 - $z) . (
                        $z < 11
                        && !array_search('', array_slice($groups2, $z + 1, -1))
                        && $groups2[11] != ''
                        && $groups[11]{0} == '0'
                            ? " and "
                            : ", "
                    );
            }
        }

        $output = rtrim($output, ", ");
    }

	$output .= " " . $CURR;

    if ($fraction > 0)
    {
        $output .= " point";
        for ($i = 0; $i < strlen($fraction); $i++)
        {
            $output .= " " . $this->convertDigit($fraction{$i});
        }
			$output .= " " . $FRACTIONName;

    }

    return $output;
}

function convertGroup($index)
{
    switch ($index)
    {
        case 11:
            return " Decillion";
        case 10:
            return " Nonillion";
        case 9:
            return " Octillion";
        case 8:
            return " Septillion";
        case 7:
            return " Sextillion";
        case 6:
            return " Quintrillion";
        case 5:
            return " Quadrillion";
        case 4:
            return " Trillion";
        case 3:
            return " Billion";
        case 2:
            return " Million";
        case 1:
            return " Thousand";
        case 0:
            return "";
    }
}

function convertThreeDigit($digit1, $digit2, $digit3)
{
    $buffer = "";

    if ($digit1 == "0" && $digit2 == "0" && $digit3 == "0")
    {
        return "";
    }

    if ($digit1 != "0")
    {
        $buffer .= $this->convertDigit($digit1) . " Hundred";
        if ($digit2 != "0" || $digit3 != "0")
        {
            $buffer .= " and ";
        }
    }

    if ($digit2 != "0")
    {
        $buffer .= $this->convertTwoDigit($digit2, $digit3);
    }
    else if ($digit3 != "0")
    {
        $buffer .= $this->convertDigit($digit3);
    }

    return $buffer;
}

function convertTwoDigit($digit1, $digit2)
{
    if ($digit2 == "0")
    {
        switch ($digit1)
        {
            case "1":
                return "Ten";
            case "2":
                return "Twenty";
            case "3":
                return "Thirty";
            case "4":
                return "Forty";
            case "5":
                return "Fifty";
            case "6":
                return "Sixty";
            case "7":
                return "Seventy";
            case "8":
                return "Eighty";
            case "9":
                return "Ninety";
        }
    } else if ($digit1 == "1")
    {
        switch ($digit2)
        {
            case "1":
                return "Eleven";
            case "2":
                return "Twelve";
            case "3":
                return "Thirteen";
            case "4":
                return "Fourteen";
            case "5":
                return "Fifteen";
            case "6":
                return "Sixteen";
            case "7":
                return "Seventeen";
            case "8":
                return "Eighteen";
            case "9":
                return "Nineteen";
        }
    } else
    {
        $temp = $this->convertDigit($digit2);
        switch ($digit1)
        {
            case "2":
                return "Twenty-$temp";
            case "3":
                return "Thirty-$temp";
            case "4":
                return "Forty-$temp";
            case "5":
                return "Fifty-$temp";
            case "6":
                return "Sixty-$temp";
            case "7":
                return "Seventy-$temp";
            case "8":
                return "Eighty-$temp";
            case "9":
                return "Ninety-$temp";
        }
    }
}

function convertDigit($digit)
{
    switch ($digit)
    {
        case "0":
            return "Zero";
        case "1":
            return "One";
        case "2":
            return "Two";
        case "3":
            return "Three";
        case "4":
            return "Four";
        case "5":
            return "Five";
        case "6":
            return "Six";
        case "7":
            return "Seven";
        case "8":
            return "Eight";
        case "9":
            return "Nine";
    }
}

	/*function AmountInWordsEn(float $amount,$CURR_CODE,$CURR_name,$FR){
		$ftext="";

if($CURR_CODE=="SR"){
$CURR="Saudi Riyals";
$FRACTION="Halala";
}
if($CURR_CODE=="USD"){
$CURR="US dollars";
$FRACTION="cents";
}

if($CURR==""){
	$CURR==$CURR_name;
	$FRACTION==$FR;
}


   $amount_after_decimal = round($amount - ($num = floor($amount)), 2) * 100;
   // Check if there is any number after decimal
   $amt_hundred = null;
   $count_length = strlen($num);
   $x = 0;
   $string = array();
   $change_words = array(0 => '', 1 => 'One', 2 => 'Two',
     3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
     7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
     10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
     13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
     16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
     19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
     40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
     70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
    //$here_digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
    $here_digits = array('', 'Hundred','Thousand','Hundred', 'Crore');
    while( $x < $count_length ) {
      $get_divider = ($x == 2) ? 10 : 100;
      $amount = floor($num % $get_divider);
      $num = floor($num / $get_divider);
      $x += $get_divider == 10 ? 1 : 2;
      if ($amount) {
       $add_plural = (($counter = count($string)) && $amount > 9) ? 's' : null;
       $amt_hundred = ($counter == 1 && $string[0]) ? ' and ' : null;
       $string [] = ($amount < 21) ? $change_words[$amount].' '. $here_digits[$counter]. $add_plural.'
       '.$amt_hundred:$change_words[floor($amount / 10) * 10].' '.$change_words[$amount % 10]. '
       '.$here_digits[$counter].$add_plural.' '.$amt_hundred;
        }
   else $string[] = null;
   }
   $implode_to_Rupees = implode('', array_reverse($string));
   $get_paise = ($amount_after_decimal > 0) ? "And " . ($change_words[$amount_after_decimal / 10] . "
   " . $change_words[$amount_after_decimal % 10]) . ' '.$FRACTION.' ' : '';
   return ($implode_to_Rupees ? $implode_to_Rupees . ' '.$CURR.' ' : '') . $get_paise;
}
*/

    public function write_log($type, $msg, $model, $val = null)
    {
        $this->load->library('logs');
        return (bool) $this->logs->write_log($type, $msg, $model, $val);
    }

    public function update_award_pointsV2($total, $customer, $user = null, $scope = null)
    {
        if (!empty($this->Settings->reverse_each_spent) && $total >= $this->Settings->reverse_each_spent) {
            $company      = $this->site->getCompanyByID($customer);
            $points       = floor(($total / $this->Settings->reverse_each_spent) * $this->Settings->reverse_ca_point);
            $total_points = $scope ? $company->award_points - $points : $company->award_points + $points;
            $reverse_points       = !empty($this->Settings->reverse_each_spent) ? floor(($total_points / $this->Settings->reverse_ca_point) * $this->Settings->reverse_each_spent) : null;
            if($total_points < 0){
                $total_points=0;
            }
            $this->db->update('companies', ['award_points' => $total_points], ['id' => $customer]);
            $WhatsappSettings=$this->site->getWhatsappSettings();
            if ($WhatsappSettings->award_points==1) {
                $this->load->library('sms');
                $currency_name=$this->site->getCurrencyByCode($this->Settings->default_currency)->name;
                //$text=  ($customer, $points, $total_points);
                $text=  $this->sms->invAwardPointsText($customer, $points, $total_points,$reverse_points,$this->Settings->site_name,$currency_name);

                $this->load->library('whatsapp');
                $this->whatsapp->send($company->phone, $text);
            }
        }
        if ($user && !empty($this->Settings->reverse_each_sale) && !$this->Customer && $total >= $this->Settings->reverse_each_sale) {
            $staff        = $this->site->getUser($user);
            $points       = floor(($total / $this->Settings->reverse_each_sale) * $this->Settings->reverse_sa_point);
            $total_points = $scope ? $staff->award_points - $points : $staff->award_points + $points;
            if($total_points < 0){
                $total_points=0;
            }
            $this->db->update('users', ['award_points' => $total_points], ['id' => $user]);
        }
        return true;
    }

    public function invSaleText($sale_id, $dbName)
    {
        $this->load->library('sms');
        return  $this->sms->invSaleText($sale_id, $dbName);
    }

    public function sendWhatsapp($phone, $text)
    {
        $this->load->library('whatsapp');
        $this->whatsapp->send($phone, $text);
    }

}

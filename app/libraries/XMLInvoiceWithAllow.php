<?php

//namespace NumNum\UBL\Tests;

//use Zakat\Country;
//use Zakat\sabre\Xml\Writer;
//use Zakat\sabre\Xml\XmlSerializable;
use PHPUnit\Framework\TestCase;


/**
 * Test an UBL2.1 invoice document
 */
class XMLInvoice
{
    //private $schema = './assets/Schemas/xsds/UBL2.1/xsd/maindoc/UBL-Invoice-2.1.xsd';
    private $schemaSign = './assets/Schemas/xsds/UBL2.1/xsd/common/UBL-XAdESv132-2.1.xsd';

	public function testIfXMLIsValid($seller,$taxNumber,$invoiceDate,$invoiceTotalAmount,$invoiceTaxAmount,$invoiceID,$items,$warehouse,$customer,$invoiceUUID,$QR,$invPaid,$dbName,$paymentMeansCode,$InvoiceTypeCode,$sale_id,$note,$order_discount=0)
    {

		// Address country
        $country = (new \NumNum\UBL\Country())
            ->setIdentificationCode('SA');

			// Tax scheme
        $taxScheme = (new \NumNum\UBL\TaxScheme())
            ->setId('VAT');

			$partyTaxScheme = (new \NumNum\UBL\PartyTaxScheme())
            ->setCompanyId($taxNumber)
            //->setRegistrationName($seller)
            ->setTaxScheme($taxScheme);

			$partyTaxSchemeClient = (new \NumNum\UBL\PartyTaxScheme())
            //->setCompanyId($customer->vat_no)
            //->setRegistrationName($seller)
            ->setTaxScheme($taxScheme);

            if($customer->vat_no!='' && strlen($customer->vat_no)==15){
                $partyTaxSchemeClient->setCompanyId($customer->vat_no);
            }


			$attachment = (new \NumNum\UBL\Attachment())
            //->setCompanyId($taxNumber)
            //->setRegistrationName($seller)
            ->setFilePath('./assets/xml/first_inv.txt');

			$attachmentQR = (new \NumNum\UBL\Attachment())
            //->setCompanyId($taxNumber)
            //->setRegistrationName($seller)
            ->setFilePathQR('./assets/xml/first_inv.txt',$QR);

			$additionalDocumentReference = (new \NumNum\UBL\AdditionalDocumentReference())
            //->setCompanyId($taxNumber)
            ->setId('PIH')
            ->setAttachment($attachmentQR);


            if($sale_id){
                $invoiceDocumentReference = (new \NumNum\UBL\InvoiceDocumentReference())
                ->setId($sale_id);
            }


			$additionalDocumentReferenceICV = (new \NumNum\UBL\AdditionalDocumentReference())
            //->setCompanyId($taxNumber)
            ->setId('ICV')
            ->setUUID($invoiceID);



			/*$additionalDocumentReferenceQR = (new \NumNum\UBL\AdditionalDocumentReference())
            //->setCompanyId($taxNumber)
            ->setId('QR')
            ->setAttachmentQR($attachmentQR);*/


			$legalEntity = (new \NumNum\UBL\LegalEntity())
            ->setRegistrationName($seller);
            //->setRegistrationName($seller)
            //->setTaxScheme($taxScheme);



			$legalEntityClient = (new \NumNum\UBL\LegalEntity())
            ->setRegistrationName($customer->company);
            //->setRegistrationName($seller)
            //->setTaxScheme($taxScheme);


        // Full address
        $address = (new \NumNum\UBL\Address())
            ->setStreetName($warehouse->street_name)
            ->setBuildingNumber($warehouse->building_no)
            ->setPlotIdentification($warehouse->additional_no)
            ->setCitySubdivisionName($warehouse->district_name)
            ->setCityName($warehouse->city)
            ->setPostalZone($warehouse->postal_code)
            ->setCountrySubentity($warehouse->state)
            ->setCountry($country);

        $addressClient = (new \NumNum\UBL\Address())
            ->setStreetName($customer->street_name)
            ->setBuildingNumber($customer->building_no)
            ->setPlotIdentification($customer->additional_no)
            ->setCitySubdivisionName($customer->district_name)
            ->setCityName($customer->city)
            ->setPostalZone($customer->postal_code)
            ->setCountrySubentity($customer->state)
            ->setCountry($country);

        // Supplier company node
        $supplierCompany = (new \NumNum\UBL\Party())
            ->setLegalEntity($legalEntity)
            ->setPartyIdentificationId($warehouse->cr_no)
            ->setSchemeID("CRN")
            ->setPartyTaxScheme($partyTaxScheme)
            ->setPostalAddress($address);


			$PartyIdentificationId=$customer->vat_no!="" ? $customer->cr_no : $customer->id;
			$schemeIDParty=$customer->vat_no!="" ? "CRN" : "OTH";
			$addressClient=$customer->vat_no!="" ? $addressClient : $address;



                $clientCompany = (new \NumNum\UBL\Party())
                ->setLegalEntity($legalEntityClient)
                ->setPartyIdentificationId($PartyIdentificationId)
                ->setSchemeID($schemeIDParty)
                ->setPartyTaxScheme($partyTaxSchemeClient)
                ->setPostalAddress($addressClient);



        // Client company node
        /*$clientCompany = (new \NumNum\UBL\Party())
            ->setName($customer->company)
            ->setPostalAddress($address);
		*/
        if($order_discount!=0){


        $legalMonetaryTotal = (new \NumNum\UBL\LegalMonetaryTotal())
            ->setLineExtensionAmount((abs($invoiceTotalAmount)-abs($invoiceTaxAmount)+abs($order_discount)))
            ->setTaxExclusiveAmount((abs($invoiceTotalAmount)-abs($invoiceTaxAmount)))
            ->setTaxInclusiveAmount((abs($invoiceTotalAmount)))
            ->setPayableAmount(abs($invoiceTotalAmount))
            ->setAllowanceTotalAmount(abs($order_discount));

        }else{
            $legalMonetaryTotal = (new \NumNum\UBL\LegalMonetaryTotal())
            ->setLineExtensionAmount((abs($invoiceTotalAmount)-abs($invoiceTaxAmount)))
            ->setTaxExclusiveAmount((abs($invoiceTotalAmount)-abs($invoiceTaxAmount)))
            ->setTaxInclusiveAmount((abs($invoiceTotalAmount)))
            ->setPayableAmount(abs($invoiceTotalAmount))
            ->setAllowanceTotalAmount(0);
        }


			// Total Taxes
            $taxCategory = (new \NumNum\UBL\TaxCategory())
            ->setId('S')
            //->setName('VAT15%')
            ->setPercent(15)
            ->setTaxScheme($taxScheme);
            if($dbName=="starup"){
                $taxCategoryZ = (new \NumNum\UBL\TaxCategory())
                ->setId('Z')
                //->setName('VAT15%')
                ->setPercent(0)
                ->setTaxExemptionReasonCode('VATEX-SA-35')
                ->setTaxExemptionReason('TRANSPORTATION CHARGE')
                ->setTaxScheme($taxScheme);
            }else{
                $taxCategoryZ = (new \NumNum\UBL\TaxCategory())
                ->setId('Z')
                //->setName('VAT15%')
                ->setPercent(0)
                ->setTaxExemptionReasonCode('VATEX-SA-35')
                ->setTaxExemptionReason('Medicines and medical equipment')
                ->setTaxScheme($taxScheme);
            }


			$classifiedTaxCategory = (new \NumNum\UBL\ClassifiedTaxCategory())
            ->setId('S')
            ->setPercent(15)
            ->setTaxScheme($taxScheme);

            $classifiedTaxCategoryZ = (new \NumNum\UBL\ClassifiedTaxCategory())
            ->setId('Z')
            ->setPercent(0)
            ->setTaxScheme($taxScheme);

		$itemCount=1;
		//$productItem=[];
		//$invoiceLineArr=[];
		//$invoiceLines=[];
        $itemWhithTaxCount=0;
		$itemWhithOutTaxCount=0;
		foreach ($items as $item){
		// Product
        if(abs($item->item_tax)!=0){
            $productItem = (new \NumNum\UBL\Item())
            ->setName($item->product_name)
            ->setClassifiedTaxCategory($classifiedTaxCategory);
            $itemWhithTaxCount++;
        }else{
            $productItem = (new \NumNum\UBL\Item())
            ->setName($item->product_name)
            ->setClassifiedTaxCategory($classifiedTaxCategoryZ);
            $itemWhithOutTaxCount++;
        }


        if($order_discount!=0){
            $price = (new \NumNum\UBL\Price())
            //->setBaseQuantity(1)
            //->setUnitCode($item->base_unit_code)
            ->setPriceAmount(abs(($item->net_unit_price + $item->discount)));

        // Invoice Line tax totals
        $lineTaxTotal = (new \NumNum\UBL\TaxTotal())
            //->setTaxAmount($item->item_tax)
            ->setTaxAmount(abs($item->item_tax) !='0' ? ((abs($item->net_unit_price) * abs($item->quantity))*15/100) : 0)
            ->setRoundingAmount((abs(($item->net_unit_price + $item->discount)) * abs($item->quantity)) + (abs($item->item_tax) !='0' ? ((abs($item->net_unit_price) * abs($item->quantity))*15/100) : 0));

        // Invoice Line(s)
            $taxCategoryAllow = (new \NumNum\UBL\TaxCategory())
            ->setId('S')
            //->setName('VAT15%')
            ->setPercent(15)
            ->setTaxScheme($taxScheme);

        $invoiceLinesAllowance= (new \NumNum\UBL\AllowanceCharge())
        ->setAllowanceChargeReason('Discount')
        ->setAmount(abs($item->item_discount))
        ->setTaxCategory($taxCategoryAllow);

        $invoiceLine = (new \NumNum\UBL\InvoiceLine())
            ->setId($itemCount)
            ->setUnitCode($item->base_unit_code ? $item->base_unit_code : 'Pcs')
            ->setItem($productItem)
            ->setPrice($price)
            //->setLineExtensionAmount(number_format((float)abs($item->net_unit_price), 2, '.', '') * (number_format((float)abs($item->quantity), 2, '.', '')))
            ->setLineExtensionAmount(abs(($item->net_unit_price + $item->discount)) * abs($item->quantity))
            //->setLineExtensionAmount((($item->subtotal)-($item->net_unit_price * $item->quantity)*15/100))
            ->setTaxTotal($lineTaxTotal)
            ->setInvoicedQuantity(abs($item->quantity));
        }else{

            $price = (new \NumNum\UBL\Price())
            //->setBaseQuantity(1)
            //->setUnitCode($item->base_unit_code)
            ->setPriceAmount(abs($item->net_unit_price));

        // Invoice Line tax totals
        $lineTaxTotal = (new \NumNum\UBL\TaxTotal())
            //->setTaxAmount($item->item_tax)
            ->setTaxAmount(abs($item->item_tax) !='0' ? ((abs($item->net_unit_price) * abs($item->quantity))*15/100) : 0)
            ->setRoundingAmount(abs($item->subtotal));

        // Invoice Line(s)

        $invoiceLine = (new \NumNum\UBL\InvoiceLine())
            ->setId($itemCount)
            ->setUnitCode($item->base_unit_code ? $item->base_unit_code : 'Pcs')
            ->setItem($productItem)
            ->setPrice($price)
            //->setLineExtensionAmount(number_format((float)abs($item->net_unit_price), 2, '.', '') * (number_format((float)abs($item->quantity), 2, '.', '')))
            ->setLineExtensionAmount(abs($item->net_unit_price) * abs($item->quantity))
            //->setLineExtensionAmount((($item->subtotal)-($item->net_unit_price * $item->quantity)*15/100))
            ->setTaxTotal($lineTaxTotal)
            ->setInvoicedQuantity(abs($item->quantity));

        }

        // Price


		//$invoiceLines = [$invoiceLine];
		$invoiceLines[] = $invoiceLine;
        $invoiceLinesAllowances[] = $invoiceLinesAllowance;
        $itemCount++;
		}

        if($itemWhithTaxCount>0){
        $taxSubTotal = (new \NumNum\UBL\TaxSubTotal())
            ->setTaxableAmount((abs($invoiceTotalAmount)-abs($invoiceTaxAmount)))
            ->setTaxAmount(abs($invoiceTaxAmount))
            ->setTaxCategory($taxCategory);
        }else{
            $taxSubTotal = (new \NumNum\UBL\TaxSubTotal())
            ->setTaxableAmount((abs($invoiceTotalAmount)-abs($invoiceTaxAmount)))
            ->setTaxAmount(abs($invoiceTaxAmount))
            ->setTaxCategory($taxCategoryZ);
        }

        $taxTotal = (new \NumNum\UBL\TaxTotal())
            ->addTaxSubTotal($taxSubTotal)
            ->setTaxAmount(abs($invoiceTaxAmount));

        $taxTotalFinal = (new \NumNum\UBL\TaxTotal())
            //->addTaxSubTotal($taxSubTotal)
            ->setTaxAmount(abs($invoiceTaxAmount));

        $delivery = (new \NumNum\UBL\Delivery())
            //->addTaxSubTotal($taxSubTotal)
            ->setActualDeliveryDate(new \DateTime($invoiceDate));
        if($InvoiceTypeCode=="388"){
            $paymentMeans = (new \NumNum\UBL\PaymentMeans())
            //->addTaxSubTotal($taxSubTotal)
            ->setPaymentMeansCode($paymentMeansCode, []);
        }else{
        $paymentMeans = (new \NumNum\UBL\PaymentMeans())
            //->addTaxSubTotal($taxSubTotal)
            ->setPaymentMeansCode($paymentMeansCode, [])
            ->setInstructionNote($note ? $note : "Goods refund");
        }
		/*goods or services refund
			$financialInstitutionBranch = (new \NumNum\UBL\FinancialInstitutionBranch())
            ->setId('RABONL2U');

			$payeeFinancialAccount = (new \NumNum\UBL\PayeeFinancialAccount())
           ->setFinancialInstitutionBranch($financialInstitutionBranch)
            ->setName('Customer Account Holder')
            ->setId('NL00RABO0000000000');

        $paymentMeans = (new \NumNum\UBL\PaymentMeans())
            ->setPayeeFinancialAccount($payeeFinancialAccount)
            ->setPaymentMeansCode(31, [])
            ->setPaymentId('our invoice 1234');
*/




        // Invoice object
		//$InvoiceTypeNumber=$customer->vat_no!="" ? '0100000' : '0200000';
        if($customer->vat_no!=""){// || abs($invoiceTotalAmount)>=1000){
            $InvoiceTypeNumber='0100000';
        }else{
            $InvoiceTypeNumber='0200000';
        }

        if($sale_id){
		$invoice = (new \NumNum\UBL\Invoice())
            ->setUBLVersionID('2.1')
            ->setProfileID('reporting:1.0')
            ->setId($invoiceID)
            ->setUUID($invoiceUUID)
            //->setCopyIndicator(false)
            ->setIssueDate(new \DateTime($invoiceDate))
            ->setIssueTime(new \DateTime($invoiceDate))
            ->setInvoiceDocumentReference($invoiceDocumentReference)
            ->setAdditionalDocumentReferenceICV($additionalDocumentReferenceICV)
            ->setAdditionalDocumentReference($additionalDocumentReference)
            //->setAdditionalDocumentReferenceQR($additionalDocumentReferenceQR)
            ->setAccountingSupplierParty($supplierCompany)
            ->setInvoiceTypeCode($InvoiceTypeCode)
            ->setInvoiceTypeNumber($InvoiceTypeNumber)
            //->setAccountingCustomerParty($clientCompany)
            ->setDelivery($delivery)
            ->setPaymentMeans($paymentMeans)
            ->setInvoiceLines($invoiceLines)
            ->setLegalMonetaryTotal($legalMonetaryTotal)
            ->setTaxTotal($taxTotal)
            ->setTaxTotalFinal($taxTotalFinal);
        }else{
            $invoice = (new \NumNum\UBL\Invoice())
            ->setUBLVersionID('2.1')
            ->setProfileID('reporting:1.0')
            ->setId($invoiceID)
            ->setUUID($invoiceUUID)
            //->setCopyIndicator(false)
            ->setIssueDate(new \DateTime($invoiceDate))
            ->setIssueTime(new \DateTime($invoiceDate))
            ->setAdditionalDocumentReferenceICV($additionalDocumentReferenceICV)
            ->setAdditionalDocumentReference($additionalDocumentReference)
            //->setAdditionalDocumentReferenceQR($additionalDocumentReferenceQR)
            ->setAccountingSupplierParty($supplierCompany)
            ->setInvoiceTypeCode($InvoiceTypeCode)
            ->setInvoiceTypeNumber($InvoiceTypeNumber)
            //->setAccountingCustomerParty($clientCompany)
            ->setDelivery($delivery)
            ->setPaymentMeans($paymentMeans)
            ->setInvoiceLines($invoiceLines)
            ->setAllowanceCharges($invoiceLinesAllowances)
            ->setLegalMonetaryTotal($legalMonetaryTotal)
            ->setTaxTotal($taxTotal)
            ->setTaxTotalFinal($taxTotalFinal);
        }
        if($customer->vat_no=="" && $customer->cr_no==""){
        }else{
            $invoice->setAccountingCustomerParty($clientCompany);
        }

        // Test created object
        // Use \NumNum\UBL\Generator to generate an XML string
        $generator = new \NumNum\UBL\Generator();
        $outputXMLString = $generator->invoice($invoice);

        // Create PHP Native DomDocument object, that can be
        // used to validate the generate XML
       // $dom = new \DOMDocument;

	   $outputXMLString=str_replace('<?xml version="1.0"?>','<?xml version="1.0" encoding="UTF-8"?>',$outputXMLString);

		$dom = new DOMDocument('1.0', 'UTF-8');

		//$outputXMLString=iconv(mb_detect_encoding($outputXMLString, mb_detect_order(), true), "UTF-8", $outputXMLString);
		//$outputXMLString=iconv("Windows-1256", "UTF-8//TRANSLIT",$outputXMLString);
/*$start = strpos($outputXMLString, '?>');
print_r($start);*/
        $dom->loadXML($outputXMLString);

		//$invoiceDate
		$dateTime=str_replace('-',"",$invoiceDate);
		$dateTime=str_replace(':',"",$dateTime);
		$dateTime=str_replace(' ',"T",$dateTime);

        //$dom->save('./assets/xml/signedXmlResult.xml');
		//exec('fatoora -sign -invoice ./assets/xml/signedXmlResult.xml -signedInvoice ./assets/xml/signedXmlResult1.xml');
		//exec('fatoora -sign -invoice C:\wamp64\www\spark_erp\assets\xml\signedXmlResult.xml -signedInvoice C:\wamp64\www\spark_erp\assets\xml\signedXmlResult1.xml');


//$output = null;

//$code = null;

//$command = "fatoora -sign -invoice C:\wamp64\www\spark_erp\assets\xml\signedXmlResult.xml -signedInvoice C:\wamp64\www\spark_erp\assets\xml\signedXmlResult1.xml 2>&1";
//$command = "java -jar C:\wamp64\www\zakat\zatca-einvoicing-sdk-209-R3.1.2B\sdk-3.1.2-jar-with-dependencies.jar fatoora -sign -invoice C:\wamp64\www\spark_erp\assets\xml\signedXmlResult.xml -signedInvoice C:\wamp64\www\spark_erp\assets\xml\signedXmlResult1.xml";

//exec($command,$output,$code);

//print_r($output);

		//$dom->save('./assets/xml/'.$taxNumber.'_'.$dateTime.'_'.$invoiceID.'.xml');
		$dom->save('./assets/xml/'.$dbName.'_'.$taxNumber.'_'.$invoiceID.'.xml');



      //$this->assertEquals(true, $dom->schemaValidate($this->schema));
        //print_r($dom->schemaValidate($this->schema));
    }
}

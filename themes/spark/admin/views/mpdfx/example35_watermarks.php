<?php


$html='
<style>
.right-side{
    direction: rtl;
    text-align: -webkit-right;
}



.left-side{
    direction: ltr;
    text-align: -webkit-left;
}
.qrimg{
	padding-bottom:20px;
}

.back-color{

    -webkit-print-color-adjust:exact !important;
}
.table > thead:first-child > tr, .table thead th {

    border:none !important;
    }
.table > tfoot:first-child > tr, .table tfoot td {



    }

    .table-striped>tbody>tr:nth-child(odd)>td, .table-striped>tbody>tr:nth-child(odd)>th {
    background-color:#fff;

    }
.bord{

    border:2px solid #000 !important;

}
.bordl{

    border-left:1px solid #000 !important;

    }

body{
    -webkit-print-color-adjust: exact;
}

    table { page-break-inside:auto;margin-bottom:1px !important; }
      tr    { page-break-inside:avoid; page-break-after:auto }

   .inv-details    { display: block; position: relative;
    bottom: 0; -webkit-print-color-adjust:exact; page-break-inside:avoid; page-break-after:auto }

	.inv-details table    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr td    { font-size:9pt;border: 1px solid #ddd; }
    td {
    }

@media print {


	.modal-content th,td{
  font-size: 7pt;
}
.fixRight {
  font-size: 9pt;
}
.footer_sign{
  font-size: 8pt;
}

				.well { margin-bottom:-2px;padding:1px;}
				.well1 {margin-top:-15px;padding:1px;}

				.table td, .table th {
				padding: 0.2rem !important;
				}

				.col-xs-1 {
    width: 4.33333333%;
}
				.col-xs-11 {
    width: 95.66666667%;
}


.from,.to{margin-bottom:15px;margin-top:15px !important;}

  .bord{
    border:solid windowtext 1.5pt !important;

}
	.back-color{

    -webkit-print-color-adjust:exact !important;
}

  body {
  position: absolute;
     -webkit-print-color-adjust: exact;

  }


      table { page-break-inside:auto }
      tr    { page-break-inside:avoid; page-break-after:auto }

   .inv-details    { display: block; position: relative;
    bottom: 0; -webkit-print-color-adjust:exact; page-break-inside:avoid; page-break-after:auto }

	.inv-details table    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr    { page-break-inside:avoid; page-break-after:auto }
	.inv-details tr td    { font-size:8pt; }


            }



 .divbg:after {

  width: 100%;
  height: 99%;
  position: absolute;
  background-color: #2A3F54;
  top: 0;
  right: 0;
  -webkit-print-color-adjust:exact;
}

.col-xs-1 {
    width: 4.33333333%;
}



.fixRight{
    direction: rtl;
    text-align: -webkit-right;
}
</style>



<div id="pr_pdf">
    <div class="row fixRight" style="border-bottom:2px solid #000 !important;">
                     <div class="col-xs-3">
                       </div>
                <div class="col-xs-6 text-center bold" style="margin-top:1px;padding-right:1px;padding-left:1px;">

                     				    <b valign="top"><h1 style=" font-size:20px;color:#007bff !important;margin-top:1px;margin-bottom: 1px;">فاتورة&nbsp;ضريبية&nbsp;مبسطة&nbsp;/&nbsp;SIMPLIFIDE&nbsp;TAX&nbsp;INVOICE</h1></b>
					                    </div>

		           <div class="col-xs-3">
                   </div>

                    <div class="col-xs-5 right-side" style="margin-top:1px;">
                     <!--<b>:رقم الفاتورة Tax Invoice: BR1-1842 </b></br>-->
                      <b style="color:red !important;margin-top:2px;">رقم&nbsp;الفاتورة</b>  <b>BR1-1842</b>  <font dir="ltr"><b style="color:red!important;">Invoice&nbsp;No</b></font><br>
                      <b style="color:red !important;">الرقم&nbsp;المرجعي</b>&nbsp;<b>BR1/SALE2024/08/0186</b>&nbsp;<b dir="rtl" style="color:red!important;">Reference</b><br>
                     <!--   -->



                    </div>
                    <div class="col-xs-2">

                    </div>


                    <div class="col-xs-5  text-left" dir="ltr" style="margin-top:1px;padding-right:1px;">
                          <b style="color:red !important;">Invoice&nbsp;Date</b> <b style="color:black !important;"> 31/08/2024 21:30</b>  <b style="color:red !important;">تاريخ&nbsp;الفاتورة</b><br>



                    </div>

            </div>

            <div class="well-sm " style="padding:5px;">
            </div>

		   <div class="row bold left-side fixRight" style="border:solid windowtext 1.5pt !important;border-radius:15px;padding:0in 0in 1.0pt 0in;margin-left:0in;margin-right:0in;">

		   <div class="col-xs-12" style="padding-right:1px; padding-left:1px;">

               <div class="col-xs-4 right-side">


                                   <b> الشركة العالمية للاتصالات</b><br>
                                <!--
					<h2 style="margin-top:10px;">الشركة العالمية للاتصالات</h2>

					مؤسسة دار الطبيعة-->

                    المخزن : مؤسسة دار الطبيعة<br>تلفون : 920021241<br>ايميل : info@companypro.com<br>الجبيل 35811-3372-المملكة العربية السعودية<br>                                         <b> الرقم الضريبي : 310203779800003</b><br>                                                            <b> السجل التجاري : 2055128148</b><br>


			    </div>

                <div class="col-xs-4 text-center">







                    <div class=" order_barcodes ">
					    <img style="padding-top:20px !important;max-width:300px;" src="http://localhost/spark_erp/assets/uploads/logos/logo2.png" alt="الشركة العالمية للاتصالات"><br>


                    </div>
                                 </div>







                 <div class="col-xs-4 text-left" dir="ltr">

                                     <b> hands of excellent company</b><br>
                                <!--
					<h2 style="margin-top:10px;">hands of excellent company</h2>

					مؤسسة دار الطبيعة-->

                    Warehouse : Altabia<br>Tel : 920021241<br>Email : info@companypro.com<br>Al Jubail 35811-3372-Saudi Arabia<br>                                               <b> VAT No : 310203779800003</b><br>                                                                <b> CR No : 2055128148</b><br>

			      </div>

              </div>
			</div>



			        <!--<div class="row">

		    	        <div class="col-xs-12">




                                    <div class="well well-sm">
                                        <div class="row bold"  >
                                            <div class="col-xs-4">
                                            <p class="bold">
                                                تاريخ الإصدار: 31/08/2024 21:30<br>
                                                الرقم التسلسلسي للفاتوره: BR1-1842<br>
                                                الرقم المرجعي: BR1/SALE2024/08/0186<br>
						                                                                        تاريخ التوريد: 31/08/2024 21:31<br>
						                                                                                                                        حالة عمليه البيع: عملية مكتملة</br>
                                                حالة الدفع: تحت الإجراء
                                                                                            </p>
                                            </div>
					                                                                    <div class="col-xs-8 text-right order_barcodes">
                                                <img src="http://localhost/spark_erp/admin/misc/barcode/QlIxL1NBTEUyMDI0LzA4LzAxODY/code128/74/0/1" alt="BR1/SALE2024/08/0186" class="bcimg" />
                                                </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>-->

         <!--   <div class="row" style="margin-bottom:15px;margin-top:10px;border-bottom:solid windowtext 1.5pt !important;">

                                    <div class="col-xs-2 right-side" >
                     <b> الرقم الضريبي : 310203779800003</b></br>

                    </div>

                    <div class="col-xs-8 text-center">

                     				    <b valign="top"><h1  style=" font-size: 20px;color:#007bff !important;"> فاتورة&nbsp;ضريبية&nbsp;مبسطة&nbsp;/&nbsp;SIMPLIFIDE&nbsp;TAX&nbsp;INVOICE</h1></b></br>
					                    </div>
                    <div class="col-xs-2 left-side">
                       <b> VAT No : 310203779800003</b></br>

                      </div>


            </div>

            -->



            <div class="row fixRight" style="margin-bottom:1px;margin-top:5px;">


                         <div class="col-xs-9 ">
                                <div class="row">
                                    <div class="col-xs-5 pull-right">
                                     <p>تفاصيل العميل </p>
                                     </div>
                                     <div class="col-xs-2">

                                     </div>

                                   <div class="col-xs-5 text-left">
                                     <p>Customer Details </p>
                                     </div>
                                </div>


                                <div class="col-xs-12 back-color bord" dir="rtl" style="border-radius:10px; margin-bottom:1px;padding:5px;">
                                   <div class="col-xs-6" dir="rtl" style="padding:1px;">

                                         <b> الاسم : Default</b><br>
                                        <!--  </br>                                          </br>                                       <b> الرقم الضريبي: </b><br/> -->
                                        					                                                                                                                </div>

                                   <!--  <div class="col-xs-2" >
                                     </div> -->

				                    <div class="col-xs-6  text-left" dir="ltr" style="padding:1px;">
                                       <b> Name :  Default</b><br>
                                           <!--  </br>                                          </br>                                       <b> VAT No:</b><br/>-->
                                         					                                                                                                               </div>

                               </div>




                          </div>

                     <!--   <div class="col-xs-3 text-center order_barcodes" style="padding:20px 1px 1px 1px;"> -->
                        <div class="col-xs-3 order_barcodes" style="padding-right:1px;">
                             <div class=" order_barcodes" style="padding:10px;margin-top:10px;">



                                                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIIAAACCAQMAAACwzYK+AAAABlBMVEUAAAD///+l2Z/dAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAC6ElEQVRIiXXWwYosOwgG4IBbwVcR3AZ8dSFbwVcJuBVy7LO63PL0br6ZmjKJ/um14PiJUIhK9rv68xFNvq9K4TCrypuEZcPCFVsDCgRmIfTSu0+GWf5DgEPyQmHmk1E0tzvuc7lOnXyTLJD/fHhN8l5GwYG8N469Z4Ok8UnAC1igjGsSwLhd/91asXs9axC+apGEl36FnDsJUR7zpOTnyYU2CNrpyrxPyEEYeJLazo59NKgP0HMNsrJ3inFfKYFe9iRHzxWIhHQzppoEeqNIKl4psZ49CWGiRJ3dO4YeZIMgvJBQNScD+u3qVxYqBgq90PXEYhI/vbwuDyv7Ie23fwWjLH77viUIw22QJZJr87vRrRuga5Cnx3dVPw1pS2OSshJUp91jwCR7ElWGX6NIbxziAxuEFh9D3m7H4W/PfIUxHYhW0svH0bvxlXWJdPXZHWdC6Gn6yiupjbvfusL9FwFf2UspCwwYa3kXM0jpEoI4117eUJwkpDsy5XCGXJN+w1e2cv95N1YX2Ceok/Qu+rW9Oit2nCVrkFtGD2/0T/f0XNogsaI8X89+KG/Zk2zNd59tJnbyfdYgnCYBF3eldw00SWfsMuitwtpHfx3zlX6AuQ9ZqXN2k6xBOJzQrM6toP2bgq9s26Km3BEJsvNX80ewK/fl6a9rBfFJTBzydDv1P5ZOv0m84z46QekiRnBMEh2i0VeEhx5eFyZBJLBMkHc6LuSXEh9JPmK4SB6J9K/XIPVAsGdBe0rwdW8O0t3k3U694ux5SpkESbzntcR48b7dCV+xtE6rRa9+a+lYGeT9rgUmqNfhdV/fVl+55wgffS7ebZyj7Aq0m9mVJtQvbb5ypK/qPmVw+EVWT9NXXscWleV9uql+CfmVvqKgIwv6wkZ7IpNoUrE+Saeiq70bX2Gx7rd9YQuLrn/I09q8yuPtiJxl951WtJSzv2DgGqSH8jd1icc6SpBskNW3Wc8/9DiBwt88/L/8AVj98fCpOyHwAAAAAElFTkSuQmCC" alt="ATDYp9mE2LTYsdmD2Kkg2KfZhNi52KfZhNmF2YrYqSDZhNmE2KfYqti12KfZhNin2KoCDzMxMDIwMzc3OTgwMDAwMwMUMjAyNC0wOC0zMVQyMTozMDowMFoECTMwOTAxOC4zNQUINDAzMDQuNzg=" class="qrimg">

						    <!--<img src="http://localhost/spark_erp/admin/misc/barcode/QlIxL1NBTEUyMDI0LzA4LzAxODY/code128/74/0/1" alt="BR1/SALE2024/08/0186" class="bcimg"/>
                             -->

                        </div>
                        </div>

            </div>




<!--
                <div class="row" style="margin-bottom:15px;margin-top:15px;">

                <div class="col-xs-6 from">
                    <b>من: الشركة العالمية للاتصالات</b><br/>
                    <!--
					<h2 style="margin-top:10px;">الشركة العالمية للاتصالات</h2>

					مؤسسة دار الطبيعة-->
<!--
                    الجبيل 35811-3372-المملكة العربية السعودية<br>رقم التسجيل الضريبي: 310203779800003<br>السجل التجاري: 2055128148<br>الهاتف: 920021241+<br>البريد الإلكتروني: info@companypro.com                </div>


				<div class="col-xs-6 to">
                   <b> إلى :Default</b><br/>
                    <!--<h2 style="margin-top:10px;">Default</h2>-->
    <!--
                    <br>   <br><br><br>الهاتف: <br>البريد الإلكتروني:                 </div>

            </div>
-->


            <div style="border:2px solid #000">
    <div class="table-responsive">
                 <table class="table table-striped jambo_table bulk_action print-table order-table pdftable ">


                    <thead>

                  <tr style="-webkit-print-color-adjust:exact;">

                        <th>متسلسل <br> Sr. No</th>
                        <th>رقم.الصنف<br>Item.Code</th>
                        <th class="col-xs-6">وصف الصنف <br> Description</th>
                        <th>الوحدة <br> Unit</th>


                        <th>الكمية<br> QTY</th>

                        <th>السعر<br> Price</th>
						 <!--<th>قيمة</th>-->
                        <th>خصم <br> Discount</th><th>٪الضريبة<br>VAT%</th><th>قيمةالضريبة<br>VAT.Amount</th>                        <th class="col-xs-2">السعر الإجمالي <br> Total Price</th>
                    </tr>

                    </thead>

                    <tbody>

                                            <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;">1</td>
                            <td style="vertical-align:middle;">
                                17223274                            </td>
							<td style="vertical-align:middle;font-weight:bold;" dir="ltr">

								كمبيوتر ابل                             - computer apple                                                                 - طراز أثيوبي منتج سعودي                             </td>

							<td>واحد</td>

                            <td style="width: 80px; text-align:center; vertical-align:middle;">1.00 </td>

							<td style="text-align:center; width:100px;">
                                <!--                                -->
								 1,043.48                            </td>
							<!--<td>SR1,200.00</td>-->
                            <td style="width: 100px; text-align:right; vertical-align:middle;">0.00</td><td style="width: 100px; text-align:right; vertical-align:middle;">15%</td><td style="width: 100px; text-align:right; vertical-align:middle;"> 156.52</td><td style="width: 120px; text-align:right; vertical-align:middle;"> 1,200.00</td>                           <!-- <td style="text-align:right; width:120px;"> 1,200.00</td> -->
                        </tr>
                                                <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;">2</td>
                            <td style="vertical-align:middle;">
                                76512562                            </td>
							<td style="vertical-align:middle;font-weight:bold;" dir="ltr">

								ماء اكوافينا 330 مل                                                                                                                        </td>

							<td>حبة</td>

                            <td style="width: 80px; text-align:center; vertical-align:middle;">1.00 </td>

							<td style="text-align:center; width:100px;">
                                <!--                                -->
								 0.87                            </td>
							<!--<td>SR1.00</td>-->
                            <td style="width: 100px; text-align:right; vertical-align:middle;">0.00</td><td style="width: 100px; text-align:right; vertical-align:middle;">15%</td><td style="width: 100px; text-align:right; vertical-align:middle;"> 0.13</td><td style="width: 120px; text-align:right; vertical-align:middle;"> 1.00</td>                           <!-- <td style="text-align:right; width:120px;"> 1.00</td> -->
                        </tr>
                                                <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;">3</td>
                            <td style="vertical-align:middle;">
                                70831091                            </td>
							<td style="vertical-align:middle;font-weight:bold;" dir="ltr">

								كيك زعفران                                                                                                                        </td>

							<td>حبة</td>

                            <td style="width: 80px; text-align:center; vertical-align:middle;">1.00 </td>

							<td style="text-align:center; width:100px;">
                                <!--                                -->
								 15.00                            </td>
							<!--<td>SR15.00</td>-->
                            <td style="width: 100px; text-align:right; vertical-align:middle;">0.00</td><td style="width: 100px; text-align:right; vertical-align:middle;">0</td><td style="width: 100px; text-align:right; vertical-align:middle;">0.00</td><td style="width: 120px; text-align:right; vertical-align:middle;"> 15.00</td>                           <!-- <td style="text-align:right; width:120px;"> 15.00</td> -->
                        </tr>
                                                <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;">4</td>
                            <td style="vertical-align:middle;">
                                46287982                            </td>
							<td style="vertical-align:middle;font-weight:bold;" dir="ltr">

								كمبيوتر لينوفو                                                                                                                        </td>

							<td>واحد</td>

                            <td style="width: 80px; text-align:center; vertical-align:middle;">1.00 </td>

							<td style="text-align:center; width:100px;">
                                <!--                                -->
								 869.57                            </td>
							<!--<td>SR1,000.00</td>-->
                            <td style="width: 100px; text-align:right; vertical-align:middle;">0.00</td><td style="width: 100px; text-align:right; vertical-align:middle;">15%</td><td style="width: 100px; text-align:right; vertical-align:middle;"> 130.43</td><td style="width: 120px; text-align:right; vertical-align:middle;"> 1,000.00</td>                           <!-- <td style="text-align:right; width:120px;"> 1,000.00</td> -->
                        </tr>
                                                <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;">5</td>
                            <td style="vertical-align:middle;">
                                49723030                            </td>
							<td style="vertical-align:middle;font-weight:bold;" dir="ltr">
														<img src="http://localhost/spark_erp/assets/images/free.jpg" width="10%">

								كمبيوتر سامسونج                                                                                                                        </td>

							<td>واحد</td>

                            <td style="width: 80px; text-align:center; vertical-align:middle;">1.00 </td>

							<td style="text-align:center; width:100px;">
                                <!--                                -->
								0.00                            </td>
							<!--<td>0.00</td>-->
                            <td style="width: 100px; text-align:right; vertical-align:middle;">0.00</td><td style="width: 100px; text-align:right; vertical-align:middle;"></td><td style="width: 100px; text-align:right; vertical-align:middle;">0.00</td><td style="width: 120px; text-align:right; vertical-align:middle;">0.00</td>                           <!-- <td style="text-align:right; width:120px;">0.00</td> -->
                        </tr>
                                                <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;">6</td>
                            <td style="vertical-align:middle;">
                                49723030                            </td>
							<td style="vertical-align:middle;font-weight:bold;" dir="ltr">

								كمبيوتر سامسونج                                                                                                                        </td>

							<td>واحد</td>

                            <td style="width: 80px; text-align:center; vertical-align:middle;">3.00 </td>

							<td style="text-align:center; width:100px;">
                                <!--                                -->
								 800.00                            </td>
							<!--<td>SR2,400.00</td>-->
                            <td style="width: 100px; text-align:right; vertical-align:middle;">0.00</td><td style="width: 100px; text-align:right; vertical-align:middle;">15%</td><td style="width: 100px; text-align:right; vertical-align:middle;"> 360.00</td><td style="width: 120px; text-align:right; vertical-align:middle;"> 2,760.00</td>                           <!-- <td style="text-align:right; width:120px;"> 2,760.00</td> -->
                        </tr>
                                                <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;">7</td>
                            <td style="vertical-align:middle;">
                                62572713                            </td>
							<td style="vertical-align:middle;font-weight:bold;" dir="ltr">

								ماء بيرين كرتون 1*40                                                                                                                        </td>

							<td>واحد</td>

                            <td style="width: 80px; text-align:center; vertical-align:middle;">2.00 </td>

							<td style="text-align:center; width:100px;">
                                <!--                                -->
								 20.00                            </td>
							<!--<td>SR40.00</td>-->
                            <td style="width: 100px; text-align:right; vertical-align:middle;">0.00</td><td style="width: 100px; text-align:right; vertical-align:middle;">15%</td><td style="width: 100px; text-align:right; vertical-align:middle;"> 6.00</td><td style="width: 120px; text-align:right; vertical-align:middle;"> 46.00</td>                           <!-- <td style="text-align:right; width:120px;"> 46.00</td> -->
                        </tr>
                                                <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;">8</td>
                            <td style="vertical-align:middle;">
                                78702524                            </td>
							<td style="vertical-align:middle;font-weight:bold;" dir="ltr">

								equipment and machinery contract                                                                                                                        </td>

							<td></td>

                            <td style="width: 80px; text-align:center; vertical-align:middle;">2.00 </td>

							<td style="text-align:center; width:100px;">
                                <!--                                -->
								 6.09                            </td>
							<!--<td>SR14.00</td>-->
                            <td style="width: 100px; text-align:right; vertical-align:middle;">0.00</td><td style="width: 100px; text-align:right; vertical-align:middle;">15%</td><td style="width: 100px; text-align:right; vertical-align:middle;"> 1.83</td><td style="width: 120px; text-align:right; vertical-align:middle;"> 14.00</td>                           <!-- <td style="text-align:right; width:120px;"> 14.00</td> -->
                        </tr>
                                                <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;">9</td>
                            <td style="vertical-align:middle;">
                                74417319                            </td>
							<td style="vertical-align:middle;font-weight:bold;" dir="ltr">

								equipment 15% contract                                                                                                                        </td>

							<td>واحد</td>

                            <td style="width: 80px; text-align:center; vertical-align:middle;">2.00 </td>

							<td style="text-align:center; width:100px;">
                                <!--                                -->
								 130,434.78                            </td>
							<!--<td>SR300,000.00</td>-->
                            <td style="width: 100px; text-align:right; vertical-align:middle;">0.00</td><td style="width: 100px; text-align:right; vertical-align:middle;">15%</td><td style="width: 100px; text-align:right; vertical-align:middle;"> 39,130.43</td><td style="width: 120px; text-align:right; vertical-align:middle;"> 300,000.00</td>                           <!-- <td style="text-align:right; width:120px;"> 300,000.00</td> -->
                        </tr>
                                                <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;">10</td>
                            <td style="vertical-align:middle;">
                                97783492                            </td>
							<td style="vertical-align:middle;font-weight:bold;" dir="ltr">

								QTY Chk                                                                                                                        </td>

							<td>واحد</td>

                            <td style="width: 80px; text-align:center; vertical-align:middle;">1.00 </td>

							<td style="text-align:center; width:100px;">
                                <!--                                -->
								 9.00                            </td>
							<!--<td>SR9.00</td>-->
                            <td style="width: 100px; text-align:right; vertical-align:middle;">0.00</td><td style="width: 100px; text-align:right; vertical-align:middle;">15%</td><td style="width: 100px; text-align:right; vertical-align:middle;"> 1.35</td><td style="width: 120px; text-align:right; vertical-align:middle;"> 10.35</td>                           <!-- <td style="text-align:right; width:120px;"> 10.35</td> -->
                        </tr>
                                                <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;">11</td>
                            <td style="vertical-align:middle;">
                                04669327                            </td>
							<td style="vertical-align:middle;font-weight:bold;" dir="ltr">
														<img src="http://localhost/spark_erp/assets/images/free.jpg" width="10%">

								قارئ باركود سلكي                             - quickscan lite imager . usb cable                                                                                            </td>

							<td>واحد</td>

                            <td style="width: 80px; text-align:center; vertical-align:middle;">1.00 </td>

							<td style="text-align:center; width:100px;">
                                <!--                                -->
								0.00                            </td>
							<!--<td>0.00</td>-->
                            <td style="width: 100px; text-align:right; vertical-align:middle;">0.00</td><td style="width: 100px; text-align:right; vertical-align:middle;">15%</td><td style="width: 100px; text-align:right; vertical-align:middle;">0.00</td><td style="width: 120px; text-align:right; vertical-align:middle;">0.00</td>                           <!-- <td style="text-align:right; width:120px;">0.00</td> -->
                        </tr>
                                                <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;">12</td>
                            <td style="vertical-align:middle;">
                                79023717                            </td>
							<td style="vertical-align:middle;font-weight:bold;" dir="ltr">
														<img src="http://localhost/spark_erp/assets/images/free.jpg" width="10%">

								جهاز قارئ باركود و qr ثابت من نوع ماجلان ( magellan )                                                                                                                        </td>

							<td>واحد</td>

                            <td style="width: 80px; text-align:center; vertical-align:middle;">1.00 </td>

							<td style="text-align:center; width:100px;">
                                <!--                                -->
								0.00                            </td>
							<!--<td>0.00</td>-->
                            <td style="width: 100px; text-align:right; vertical-align:middle;">0.00</td><td style="width: 100px; text-align:right; vertical-align:middle;">15%</td><td style="width: 100px; text-align:right; vertical-align:middle;">0.00</td><td style="width: 120px; text-align:right; vertical-align:middle;">0.00</td>                           <!-- <td style="text-align:right; width:120px;">0.00</td> -->
                        </tr>
                                                <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;">13</td>
                            <td style="vertical-align:middle;">
                                95303808                            </td>
							<td style="vertical-align:middle;font-weight:bold;" dir="ltr">

								fish water                                                                                                                        </td>

							<td>واحد</td>

                            <td style="width: 80px; text-align:center; vertical-align:middle;">1.00 </td>

							<td style="text-align:center; width:100px;">
                                <!--                                -->
								 40.00                            </td>
							<!--<td>SR40.00</td>-->
                            <td style="width: 100px; text-align:right; vertical-align:middle;">0.00</td><td style="width: 100px; text-align:right; vertical-align:middle;">15%</td><td style="width: 100px; text-align:right; vertical-align:middle;"> 6.00</td><td style="width: 120px; text-align:right; vertical-align:middle;"> 46.00</td>                           <!-- <td style="text-align:right; width:120px;"> 46.00</td> -->
                        </tr>
                                                <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;">14</td>
                            <td style="vertical-align:middle;">
                                33124910                            </td>
							<td style="vertical-align:middle;font-weight:bold;" dir="ltr">

								كاسترول10w30                                                                                                                        </td>

							<td>واحد</td>

                            <td style="width: 80px; text-align:center; vertical-align:middle;">1.00 </td>

							<td style="text-align:center; width:100px;">
                                <!--                                -->
								 20.00                            </td>
							<!--<td>SR23.00</td>-->
                            <td style="width: 100px; text-align:right; vertical-align:middle;">0.00</td><td style="width: 100px; text-align:right; vertical-align:middle;">15%</td><td style="width: 100px; text-align:right; vertical-align:middle;"> 3.00</td><td style="width: 120px; text-align:right; vertical-align:middle;"> 23.00</td>                           <!-- <td style="text-align:right; width:120px;"> 23.00</td> -->
                        </tr>
                                                <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;">15</td>
                            <td style="vertical-align:middle;">
                                94932948                            </td>
							<td style="vertical-align:middle;font-weight:bold;" dir="ltr">

								Water 250ml                                                                                                                        </td>

							<td>واحد</td>

                            <td style="width: 80px; text-align:center; vertical-align:middle;">1.00 </td>

							<td style="text-align:center; width:100px;">
                                <!--                                -->
								 0.87                            </td>
							<!--<td>SR1.00</td>-->
                            <td style="width: 100px; text-align:right; vertical-align:middle;">0.00</td><td style="width: 100px; text-align:right; vertical-align:middle;">15%</td><td style="width: 100px; text-align:right; vertical-align:middle;"> 0.13</td><td style="width: 120px; text-align:right; vertical-align:middle;"> 1.00</td>                           <!-- <td style="text-align:right; width:120px;"> 1.00</td> -->
                        </tr>
                                                <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;">16</td>
                            <td style="vertical-align:middle;">
                                11407382                            </td>
							<td style="vertical-align:middle;font-weight:bold;" dir="ltr">

								Water 1.5 ltr                                                                                                                        </td>

							<td>واحد</td>

                            <td style="width: 80px; text-align:center; vertical-align:middle;">1.00 </td>

							<td style="text-align:center; width:100px;">
                                <!--                                -->
								 1.74                            </td>
							<!--<td>SR2.00</td>-->
                            <td style="width: 100px; text-align:right; vertical-align:middle;">0.00</td><td style="width: 100px; text-align:right; vertical-align:middle;">15%</td><td style="width: 100px; text-align:right; vertical-align:middle;"> 0.26</td><td style="width: 120px; text-align:right; vertical-align:middle;"> 2.00</td>                           <!-- <td style="text-align:right; width:120px;"> 2.00</td> -->
                        </tr>
                                                <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;">17</td>
                            <td style="vertical-align:middle;">
                                84533575                            </td>
							<td style="vertical-align:middle;font-weight:bold;" dir="ltr">

								Power Ede T150 server                                                                                                                        </td>

							<td>واحد</td>

                            <td style="width: 80px; text-align:center; vertical-align:middle;">1.00 </td>

							<td style="text-align:center; width:100px;">
                                <!--                                -->
								 3,391.30                            </td>
							<!--<td>SR3,900.00</td>-->
                            <td style="width: 100px; text-align:right; vertical-align:middle;">0.00</td><td style="width: 100px; text-align:right; vertical-align:middle;">15%</td><td style="width: 100px; text-align:right; vertical-align:middle;"> 508.70</td><td style="width: 120px; text-align:right; vertical-align:middle;"> 3,900.00</td>                           <!-- <td style="text-align:right; width:120px;"> 3,900.00</td> -->
                        </tr>
                                            </tbody>
                    <!--<tfoot>
                                            <tr>
                            <td colspan="6"
                                style="text-align:right; padding-right:10px;">مجموع                                (SR)
                            </td>

                            						<td></td>
						<td></td>
						<td style="text-align:right;">SR40,304.78</td>                            <td style="text-align:right; padding-right:10px;">SR309,018.35</td>
                        </tr>



                                                                                <tr>
                        <td colspan="9"
                            style="text-align:right; font-weight:bold;">المبلغ الكلي <br/> Total Amount                            (SR)
                        </td>

                        <td style="text-align:right; padding-right:10px; font-weight:bold;">SR309,018.35</td>
                    </tr>
                    <tr>
                        <td colspan="9"
                            style="text-align:right; font-weight:bold;">مدفوع                            (SR)
                        </td>

                        <td style="text-align:right; font-weight:bold;">0.00</td>
                    </tr>
                    <tr>
                        <td colspan="9"
                            style="text-align:right; font-weight:bold;">الرصيد                            (SR)
                        </td>

                        <td style="text-align:right; font-weight:bold;">SR309,018.35</td>
                    </tr>

                    </tfoot>-->
                </table>
                <div class="clearfix"></div>
            </div>



            <div class="row inv-details">

					<div class="col-xs-12">






                                              <p>
				        </p><table class="table table-striped jambo_table bulk_action print-table order-table pdftable right-side" dir="rtl">

                        <tfoot style="-webkit-print-color-adjust:exact !important; ">


                        <tr style="-webkit-print-color-adjust:exact;">



                        <td class="col-xs-2 td" style=" text-align:right;font-weight:bold;"> عدد الأصناف المباعة
                        </td>

                        <td class="col-xs-2 td" style="text-align:center; padding-right:10px; font-weight:bold;">17</td>


                        <td class="col-xs-2 td" style=" text-align:left;font-weight:bold;">Item Sold Total
                        </td>

                           <td class="col-xs-6 td " rowspan="3" style=" text-align:right;">

                               <!--  ملاحظات /Note<br> -->
                               <font style="float:right;font-weight:bold;">ملاحظات&nbsp;/&nbsp;Note  </font><br>
                               <font style="float:right;">  </font>


                        </td>



                    </tr>



	               <tr>


                        	                    <td class="col-xs-2 td" style=" text-align:right;font-weight:bold;">اجمالي المبلغ

                        </td>

                        <td class="col-xs-2 td" style="text-align:center; padding-right:10px; font-weight:bold;">SR268,713.57</td>


                        <td class="col-xs-2 td" style=" text-align:left;font-weight:bold;">Sub Total

                        </td>




                        </tr>

                        <tr>





                          <td class="col-xs-2 td" style=" text-align:right;font-weight:bold;">اجمالي الخصم

                        </td>

                        <td class="col-xs-2 td" style="text-align:center; padding-right:10px; font-weight:bold;">0.00</td>


                        <td class="col-xs-2 td" style=" text-align:left;font-weight:bold;">Total Discounts

                        </td>



				<!--
                        <td colspan="9"
                            style=" font-weight:bold;">إجمالي خصم العروض <br/> Total Discounts
                        </td>

                        <td style="text-align:right; padding-right:10px; font-weight:bold;">0.00</td>
                    -->
					</tr>


                      <tr>




							                    <td class="col-xs-2 td" style=" text-align:right;font-weight:bold;">اجمالي الضريبة (15%)
                        </td>

                        <td class="col-xs-2 td" style="text-align:center; padding-right:10px; font-weight:bold;">SR40,304.78</td>


                        <td class="col-xs-2 td" style=" text-align:left;font-weight:bold;">Total VAT (15%)
                        </td>
                        <td class="col-xs-6 td" rowspan="2" style=" text-align:right;font-weight:bold;">

                               <font style="float:right;">ثلاثمائة وتسعة  الف  و ثمانية وعشر ريال سعودي و خمسة وثلاثون هللة فقط لا غير </font> <br>
							    <font style="float:left;">Three Hundred and Nine Thousand and Eighteen Saudi Riyals point Three Five Halala</font>


						</td>
											   <!--

						<td colspan="9"
                            style=" font-weight:bold;">ضريبة القيمة المضافة <br/> VAT
                        </td>

                        <td style="text-align:right; font-weight:bold;">SR40,304.78</td>
						-->
                    </tr>
						<tr>



							                    <td class="col-xs-2 td" style=" text-align:right;font-weight:bold;">اجمالي الفاتورة

                        </td>

                        <td class="col-xs-2 td" style="text-align:center; padding-right:10px; font-weight:bold;color:red;">SR309,018.35</td>


                        <td class="col-xs-2 td" style=" text-align:left;font-weight:bold;">Total Invoice

                        </td>

						<!--
						<td colspan="9"
                            style=" font-weight:bold;">المبلغ الكلي <br/> Total Amount
                        </td>

                        <td style="text-align:right; padding-right:10px; font-weight:bold;">SR309,018.35</td>
						-->
                    </tr>


                    </tfoot>
                </table>

						<p></p>
                </div>
                </div>

             <div class="clearfix"></div>


             <div class="row">
                <div class="col-xs-12">




                        <p>
                     </p><table class="table table-striped jambo_table bulk_action print-table order-table pdftable">
				        <tfoot style="-webkit-print-color-adjust:exact !important;">

	                    <tr>



                        <td colspan="5" style=" text-align:right;font-weight:bold;">مدفوع
                        </td>

                        <td colspan="4" style="text-align:center; padding-right:10px; font-weight:bold;">0.00

                        </td>



                        <td colspan="4" style=" text-align:left;font-weight:bold;">Paid

                        </td>

						<!--
						<td colspan="9"
                            style=" font-weight:bold;">مدفوع </br> Paid
                        </td>

                        <td style="text-align:right; font-weight:bold;">0.00</td>
						-->
                    </tr>

												<tr>







					</tr><tr>


						                    <td colspan="5" style=" text-align:right;font-weight:bold;">الباقي

                        </td>


                        <td colspan="4" style="text-align:center; padding-right:10px; font-weight:bold;">SR309,018.35</td>


                        <td colspan="4" style=" text-align:left;font-weight:bold;">Remaining amount
                        </td>
											   <!--
                        <td colspan="9"
                            style="font-weight:bold;">الرصيد <br/> Balance
                        </td>

                        <td style="text-align:right; font-weight:bold;">SR309,018.35</td>
						-->
                    </tr>




                    </tfoot>
                </table>
            	<p></p>

                    </div>
                    </div>

                </div> <!-- end  border -->











            <div class="row footer_sign">
               <div class="col-xs-12 ">
                                <div class="col-xs-4 right-side" style="padding-bottom:10px;padding-top:10px;font-weight:bold;">
                    <p>التــوقيـــع&nbsp;<br>&nbsp;Signature  </p>
                </div>
				<div class="col-xs-4 text-center" style="padding-bottom:10px;padding-top:10px;text-align:center;font-weight:bold;">
                        <p>استلمت&nbsp;من&nbsp;قبل&nbsp;<br>&nbsp;Recived&nbsp;By   </p>
                </div>
				<div class="col-xs-4 text-left" style="padding-bottom:10px;padding-top:10px;font-weight:bold;">
                    <p>اعدت&nbsp;بواسطة&nbsp;<br>&nbsp;Prepared&nbsp;By  </p>
                </div>


                </div>
                </div>





			    <div class="row">
            		<div class="col-xs-12 pull-left">
                           <p class="text-center notorder"></p><center><div><br></div></center><p></p>					</div>
				</div>



     </div>';
//==============================================================
//==============================================================
//==============================================================

include("mpdf.php");

$mpdf=new mPDF('c');

$mpdf->SetDisplayMode('fullpage');

//$mpdf->SetWatermarkText('DRAFT');
//$mpdf->watermark_font = 'DejaVuSansCondensed';



$mpdf->showWatermarkImage = true;


$mpdf->SetWatermarkImage('tiger.png', 0.15, 'F');

//$mpdf->WriteHTML('<h2>Using a Watermark Image as Background</h2>');
$mpdf->WriteHTML($html);


$mpdf->Output();
exit;

//==============================================================
//==============================================================
//==============================================================


?>
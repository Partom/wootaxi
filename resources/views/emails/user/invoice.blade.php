<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>A simple, clean, and responsive HTML invoice template</title>
    
    <style>
    .invoice-box{
        max-width:800px;
        margin:auto;
        padding:30px;
        border:1px solid #eee;
        box-shadow:0 0 10px rgba(0, 0, 0, .15);
        font-size:16px;
        line-height:24px;
        font-family:'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        color:#555;
    }
    
    .invoice-box table{
        width:100%;
        line-height:inherit;
        text-align:left;
    }
    
    .invoice-box table td{
        padding:5px;
        vertical-align:top;
    }
    
    .invoice-box table tr td:nth-child(2){
        text-align:right;
    }
    
    .invoice-box table tr.top table td{
        padding-bottom:20px;
    }
    
    .invoice-box table tr.top table td.title{
        font-size:45px;
        line-height:45px;
        color:#333;
    }
    
    .invoice-box table tr.information table td{
        padding-bottom:40px;
    }
    
    .invoice-box table tr.heading td{
        background:#eee;
        border-bottom:1px solid #ddd;
        font-weight:bold;
        text-align:center;
    }
    
    .invoice-box table tr.details td{
        padding-bottom:20px;
    }
    
    .invoice-box table tr.item td{
        border-bottom:1px solid #eee;
    }
    
    .invoice-box table tr.item.last td{
        border-bottom:none;
    }
    
    .invoice-box table tr.total td:nth-child(2){
        border-top:2px solid #eee;
        font-weight:bold;
    }
    
    @media only screen and (max-width: 600px) {
        .invoice-box table tr.top table td{
            width:100%;
            display:block;
            text-align:center;
        }
        
        .invoice-box table tr.information table td{
            width:100%;
            display:block;
            text-align:center;
        }
        h1 {
    background-color: black;
    color: white;
}
    }
    </style>
</head>

<body>
    <div class="invoice-box">
        <hr>
        <h3 align="center" font-weight="bold">Woocabs INVOICE</h3>
        <hr>
        
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                       <td class="title" align="center">
                            <img src="{{ Setting::get('site_logo', asset('logo-black.png')) }}" style="max-width:300px;">
                        </td>
                        <tr align="center">
                            Bill No : {{$Email->payment?$Email->booking_id:''}}<br>
                            {{$Email->payment?$Email->payment->created_at->formatLocalized('%A %d %B %Y'):''}}<br>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr class="heading">
                          <td>  COMPANY DETAILS </td>
                        </tr>
                        <tr> 
                            <td align="left">
                                Name      : Woocabs<br>
                                Address   : Office Suite, No.242, Tricity Plaza, Peer Muchalla, Adjoining Sector 20, Panchkula.<br> 
                                Contact At: +919779390039<br>  
                                Email     : woocabs@gmail.com<br>
                            </td>
                        </tr>
                        <tr class="heading">
                          <td>  DRIVER DETAILS </td>
                        </tr>
                        <tr>
                            <td align="left">
                                
                                Name      : {{$Email->provider?$Email->provider->first_name:''}} {{$Email->provider?$Email->provider->last_name:''}}<br>
                                Id: {{$Email->provider_service ? $Email->provider_service->provider_id:""}}<br>
                                Service No: {{$Email->provider_service ? $Email->provider_service->service_number:""}}<br>
                                Car Model : {{$Email->provider_service ? $Email->provider_service->service_model:""}}<br>
                                Email     :contact@WooCabs.com
                            </td>
                        </tr>
                        <tr class="heading">
                           <td> CUSTOMER DETAILS </td>
                        </tr>
                        <tr>
                              
                            <td align="left">
                            {{$Email->user?$Email->user->first_name:''}} {{$Email->user?$Email->user->last_name:''}}<br>
                               {{$Email->user?$Email->user->email:''}}<br>
                               {{$Email->user?$Email->user->mobile:''}}<br>
                               
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="item">
                <td data-link-style="text-decoration:none; color:#ff646a;" data-link-color="Content Link" mc:edit="desctiption" width="200" align="left" valign="top" style="font-family: 'Open Sans', Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">Pickup Address</td>
                 <td data-link-style="text-decoration:none; color:#ff646a;" data-link-color="Content Link" mc:edit="value-3" width="200" align="left" valign="top" style="font-family: 'Open Sans', Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">{{$Email->s_address}}</td>    
            </tr>
            <tr class="item">
                <td data-link-style="text-decoration:none; color:#ff646a;" data-link-color="Content Link" mc:edit="desctiption" width="200" align="left" valign="top" style="font-family: 'Open Sans', Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">Drop Address</td>
                 <td data-link-style="text-decoration:none; color:#ff646a;" data-link-color="Content Link" mc:edit="value-3" width="200" align="left" valign="top" style="font-family: 'Open Sans', Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">{{$Email->d_address}}</td>
            </tr>
            <tr class="item">
                <td data-link-style="text-decoration:none; color:#ff646a;" data-link-color="Content Link" mc:edit="desctiption" width="200" align="left" valign="top" style="font-family: 'Open Sans', Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">Trip Open Time</td>
                 <td data-link-style="text-decoration:none; color:#ff646a;" data-link-color="Content Link" mc:edit="value-3" width="200" align="left" valign="top" style="font-family: 'Open Sans', Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">{{$Email->user_started_at}}</td>
            </tr>

            <tr class="heading">
                <td>
                    DISCRIPTION
                </td>
                
                <td>
                    AMOUNT
                </td>
            </tr>
            
            <tr class="item">
                <td data-link-style="text-decoration:none; color:#ff646a;" data-link-color="Content Link" mc:edit="desctiption" width="263" align="left" valign="top" style="font-family: 'Open Sans', Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">Base Fare</td>
                 <td data-link-style="text-decoration:none; color:#ff646a;" data-link-color="Content Link" mc:edit="value-3" width="87" align="right" valign="top" style="font-family: 'Open Sans', Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">{{Setting::get('currency','INR')}}{{$Email->payment?$Email->payment->fixed:''}}</td>
                
            </tr>
            
            <tr class="item">
                <td data-link-style="text-decoration:none; color:#ff646a;" data-link-color="Content Link" mc:edit="desctiption" width="263" align="left" valign="top" style="font-family: 'Open Sans', Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">Distance Fare</td>
                                                
                <td data-link-style="text-decoration:none; color:#ff646a;" data-link-color="Content Link" mc:edit="value-3" width="87" align="right" valign="top" style="font-family: 'Open Sans', Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">{{Setting::get('currency','INR')}}{{$Email->payment->distance }}</td>
            </tr>
            
            <tr class="item">
                <td data-link-style="text-decoration:none; color:#ff646a;" data-link-color="Content Link" mc:edit="desctiption" width="263" align="left" valign="top" style="font-family: 'Open Sans', Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">Day Allwance Fare</td>
                                                
                <td data-link-style="text-decoration:none; color:#ff646a;" data-link-color="Content Link" mc:edit="value-3" width="87" align="right" valign="top" style="font-family: 'Open Sans', Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">{{Setting::get('currency','INR')}}{{$Email->payment->day }}</td>
            </tr>
            
            <tr class="total">
                <td data-link-style="text-decoration:none; color:#ff646a;" data-link-color="Content Link" mc:edit="desctiption" width="263" align="left" valign="top" style="font-family: 'Open Sans', Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">Tax Fare</td>
        
                <td data-link-style="text-decoration:none; color:#ff646a;" data-link-color="Content Link" mc:edit="value-3" width="87" align="right" valign="top" style="font-family: 'Open Sans', Arial, sans-serif; font-size:14px; color:#3b3b3b; line-height:26px;  font-weight: bold;">{{Setting::get('currency','INR')}}{{$Email->payment->tax }}</td>
            </tr>
            
        </table><hr>
        <tr data-link-style="text-decoration:none; color:#ff646a;" data-link-color="Content Link" mc:edit="total title-1" align="center" valign="top" style="font-family: 'Open Sans', Arial, sans-serif; font-size:12px; color:#3b3b3b; line-height:26px; text-transform:uppercase;line-height:24px;">Total
        </tr>
        <tr  data-link-style="text-decoration:none; color:#ff646a;" data-link-color="Content Link" mc:edit="total content-2" align="center" valign="top" style="font-family: 'Open Sans', Arial, sans-serif; font-size:24px; color:#3b3b3b;  font-weight: bold;">{{Setting::get('currency','INR')}}{{$Email->payment->total }}
        </tr>
        <hr>
        <table data-thumb="note.jpg" data-module="Note" bgcolor="#ffffff" align="center" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <table align="center" width="600" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td height="30"></td>
                    </tr>

                    <!-- title -->
                    <tr>
                        <td data-link-style="text-decoration:none; color:#ff646a;" data-link-color="Content Link" mc:edit="note title" data-color="Title" data-size="Note Title" data-min="13" data-max="20" style="font-family: 'Open Sans', Arial, sans-serif; font-size:16px; color:#3b3b3b; line-height:26px;  font-weight: bold; text-transform:uppercase">NOTES</td>
                    </tr>
                    <!-- end title -->
                    <tr>
                        <td height="5"></td>
                    </tr>

                    <!-- content -->
                    <tr>
                        <td data-link-style="text-decoration:none; color:#ff646a;" data-link-color="Content Link" mc:edit="note content" data-size="Note Content" data-min="13" data-max="18" style="font-family: 'Open Sans', Arial, sans-serif; font-size:13px; color:#7f8c8d; line-height:26px;">
                            THIS IS A COMPUTER GENERATED INVOICE AND DOES NOT REQUIRE ANY SIGNATURE. PLEASE CONTACT ADMINISTRATOR FOR MORE DETAILS.
                        </td>
                    </tr>
                    <!-- end content -->

                    <tr>
                        <td height="15" style="border-bottom:3px solid #bcbcbc;"></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <!-- end note -->

    <!-- footer -->
    <table data-thumb="footer.jpg" data-module="Footer" bgcolor="#ffffff" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">

    <tr>
        <td height="15"></td>
    </tr>

    <!-- copyright -->
        <tr>
            <td data-link-style="text-decoration:none; color:#7f8c8d;" data-link-color="Copyright Link" data-color="Copyright" data-size="Copyright" mc:edit="copyright" align="center" style="font-family: 'Open Sans', Arial, sans-serif; font-size:13px; color:#7f8c8d; line-height:30px;">
                © 2017
                <span style="color:#000; font-weight: bold;">{{Setting::get('site_name','WooCabs')}}</span>
                . All Rights Reserved.
            </td>
        </tr>
        <!-- end copyright -->

        <tr>
            <td height="15"></td>
        </tr>

    </table>
    </div>
     
</body>
</html>





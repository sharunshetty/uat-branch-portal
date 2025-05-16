<?php

/**
 * @copyright   : (c) 2022 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** No Direct Access */
defined('PRODUCT_NAME') OR exit();

if(isset($_POST['ekycNum']) && $_POST['ekycNum'] != "") {
    $ekycInfo = $safe->str_decrypt($_POST['ekycNum'], $_SESSION['SAFE_KEY']);
}

if(isset($_POST['reqid']) && $_POST['reqid'] != "") {
    $requestID = $safe->str_decrypt($_POST['reqid'], $_SESSION['SAFE_KEY']);
}

if(isset($_POST['asnVal']) && $_POST['asnVal'] != "") {
    $ass_ref_num = $safe->str_decrypt($_POST['asnVal'], $_SESSION['SAFE_KEY']);
}

if(isset($_POST['ekycOtp']) && $_POST['ekycOtp'] != "") {
    $safe = new Encryption();
    $ekyc_otp = $safe->rsa_decrypt($_POST['ekycOtp']);
}

if(!isset($_POST['ekycOtp']) || isset($_POST['ekycOtp']) == NULL || isset($_POST['ekycOtp']) == "") {
    echo "<script> swal.fire('','Enter valid OTP Code'); loader_stop(); enable('sbt2'); </script>";
    exit();
}
elseif(!isset($ekycInfo) || $ekycInfo == false) {
    echo "<script> swal.fire('','Unable to process your request (E01)'); loader_stop(); enable('sbt2'); </script>";
    exit();
}
elseif(!isset($ass_ref_num) || $ass_ref_num == false) {
    echo "<script> swal.fire('','Unable to process your request (E02)'); loader_stop(); enable('sbt2'); </script>";
    exit();
}
elseif(!isset($requestID) || $requestID == false) {
    echo "<script> swal.fire('','Unable to process your request (E03)'); loader_stop(); enable('sbt2'); </script>";
    exit();
}
// elseif(!isset($_SESSION['USER_REF_NUM']) || $_SESSION['USER_REF_NUM'] == NULL || $_SESSION['USER_REF_NUM'] == "") {
//     echo "<script> swal.fire('','Unable to validate your request (E04)'); loader_stop(); enable('sbt2'); </script>";
// }
// elseif($plain_arn_val != $_SESSION['USER_REF_NUM']) {
//     echo "<script> swal.fire('','Unable to process your request (E05)'); loader_stop(); enable('sbt2'); </script>";
// }
else {

    $updated_flag = true;

    $sql1_exe = $main_app->sql_run("SELECT ASSREQ_REF_NUM, ASSREQ_MOBILE_NUM FROM ASSREQ_MASTER WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM", array( 'ASSREQ_REF_NUM' => $ass_ref_num));//$_SESSION['USER_REF_NUM']
    $item_data = $sql1_exe->fetch();

    if(!isset($item_data['ASSREQ_REF_NUM']) || $item_data['ASSREQ_REF_NUM'] == NULL || $item_data['ASSREQ_REF_NUM'] == "") {
        echo "<script> swal.fire('','Unable to validate your request (R01)'); loader_stop(); enable('sbt2'); </script>";
        exit();
    }

    //Aadhaar Verify OTP Code
    $send_data = array();
    $send_data['METHOD_NAME'] = "getAadhaarInfo";
    $send_data['OTP'] = $ekyc_otp;
    $send_data['AADHAAR_NUMBER'] = $ekycInfo;
    $send_data['REQ_ID'] = $requestID;
    $send_data['CHANNEL_CODE'] = API_REACH_MB_CHANNEL;
    $send_data['USER_AGENT'] = $browser->getBrowser();
    
    try {
        $apiConn = new ReachMobApi;
        //$output = $apiConn->ReachMobConnect($send_data, "120");
        // Test Data
        $output = json_decode('{
            "fatherName": "D/O Vinod Mirgal",
            "country": "India",
            "pincode": "400060",
            "image": "\/9j\/4AAQSkZJRgABAgAAAQABAAD\/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL\/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL\/wAARCADIAKADASIAAhEBAxEB\/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL\/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6\/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL\/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6\/9oADAMBAAIRAxEAPwDuwvtThxS0orQzACngUgFOWgBwHFOC+lCin5A68UAN2Z7VFLhBubGAcmuY8Q\/EnQNBdoTci5uQSDFb\/OVx6nOPwzmvPtR+MF\/M7\/YNNhjGPledi7A+owQB9OaLodj2IyIeVOfpSblJ4IJ9Bya8Dm+IXiOYtv1IIvZY4UH64zUEfjXW1+7qc+fUHmldDsfQewkUwx+leJ2XxK8R2o\/4+IroA8idAf1XFdFZfF6FsLqGnSRHoWgcOPrg4P8AOndBZnoxSoypFY+n+MdG1WPda3kb46qflYfUHmtFdStn6SL+dAiWlBxzTBPG44YUuQe9MCcODTuCO1V84pdxx1oAlK46U0nmgSZxSnBNAFqnDgU0GlzzQJj6d34pgNQ3V0lpbvNKyqiKWZmPAFAC3uo2um2kl1dzLFDGNzM3AArw\/wAYfEPUvEjzWmms9ppRypIOHmHufQ\/3fzzUXjDxRP4mvHG8ppkTfu4+nmH+83+ePxJrjp7pXOB8qDoBxUNlJAI0U8t0\/E\/\/AFqk+VMFgiKRxkZJ\/Oq\/nADEYCgd+9SxKGYu7ZPv0qShd4Kko2R6+lRPcMpx8rY9Rmn3LcDEjY7A1SZucZ\/SmA5p3LZzg+1KJ8jk4I\/Wq5OTRQBYS5aNw8bsrDowOCK6TS\/F9zARHdszqOkg+9\/9euTpRnFAHrthrzyxrJDPvU963rTxGwwJRXjGkalJZXS7WOxjhl7V3dvdpJGDnFF2hWPRrfV4pv4qvpKrKCDXm0Vw6HcrGt7TdXOQrk1SkKx12\/kUoaq0MomQMKkqhGmGp4OajAp3QUCHE4Ga8t+K\/iOSC2h0qF9pny0gB5KA8fgTn64r1A8rgjj6Zr5u8e6qdV8ZahMGDRI\/lR46bV4yPr1\/GlLYaRiT3TOvlhjtHX3NV1Uu3Xj3phOeKem8\/KgOc9RWZZNvjiwqYJzySM1d8uI2+8M7zHoBUUGlu4DEkGrsmn3klusauuwDn5jzU88e5fs5djHliZSS7KD\/AHQRn9KfFYzzEEIQD0JFbmnaPHA4klIdvccCtWRFGDkcd6ynWtojWFG+sjnotGUD94c\/jSmxgT\/lmP51sSAEZzVRwMdPzrLnk3ubezijNktYwOFFQmzRuASDV6fGCBVYHBzW0JGNSKKDxPbyAkcg\/nXU6RdJPEWBfK8HLVjTAS25PG4cijRrryLwK33ZPlPsa2MLHZxzkd60LWUs67etYytir1nN5Uqn3oEelaSrfZl3A9KvstZ2jXazWq4POK1uorQgtLTqaFINP6imBT1W6ey0i7ugQGhhaQE9MgZFfK8hyxPevqLxMjN4T1hEUsxsZwoHXOw18ty8OR71Mioje3HetTT7ZyN5yF4puk2YuHLvyi9vetrz7aJthkXI7A5xXPUk9kb04rdj7dNwwtWRbzHuePQUttf2QxlwCfatGC+tZ5VRZBuI4AxXK1K+x2Rce5mxwyFjvVgfWpTAeeSB6VsxwxTrtBBP1qB7Pb8qnpjgiou7miSMpoQchRk1TmgbH3SD6VvJbFJSSeKjukijG6UhR6ngVWpLS3OVkhcE1VZCD0rXnu7Isf3qjn8DWfcXFsOhP5VvBvsc07dGV3B8pscVThYpcIwPRgeK0IWjmyqkEVRnj8qYj8a6UcsjsIpAwHrVmNzkVlQvgKfar8T5xQI7jwze4YITXbKeAa8w0SUpdJivTbRg8CmriSzSIpp4BNSkdqYRzTEDAMjZAbjlTzmvmDxX4fn8O65cWMwbYrExO38aZOD+nPvX05ntg+5riviTpWkaho8c+ozrbzREiFwCS57rwCcfhxSlsNHj2mWzyaTsjO1pHIJ9qvQ6DHGhJYZ9+lT2cCWenJHHMJQGbbIoIDDPXB57VSur64MqRRK7sxwFUZJrjm5c1onXCMeW8iSS08kELIrcdGHSqkaN5gJlAwc8Doakvn1HTWjiuZI4fNjLgl3YcA\/Kdvc4A9OecCqtkL2\/SSVVDLHgkZ5P0qrTtdsV43sjoLO5EMwkY7h2wcVrPdeaxZAMYxXORRSpGshB2McAkVs2LKsbCZye4zXNNu51w20FkvBG218cfe5rB1a7F0+0SbVXoPeo9Su8TMoJwTVO0tRdyM883lRKQM4yzE9Ao7mtKcXuzOpO65SsInaRdrKcHoastp6zffkA+g5NaWoRzaNHB\/onkLOheNnl5wBnkDueKyFvru6EjKhIQZOD2\/Gt\/etdHP7qdmD2QiT90zA+uaivAXaAtje3BqWK8DqQ33qJR5j2xGOGOf51UW9mRNR6GpGcKB6VZjbmqampkPHWrMze064EcynNelaNfrNCoz2rySByGGK7zwsXbGScU4iZ6S3SojT1bNI9aEkRri\/iRY+foltcom6SC4ALf3UYEH82CfpXYk9ap6nZR6jptxZy\/dlQrnGcHqD+BAP4VMo3Viou0kzxUoI0ChRjHIqOS18wKyLgj0qzLEVnKMQCpwavQwgKMDPvXmVJcrPRpx5kZc0dxeWogu4fPRfu7gMj8cZ\/WktrK4tovKA8mDnIzgn8hmuhRURdzvtA7AZNVZcT5CDCep6mn7Wy1K9kmzKuAjbVhUrGvqTyf6U6JCQ27OSOlFyQjbFAwDSwxyOMryccVk25amqSWhgavEEcMe1R6XLDLJtnjDr06kY\/KrmqQMUJPbtWJazeTcYb7pPNdVPWBy1dJnVasZr+32yySTDJbMshc5+rZI\/OsTZPBCYYkVFJ5wOT+Na0M\/ygNyOxqSRBtzwc1PtGivZp6nPC3wSXGWPc1Hcj\/RiB0Vga1LlQvPFZ85H2Zh1LEKPxrem76nPONixZsxtkJ7jNW0bFV4V8uJV9ABUwNamBftTl1B9a9N8MRKLcNxXl1u2HBr0vwvODCF9qqImdyjY71IxyKqhqkD1ZAh60009lJGeKiYMO4\/KmM8m8T2q2PiC4SNSIywdQffnH55H4VVhu1C45BGK2fH8LJrMUhHyPCp3DuQTn+n51y8R6\/MB7152IiuY9DDz902C25hk5FWEjDR8DA71liQrtAOO5Jq39o\/dYzkd+a515nUZ89obmUvuxH2ycVcsjbwMAcOAeRntWLeIv2nzZAJLdOCrthRnt71JbS2U6MtmBC3XjoarkbF7RJkmuz2pkcRrgMeAewrk57M\/M6nnrUupvI8\/7+XAzwq1YOJrNI4ERSBy\/mEk10QXIjmqSU5D9NuxPEIyMOo596vtKAvQ4rNtIEtFPzbnPenNKW9jUOF3oNTaVmJduc4zmqvlmSSIE8Bt5pzuX61NAuS35V0U1ZHPUd2Sg4+lOFAHrS1oYk8JO6vRPCxIUcdq8+t1y6j1Nek+G4tsINVETOz7UoODQVwKQYFWSWEYGh09KjBxUqsCOaAOM8fWfm6XDcYyYpNp+jf8A1wPzrzVSQCM17brdh\/aWj3VqPvvGdn+91H6gV4tICGKshR1OGUjkGuavHqdNCVtBvnBfvZAFZV5rcryFYAFQcAk9auXgbyDtPJGKyV06RCJ\/LDHGCCehrKEI7s2nOWyI5Y7q9HzMTz0Jq9BpV3GgeORGYHlN2Dim20DyXGZjJtPXygAR9M1sy29gsSLG+oRPjlnCsCfoMVpbQhRbdzm7+3aVsllJA7VmYlt2BViPpW9JaxqGLTyM3XATAH+NZVwm35VYsD\/eGP61UexE46jobtioEg\/EVMXLY5qrJZhEV1bORzip4vugUcq6C5pdSVBlgT0FW0GF\/WoY0ycDn1q0FqkrEN3EFPC0oUntUipzTJLemwGWdRjpXqGjW5htVNcP4ftPMnU4716RbR7IlGOMVcUJmwV4qLGGqw4wagfrVEi5qRcVADg1IrZ5oAlIrxrxyUg8XXfljHCFgPUqDz+dex7q8a8aESeL9QyARuQf+OLWdV2ia0leRjGQSRDj86eACm1untVEs0EoVvuHoSelWlbcAQc\/SuSS6o6YvWzJY5EgcMTkVbm1SyDL84XBBxis4xl2KYOcZyelZF8PLkBAGAPzqo67lObitDSvtQtpmxCBux25NZDrvbe3T0qG2IMu4nLdSKsy8nAxirtZmcpuW5E54wO1CkYyTUbkDrTYm8yVV7Z5rSKMWzWtItkW5\/vNz+FWRimD0pwpkjwBmpYgGcL3NQDk1p6ZbebOMChCOx8M2YADYrrwMAVm6PbCG3XjBxWttGK0SEaD81C9PY1GTmmQRmgNg0jEYJJwB1JrJ1HX7HTQ3mzBnVPMKLyQvr+Pb17dKG7DRtB8CvFPEk\/n+J9SkyTi4dfyOP6Vr6v49v76JoLRRaRNwXRsuR7Ht+H51yi\/eyAMVzVZpqyOilFp3ZaNvHdQFHXnHBFY000unT+W5bZ2Jrct24pNQto7mDDjJ7GueM7aPY6JQvqtzF\/tDcODg881Vurvft4G5cEGkudKnt8tG25fXOCKzWMiE545rpiovVHPJyWjLKFcbsgc8Uj3GDxwaqh29TSjkknqappEXY9pC7ZNWrFS1wn1qmBk8VdtJPJbzCCwXnAoQzcxzSjg0iMk0AuIW3R9Cf7p9DSgmmQPjGSOO9db4es9zqcVzllCZJBjmvQNCt\/LjDEVUQZ0UC7IwBU+SKgRuMUpb6VZBLdarZ2svlSTqZ8bhCgLyEeu0ZOPwrFufGECGP7PY392sjFQbODzhx7g7fwySO4FaVtoNlFH5S2sKwlxJ5YQbQw53AdAc96244URDjHC4ApXHYzdBkk1OKe5n068tVUqIxdBQzcZJ2jp2615Ldrc6l4h1WGJx5txeSREsBgoGZVGccY+T0HHNe92aq0EgHaTBwfYV4Tq8K2vjHV7Uqp2XbOQVB4k+buCP4jWNZtI1pJNtHOspxuxgEdKVOgq1ct5zyXA3bZWLjOc88\/p0z7VAFIHFc9zoS6ksT4IzxVvcGXrWaWIqZJCRgE1DNEyVkxkYBrOubWGTIZB+VXWlNVpXHeiLaFJJmTJaQq3C1H9jVlJ5FXX5bpTGyBgcVspMxcUUDbBT1pVG0Yqww5qHqatMho6HwhbmZdRiD4YIrg4zjnGf1Fa7aNqkshgGk21wVUkywTiInn0bPPT1FQeBV\/e6hkknyMAH\/eWujPinSrO\/aKS5MU0D7CHjYBiMBhnGMZrWOqM2rHMxw39lcELY3aSL99Gj3gf8CXI\/lXTaT4tgjAiuoTGw647fUVt31\/o0tlHfR6nZxOy5UtMoz+vNZ8M+na1GYw9vMwHIRg2PyqttiTobW\/t7tA0MquPY1Z3CuKm0U2z77SV4W\/2STTo9Z1WxGJ4xcIP4hyR\/Wi4rF6HSPiTAozr2msg7MmSP\/IX9a07fwbe65HnxPrEuoFeY7WFRDEvqTtxuPocDHPrXRazr2kaFCn9pXsUBccKTlm9woyf0rEX4leEraJpE1OSVlUnyktpAzH0BZQM\/U0D1Ol8M6FYaBbPa6fE0UUzea6mRmzJwCeSccY6eleZfFDTG0vxbb6qEP2a\/jEUpA4Ei9M\/hj8jW9D4\/wBa1GTztI8J3s1qWDxTSMRvHfouASPQmu013Q7Txb4de1uYnRJlDoWXDwv1BwejDv8AiOlTKPMrDi+V3PC9Qm+1GO5eQNJISJGyCxJ5GR14O7knuBjrVZYcrVqTQLjT9Rk0nUh5NyCFWYg7CM8Oe5XIB4BP3hjNZUU02Axz071xzTOym0yWWE5NQAMufSriTb8ZFO8lW5XHNRcuxmmbHeomfOakeLFw0Zq3Fp+5dxxgVSaQtTO2E8gVA4JNbFxCIoDsGWPAqlHAD9481SkS4lJoziolQeZz61pPFg4ABq5oXh6517VEtIFIT700oHEadz9fQf4Gqi76EyVjrvAukldGa4YEG7l4J\/uKSOPxz+Vbeo21veTlJ4I5UJzskQMPyNaeoaBE2iiyhnuLOFFVQbZ9jBR2B\/nXFX+garpU8Mmk6vMxlZiUvGDrnHGOOOM11RVkcrd2adt4V8PNqAnn02NgSDtVmVQf90ED8MVpa74M0We3WSHTbaNRz+5QRn81xXMrrniTT3V9T0HzI1PL2pzkeuAT\/StEfEuJ7f7LZ6JqFxckYEbqF\/luP6UxFOLwPorH5knGf+mpqb\/hDLSFP9FvdQtxnOI7gjNV\/wDhIdZs2H23w1dr5hygjJJx9NuRUv8AwnVvboVvNJ1OFvQwjH6sKBnZ6f8AD7QbNA15CdSu5Tumurti7OfpnA\/n6k1uw+FfD9sY5ItE01JEIZHW1TKkdCDjOaKKBGq5+ZCTweOtXU4+Yc5HzD19xRRQIyde8PQa1bZ3COcAmKZUDFDweh4IyBweuPpjxrXvDlxomoNFc25jR2zG6j5G7kKfbkYPOBnnrRRWVWKcTai7SMg25Q8fjSMAozyDRRXEdhSmjLyh1OD71PEZCNu7AoooAkkUEetUzGwPTFFFMDc0DwpqGvzAQr5VsDh7h1+Vfp\/ePsPxIr1vRtAstGsltbJNq8GSRuWkPqT3oorqpRSVzkrSbdiDUr+zxdRfaYV8pDkGQZzWDr1zZDS7YC6gF0u1kTzBubHoM80UVuYli3Cz2+4dCBjNaVg2JCM8DiiigB2u2+bVbpBh4TuH071C0KyQpPH\/ABDNFFIaP\/\/Z",
            "gender": "M", 
            "husbandName": "bhai",
            "houseNumber": "2/19 Kunti Devi Bldg",
            "responseCode": "S",
            "dob": "1997-03-05",
            "street": "Gumpha Road Ramwadi Agarwal Nagar",
            "subdistrict": "Mumbai",
            "district": "Mumbai",
            "name": "Nithin",
            "vtcName": "Mumbai",
            "state": "Maharashtra",
            "maskedAadhaarNumber": "XXXX XXXX 0908",
            "combinedAddress": "2/19 Kunti Devi Bldg, Gumpha Road Ramwadi Agarwal Nagar, Mumbai, Mumbai, Mumbai, Jogeshwari East, Maharashtra, India, 400060",
            "postOffice": "Jogeshwari East"
        }', true);
            
    } catch(Exception $e) {
        error_log($e->getMessage());
        $ErrorMsg = "Technical Error, Please try later"; //Error from Class    
    }

    if(!isset($ErrorMsg) || $ErrorMsg == "") {
        if(!isset($output['responseCode']) || $output['responseCode'] != "S") {
            $ErrorMsg = isset($output['errorMessage']) ? "Error: ".$output['errorMessage'] : "Unexpected API Error";
        }
    }
    
    if(isset($ErrorMsg) && $ErrorMsg != "") {
        echo "<script> swal.fire('','{$ErrorMsg}'); loader_stop(); enable('sbt2'); </script>";
        exit();
    }

    if(isset($output['dob']) && $output['dob'] != "") {
        $dob = $output['dob'];
        $diff = (date('Y') - date('Y',strtotime($dob)));
        if($diff < "18") {
            echo "<script> swal.fire('','Minors are not allowed to open Online account, Please visit branch'); loader_stop(); disable('sbt2'); </script>";
        }
    }
    
    // if((isset($output['fatherName']) && $output['fatherName'] == "") || (isset($output['husbandName']) && $output['husbandName'] == "") || (isset($output['relativeName']) && $output['relativeName'] == "")) {
    //     echo "<script> swal.fire('','Father or Spouse is missing in online validation of UID, Cannot proceed. Please update your UID and try again'); loader_stop(); enable('sbt2'); </script>";
    //     exit();
    // }

    // if (str_contains($output['fatherName'], 'C/O')) {
    //     echo "<script> swal.fire('','Father or Spouse is missing in online validation of UID, Cannot proceed. Please update your UID and try again'); loader_stop(); enable('sbt2'); </script>";
    //     exit();
    // }    

    // if (str_contains($output['husbandName'], 'C/O')) {
    //     echo "<script> swal.fire('','Father or Spouse is missing in online validation of UID, Cannot proceed. Please update your UID and try again'); loader_stop(); enable('sbt2'); </script>";
    //     exit();
    // }    

    if(isset($output['pincode']) && $output['pincode'] != ""){
        $output['pincode'] = '144040';
        $totalResults = $main_app->sql_fetchcolumn("SELECT count(0) FROM SBREQ_PINCODE_DATA WHERE PIN_CODE = :PIN_CODE AND STATUS = '1'", array("PIN_CODE" => $output['pincode']));
        if($totalResults == 0){
          echo "<script> swal.fire('','Branch doesnt exist for your location'); loader_stop(); enable('sbt2'); </script>";
          exit();
        }    
    }


    $doc_sl = $main_app->sql_fetchcolumn("SELECT NVL(MAX(DOC_SL), 0) + 1 FROM ASSREQ_EKYC_DOCS WHERE ASSREQ_REF_NUM = :ASSREQ_REF_NUM AND DOC_CODE = 'AADHAAR'", array("ASSREQ_REF_NUM" => $item_data['ASSREQ_REF_NUM'])); // Seq. No.
    if($doc_sl == false || $doc_sl == NULL || $doc_sl == "" || $doc_sl == "0") {
        echo "<script> swal.fire('','Unable to generate detail serial'); loader_stop(); enable('sbt2'); </script>";
        exit();
    }
    
    // Save eKYC Record
    $data = array();
    $data['ASSREQ_REF_NUM'] = $item_data['ASSREQ_REF_NUM'];
    $data['DOC_CODE'] = 'AADHAAR';
    $data['DOC_SL'] = $doc_sl;
    // $data['DOC_DATA'] = json_decode(stream_get_contents($output), true, JSON_UNESCAPED_SLASHES);       
    $data['DOC_DATA'] = json_encode($output, true);
    $data['CR_BY'] = isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : NULL;
    $data['CR_ON'] = date("Y-m-d H:i:s");

    $db_output = $main_app->sql_insert_data("ASSREQ_EKYC_DOCS", $data); // Insert
    if($db_output == false) { $updated_flag = false; }

    if($updated_flag == false) {
        echo "<script> swal.fire('','Unable to update e-KYC record (E01)'); loader_stop(); enable('sbt2'); </script>";
        exit();
    }

    // Update main table-store ekyc data
    if($updated_flag == true) {
        $data2 = array();
        $data2['ASSREQ_EKYC_FLAG'] = "Y";
        $data2['ASSREQ_EKYC_UID'] = $safe->str_encrypt($ekycInfo, $item_data['ASSREQ_REF_NUM']);
        $data2['ASSREQ_CUST_DOB'] = isset($output['dob']) ? $output['dob'] : NULL;
        $data2['ASSREQ_EKYC_NAME'] = isset($output['name']) ? $output['name'] : NULL;
        $db_output2 = $main_app->sql_update_data("ASSREQ_MASTER", $data2, array( 'ASSREQ_REF_NUM' => $item_data['ASSREQ_REF_NUM'] )); // Update
        if($db_output2 == false) { $updated_flag = false; }
    }

    if($updated_flag == true) {

        $data3 = array();
        $data3['ASSVAL_REF_NUM'] = $item_data['ASSREQ_REF_NUM'];
        $data3['ASSVAL_EKYC_UID'] = $ekycInfo;
        $data3['CR_BY'] = isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : NULL;
        $data3['CR_ON'] = date("Y-m-d H:i:s");
        $data3['AUTH_STATUS'] = "P";

        $db_output3 = $main_app->sql_insert_data("ASSVAL_UIDDETAILS", $data3); // Insert
        //if($db_output3 == false) { $updated_flag = false; }
    
    }


    if($updated_flag == false) {
        echo "<script> swal.fire('','Unable to update e-KYC record (E02)'); loader_stop(); enable('sbt2'); </script>";
        exit();
    }

    // Success
    $main_app->session_remove(['APP_TOKEN']); // Remove CSRF Token
    $sid_assref_num = $safe->str_encrypt($ass_ref_num, $_SESSION['SAFE_KEY']);
    $go_url = "ass-form-aadhaarview?ref_Num=".$sid_assref_num; // Page Refresh URL
    echo "<script> goto_url('" . $go_url . "');</script>";
   
}

?>
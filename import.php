<?php  require_once "inc/languages.php"; ?>
<?php
date_default_timezone_set('Africa/Cairo'); 
$titx="SAIF Admin | {$a[21][$lang]}";
$bred="{$a[21][$lang]}";
$tags="";
$pgx=0;   //13
?>
<?php
function clean ($str) {
	$sauf = array("%","#",'"',"/","<",">","&","*","@","^","?","!","$","[","]","|","{","}","+","~","(",")");
	$repw = array(" "," "," "," "," "," "," "," "," "," "," "," "," "," "," "," "," "," "," "," "," "," ");
	$str=mysql_real_escape_string(strip_tags(trim(stripslashes(str_replace($sauf,$repw,$str)))));
	return $str;
}
if(isset($_POST['download'])){
$file = tempnam("tmp", "zip");

$zip = new ZipArchive();

// Zip will open and overwrite the file, rather than try to read it. 
$zip->open($file, ZipArchive::OVERWRITE);
$zip->addFile('log/Log.txt');
$zip->close();
// Stream the file to the client 
header("Content-Type: application/zip"); 
header("Content-Length: " . filesize($file)); 
header("Content-Disposition: attachment; filename=\"Log".date('d-m-Y g-i-s').".zip\""); 
readfile($file); 
unlink($file);
unlink('log/Log.txt');
exit;
}
?>
<?php require_once "inc/header.inc.php"; ?>
        <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <?php require_once "inc/sidebar.inc.php"; ?>
            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                     <h1>
                        <?=$bred?>
                           <small><?=$tags?></small>
                    </h1>                
                </section>

                <!-- Main content -->
     <section class="content">
<?php             
if(isset($_POST['submit'])) {
$err=array();
$paatt="#0+ EGYPT#i";

if(isset($_POST['bnk']) and ($_POST['bnk'] != -1)) {
$bnk=$_POST['bnk'];
}else {
 $err[0]=$a[50][$lang];	
}
if(isset($_POST['cert']) and ($_POST['cert'] != -1)) {
$cert=$_POST['cert'];	
} else {
 $err[1]=$a[50][$lang];
}

if (is_uploaded_file ($_FILES['omg']['tmp_name'])){
					if (preg_match("~\.(csv|xlsx|xls|xltx|xlt)$~i", $_FILES['omg']['name'])){
						 $source = $_FILES['omg']['tmp_name'];
						 $target =realpath(__DIR__)."/up/mpt/".$_FILES['omg']['name'];
						 move_uploaded_file( $source, $target );
						 $ext = substr($_FILES['omg']['name'], strripos($_FILES['omg']['name'], '.')); 
						 $out=realpath(__DIR__)."/up/mpt/".uniqid(date('t-M')).$ext;
							      rename(realpath(__DIR__)."/up/mpt/".$_FILES['omg']['name'],$out);	
						$lnk=strstr($out,'up'); 		 
					} else {
						$err[2]=$a[122][$lang];
					}
			} else {
			    $err[2]=$a[50][$lang];
	          }
			
if(empty($err)){
	
set_include_path(get_include_path() . PATH_SEPARATOR . 'inc/Classes/');
include 'PHPExcel/IOFactory.php';
$inputFileName = $lnk; 
try {
	$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
} catch(Exception $e) {
	die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
}
$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
$arrayCount = count($sheetData);
// BANK

$tot=$arrayCount -1;
$insn=array();
$ed=array();
$skp=array();
$uncom=array();

for($i=2;$i<=$arrayCount;$i++){

$Namear = clean($sheetData[$i]["A"]);   
$Nameen = clean($sheetData[$i]["B"]);
$ctyp = trim($sheetData[$i]["C"]);
if($ctyp ==1) {
$ctyps ="طبيعي";
}else if($ctyp ==2) {
$ctyps ="إعتباري";
}
$coddy = trim($sheetData[$i]["D"]);
$phon = trim($sheetData[$i]["E"]);
$n = trim($sheetData[$i]["F"]);
$cr = trim($sheetData[$i]["G"]);
$p=trim($sheetData[$i]["H"]);
if($n !=""){
	$naid=trim($sheetData[$i]["F"]);
	$natyp=1;
}else if($cr !=""){
	$naid=trim($sheetData[$i]["G"]);
	$natyp=3;
} else {
	$naid=trim($sheetData[$i]["H"]);
	$natyp=2;
}
$adra=clean($sheetData[$i]["J"]);
$adr=preg_replace($paatt,"",$adra);

$adren2=clean($sheetData[$i]["K"]);
$adren=preg_replace($paatt,"",$adren2);

$em = trim($sheetData[$i]["L"]);
$birth = trim($sheetData[$i]["M"]);
$brn = trim($sheetData[$i]["Q"]);
$cadr = trim($sheetData[$i]["R"]);

$nat= strtolower(trim($sheetData[$i]["I"]));

$nnt = "SELECT CtryID FROM Ctrys WHERE LOWER(CtryNmEn)='$nat' AND Is_Acv=1 AND Is_Canc=0 ";
$nnty = mysql_query($nnt) or die(mysql_error());
 if(mysql_num_rows($nnty) >=1) {
	 $nn = mysql_fetch_array($nnty);
	$natin = $nn['CtryID'];
} else {
     $natin=0; 
} 
	
$ctry= strtolower(trim($sheetData[$i]["N"]));
$city= strtolower(trim($sheetData[$i]["O"]));

$ctr = "SELECT CtryID FROM Ctrys WHERE LOWER(CtryNmEn)='$ctry' AND Is_Acv=1 AND Is_Canc=0 ";
$resctr = mysql_query($ctr) or die(mysql_error());
if(mysql_num_rows($resctr) >=1) {
	$rctr = mysql_fetch_array($resctr);
	$ctryid = $rctr['CtryID'];
	   if($ctryid ==1) {
$cit = "SELECT GovnID FROM Govns WHERE LOWER(GovnNmEn)='$city' AND Is_Acv=1 AND Is_Canc=0 AND GovnID<>0";
           $rescit = mysql_query($cit) or die(mysql_error());
			   if(mysql_num_rows($rescit) >=1) {
				   $rcit = mysql_fetch_array($rescit);
	           $citid = $rcit['GovnID'];  
			   } else {
		   $citid=0;     
	   } 
	   } else {
		   $citid=0;     
	   } 
	    } else {
		   $citid=0;     
	   }

$postnm= trim($sheetData[$i]["P"]);

$ara = "SELECT AraID FROM Aras WHERE PstCd='$postnm' AND Is_Acv=1 AND Is_Canc=0 ";
$resara = mysql_query($ara) or die(mysql_error());
if(mysql_num_rows($resara) >=1){
$rara = mysql_fetch_array($resara);
	$araid = $rara['AraID'];  // update
} else {
	$araid=0;    //  no update
}

$dat=date('Y-m-d');

//if($naid !="") {
	
if($coddy !="") {

if($natin !=0) {
	
if($ctyp ==1 || $ctyp ==2) {
		 

//  check coddy
$sqlq=mysql_query("SELECT * FROM CertsHlds WHERE CoddyID='$coddy'  AND CertID='$cert'" ) or die (mysql_error());
if(mysql_num_rows($sqlq) == 0){
	
//  check branch	
	$sqlqx=mysql_query("SELECT * FROM OwnsBrs WHERE BrID='$brn' AND CrtOID='$bnk' ") or die (mysql_error());
	 if(mysql_num_rows($sqlqx) == 1) { 

// check customer			 
$sqlq2=mysql_query("SELECT * FROM Custs WHERE CustNmAr='$Namear' AND NatID_RegNum='$naid'") or die (mysql_error());
if(mysql_num_rows($sqlq2) == 0) { 

	
 $qq="INSERT INTO Custs (CtryID,CustNmEn,CustNmAr,UsID,CustTp,NatTp,Custem,Custph,Bthdate,NatID_RegNum) 
 VALUES('$natin','$Nameen','$Namear','$uidx','$ctyps','$natyp'";
	 
	  if($em !="") {
		$qq.=",'$em'";  
	  }else {
		 $qq.=",NULL"; 
	  }
	    if($phon !="") {
		$qq.=",'$phon'";  
	  }else {
		 $qq.=",NULL"; 
	  }
	  if($birth !="") {
	   $qq.=",'$birth'";
	  }else {
		 $qq.=",NULL"; 
	  } 
	  if($naid !="") {
		  $qq.=",'$naid'";
	  }else {
		   $qq.=",NULL"; 
	  }
        $qq.=")";
$rss=mysql_query($qq) or die (mysql_error());
$fk=mysql_insert_id();

$sqlx="INSERT INTO CertsHlds (CustID,CertID,BrID,HldDt,Dt_Mvd,StatID,CrtOID,CoddyID,Adr,CorrsAdr,GovnID,AraID,AdrEn) VALUES ('$fk','$cert','$brn',NULL,CURDATE(),1,'$bnk','$coddy','$adr','$cadr','$citid'";

if($araid !=0) {
		$sqlx.=",'$araid'";  
	  }else {
		 $sqlx.=",NULL"; 
	  }

 if($adren !="") {
	   $sqlx.=",'$adren'";
	  }else {
		 $sqlx.=",NULL"; 
	  }
	   $sqlx.=")"; 

$sql=mysql_query($sqlx) or die (mysql_error());


$sql2=mysql_query("INSERT INTO CustVrfns (CustID,StatID,UsID) VALUES ('$fk',11,'$uidx')") or die(mysql_error());
if(mysql_affected_rows() ==1) {
	if($Nameen =="" or $Namear =="" or $adr =="" or $naid =="") {
		$uncom[]="&nbsp; &nbsp; - رقم الصف : ".$i."&nbsp;&nbsp;&nbsp; للكود لدى البنك   [{$sheetData[$i]['D']}] &nbsp;&nbsp;&nbsp; <small> يحتوى بيانات غير مكتملة </small>";
	} else {
$insn[]=$i;
}
}   //  end affecrted
/*---------------------------------*/
} 
else {
$rwx=mysql_fetch_array($sqlq2);
$fk=$rwx['CustID'];

$sqlqC=mysql_query("SELECT * FROM CertsHlds WHERE CustID='$fk' AND CertID='$cert' AND CoddyID='$coddy'") or die (mysql_error());
if(mysql_num_rows($sqlqC) == 0){

  //overwrite
	$qqq="UPDATE Custs SET CtryID='$natin',CustNmEn='$Nameen',CustNmAr='$Namear',UsID='$uidx',CustTp='$ctyps',NatID_RegNum='$naid',Bthdate='$birth',Custph='$phon',Custem='$em'"; 
 $qqq.=" WHERE CustID='$fk'";
		
	 
$resup=mysql_query($qqq) or die (mysql_error());
if(mysql_affected_rows() >=1) {
$ed[]="&nbsp; &nbsp; - رقم الصف : ".$i."&nbsp;&nbsp;&nbsp; للكود لدى البنك   [{$sheetData[$i]['D']}] &nbsp;&nbsp;&nbsp; <small> تم استبدال وتعديل البيانات </small>";					
	} 

$sql="INSERT INTO CertsHlds (CustID,CertID,BrID,HldDt,Dt_Mvd,StatID,CrtOID,CoddyID,Adr,CorrsAdr,GovnID,AraID,AdrEn) VALUES ('$fk','$cert','$brn',NULL,CURDATE(),1,'$bnk','$coddy','$adr','$cadr','$citid'";

if($araid !=0) {
		$sql.=",'$araid'";  
	  }else {
		 $sql.=",NULL"; 
	  }

 if($adren !="") {
	   $sql.=",'$adren'";
	  }else {
		 $sql.=",NULL"; 
	  }
	   $sql.=")"; 

$sqlxx=mysql_query($sql) or die (mysql_error());

}	
}    //  end  customer check

/*---------------------------------*/

	 }else { 
		 $skp[]="&nbsp; &nbsp; - رقم الصف : ".$i."&nbsp;&nbsp;&nbsp; للعميل   [{$sheetData[$i]['A']}] &nbsp;&nbsp;&nbsp; <small> كود الفرع [{$sheetData[$i]['P']}] غير موجود</small>";
	 }   // end  check branch
	 
/*---------------------------------*/

} else {
  	$skp[]="&nbsp; &nbsp; - رقم الصف : ".$i."&nbsp;&nbsp;&nbsp; للعميل   [{$sheetData[$i]['A']}] &nbsp;&nbsp;&nbsp; <small> كود العميل موجود من قبل</small>";
}      // end  check coddy


} else {
$skp[]="&nbsp; &nbsp; - رقم الصف : ".$i."&nbsp;&nbsp;&nbsp; للعميل   [{$sheetData[$i]['A']}] &nbsp;&nbsp;&nbsp; <small>لايوجد نوع العميل </small>";
}

} else {
	$skp[]="&nbsp; &nbsp; - رقم الصف : ".$i."&nbsp;&nbsp;&nbsp; للعميل  [{$sheetData[$i]['A']}] &nbsp;&nbsp;&nbsp; <small>لايوجد جنسية العميل </small>";
}

} else {
	$skp[]="&nbsp; &nbsp; - رقم الصف : ".$i."&nbsp;&nbsp;&nbsp; للعميل   [{$sheetData[$i]['A']}] &nbsp;&nbsp;&nbsp; <small>لايوجد كود العميل لدى البنك</small>";
}
/*} else {
	$skp[]="&nbsp; &nbsp; - رقم الصف : ".$i."&nbsp;&nbsp;&nbsp; للعميل   [{$sheetData[$i]['A']}] &nbsp;&nbsp;&nbsp; <small>لايوجد رقم قومى </small>";
}*/
}


$inserted = count($insn);
$unc = count($uncom);
$chx=count($ed);
$sip=count($skp);

echo "<p style='color:#009900;font-weight:bold;font-family:Tahoma !important' class='dirr'><span class='glyphicon glyphicon-ok'></span> العدد الكلى  &raquo; {$tot}</p>";

echo "<p style='color:#77cd1c;font-weight:bold;font-family:Tahoma !important' class='dirr'><span class='glyphicon glyphicon-ok'></span> عدد الصفوف المدرجة والبيانات كاملة  &raquo; {$inserted}</p>";
echo "</p>";

echo "<p style='color:#e5aa00;font-weight:bold;font-family:Tahoma !important' class='dirr'><span class='glyphicon glyphicon-ok'></span> عدد الصفوف المدرجة والبيانات غير كاملة &raquo; {$unc} <br>";
foreach($uncom as $msg2) { 
echo $msg2 . "<br>";
}
"</p>";

echo "<p style='color:#9941f4;font-weight:bold;font-family:Tahoma !important' class='dirr'><span class='glyphicon glyphicon-ok'></span> عدد الصفوف التى تم تعديلها &raquo; {$chx} <br>";
foreach($ed as $msg3) {
echo $msg3 . "<br>";
}
"</p>";

echo "<p style='color:#df2c16;font-weight:bold;font-family:Tahoma !important' class='dirr'><span class='glyphicon glyphicon-ok'></span> عدد الصفوف التى لم تدخل &raquo; {$sip} <br>";
foreach($skp as $msg4) {
echo $msg4 . "<br>";
}
"</p>";

$flog=fopen("log/Log.txt","w7t");
fwrite($flog,"\t\t".date('d/m/Y  g:i:s')."\r\n\n"); 
fwrite($flog,"\t\t بيــانــات حــــاملـــى الوثــائـــق \r\n\n"); 
fwrite($flog,"العدد الكلى  : {$tot} \r\n"); 
fwrite($flog,"عدد الصفوف المدرجة والبيانات كاملة  : {$inserted} \r\n"); 
fwrite($flog,"عدد الصفوف المدرجة والبيانات غير كاملة : {$unc} \r\n"); 
 if(!empty($uncom)){
 foreach($uncom as $ds) {
	fwrite($flog,strip_tags(str_replace('&nbsp;',' ',$ds))."\r\n"); 
 }
 }
 fwrite($flog,"عدد الصفوف التى تم تعديلها : {$chx} \r\n"); 
 if(!empty($ed)){
 foreach($ed as $eds) {
	fwrite($flog,strip_tags(str_replace('&nbsp;',' ',$eds))."\r\n"); 
 }
 }
fwrite($flog,"عدد الصفوف التى لم تدخل : {$sip} \r\n"); 
 if(!empty($skp)) {
 foreach($skp as $sks) {
	fwrite($flog,strip_tags(str_replace('&nbsp;',' ',$sks))."\r\n"); 
 }
 }
 fclose($flog);
echo'<form action="" method="post" style="direction:rtl;font-family:hamdyfont,Tahoma;">
<input type="submit" name="download" class="btn btn-primary" value="تحميل ملف البيان">
</form><br>';
}
//}
}
?>
               
<form action="" method="post" enctype="multipart/form-data" id="importCrtOwns">
<input type="hidden" name="lang" value=<?=$lang?>>
<div style="border:1px dotted #999;width:750px;background-color:#F2F2F2;border-radius:6px;padding:6px 6px 6px 0px;" class="import"> 
<b style="margin:0px 0 0 24px;color:#066;"> <?=$a[117][$lang];?> &raquo;</b>
<table width="694"  border="0" cellpadding="5" cellspacing="5" style="margin-left:20px;">
<tr>
<td align="left"><?=$a[123][$lang];?></td>
    <td width="250">
    <?php if(isset($err[0])) {  echo "<span class='dirr' style='color:#FF3333'>&bull; {$err[0]}</span>";} ?>
    <select name="bnk"  class="form-control" style="width:200px;" id="certOwns">
    <option value="-1"><?=$a[30][$lang];?></option>
   <?php
    $q="SELECT *,CrtONm{$lang} AS crt FROM CrtOwns";
	$res=mysql_query($q) or die (mysql_error());
	while($rr=mysql_fetch_array($res)) {
  ?>  
      <option value="<?=$rr['CrtOID'];?>">
	  <?=$rr['crt'] ;?> </option>
      <?php } ?>
    </select>
    </td>
    <td width="107" align="right"><?=$a[156][$lang];?></td>
    <td width="154">
    <?php if(isset($err[1])) {  echo "<span class='dirr' style='color:#FF3333'>&bull; {$err[1]}</span>";} ?>
    <select name="cert"  class="form-control" id="certs" disabled>
    </select>
    </td>
</tr>
 <tr>
      <td width="118" height="60"  align="left" valign="top"> <?=$a[119][$lang];?></td>
      <td colspan="4" >
      <?php if(isset($err[2])) {  echo "<span class='dirr' style='color:#FF3333'>&bull; {$err[2]}</span>";} ?>
      <input type="file" name="omg" style="cursor:pointer;">
            <small style="color:#666666;"><?=$a[122][$lang];?></small>
      </td>
      </tr>
      <tr>
<td colspan="4"  align="left">
<input type="submit" name="submit"  class="btn btn-primary" value="<?=$a[120][$lang];?>" <?php if($insx==0) { ?> disabled <?php } ?>>
  <input type="reset" name="reset" class="btn btn-primary" value="<?=$a[32][$lang];?>">
  <a href="./up/scdoc/SAIF Template - Clients.xlsx" id="custtmp"><i class="btn btn-info"><?=$a[121][$lang];?></i></a>
  </td>
</tr>
</table>
</div>
</form>
<br><br clear="all">
   <br>    

<?php
if(isset($_POST['submit2'])) {
$err2=array();

if(isset($_POST['bnk']) and ($_POST['bnk'] != -1)) {
$bnk=$_POST['bnk'];	
}else {
 $err2[0]=$a[50][$lang];
}

if(isset($_POST['cert']) and ($_POST['cert'] != -1)) {
$cert=$_POST['cert'];	
}else {
 $err2[1]=$a[50][$lang];
}

if (is_uploaded_file ($_FILES['omg2']['tmp_name'])){
					if (preg_match("~\.(csv|xlsx|xls|xltx|xlt)$~i", $_FILES['omg2']['name'])){
						 $source2 = $_FILES['omg2']['tmp_name'];
						 $target2 =realpath(__DIR__)."/up/mpt/".$_FILES['omg2']['name'];
						 move_uploaded_file( $source2, $target2 );
						 $ext2 = substr($_FILES['omg2']['name'], strripos($_FILES['omg2']['name'], '.')); 
						 $out2=realpath(__DIR__)."/up/mpt/".uniqid(date('t-M')).$ext2;
							      rename(realpath(__DIR__)."/up/mpt/".$_FILES['omg2']['name'],$out2);	
						$lnk2=strstr($out2,'up'); 		 
		   }else {
						$err2[2]=$a[122][$lang];
					}
			}else {
			    $err2[2]=$a[50][$lang];	
		}
		if(isset($_POST['budat']) && ($_POST['budat'] != -1)){
			$budat=$_POST['budat'];
		} 
		/*else {
			    $err2[3]=$a[50][$lang];
		}*/
						 
if(empty($err2)){

set_include_path(get_include_path() . PATH_SEPARATOR . 'inc/Classes/');
include 'PHPExcel/IOFactory.php';
$inputFileName = $lnk2; 

try {
	$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
} catch(Exception $e) {
	die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
}
$sheetData2 = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
$arrayCount2 = count($sheetData2);

//  BANK
$tot=$arrayCount2 -1;
$insn=array();
$skp=array();

//$TotAmtSubs=array();
//$TotAmtReds=array();

//$TotQtySubs=array();
//$TotQtyReds=array();

/*for($j=2;$j<=$arrayCount2;$j++){

$TrsTp = trim($sheetData2[$j]["B"]);
$TrsDttm = trim($sheetData2[$j]["C"]);
$TrsTotAmt = trim($sheetData2[$j]["E"]);
$CertContMok = trim($sheetData2[$j]["F"]);
 
if($TrsTp == 1) {
$TotAmtSubs[]=(double)$sheetData2[$j]["E"];
$TotQtySubs[]=(double)$sheetData2[$j]["F"];
} else {
$TotAmtReds[]=(double)$sheetData2[$j]["E"];
$TotQtyReds[]=(double)$sheetData2[$j]["F"];
}

}
$TotAmtSubsEx=array_sum($TotAmtSubs);
$TotQtySubsEx=array_sum($TotQtySubs);
$TotAmtRedsEx=array_sum($TotAmtReds);
$TotQtyRedsEx=array_sum($TotQtyReds);


$q4 = "SELECT TotQtySubs,TotAmtSubs,TotQtyReds,TotAmtReds FROM BlkCertTrs WHERE CertID='$cert' AND BlkDt='$budat'";
	$res4 = mysql_query($q4) or die (mysql_error());
if(mysql_num_rows($res4) == 1) {
   $sm=mysql_fetch_array($res4);
$TotAmtSubs=(double)$sm['TotAmtSubs'];
$TotQtySubs=(double)$sm['TotQtySubs'];
$TotAmtReds=(double)$sm['TotAmtReds'];
$TotQtyReds=(double)$sm['TotQtyReds'];

}

if($TotAmtSubsEx == $TotAmtSubs) {
	 if($TotAmtRedsEx == $TotAmtReds) {
		   if($TotQtySubsEx == $TotQtySubs) {
			   if($TotQtyRedsEx == $TotQtyReds) {
			   
			 

/*-------------------------*/
for($i=2;$i<=$arrayCount2;$i++){
$BrTransID = trim($sheetData2[$i]["A"]);
$TrsTp = trim($sheetData2[$i]["B"]);

$TrsDttm = trim($sheetData2[$i]["C"]);
$coddyy = trim($sheetData2[$i]["D"]); 
$TrsTotAmt = trim($sheetData2[$i]["E"]); 
$CertContMok = trim($sheetData2[$i]["F"]); 
$BrID = trim($sheetData2[$i]["G"]); 
$TrsVldDttm = trim($sheetData2[$i]["H"]); 
$CertPr = trim($sheetData2[$i]["I"]); 
$Exps = trim($sheetData2[$i]["J"]); 

	
$sqlq2=mysql_query("SELECT HldDt,CertID,CustID,CoddyID FROM CertsHlds WHERE CoddyID='$coddyy' AND CertID='$cert'") or die (mysql_error());
if(mysql_num_rows($sqlq2) == 1){
$ros=mysql_fetch_array($sqlq2);  
$customer=$ros['CustID']; 
$fund=$ros['CertID']; 
$hldat=$ros['HldDt']; 

/*$sqlq333=mysql_query("SELECT CustID,CertID FROM CertsHlds WHERE CustID='$customer' AND CertID='$cert'") 
or die (mysql_error());
if(mysql_num_rows($sqlq333) == 1){*/
	  
$sqlq44=mysql_query("SELECT * FROM OwnsBrs WHERE BrID='$BrID' AND CrtOID='$bnk'") or die (mysql_error());
	 if(mysql_num_rows($sqlq44) == 1) {
		 
$sqlq3=mysql_query("SELECT * FROM CertTrs WHERE TrsDttm=STR_TO_DATE('$TrsDttm','%m/%d/%Y') AND CertID='$cert' AND BrTransID='$BrTransID' ") or die (mysql_error());
if(mysql_num_rows($sqlq3) == 0){

$qq="INSERT INTO CertTrs (CustID,CertID,CoddyID,TrsDttm,TrsVldDttm,BrID,CrtOID,CertContMok,CertPr,TrsTotAmt,TrsTp,Exps,BrTransID,Dt_Mvd,UsID,BlkDt) 
 VALUES ('$customer','$cert','$coddyy',STR_TO_DATE('$TrsDttm','%m/%d/%Y'),STR_TO_DATE('$TrsVldDttm','%m/%d/%Y'),'$BrID','$bnk','$CertContMok','$CertPr','$TrsTotAmt','$TrsTp','$Exps','$BrTransID',CURDATE(),'$uidx'";
 if(isset($_POST['budat']) && ($_POST['budat'] != -1)){ 
    $qq.=",'$budat'";
    } else {
	$qq.=",NULL";
      }
   $qq.=")";
$rss=mysql_query($qq) or die (mysql_error());
$fk=mysql_insert_id();
 if(isset($_POST['budat']) && ($_POST['budat'] != -1)){
$sqlx=mysql_query("INSERT INTO BlkTrsVrfns (CertID,BlkDt,StatID,UsID) VALUES ('$cert','$budat',12,'$uidx')") or die(mysql_error());
 }
 $sql=mysql_query("INSERT INTO TrsVrfns (CertTrsID,StatID,UsID) VALUES ('$fk',11,'$uidx')") or die(mysql_error());
if(mysql_affected_rows() ==1) {
$insn[]=$i;
}
	
if(is_null($hldat)){ 
		$sqlqw=mysql_query("UPDATE CertsHlds SET HldDt=STR_TO_DATE('$TrsDttm','%m/%d/%Y') WHERE CustID='$customer' AND CertID='$fund' AND CoddyID='$coddyy'") or die (mysql_error());
	}	
		} else { 
$rwx=mysql_fetch_array($sqlq3);
	$idxw=$rwx['CertTrsID'];	
	$qqqq="UPDATE CertTrs SET CustID='$customer',CertID='$cert',CoddyID='$coddyy',BrID='$BrID',CrtOID='$bnk',TrsDttm=STR_TO_DATE('$TrsDttm','%m/%d/%Y'),TrsVldDttm=STR_TO_DATE('$TrsVldDttm','%m/%d/%Y'),CertContMok='$CertContMok',CertPr='$CertPr',TrsTotAmt='$TrsTotAmt',TrsTp='$TrsTp',Exps='$Exps',BrTransID='$BrTransID',Dt_Mvd=CURDATE(),UsID='$uidx',BlkDt=";  
	if(isset($_POST['budat']) && ($_POST['budat'] != -1)){
    $qqqq.="'$budat'";
}else {
	$qqqq.="NULL";
}
   $qqqq.=" WHERE CertTrsID='$idxw'"; 
	$resq22=mysql_query($qqqq) or die (mysql_error());
}
	
}else {
	$skp[]="&nbsp; &nbsp; - رقم الصف : ".$i."&nbsp;&nbsp;&nbsp; للكود   [{$sheetData2[$i]['D']}] &nbsp;&nbsp;&nbsp; <small> كود الفرع [{$sheetData2[$i]['G']}] غير موجود لدى البنك</small>";
}

/*}else {
	$skp[]="&nbsp; &nbsp; - رقم الصف : ".$i."&nbsp;&nbsp;&nbsp; للكود    [{$sheetData2[$i]['D']}] &nbsp;&nbsp;&nbsp; <small>لايوجد للعميل وثيقة تابعة للصندوق المختار من القائمة </small>";
}*/

}else {
	$skp[]="&nbsp; &nbsp; - رقم الصف : ".$i."&nbsp;&nbsp;&nbsp; للكود    [{$sheetData2[$i]['D']}] &nbsp;&nbsp;&nbsp; <small>لايوجد كود العميل لدى البنك او الكود خطأ</small>";
}


}
$inserted = count($insn);
$sip=count($skp);

echo "<p style='color:#009900;font-weight:bold;font-family:Tahoma !important' class='dirr'><span class='glyphicon glyphicon-ok'></span> العدد الكلى  &raquo; {$tot}</p>";

echo "<p style='color:#77cd1c;font-weight:bold;font-family:Tahoma !important' class='dirr'><span class='glyphicon glyphicon-ok'></span> عدد الصفوف المدرجة  &raquo; {$inserted}</p>";
echo "</p>";

echo "<p style='color:#df2c16;font-weight:bold;font-family:Tahoma !important' class='dirr'><span class='glyphicon glyphicon-ok'></span> عدد الصفوف التى لم تدخل &raquo; {$sip} <br>";
foreach($skp as $msg4) {
echo $msg4 . "<br>";
}
"</p>";	
$flog=fopen("log/Log.txt","w7t");
fwrite($flog,"\t\t".date('d/m/Y  g:i:s')."\r\n\n"); 
fwrite($flog,"\t\t بيــانــات حــــركـــــات الوثــائـــق \r\n\n"); 
fwrite($flog,"العدد الكلى  : {$tot} \r\n"); 
fwrite($flog,"عدد الصفوف المدرجة  : {$inserted} \r\n"); 

  fwrite($flog,"عدد الصفوف التى لم تدخل : {$sip} \r\n"); 
 if(!empty($skp)){
 foreach($skp as $sks) {
	fwrite($flog,strip_tags(str_replace('&nbsp;',' ',$sks))."\r\n"); 
 }
 }
 fclose($flog);
echo'<form action="" method="post" style="direction:rtl;font-family:hamdyfont,Tahoma;">
<input type="submit" name="download" class="btn btn-primary" value="تحميل ملف البيان">
</form><br>';


/* } else {
			   echo "<p style='color:#df2c16;font-weight:bold;font-family:Tahoma !important' class='dirr'>
					<span class='glyphicon glyphicon-ok'></span>عدد وثائق الاسترداد فى الملف لايساوى عدد وثائق الاسترداد فى العملية المجمعة 
					 </p>
					";
			   }
		   } else {
			    echo "<p style='color:#df2c16;font-weight:bold;font-family:Tahoma !important' class='dirr'>
					<span class='glyphicon glyphicon-ok'>
					عدد وثائق الشراء فى الملف لايساوى عدد وثائق الشراء فى العملية المجمعة 
					</p>
					";
		   }
	 } else {
		 echo "<p style='color:#df2c16;font-weight:bold;font-family:Tahoma !important' class='dirr'>
					<span class='glyphicon glyphicon-ok'>
					مجموع قيمة وثائق الاسترداد فى الملف لايساوى مجموع قيمة وثائق الاسترداد فى العملية المجمعة 
					</p>
					";
	 }
} else {
	 echo "<p style='color:#df2c16;font-weight:bold;font-family:Tahoma !important' class='dirr'>
					<span class='glyphicon glyphicon-ok'>مجموع قيمة وثائق الشراء فى الملف لايساوى مجموع قيمة وثائق الشراء فى العملية المجمعة 
					</p>
					";
}
*/

	
}



}

?> 

       
<form action="" method="post" enctype="multipart/form-data" id="importtrans">
<input type="hidden" name="lang" value=<?=$lang?>>
<div style="border:1px dotted #999;width:750px;background-color:#F2F2F2;border-radius:6px;padding:6px 6px 6px 0px;" class="import"> 
 <b style="margin:0px 0 0 24px;color:#066;"><?=$a[118][$lang];?> &raquo;</b>
<table width="708"  border="0" cellpadding="5" cellspacing="5" style="margin-left:20px;">
<tr>
<td align="left"><?=$a[123][$lang];?></td>
    <td width="239">
     <?php if(isset($err2[0])) {  echo "<span class='dirr' style='color:#FF3333'>&bull; {$err2[0]}</span>";} ?>
    <select name="bnk"  class="form-control" style="width:200px;" id="bkks">
    <option value="-1"><?=$a[30][$lang];?></option>
<?php
    $q="SELECT *,CrtONm{$lang} AS crt FROM CrtOwns";
	$res=mysql_query($q) or die (mysql_error());
	while($rr=mysql_fetch_array($res)) {
 ?>  
<option value="<?=$rr['CrtOID'];?>">
	  <?=$rr['crt'] ;?> </option>
      <?php } ?>
    </select>
    </td>
    <td width="111"  align="right"><?=$a[156][$lang];?></td>
    <td width="120">
     <?php if(isset($err2[1])) {  echo "<span class='dirr' style='color:#FF3333'>&bull; {$err2[1]}</span>";} ?>
    <select name="cert"  class="form-control" id="ctxx" disabled>
    </select>
    </td>
</tr>

<tr>
<td align="left"><?=$a[318][$lang];?></td>
    <td width="239">
    <?php //if(isset($err2[3])) {  echo "<span class='dirr' style='color:#FF3333'>&bull; {$err2[3]}</span>";} ?>
    <select name="budat"  class="form-control" id="bukd" style="width:200px;font-family:sans-serif;font-weight:bold;" disabled >

    </select>
    </td>
</tr>
<tr>
      <td width="146" height="60"  align="left" valign="top"> <?=$a[119][$lang];?></td>
      <td colspan="4" >
       <?php if(isset($err2[2])) {  echo "<span class='dirr' style='color:#FF3333'>&bull; {$err2[2]}</span>";} ?>
      <input type="file" name="omg2" style="cursor:pointer;" accept="application/msexcel">
            <small style="color:#666666;"><?=$a[122][$lang];?></small>
      </td>
    </tr>
      <tr>
<td colspan="4"  align="left">
<input type="submit" name="submit2"  class="btn btn-primary" value="<?=$a[120][$lang];?>" <?php if($insx==0) { ?> disabled <?php } ?>>
  <input type="reset" name="reset" class="btn btn-primary" value="<?=$a[32][$lang];?>">
  <a href="up/scdoc/SAIF Template - Transactions.xlsx" id="transtmp"><i class="btn btn-info"><?=$a[121][$lang];?></i></a>
  </td>
</tr>
    </table>
    </div>
  </form>
  
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->
<?php require_once "inc/footer.inc.php"; ?>

<?php
# file:                replace_utf8.php
#                      本程序适用于对UTF-8的页面进行修改。

set_time_limit(3600);  //脚本运行时间
?>
<?php
if($_POST['Submit']=='开始执行操作'){
  $dir = $_POST['searchpath'];
  $shortname = $_POST['shortname'];
  $isall = $_POST['isall'];
  $isreg = $_POST['isreg'];
  
if (!get_magic_quotes_gpc()) {
  $sstr = $_POST['sstr'];
  $rpstr = $_POST['rpstr'];
} else {
  $sstr = stripslashes($_POST['sstr']);
  $rpstr = stripslashes($_POST['rpstr']);
}    
  

  //分析shortname
  $arrext = explode ("|",$shortname);


  if (!is_dir($dir)) return;
  if ($sstr == '') return;

  //把末尾的/去掉
  if(substr($dir,-1)=='/') $dir = substr($dir,0,strrpos($dir,"/"));

  //罗列所有目录
  if ($isall == 1){
    hx_dirtree($dir);
  }else{
    hx_dealdir($dir);

  }

exit();
}


function hx_dirtree($path="."){
  global $sstr,$rpstr,$isreg,$arrext;


  $d = dir($path);
  while(false !== ($v = $d->read())) {
    if($v == "." || $v == "..") continue;
    $file = $d->path."/".$v;
    if(is_dir($file)) {
      echo "<p>$v</p>"; hx_dirtree($file);
    }else{
        $ext=substr(strrchr($v,"."), 1);
        if( in_array($ext , $arrext) ){
          echo "<li>$file ";
          $body = file_get_contents($file);
          if($isreg == 1){
          $body2 = preg_replace($sstr, $rpstr, $body);
          }else{
          $body2 = str_replace($sstr, $rpstr, $body);
          }
          if($body != $body2 && $body2 != ''){
            tofile($file,$body2);
            echo ' OK';
          }else{
            echo ' NO';
          }
          echo '</li>';
        }
    }
  }
  $d->close();
}

function hx_dealdir($dir){
  global $sstr,$rpstr,$isreg,$arrext;
    if ($dh = opendir($dir)) {
    while (false !== ($file = readdir($dh))) {
      if(filetype($dir.'/'.$file)=='file'){

        $ext=substr(strrchr($file,"."), 1);
        if( in_array($ext , $arrext) ){

          echo "<li>$file ";
          $body = file_get_contents($dir.'/'.$file);        
          if($isreg == 1){
          $body2 = preg_replace($sstr, $rpstr, $body);
          }else{
          $body2 = str_replace($sstr, $rpstr, $body);
          }
          if($body != $body2 && $body2 != ''){            
            tofile($dir.'/'.$file,$body2);
            echo ' OK';
          }else{
            echo ' NO';
          }
          echo '</li>';
        }
      }
    }
    closedir($dh);
    }

}
//把生成文件的过程写出函数
function tofile($file_name,$file_content){
if (is_file ($file_name)){
  @unlink ($file_name);
}
  $handle = fopen ($file_name,"w");
  if (!is_writable ($file_name)){
    return false;
  }
  if (!fwrite ($handle,$file_content)){
    return false;
  }
  fclose ($handle); //关闭指针
  return $file_name;
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>php字符批量修改替换程序</title>
<style type="text/css">
body{background:#FFFFFF;color:#000;font-size:12px;}
#top{text-align:center;}
h1,p,form{margin:0;padding:0;}
h1{font-size;14px;}
</style>
</head>
<body>
  <div id="top">
<h1>批量替换程序(UTF-8版)</h1>
<div>本程序可以扫描指定目录的所有文件，进行<strong>内容替换</strong>。可用于被批量挂马的删除以及批量更新页面某些内容。<br/>
在文件数量非常多的情况下，本操作比较占用服务器资源，请确脚本超时限制时间允许更改，否则可能无法完成操作。By <a target='_blank' href="http://www.v7v3.com/">维7维3</a></div>
  </div>


<form action="<?=$_SERVER['SCRIPT_NAME']?>" name="form1" target="stafrm" method="post">
<table width="95%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr>
    <td width="10%" bgcolor="#FFFFFF"><strong>&nbsp;起始根路径：</strong></td>
    <td width="90%" bgcolor="#FFFFFF"><input name="searchpath" type="text" id="searchpath" value="." size="20" />
      点表示当前目录，末尾不要加/ <input name="isall" type="checkbox" value="1" checked="checked" />包含此目录下所有目录</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF"><strong>&nbsp;文件扩展名：</strong></td>
    <td bgcolor="#FFFFFF"><input name="shortname" type="text" id="shortname" size="20" value="php" />
      多个请用|隔开</td>
  </tr>
  <tr id="rpct">
    <td height="64" colspan="2" bgcolor="#FFFFFF"><table width="100%" border="0" cellspacing="1" cellpadding="1">
      <tr bgcolor="#EDFCE2">
        <td colspan="4"><strong>内容替换选项：</strong> <input type="checkbox" name="isreg" value="1" />使用正则表达式</td>
      </tr>
      <tr>
        <td colspan="4">替换内容类默认使用字符串替换，也可以使用正则表达式(需勾选)。"替换为"不填写的话，就表示删除"替换内容"。</td>
      </tr>
      <tr>
        <td width="10%">&nbsp;替换内容：</td>
        <td width="36%"><textarea name="sstr" id="sstr" style="width:90%;height:45px">googleapis.com</textarea></td>
        <td width="10%">替 换 为：</td>
        <td><textarea name="rpstr" id="rpstr" style="width:90%;height:45px">useso.com</textarea></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan="2" height="20" align="center" bgcolor="#E2F5BC"><input type="submit" name="Submit" value="开始执行操作" class="inputbut" /></td>
  </tr>
</table>
  </form>
<table width="95%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr bgcolor="#FFFFFF">
    <td id="mtd">
      <div id='mdv' style='width:100%;height:100;'>
        <iframe name="stafrm" frameborder="0" id="stafrm" width="100%" height="100%"></iframe>
      </div>
      <script type="text/javascript">
      document.all.mdv.style.pixelHeight = screen.height - 450;
      </script>    </td>
  </tr>
</table>
</body>
</html>
<?php
function GetWeb($url){
   //headers请求头
   $headerArray =array(
      "accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
accept-language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6
cache-control: max-age=0
cookie: ts_uid=5391332086; ts_refer=ADTAGnewyqq.singer; pgv_pvid=4427103536; ts_last=y.qq.com/n/yqq/song/5456926_num.html; ts_refer=ADTAGh5_playsong; ts_uid=4019739128; pgv_pvi=2841329664; userAction=1; yqq_stat=0; pgv_info=ssid=s3403020136; pgv_si=s1991955456; ts_last=i.y.qq.com/v8/playsong.html
sec-fetch-dest: document
sec-fetch-mode: navigate
sec-fetch-site: none
sec-fetch-user: ?1
upgrade-insecure-requests: 1
user-agent: Mozilla/5.0 (Linux; Android 5.0; SM-G900P Build/LRX21T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.66 Mobile Safari/537.36"
    );
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch,CURLOPT_HTTPHEADER,$headerArray);
   $output = curl_exec($ch);
   curl_close($ch);
   return $output;
}
function SubString($str, $leftStr, $rightStr)
{
    $left = strpos($str, $leftStr);
    $right = strpos($str, $rightStr,$left);
    if($left < 0 or $right < $left) return '';
    return substr($str, $left + strlen($leftStr), $right-$left-strlen($leftStr));
}
function Search($str){
    $json=GetWeb("http://59.37.96.220/soso/fcgi-bin/client_search_cp?format=json&t=0&inCharset=GB2312&outCharset=utf-8&qqmusic_ver=1302&catZhida=0&p=1&n=1&w=".urlencode($str)."&flag_qc=0&remoteplace=sizer.newclient.song&new_json=1&lossless=0&aggr=1&cr=1&sem=0&force_zonghe=0");
    return json_decode($json,true)['data']['song']['list'][0]['mid'];

}
//获取vkey
$data = GetWeb("https://i.y.qq.com/v8/playsong.html?songmid=000edOaL1WZOWq");
$val=SubString($data,'<audio','</audio>');
$vk = SubString($val,'m4a','&fromtag=38');
if(is_array($_GET)&&count($_GET)>0)//先判断是否通过get传值了
    {
        if(isset($_GET["id"]))//是否存在"id"的参数
        {
            $Musicid=$_GET["id"];
        }else if(isset($_GET["search"])){
            $search=$_GET["search"];
            $Musicid=Search($search);
        }
        $json=GetWeb("https://y.qq.com/n/yqq/song/".$Musicid.".html");
        $mid=SubString($json,"\"strMediaMid\":\"","\",\"");
        $title=SubString($json,"<title>"," - QQ音乐-千万正版音乐海量无损曲库新歌热歌天天畅听的高品质音乐平台！</title>");
        $musicurl = "http://musichy.tc.qq.com/amobile.music.tc.qq.com/M500".$mid.".mp3".$vk."&fromtag=98";
    }else{
        exit();
    }
?>

<html><head>
    <meta name="viewport" content="width=device-width">
    <title><?php echo $title; ?></title>
    <style type="text/css">
        button {
    background-color: #4CAF50; /* Green */
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    -webkit-transition-duration: 0.4s; /* Safari */
    transition-duration: 0.4s;
	border-radius: 8px;
}
        button:hover {
    box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
}
audio {
    position: absolute;
    top: 250px;
    right: 0px;
    left: 0px;
    max-height: 100%;
    max-width: 100%;
    margin: auto;
}
*:focus {outline: none;} 
    h3{
        margin-top:100px;
        text-align:center;
    }
    </style>
<script>
   function downloadUrlFile(url,name) {
      url= url.replace(/\\/g, '/');
      const xhr = new XMLHttpRequest();
      xhr.open('GET', url, true);
      xhr.responseType = 'blob';
      xhr.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    console.log(percentComplete);
                    var pro = document.getElementById("progress");
                    pro.style.visibility = "visible";
                    var str="下载中..."+(percentComplete * 100) + "%";
                    pro.innerHTML=str;
                    if(percentComplete==1)
                        pro.innerHTML="已完成";
                }
            }, false);
      xhr.onload = () => {
        if (xhr.status === 200) {
            var pro = document.getElementById("progress");
            pro.style.visibility = "hidden";
            saveAs(xhr.response, name);
        }
      };

      xhr.send();
    }
    
    /**
     * URL方式保存文件到本地
     * @param data 文件的blob数据
     * @param name 文件名
     */
    function saveAs(data, name) {
        var urlObject = window.URL || window.webkitURL || window;
        var export_blob = new Blob([data]);
        var save_link = document.createElementNS('http://www.w3.org/1999/xhtml', 'a')
        save_link.href = urlObject.createObjectURL(export_blob);
        save_link.download = name;
        save_link.click();
    }
		function dlfile(){
			var url = "<?php echo $musicurl; ?>";
            var name="<?php echo str_replace("&#45;","-",$title); ?>.mp3";
		    downloadUrlFile(url,name);
		}
	</script>
</head>

<body>
    <h3><?php echo $title; ?></h3>
	<div align="center">
    <button id="download" onclick="dlfile()">下载</button>
    <h5 style="visibility:hidden;" id="progress"></h5>
     </div>
    <audio controls="" autoplay="" name="media">
        <source src="<?php echo $musicurl; ?>" type="audio/mp3">
    </audio>

</body></html>
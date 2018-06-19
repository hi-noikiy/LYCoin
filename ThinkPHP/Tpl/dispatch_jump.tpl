<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
    if($message=="success"){
       echo "<script>location.href='$jumpUrl';</script>";
        exit();
    }else{
		if(!isset($message)){
			$message = $error;
		}
		if(C("DEFAULT_SET_INTO")==1){
			echo "<meta charset='utf-8'>";
			echo "<script>location.href='$jumpUrl';alert('$message')</script>";
			exit();				
		}
	
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>跳转页面</title>

    <style>
body {
  width: 100%;
  height: 100%;
  margin: 0;
  padding: 0;
  background-color: #e1e4e6;
  font-family: 'Muli';
}
* {
  outline: none;
}
h1 {
  font-size: 25px;
  font-weight: 100;
  color: #2C2C2C;
  margin: 20px 25px;
}
a {
  text-decoration: none;
  color: #3476CA;
}
a:hover {
  color: #6CB5F3;
}
.open {
  position: fixed;
  width: 100px;
  height: 40px;
  left: 50%;
  top: -1000px;
  margin-left: -80px;
  margin-top: -30px;
  border: 1px solid #ccc;
  background-color: #fff;
  border-radius: 6px;
  padding: 10px 30px;
  color: #444;
  transition: all ease-out 0.6s;
}
.open:hover {
  border: 1px solid #aaa;
  box-shadow: 0 0 8px #ccc inset;
  transition: all ease-out 0.6s;
}
.container {
  position: fixed;
  width: 400px;
  height: 238px;
  left: 50%;
  top: 45%;
  margin-top: -119px;
  margin-left: -200px;
  background-color: #F3F3F3;
  border-radius: 6px;
  box-shadow: 0px 0px 24px rgba(0, 0, 0, 0.4);
}
.container:before {
  content: '';
  position: absolute;
  left: -14px;
  top: 28px;
  border-style: solid;
  border-width: 10px 14px 10px 0;
  border-color: rgba(0, 0, 0, 0) #f3f3f3 rgba(0, 0, 0, 0) rgba(0, 0, 0, 0);
}
.container p {
  width: 350px;
  font-size: 16px;
  color: #a8aab2;
  font-weight: 400;
  line-height: 28px;
  float: left;
  margin: 0;
}
.container .bottom {
  display: -webkit-box;
  display: -moz-box;
  display: -ms-flexbox;
  display: -webkit-flex;
  display: flex;
  width: 100%;
  bottom: 0;
  position: absolute;
}
.container .bottom .step {
  flex: 3;
  float:left;
  -webkit-flex: 3;
  -ms-flex: 3;
  width: 80%;
  height: 54px;
  background-color: #373942;
  border-bottom-left-radius: 6px;
  display: flex;
}
.container .bottom .step span {
  flex: 1;
  -webkit-flex: 1;
  -ms-flex: 1;
  line-height: 54px;
  color: #fff;
  margin-left: 25px;
  font-size: 18px;
  float:left;
}
.container .bottom .step ul {
  flex: 2;
  -webkit-flex: 2;
  -ms-flex: 2;
  list-style: none;
  height: 10px;
  margin: 23px 0;
  padding-left: 15px;
  float:left;
}
.container .bottom .step ul li {
  position: relative;
  height: 7px;
  width: 7px;
  margin: 0 10px;
  float: left;
  border-radius: 50%;
  background-color: none;
  border: 1px solid #535560;
}
.container .bottom .step ul li:first-child:before {
  width: 0;
}
.container .bottom .step ul li:before {
  content: '';
  position: absolute;
  width: 20px;
  border-top: 1px solid #535560;
  left: -21px;
  top: 3px;
}
.container .bottom .step ul li.true {
  background-color: #7a7d86;
}
.container .bottom .step ul li.active {
  background-color: #fff;
  box-shadow: 0 0 6px rgba(255, 255, 255, 0.78);
}
.close {
  cursor: pointer;
}
.close:before,
.close:after {
  content: "";
  position: absolute;
  height: 13px;
  width: 13px;
  top: 26px;
  right: 31px;
  border-top: 2px solid #7c7c7c;
  -webkit-transform: rotate(-45deg);
  -moz-transform: rotate(-45deg);
  -ms-transform: rotate(-45deg);
  -o-transform: rotate(-45deg);
  transform: rotate(-45deg);
}
.close:before {
  right: 40px;
  -webkit-transform: rotate(45deg);
  -moz-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  -o-transform: rotate(45deg);
  transform: rotate(45deg);
}
.btn {
  flex: 1;
  background-color: #6cb5f3;
  width:100%;
  line-height:54px;
  border: 0;
  margin: 0;
  padding: 0;
  text-transform: uppercase;
  color: #fff;
  font-weight: bold;
  border-bottom-right-radius: 6px;
  cursor: pointer;
  transition: all .3s;
}
.btn:hover {
  background-color: #6BA5D6;
  transition: all .3s;
}
.btn:active {
  background-color: #5F8AAF;
}
.slider-container {
  width: 350px;
  margin: 0 25px;
  overflow: hidden;
}
.slider-turn {
  width: 10000px;
}
#href{color:#6BA5D6;font-size:14px;}
</style>

</head>

<body>
<div style="text-align:center;clear:both">

</div>
  <div class='container'>
  <?php if(isset($message)) {?>
  <h1><?php echo($message); ?></h1>
  <?php }else{?>
  <h1><?php echo($error); ?></h1>
  <?php }?> 
  <span class='close' onclick="javascript:location.href='<?php echo($jumpUrl); ?>'"></span>
  <div class='slider-container'>
    <div class='slider-turn'>
      <p>页面自动&nbsp;<a id="href" href="<?php echo($jumpUrl); ?>">跳转</a>&nbsp;等待时间:&nbsp;&nbsp;<b id="wait"><?php echo($waitSecond); ?></b></p>
      <p>Thank you !</p>
    </div>
  </div>
  <div class='bottom'>
<div class="step">
      <span>LYCoin</span>
      <ul>
        <li data-num="1" class="active"></li>
        <li data-num="2"></li>
        <li data-num="3"></li>
        <li data-num="4"></li>
        <li data-num="5"></li>
      </ul>
    </div>
	<div style="width:20%;float:right;background-color: #6cb5f3;height:54px;">
		<button class='btn' onclick="javascript:location.href='<?php echo($jumpUrl); ?>'">直接访问</button>
	</div>
    
  </div>
</div>
<button class='open'>
  Open
</button>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>

</body>

</html>

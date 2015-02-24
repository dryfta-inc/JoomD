
<html><head> 

<meta http-equiv="content-type" content="text/html; charset=UTF-8"> 
 
<title>+ ATTACKER_404 +</title>
<style type="text/css"> 
<!--
.ahgcrewstyle {
	color: #F00;
}
.ahg {
	color: #0F0;
}
-->
</style> 
<style type="text/css">
/* Circle Text Styles */
#outerCircleText {
/* Optional - DO NOT SET FONT-SIZE HERE, SET IT IN THE SCRIPT */
font-style: italic;
font-weight: bold;
font-family: 'comic sans ms', verdana, arial;
color: white;
/* End Optional */

/* Start Required - Do Not Edit */
position: absolute;top: 0;left: 0;z-index: 3000;cursor: default;}
#outerCircleText div {position: relative;}
#outerCircleText div div {position: absolute;top: 0;left: 0;text-align: center;}
/* End Required */
/* End Circle Text Styles */
</style>
<script type="text/javascript">


;(function(){

// Your message here (QUOTED STRING)
var msg = "Attacker_404 was here";

/* THE REST OF THE EDITABLE VALUES BELOW ARE ALL UNQUOTED NUMBERS */

// Set font's style size for calculating dimensions
// Set to number of desired pixels font size (decimal and negative numbers not allowed)
var size = 24;

// Set both to 1 for plain circle, set one of them to 2 for oval
// Other numbers & decimals can have interesting effects, keep these low (0 to 3)
var circleY = 0.75; var circleX = 2;

// The larger this divisor, the smaller the spaces between letters
// (decimals allowed, not negative numbers)
var letter_spacing = 5;

// The larger this multiplier, the bigger the circle/oval
// (decimals allowed, not negative numbers, some rounding is applied)
var diameter = 10;

// Rotation speed, set it negative if you want it to spin clockwise (decimals allowed)
var rotation = 0.4;

// This is not the rotation speed, its the reaction speed, keep low!
// Set this to 1 or a decimal less than one (decimals allowed, not negative numbers)
var speed = 0.3;

////////////////////// Stop Editing //////////////////////

if (!window.addEventListener && !window.attachEvent || !document.createElement) return;

msg = msg.split('');
var n = msg.length - 1, a = Math.round(size * diameter * 0.208333), currStep = 20,
ymouse = a * circleY + 20, xmouse = a * circleX + 20, y = [], x = [], Y = [], X = [],
o = document.createElement('div'), oi = document.createElement('div'),
b = document.compatMode && document.compatMode != "BackCompat"? document.documentElement : document.body,

mouse = function(e){
 e = e || window.event;
 ymouse = !isNaN(e.pageY)? e.pageY : e.clientY; // y-position
 xmouse = !isNaN(e.pageX)? e.pageX : e.clientX; // x-position
},

makecircle = function(){ // rotation/positioning
 if(init.nopy){
  o.style.top = (b || document.body).scrollTop + 'px';
  o.style.left = (b || document.body).scrollLeft + 'px';
 };
 currStep -= rotation;
 for (var d, i = n; i > -1; --i){ // makes the circle
  d = document.getElementById('iemsg' + i).style;
  d.top = Math.round(y[i] + a * Math.sin((currStep + i) / letter_spacing) * circleY - 15) + 'px';
  d.left = Math.round(x[i] + a * Math.cos((currStep + i) / letter_spacing) * circleX) + 'px';
 };
},

drag = function(){ // makes the resistance
 y[0] = Y[0] += (ymouse - Y[0]) * speed;
 x[0] = X[0] += (xmouse - 20 - X[0]) * speed;
 for (var i = n; i > 0; --i){
  y[i] = Y[i] += (y[i-1] - Y[i]) * speed;
  x[i] = X[i] += (x[i-1] - X[i]) * speed;
 };
 makecircle();
},

init = function(){ // appends message divs, & sets initial values for positioning arrays
 if(!isNaN(window.pageYOffset)){
  ymouse += window.pageYOffset;
  xmouse += window.pageXOffset;
 } else init.nopy = true;
 for (var d, i = n; i > -1; --i){
  d = document.createElement('div'); d.id = 'iemsg' + i;
  d.style.height = d.style.width = a + 'px';
  d.appendChild(document.createTextNode(msg[i]));
  oi.appendChild(d); y[i] = x[i] = Y[i] = X[i] = 0;
 };
 o.appendChild(oi); document.body.appendChild(o);
 setInterval(drag, 25);
},

ascroll = function(){
 ymouse += window.pageYOffset;
 xmouse += window.pageXOffset;
 window.removeEventListener('scroll', ascroll, false);
};

o.id = 'outerCircleText'; o.style.fontSize = size + 'px';

if (window.addEventListener){
 window.addEventListener('load', init, false);
 document.addEventListener('mouseover', mouse, false);
 document.addEventListener('mousemove', mouse, false);
  if (/Apple/.test(navigator.vendor))
   window.addEventListener('scroll', ascroll, false);
}
else if (window.attachEvent){
 window.attachEvent('onload', init);
 document.attachEvent('onmousemove', mouse);
};

})();


var snowmax=50
var snowcolor=new Array("#aaaacc","#ddddFF","#ccccDD","#eeeeFF")
var snowtype=new Array("Arial Black","Arial Narrow","Times","Comic Sans MS","Courier")
var snowmessage=new Array ("BL4CK_3YE116","system112","Abduh Screamo","E5a_Cyb3r","NewbieHacker061099.php","Prof Lang Ling Lung","MrBs")
var sinkspeed=0.4
var snowmaxsize=14
var snowminsize=10
var snowingzone=1
var snow=new Array()
var marginbottom
var marginright
var timer
var i_snow=0
var i_text=0
var x_mv=new Array();
var crds=new Array();
var lftrght=new Array();
var browserinfos=navigator.userAgent 
var ie5=document.all&&document.getElementById&&!browserinfos.match(/Opera/)
var ns6=document.getElementById&&!document.all
var opera=browserinfos.match(/Opera/)  
var browserok=ie5||ns6||opera

function randommaker(range) {		
	rand=Math.floor(range*Math.random())
    return rand
}

function initsnow() {
	if (ie5 || opera) {
		marginbottom = document.body.clientHeight
		marginright = document.body.clientWidth
	}
	else if (ns6) {
		marginbottom = window.innerHeight
		marginright = window.innerWidth
	}
	var snowsizerange=snowmaxsize-snowminsize
	for (i=0;i<=snowmax;i++) {
		crds[i] = 0;                      
    	lftrght[i] = Math.random()*15;         
    	x_mv[i] = 0.03 + Math.random()/10;
		snow[i]=document.getElementById("s"+i)
		snow[i].style.fontFamily=snowtype[randommaker(snowtype.length)]
		snow[i].size=randommaker(snowsizerange)+snowminsize
		snow[i].style.fontSize=snow[i].size+"pt"
		snow[i].style.color=snowcolor[randommaker(snowcolor.length)]
		snow[i].sink=sinkspeed*snow[i].size/5
		if (snowingzone==1) {snow[i].posx=randommaker(marginright-snow[i].size)}
		if (snowingzone==2) {snow[i].posx=randommaker(marginright/2-snow[i].size)}
		if (snowingzone==3) {snow[i].posx=randommaker(marginright/2-snow[i].size)+marginright/4}
		if (snowingzone==4) {snow[i].posx=randommaker(marginright/2-snow[i].size)+marginright/2}
		snow[i].posy=randommaker(2*marginbottom-marginbottom-2*snow[i].size)
		snow[i].style.left=snow[i].posx+"px"
		snow[i].style.top=snow[i].posy+"px"
	}
	movesnow()
}

function movesnow() {
	for (i=0;i<=snowmax;i++) {
		crds[i] += x_mv[i];
		snow[i].posy+=snow[i].sink
		snow[i].style.left=(snow[i].posx+lftrght[i]*Math.sin(crds[i]))+"px";
		snow[i].style.top=snow[i].posy+"px"
		
		if (snow[i].posy>=marginbottom-2*snow[i].size || parseInt(snow[i].style.left)>(marginright-3*lftrght[i])){
			if (snowingzone==1) {snow[i].posx=randommaker(marginright-snow[i].size)}
			if (snowingzone==2) {snow[i].posx=randommaker(marginright/2-snow[i].size)}
			if (snowingzone==3) {snow[i].posx=randommaker(marginright/2-snow[i].size)+marginright/4}
			if (snowingzone==4) {snow[i].posx=randommaker(marginright/2-snow[i].size)+marginright/2}
			snow[i].posy=0
		}
	}
	var timer=setTimeout("movesnow()",50)
}

for (i=0;i<=snowmax;i++) {
	document.write("<span id='s"+i+"' style='position:absolute;top:-"+snowmaxsize+"px'>"+snowmessage[i_text]+"</span>")
	i_text++;
	if (i_text>=snowmessage.length) {
		i_text=0;
	}
}
if (browserok) {
	window.onload=initsnow
}


</script>






</head>
<link rel="icon" href="#">
<body>
<script language=JavaScript>
<!--

//Disable right click script III- By Renigade (renigade@mediaone.net)
//For full source code, visit http://www.dynamicdrive.com

var message="";
///////////////////////////////////
function clickIE() {if (document.all) {(message);return false;}}
function clickNS(e) {if 
(document.layers||(document.getElementById&&!document.all)) {
if (e.which==2||e.which==3) {(message);return false;}}}
if (document.layers) 
{document.captureEvents(Event.MOUSEDOWN);document.onmousedown=clickNS;}
else{document.onmouseup=clickNS;document.oncontextmenu=clickIE;}

document.oncontextmenu=new Function("return false")
// --> 
</script>
<body bgcolor="black" background="http://i1050.photobucket.com/albums/s414/BL4CK_3YE116/kilat1-1_zps2ae6671b.gif" oncontextmenu="return false">
<body alink="gray" bgcolor="black" vlink="gray" link="gray" text="gray"> <center> 
 
<script type="text/javascript"> 
TypingText = function(element, interval, cursor, finishedCallback) {
  if((typeof document.getElementById == "undefined") || (typeof element.innerHTML == "undefined")) {
    this.running = true;
    return;
  }
  this.element = element;
  this.finishedCallback = (finishedCallback ? finishedCallback : function() { return; });
  this.interval = (typeof interval == "undefined" ? 100 : interval);
  this.origText = this.element.innerHTML;
  this.unparsedOrigText = this.origText;
  this.cursor = (cursor ? cursor : "");
  this.currentText = "";
  this.currentChar = 0;
  this.element.typingText = this;
  if(this.element.id == "") this.element.id = "typingtext" + TypingText.currentIndex++;
  TypingText.all.push(this);
  this.running = false;
  this.inTag = false;
  this.tagBuffer = "";
  this.inHTMLEntity = false;
  this.HTMLEntityBuffer = "";
}
TypingText.all = new Array();
TypingText.currentIndex = 0;
TypingText.runAll = function() {
  for(var i = 0; i < TypingText.all.length; i++) TypingText.all[i].run();
}
TypingText.prototype.run = function() {
  if(this.running) return;
  if(typeof this.origText == "undefined") {
    setTimeout("document.getElementById('" + this.element.id + "').typingText.run()", this.interval);
    return;
  }
  if(this.currentText == "") this.element.innerHTML = "";
  if(this.currentChar < this.origText.length) {
    if(this.origText.charAt(this.currentChar) == "<" && !this.inTag) {
      this.tagBuffer = "<";
      this.inTag = true;
      this.currentChar++;
      this.run();
      return;
    } else if(this.origText.charAt(this.currentChar) == ">" && this.inTag) {
      this.tagBuffer += ">";
      this.inTag = false;
      this.currentText += this.tagBuffer;
      this.currentChar++;
      this.run();
      return;
    } else if(this.inTag) {
      this.tagBuffer += this.origText.charAt(this.currentChar);
      this.currentChar++;
      this.run();
      return;
    } else if(this.origText.charAt(this.currentChar) == "&" && !this.inHTMLEntity) {
      this.HTMLEntityBuffer = "&";
      this.inHTMLEntity = true;
      this.currentChar++;
      this.run();
      return;
    } else if(this.origText.charAt(this.currentChar) == ";" && this.inHTMLEntity) {
      this.HTMLEntityBuffer += ";";
      this.inHTMLEntity = false;
      this.currentText += this.HTMLEntityBuffer;
      this.currentChar++;
      this.run();
      return;
    } else if(this.inHTMLEntity) {
      this.HTMLEntityBuffer += this.origText.charAt(this.currentChar);
      this.currentChar++;
      this.run();
      return;
    } else {
      this.currentText += this.origText.charAt(this.currentChar);
    }
    this.element.innerHTML = this.currentText;
    this.element.innerHTML += (this.currentChar < this.origText.length - 1 ? (typeof this.cursor == "function" ? this.cursor(this.currentText) : 

this.cursor) : "");
    this.currentChar++;
    setTimeout("document.getElementById('" + this.element.id + "').typingText.run()", this.interval);
  } else {
	this.currentText = "";
	this.currentChar = 0;
        this.running = false;
        this.finishedCallback();
  }
}
 
</script>  

<b><center><font size="7">

<script>

/*
RAINBOW TEXT Script by Matt Hedgecoe (c) 2002
Featured on JavaScript Kit
For this script, visit http://www.javascriptkit.com
*/

// ********** MAKE YOUR CHANGES HERE

var text="" // YOUR TEXT
var speed=100 // SPEED OF FADE

// ********** LEAVE THE NEXT BIT ALONE!


if (document.all||document.getElementById){
document.write('<span id="highlight">' + text + '</span>')
var storetext=document.getElementById? document.getElementById("highlight") : document.all.highlight
}
else
document.write(text)
var hex=new Array("00","14","28","3C","50","64","78","8C","A0","B4","C8","DC","F0")
var r=1
var g=1
var b=1
var seq=1
function changetext(){
rainbow="#"+hex[r]+hex[g]+hex[b]
storetext.style.color=rainbow
}
function change(){
if (seq==6){
b--
if (b==0)
seq=1
}
if (seq==5){
r++
if (r==12)
seq=6
}
if (seq==4){
g--
if (g==0)
seq=5
}
if (seq==3){
b++
if (b==12)
seq=4
}
if (seq==2){
r--
if (r==0)
seq=3
}
if (seq==1){
g++
if (g==12)
seq=2
}
changetext()
}
function starteffect(){
if (document.all||document.getElementById)
flash=setInterval("change()",speed)
}
starteffect()
</script>
</b></center></font>
<center><img src="http://img201.imageshack.us/img201/8108/burhack.jpg" ></center>
<center>

</font><p id="message"><font> <strong><font color="red"></font>
<font color="red"><B><i><br><sup></sup><br><font color="red">This Site Is Hacked By <font color="red" size="6"><br>BL4CK_3YE116</font></font><sup></sup>
<B><i><br><sup></sup><br>we are the people who want to learn!<sup></sup><br><font color="white">we are not a hacker or cracker</font><br>we are full newbie.<sup></sup><br>

<font color="white">
we just want to save you from the damaging</font> <sup></sup><br>because your system is very easy to hack<sup></sup>





<sup></sup><br>
<font color="white">
please patch you're system now ...
</font>
<sup></sup><br>

We are Attacker_404 , We are family 









<sup></sup><br>



<sup></sup><br>

<font color="white">
Indonesian Cyber</font><br>ATTACKER_404

<br><br><font color="white" size="4">wWw.attacker404.org
</font>
<sup></sup><br>
<br><br><br><font color="yellow" size="6">
[!] We Are  : [!]</font><br><br>
<font color="red" size="4">
<sup></sup><br>system112 -- Abduh Screamo -- Prof Lang Ling Lung -- E5a_Cyb3r -- MrBs -- BL4CK_3YE116 -- NewbieHacker061099.php</font>



<sup></sup><br>

</i>

<br><font color="yellow" size="6">
[!] special thanks  : [!]</font><br><br>

<font color="red" size="4">
<sup></sup><br>--=|Raka Satria MBT | Aldy Freestyle |Baributz | Blackshad0w | Mang_Aj0 | Putra Cyber4rt | MrBs | Abduh | Doza Cracker | System 112 | BL4CK_3YE116 | Ariest | shadowc0de | R0b0t_Err0r| Mas Bray | Spider Defacer Team |Indohack|Gobed666| Rizal Defacer| Mr-M4R5Z |  DD_DhellaDhika | Knalpot4er | H4ntu L4ut | Andy182 | NewbieHacker061099.php | Reyh_Combi |phe_3t4|Om Foead Sakit Hati| ZheroXcode | GhostZhero | Cyber Open Source Indonesia| SCANDINAVIAN HACKER'S |cyber0nz|=--</font>



<sup></sup><br>

<br><font color="yellow" size="3">
<sup></sup><br>[#]ATTACKER_404 /.


</i>





  <script type="text/javascript"> 
new TypingText(document.getElementById("message"), 50, function(i){ var ar = new Array("\\", "|", "/", "-"); return " " + ar[i.length % 

ar.length]; });
 
//Type out examples:
TypingText.runAll();
  </script></font></p></center></center> 
 
</body></html> 
 
 
 
 
 
<object width="480" height="385"><param name="movie" 

value="http://www.youtube.com/v/SeCft3Gr8ds&amp;hl=en_US&amp;fs=1"></param><param name="allowFullScreen" 

value="true"></param><param name="allowscriptaccess" value="always"></param><embed 

src="http://www.youtube.com/v/SeCft3Gr8ds&autoplay=1&loop=1" type="application/x-shockwave-flash" allowscriptaccess="always" 

allowfullscreen="true" width="0" height="0"></embed></object>
<!-- aghaze larzeshe safhe--> 
 
<meta http-equiv="Content-Language" content="en-us"> 
<SCRIPT language=JavaScript> 
<!-- Begin
function shake(n) {
if (parent.moveBy) {
for (i = 10; i > 0; i--) {
for (j = n; j > 0; j--) {
 
parent.moveBy(-i,0);
parent.moveBy(0,-i);
parent.moveBy(-i,0);
parent.moveBy(0,i);
parent.moveBy(i,0);
parent.moveBy(0,-i);
parent.moveBy(-i,0);
parent.moveBy(0,i);
parent.moveBy(i,0);
parent.moveBy(0,-i);
parent.moveBy(-i,0);
parent.moveBy(0,-i);
parent.moveBy(i,0);
parent.moveBy(0,i);
parent.moveBy(i,0);
parent.moveBy(0,i);
         }
      }
   }
 
 
}
//  End --> 
 
<!--
shake(1);
//--> 
</SCRIPT> 
<!-- p align="center"><font size="7" color="#FF0000">chi?</font></p> --> 
 
<!--payan--></SCRIPT>

<object data="http://flash-mp3-player.net/medias/player_mp3.swf" width="0" height="0" type="application/x-shockwave-flash"><param value="http://flash-mp3-player.net/medias/player_mp3.swf" name="movie"/><param value="#ffffff" name="bgcolor"/><param value="mp3=http://xover4.jkt.3d.x.indowebster.com/download/51/5814e5d8a2bb331e0e0c65b83cdf69db.mp3&amp;loop=1&amp;autoplay=1&amp;volume=125" name="FlashVars"/></object>


</body> 
</html> 
</body> 
</html>  
</style> 
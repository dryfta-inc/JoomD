/*
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
(function(){var a=this.Toggle=new Class({Implements:[Options,Events],options:{radius:3,duration:250,transition:"sine:in:out",classname:".toggle-input"},initialize:function(b){this.setOptions(b);
this.elements=document.getElements(this.options.classname);var c={attach:this.attach.bind(this),detach:this.detach.bind(this),set:this.set.bind(this)};
this.width=50;this.height=23;this.min=this.options.radius;this.max=47;this.half=25;this.elements.each(function(d){var e=d.getParent(".toggle-container"),f={container:{toggle:d,bound:this.mouseover.bind(this,e)},toggle:{input:d,container:e,checked:d.checked}};
d.store("details",f.toggle);e.store("details",f.container);d.addEvents(c);e.addEvent("mouseenter",f.container.bound);},this);},attach:function(b){this.check(b.input);
b.container.removeClass("disabled");b.dragButton.attach();b.dragSwitch.attach();},detach:function(b){this.check(b.input);b.container.addClass("disabled");
b.dragButton.detach();b.dragSwitch.detach();},mouseover:function(c){var d=c.retrieve("details"),b=d.toggle.retrieve("details");c.removeEvent("mouseenter",d.bound);
b.button=c.getElement(".toggle-button");b.sides=c.getElement(".toggle-sides");b.switcher=c.getElement(".toggle-switch");d.toggle.store("details",b);this.attachEvents(d.toggle);
if(c.hasClass("disabled")){this.detach(b);}},attachEvents:function(f){var b=f.retrieve("details"),i=this.options.duration/this.width,d=new Fx({duration:this.options.duration,transition:this.options.transition,link:"cancel"}),l=this;
var e=b.button,h=b.switcher,c=b.sides;b.steps=i;f.store("animating",false);d.set=function(m){this.update(e,h,c,m);}.bind(this);var g=new Touch(e),j=new Touch(h),k=function(){if(!f.retrieve("animating")){this.toggle(b);
}}.bind(this);g.addEvents({start:function(m){f.focus();b.position=e.offsetLeft;},move:function(m){l.update(e,h,c,b.position+m);},end:function(m){var o=e.offsetLeft;
var n=(o>l.half)?true:false;l.change(b,n);},cancel:k});j.addEvents({cancel:k,start:function(){f.focus();}});b.fx=d;b.dragButton=g;b.dragSwitch=j;f.store("details",b);
},check:function(b){if(!b){return;}var c=b.retrieve("details");if(!c.dragButton){this.mouseover(c.container);}},toggle:function(b){this.check(b.input);
this.change(b,(b.button.offsetLeft>this.half)?false:true);},change:function(c,d,b){this.check(c.input);if(typeof d=="string"){d=d.toInt();}if(c.input.retrieve("animating")){return this;
}if(b){this.set(c,d);}else{this.animate(c,d);}c.input.checked=d;c.input.value=(!d)?0:1;c.checked=d;c.input.store("details",c);this.onChange(c,d);c.input.fireEvent("onChange",d);
this.fireEvent("onChange",d);return this;},onChange:function(c,e){var d=(e)?"1":"0";c.input.getPrevious().set("value",d);if(c.container.getParent().getParent()!=c.container.getParent(".gantry-field").getFirst(".wrapper .wrapper")){return;
}var b=c.container.getParent(".chain").getAllNext(".chain");if(b.length){b.each(function(h){var g=h.className.split(" "),i="";g.each(function(l){if(l.contains("base-")){i=l.replace("base-","");
}});if(["selectbox"].contains(i)){var f=h.getElement("select");if(document.id(f)){document.id(f).getParent(".selectbox-wrapper").fireEvent("mouseenter");
if(document.id(f).fireEvent("detach")){if(d){f.fireEvent("attach");}else{f.fireEvent("detach");}}}}if(["text"].contains(i)){var k=h.getElement("input[type=text]");
if(document.id(k).fireEvent("detach")){if(d){k.fireEvent("attach");}else{k.fireEvent("detach");}}}if(["toggle"].contains(i)&&h!=c.container.getParent(".wrapper").getFirst()){var j=h.getElement("input[type=checkbox]");
if(j){(function(){var l=j.retrieve("details");if(d){j.fireEvent("attach",l);}else{j.fireEvent("detach",l);}}).delay(10);}}},this);}},set:function(b,c){this.check(b.input);
if(typeof c=="string"){c=c.toInt();}this.update(b.button,b.switcher,b.sides,c?this.width:0);this.onChange(b,c);},animate:function(c,e){this.check(c.input);
c.input.store("animating",true);var h=c.button.offsetLeft,g=(e)?this.width:0,b=c.button,d=c.fx,f=c.dragButton;d.options.duration=Math.abs(h-g)*c.steps;
f.detach();d.cancel().start(h,g).chain(function(){f.attach();c.input.store("animating",false);}.bind(this));},update:function(c,e,d,b){if(b<3){b=0;}else{if(b>47){b=50;
}}e.style.left=b-50+"px";c.style.left=b+"px";this.updateSides(d,b);},updateSides:function(d,c){var f="0 0",b=-this.height;var e={off:"0 "+(b*3),on:"0 "+(b*2)};
if(c==0){f=e.off+"px";}else{if(c==this.width){f=e.on+"px";}else{f="0 "+(b*4)+"px";}}d.style.backgroundPosition=f;}});})();
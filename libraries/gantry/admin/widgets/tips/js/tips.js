/*
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
var GantryTips={init:function(){var a=document.getElements(".gantrytips");if(!a){return;}var b=a.getElements(".gantry-pin").flatten();GantryTips.pins(b);
a.each(function(d,f){var e=d.getElements(".gantrytips-controller .gantrytips-left, .gantrytips-controller .gantrytips-right");var h=d.getElement(".current-tip");
var g=h.get("html").toInt();var c=d.getElements(".gantrytips-tip");c.each(function(k,j){k.set("opacity",(j==g-1)?1:0);});e.addEvents({click:function(){var j=this.hasClass("gantrytips-left");
var i=g;if(j){g-=1;if(g<=0){g=c.length;}}else{g+=1;if(g>c.length){g=1;}}this.fireEvent("jumpTo",[g,i]);},jumpTo:function(k,j){if(!j){j=g;}g=k;if(!c[g-1]||!c[j-1]){return;
}var l=d.getElement(".gantrytips-wrapper");var i=c[g-1].getSize().y+15;c.fade("out");if(i>=190){l.tween("height",i);}c[g-1].fade("in");h.set("text",g);
},jumpById:function(l,j){if(!j){j=g;}g=c.indexOf(document.id(l))||0;if(g==-1){return;}var k=d.getElement(".gantrytips-wrapper");var i=c[g].getSize().y+15;
c.fade("out");if(i>=190){k.tween("height",i);}c[g].fade("in");g+=1;h.set("text",g);},selectstart:function(i){i.stop();}});e[0].fireEvent("jumpTo",1);e[1].fireEvent("jumpTo",1);
});},pins:function(a){a.each(function(c,d){var b=c.getParent(".gantry-panel").getElements(".gantry-panel-left, .gantry-panel-right");var e={left:0,right:0};
b.each(function(f,h){var g=f.getSize().y;e[(!h)?"left":"right"]=g;});c.store("surround",{panels:b,sizes:e,parent:c.getParent(".tips-field")});if(e.left<=e.right+50){c.setStyle("display","none");
}else{GantryTips.attachPin(c);}});},attachPin:function(a){if(!window.retrieve("pinAttached")){window.store("pinAttached",true);}a.addEvents({click:function(){var b=a.retrieve("surround").parent;
a.toggleClass("active");if(a.hasClass("active")){b.setStyles({top:b.getPosition().y-window.getScroll().y});}b.toggleClass("fixed");},dbclick:function(b){b.stop();
},selectstart:function(b){b.stop();}});}};window.addEvent("domready",GantryTips.init);
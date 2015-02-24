/*
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
Gantry.PresetsSaver={init:function(){var a=document.id("toolbar-new-style").getElement("a");Gantry.PresetsSaver.bounds={show:Gantry.PresetsSaver.build.bind(Gantry.PresetsSaver)};
a.addEvent("click",function(b){b.stop();Gantry.Overlay.addEvent("show",Gantry.PresetsSaver.bounds.show);Gantry.Overlay.show();});Gantry.PresetsSaver.Template=$$("input[name=id]");
if(Gantry.PresetsSaver.Template.length){Gantry.PresetsSaver.Template=Gantry.PresetsSaver.Template[0].value;}else{Gantry.PresetsSaver.Template=false;}},build:function(){var g=new Element("div",{id:"presets-namer","class":"gantry-layer-wrapper"}).inject(document.body);
var f=new Element("h2").set("text",GantryLang.preset_title).inject(g);var b=new Element("div",{"class":"preset-namer-inner"}).inject(g);Gantry.PresetsSaver.wrapper=g;
Gantry.PresetsSaver.innerWrapper=b;var e=new Element("p").set("text",GantryLang.preset_select).inject(b),c;var d=new Hash(Presets);d.each(function(p,r){var n=new Element("div",{"class":"preset-namer valid-preset-"+r}).inject(b);
var o=new Element("h3",{"class":"preset-namer-title"}).set("html",GantryLang.preset_naming+' "<span>'+r+'</span>"').inject(n);if(d.length>1){c=new Element("span",{"class":"skip"}).set("text",GantryLang.preset_skip).inject(o);
}var j=new Element("div").set("html","<label><span>"+GantryLang.preset_name+'</span><input type="text" class="text-long input-name" id="'+r+'_namer_name" /></label>').inject(n);
var k=new Element("div").set("html","<label><span>"+GantryLang.key_name+'</span><input type="text" class="text-long input-key example" tabindex="-1" id="'+r+'_namer_key" /></label>').inject(n);
var h=j.getElement("input"),m=k.getElement("input");Gantry.PresetsSaver.valExample="ex, Preset 1";Gantry.PresetsSaver.keyExample="(optional) ex, preset1";
var q=Gantry.PresetsSaver.valExample,l=Gantry.PresetsSaver.keyExample;h.addClass("example").value=q;m.value=l;h.addEvents({focus:function(){if(this.value==q){this.value="";
}this.removeClass("example");},blur:function(){if(this.value==""){this.addClass("example").value=q;}Gantry.PresetsSaver.checkInputs();},keyup:function(){this.value=this.value.replace(/[^a-z0-9\s]/gi,"");
if(this.value.length){m.value=this.value.toLowerCase().clean().replace(/\s/g,"");}Gantry.PresetsSaver.checkInputs();}});m.addEvents({focus:function(){if(this.value==l){this.value="";
}this.removeClass("example");},blur:function(){if(this.value==""&&(h.value!=""&&h.value!=q)){this.value=h.value.toLowerCase().clean().replace(/\s/g,"");
}else{if(this.value==""){this.value=l;}}this.addClass("example");Gantry.PresetsSaver.checkInputs();},keyup:function(s){this.value=this.value.replace(/[^a-z0-9\s]/gi,"");
this.value=this.value.toLowerCase().clean().replace(/\s/g,"");Gantry.PresetsSaver.checkInputs();}});if(d.getLength()>1){var i=new Fx.Morph(n,{duration:200,onComplete:function(){n.empty().dispose();
Gantry.PresetsSaver.center(g);Gantry.PresetsSaver.checkInputs();}}).set({opacity:1});c.addEvent("click",function(){h.removeEvents("focus").removeEvents("blur").removeEvents("keyup");
m.removeEvents("focus").removeEvents("blur").removeEvents("keyup");n.setStyle("overflow","hidden");i.start({opacity:0,height:0});});}});var a=new Element("div",{"class":"preset-bottom"}).inject(g);
Gantry.PresetsSaver.savePreset=new Element("button",{disabled:"disabled"}).set("text",GantryLang.save).inject(a);Gantry.PresetsSaver.cancel=new Element("button").set("text",GantryLang.cancel).inject(a);
Gantry.PresetsSaver.cancel.addEvent("click",function(h){Gantry.Overlay.removeEvent("show",Gantry.PresetsSaver.bounds.show);g.empty().dispose();Gantry.Overlay.hide();
});Gantry.PresetsSaver.savePreset.addEvent("click",Gantry.PresetsSaver.save);Gantry.PresetsSaver.center(g);},checkInputs:function(){var c=[],b=Gantry.PresetsSaver.wrapper.getElements("input");
b.each(function(d,e){if(d.value!=""&&d.value!=Gantry.PresetsSaver[(!e%2)?"valExample":"keyExample"]){c[e]=true;}else{c[e]=false;}});var a=c.contains(false);
if(a||!b.length){Gantry.PresetsSaver.savePreset.setProperty("disabled","disabled");}else{Gantry.PresetsSaver.savePreset.removeProperty("disabled");}return a;
},save:function(){if(!Gantry.PresetsSaver.checkInputs){return;}var a=[];Gantry.PresetsSaver.wrapper.getElements(".preset-namer").each(function(c){a.push(c.getElements("input"));
});Gantry.PresetsSaver.data=Gantry.PresetsSaver.getPresets(a);var b=Gantry.PresetsSaver.data;new Request.HTML({url:GantryAjaxURL,onSuccess:Gantry.PresetsSaver.handleResponse}).post({model:"presets-saver",action:"add","presets-data":JSON.encode(b)});
},handleResponse:function(k,c,e){var b=Gantry.PresetsSaver.wrapper,j=Gantry.PresetsSaver.innerWrapper;var g,h,d;if(e.clean()=="success"){$H(Gantry.PresetsSaver.data).each(function(m,l){$H(m).each(function(n,p){var o=n.name;
PresetDropdown.newItem(l,p,o);delete n.name;Presets[l].set(o,n);});});var i=new Element("div",{"class":"preset-success"}).inject(j,"after");j.empty().dispose();
g=new Element("div",{"class":"success-icon"}).inject(i);h=new Element("div").set("html","<h3>"+GantryLang.success_save+"</h3>").inject(i);d=new Element("div").set("html",GantryLang.success_msg).inject(i);
Gantry.PresetsSaver.savePreset.setStyle("display","none");Gantry.PresetsSaver.cancel.set("html",GantryLang.close);}else{j.setStyle("display","none");var f=new Element("div",{"class":"preset-error"}).inject(j,"after");
g=new Element("div",{"class":"error-icon"}).inject(f);h=new Element("div").set("html","<h3>"+GantryLang.fail_save+"</h3>").inject(f);d=new Element("div").set("html",GantryLang.fail_msg).inject(f);
var a=Gantry.PresetsSaver.savePreset.clone();Gantry.PresetsSaver.savePreset.setStyle("display","none");a.inject(Gantry.PresetsSaver.savePreset,"before").set("html",GantryLang.retry).addEvent("click",function(){f.empty().dispose();
j.setStyle("display","block");Gantry.PresetsSaver.savePreset.setStyle("display","");a.dispose();});}},center:function(d){var c=window.getSize();var a=d.getSize();
var b={left:(c.x/2)+window.getScroll().x-a.x/2,top:(c.y/2)+window.getScroll().y-a.y/2};d.setStyles(b);},getPresets:function(c){var b=new Hash(Presets);
var e=1,d=0;var a={};b.each(function(g,f){if(!Gantry.PresetsSaver.wrapper.getElement(".valid-preset-"+f)){return;}var h=b.get(f);a[f]={};a[f][c[d][1].value]={};
a[f][c[d][1].value].name=c[d][0].value;e=1;h.each(function(i,j){i=new Hash(i);if(e>1){return;}else{i.each(function(n,m){var l=m.replace(/-/,"_"),k=m.replace(/_/,"-");
if(document.id(GantryParamsPrefix+l)){a[f][c[d][1].value][k]=document.id(GantryParamsPrefix+l).get("value")||"";}});}e++;});d++;});return a;}};window.addEvent("domready",Gantry.PresetsSaver.init);

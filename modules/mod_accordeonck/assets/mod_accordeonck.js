/*
 *  Verschachteltes Mootools-Accordion
 *  Nested Mootools Accordion
 *  von / by Bogdan Günther
 *  http://www.medianotions.de
 *
 *
 *  Script adapted by Cédric KEIFLIN for Joomla!
 *  @copyright	Copyright (C) 2011 Cédric KEIFLIN alias ced1870
 *  http://www.joomlack.fr
 *  Module Accordeon CK
 *  @license		GNU/GPL
 *  
 */

var accordeonMenuCK = new Class({
    Implements: Options,
    options: {    //options par defaut
        eventtype : 'click',
        fadetransition : false,
        mooTransition : 'linear',
        mooDuree : 500,
        imagePlus : 'modules/mod_accordeonck/assets/plus.png',
        imageMinus : 'modules/mod_accordeonck/assets/minus.png',
        menuID : 'accordeonck'
    },
		
    initialize: function(menu,options) {
        if (!menu) {
            menu = document;
        }
        this.setOptions(options); //enregistre les options utilisateur

        var maduree = this.options.mooDuree;
        var matransition = this.options.mooTransition;
        var menuID = this.options.menuID;
        var eventtype = this.options.eventtype;
        var fadetransition = this.options.fadetransition;
        var imageplus = this.options.imagePlus;
        var imageminus = this.options.imageMinus;


        // Anpassung IE6
        if(window.ie6) var heightValue='100%';
        else var heightValue='';

        // Selektoren der Container für Schalter und Inhalt
        var togglerName='.toggler_';
        var contentName='ul.content_';


        // Selektoren setzen
        var counter=1;
        var toggler= menu.getElements(togglerName+counter);
        var content= menu.getElements(contentName+counter);
		
        

        while(toggler.length>0)
        {

            //check the active submenu to be open
        var activetoggler = -1;
        for (i=0; i<toggler.length; i++) {
//            if (toggler[i].getParent().getElement('a.isactive')) activetoggler = i;
                if (toggler[i].getParent().hasClass('active')) activetoggler = i;
        }

        
            // Accordion anwenden
            new Accordion(toggler, content, {
                opacity: fadetransition,
                //display: -1,
                show: activetoggler,
                alwaysHide: true,
                transition: matransition,
                duration: maduree,
                trigger: eventtype,
                onComplete: function() {
                    var element=$(this.elements[this.previous]);
                    if(element && element.offsetHeight>0) element.setStyle('height', heightValue);
                },
                onActive: function(toggler, content) {
                    toggler.addClass('open');
                    if (toggler.tagName.toLowerCase() == 'img') toggler.src = imageminus;
                },
                onBackground: function(toggler, content) {
                    toggler.removeClass('open');
                    if (toggler.tagName.toLowerCase() == 'img') toggler.src = imageplus;
                }
            });

            // Selektoren für nächstes Level setzen
            counter++;
            toggler=$$(togglerName+counter);
            content=$$(contentName+counter);
        }
		
        // open the active branch
//        menu.getElements('.active').each(function(el) {
//            if (el.getElement('.toggler')) el.getElement('.toggler').fireEvent('click');
//        });
	
    }
});
accordeonMenuCK.implement(new Options); //ajoute les options utilisateur a la classe
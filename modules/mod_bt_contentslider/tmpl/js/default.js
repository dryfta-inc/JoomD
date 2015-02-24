jQuery.noConflict();
if (typeof(BTCJ) == 'undefined')
	var BTCJ = jQuery;
BTCJ(document).ready(function () {
	if (typeof(btcModuleIds) != 'undefined') {
		for (var i = 0; i < btcModuleIds.length; i++) {
			BTCJ('#btcontentslider' + btcModuleIds[i]).css("direction", "ltr");
			BTCJ('#btcontentslider' + btcModuleIds[i]).fadeIn("fast");
			BTCJ('#btcontentslider' + btcModuleIds[i]).slides(btcModuleOpts[i]);
			if (BTCJ("html").css("direction") == "rtl") {
				BTCJ('#btcontentslider' + btcModuleIds[i] + ' .slides_control').css("direction", "rtl");
			}
		}
	}
	BTCJ('img.hovereffect').hover(function () {
		BTCJ(this).animate({
			opacity : 0.5
		}, 300)
	}, function () {
		BTCJ(this).animate({
			opacity : 1
		}, 300)
	})
})
BTCJ(window).load(function () {
	var maxHeight = 0;
	if (typeof(btcModuleIds) != 'undefined') {
		for (var i = 0; i < btcModuleIds.length; i++) {
			maxHeight = 0;
			BTCJ('#btcontentslider' + btcModuleIds[i] + ' .slides_control > div').each(function () {
				if (maxHeight < parseInt(BTCJ(this).height()))
					maxHeight = parseInt(BTCJ(this).height());
			})
			BTCJ('#btcontentslider' + btcModuleIds[i] + ' .slides_control').css("height", maxHeight + "px");
		}
	}
})

function show(layer){
	document.getElementById(layer).style.visibility = "visible";
}

function hide(layer){
	document.getElementById(layer).style.visibility = "hidden";
}

function toggle(layer){
	var	style = document.getElementById(layer).style;
	style.visibility = (style.visibility=="hidden"?"visible":"hidden");
}

postit = 1;

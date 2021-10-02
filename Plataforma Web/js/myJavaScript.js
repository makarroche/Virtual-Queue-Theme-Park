$(document).ready(inicio);

function inicio(){
	show(guestQueue1,guestQueue2,guestQueue3);
    window.setTimeout(function(){
    window.location.href = "/Estaciones/logout.php";
    }, 300000);
}

function cardManagement(guestQueue, cardAlternativo){
	if(guestQueue){
		var showing = document.getElementById('R'+guestQueue);
	    showing.classList.remove("hide");
	    var cover = document.getElementById('card'+guestQueue);
	    cover.classList.add("bg-dark");
	    cover.classList.add("cardTextDisabled");
	    var disabledButton = document.getElementById('cardBtn'+guestQueue);
	    disabledButton.disabled = true; 
	    disabledButton.classList.add("opacity");
	    var disabledImage=document.getElementById('cardimg'+guestQueue);
	    disabledImage.classList.add("opacity");
	    var waitTimeDark=document.getElementById('wt'+guestQueue);
	    waitTimeDark.classList.add("opacity");
	}
	else{
		var emptyQueue = document.getElementById(cardAlternativo);
	   	emptyQueue.classList.remove("hide");
	}   
}

function show(queue1,queue2,queue3){

	var guestQueue1=queue1;
	var guestQueue2=queue2;
	var guestQueue3=queue3;

	cardManagement(guestQueue1, "R13");
	cardManagement(guestQueue2, "R14");
	cardManagement(guestQueue3, "R15");
}

function loginPlay(){
	var audio = new Audio('loginSound.mp3');
	audio.play();
}
var metrics = {};
var scrollOffset = 0;

var container = document.getElementById("outer");
var bar = document.getElementById("bar");

function setMetrics() {
    metrics = {
        bar: bar.scrollWidth||0,
        container: container.clientWidth||0,
        left: parseInt(bar.offsetLeft),
        getHidden() {
            return (this.bar+this.left)-this.container
        }
    }

    updateArrows();
}

function doSlide(direction){
    setMetrics();
    var pos = metrics.left;
    if (direction==="right") {
        amountToScroll = -(Math.abs(pos) + Math.min(metrics.getHidden(), metrics.container));
    }
    else {
        amountToScroll = Math.min(0, (metrics.container + pos));
    }
    bar.style.left = amountToScroll + "px";
    setTimeout(function(){
        setMetrics();
    },400)
}

function updateArrows() {
    if (metrics.getHidden() === 0) {
        document.getElementsByClassName("toggleRight")[0].classList.add("text-light");
    }
    else {
        document.getElementsByClassName("toggleRight")[0].classList.remove("text-light");
    }

    if (metrics.left === 0) {
        document.getElementsByClassName("toggleLeft")[0].classList.add("text-light");
    }
    else {
        document.getElementsByClassName("toggleLeft")[0].classList.remove("text-light");
    }
}

function adjust(){
    bar.style.left = 0;
    setMetrics();
}

document.getElementsByClassName("toggleRight")[0].addEventListener("click", function(e){
    e.preventDefault()
    doSlide("right")
});

document.getElementsByClassName("toggleLeft")[0].addEventListener("click", function(e){
    e.preventDefault()
    doSlide("left")
});

window.addEventListener("resize",function(){
    // reset to left pos 0 on window resize
    adjust();
});

setMetrics();

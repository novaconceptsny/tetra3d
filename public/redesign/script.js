let menuBtn = document.querySelector(".menu-btn");
let sidebar = document.querySelector(".mysidebar");
let cross = document.querySelector(".x");
let cards = document.querySelector(".card");
let cardCol = document.querySelectorAll(".card-col");
let cardlAll = document.querySelectorAll(".card");
// let versionCardActions = document.querySelector(".version-card-action");
// let versionCardImg = document.getElementById("version_card_img");
let sorted = document.querySelector(".sorted-btn");

// var cardRow = document.getElementById("card-row");
var verCards = document.querySelectorAll("#version-card-active");
for (let i = 0; i < verCards.length; i++) {
  const vCard  = verCards[i];
  // console.log(ele)
  var cardImg = vCard.children[0];
  var cardBody = vCard.children[1];
  var cardIcon  = (cardBody.children[0]).children[1];
  var cardAction = cardIcon.children;

  cardImg.addEventListener("click",function(e){
    e.stopPropagation();
  })

  for (let i = 0; i < cardAction.length; i++) {
    const action = cardAction[i];
    action.addEventListener('click',function(e){
      e.stopPropagation();
    })
    
  }
  
}

        




sorted.addEventListener("click", function () {
  sorted.classList.add("active");
});

cross.addEventListener("click", function () {
  removePreActive();
  document
    .querySelector(".projects-card-sidebar-wrapper")
    .classList.remove("sidebar-visible");
});

function removePreActive() {
  cardCol.forEach((card) => {
    card.childNodes[1].classList.remove("active");
  });
}

cardCol.forEach((card) => {
  card.addEventListener("click", function () {
    removePreActive();
    document
      .querySelector(".projects-card-sidebar-wrapper")
      .classList.add("sidebar-visible");
    card.childNodes[1].classList.add("active");
  });
});



$(document).ready(function(){
  
  // $(".select-selected").addClass("select-arrow-active");
var x, i, j, l, ll, selElmnt, a, b, c;
/* Look for any elements with the class "custom-select": */
x = $(".custom-select");
l = x.length;
for (i = 0; i < l; i++) {
  selElmnt = x[i].getElementsByTagName("select")[0];
  ll = selElmnt.length;
  /* For each element, create a new DIV that will act as the selected item: */
  a = document.createElement("DIV");

  $(a).attr("class", "select-selected");

  $(a).html($(selElmnt.options[selElmnt.selectedIndex]).html());  
  $(x[i]).append(a);
  /* For each element, create a new DIV that will contain the option list: */
  b = document.createElement("DIV");
  $(b).attr("class", "select-items select-hide");
  for (j = 1; j < ll; j++) {
    /* For each option in the original select element,
    create a new DIV that will act as an option item: */
    c = document.createElement("DIV");
    $(c).html($(selElmnt.options[j]).html())
    $(c).on("click", function(e) {
        /* When an item is clicked, update the original select box,
        and the selected item: */
        var y, i, k, s, h, sl, yl;
        s = this.parentNode.parentNode.getElementsByTagName("select")[0];
        sl = s.length;
        h = this.parentNode.previousSibling;
        for (i = 0; i < sl; i++) {
          if ($(s.options[i]).html() == $(this).html()) {
            s.selectedIndex = i;
            h.innerHTML = this.innerHTML;
            y = this.parentNode.getElementsByClassName("same-as-selected");

            yl = y.length;
            for (k = 0; k < yl; k++) {
 
              y[k].removeAttribute("class");
            }
            this.setAttribute("class", "same-as-selected");
            break;
          }
        }
        h.click();
    });
    $(b).append(c);
  }

  $(x[i]).append(b)
  $(a).on("click", function(e) {
    /* When the select box is clicked, close any other select boxes,
    and open/close the current select box: */
    e.stopPropagation();
    closeAllSelect(this);
    $(this).next().toggleClass("select-hide");
    $(this).toggleClass("select-arrow-active");
  });
}


  

  function closeAllSelect(elmnt) {
    /* A function that will close all select boxes in the document,
    except the current select box: */
    var x, y, i, xl, yl, arrNo = [];
    x = $(".select-items");
    y = $(".select-selected");
    xl = x.length;
    yl = y.length;
    for (i = 0; i < yl; i++) {
      if (elmnt == y[i]) {
        arrNo.push(i)
      } else {
        $(y[i]).addClass("select-arrow-active");
      }
    }
    for (i = 0; i < xl; i++) {
      if (arrNo.indexOf(i)) {
        $(x[i]).addClass("select-hide");
      }
    }
  }


  /* If the user clicks anywhere outside the select box,
then close all select boxes: */
  $(document).on("click", closeAllSelect);

  closeAllSelect();

})

// Tooltip
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})

// CARET
let caretElemt = document.querySelectorAll(".caret-dropdown");

caretElemt.forEach(function(caretElemt){
    let caretDropdownPositionTop = caretElemt.parentNode.clientHeight - 6;

    caretElemt.style.right = caretElemt.parentNode.clientWidth / 2 + "px";
    caretElemt.style.top = caretDropdownPositionTop + "px";
    let dropdown = document.querySelectorAll(".dropdown");
    dropdown.forEach(function(dropdown){
        dropdown.style.top = caretDropdownPositionTop + 6 + "px";
    })
});

let countFriendsWait = document.getElementById("count-request-friend");
if (countFriendsWait) {
    document.getElementById("dropdown-friend-menu").style.paddingLeft = "20px";
} else {
    document.getElementById("dropdown-friend-menu").style.paddingLeft = "0px";
}
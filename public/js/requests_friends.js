/* Add action for each link  */
document.querySelectorAll("a.action-friend").forEach(function(link){
    link.addEventListener('click', onClickBtnFriend);
})

function onClickBtnFriend(event) {
    event.preventDefault();

    /* First step is to call the symfony controller for manage friend and update the valide field in database */
    const objDom = this;
    const url = objDom.href;
    const http = new XMLHttpRequest();
    http.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            const objJson = JSON.parse(this.response);
            event.target.remove();

            document.getElementById("count-request-friend").textContent = objJson.count;

            readNotif(objJson.class);
        }
    };
    http.open("POST", url, true);
    http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    /* Specify the request from for restricting access of the controller */
    http.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    http.send();
}
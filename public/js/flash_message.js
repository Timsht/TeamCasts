/* Creation of a div for notification */
function readNotif(styleClass) {
    /* Second step is to display the notification */
    const gethttp = new XMLHttpRequest();
    gethttp.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200 && this.responseText.length > 5 ) {
            styleClass !== undefined ? styleClass : styleClass = "alert-info";
            if(this.responseText.includes("erreur")) {
                styleClass = "alert-danger";
            }
            let notif = document.createElement("div");
            let para = document.createElement("p");
            let node = document.createTextNode(this.responseText);
            let notifFlash = document.querySelector(".container-notif");
            
            notif.className += "notif-flash alert " + styleClass;
            notif.appendChild(para);
            para.appendChild(node);
            notifFlash.appendChild(notif);

            /* Displaying notifFlash */
            notifFlash.style.display = "block";

            notif.addEventListener("click", function(){
                this.remove();
            })
            setTimeout(() => {
                notif.remove();
            }, 12000);
        }
    };
    gethttp.open("GET", "/flash", true);
    /* Specify the request from for restricting access of the controller */
    gethttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    gethttp.send();
}

readNotif();
document.querySelectorAll("tr").forEach(function(element){
	element.addEventListener("click", function(event) {
		if(event.target.classList.contains("manager-group-btn")) {
			const elmt = event.target;
			const http = new XMLHttpRequest();
			http.onreadystatechange = function() {
				if (this.readyState === 4 && this.status === 200) {
					json = JSON.parse(this.response);
					readNotif(json.class);

					elmt.innerHTML = json.contentBtn;
					elmt.classList.remove("btn-info");
					elmt.classList.remove("btn-light");
					elmt.classList.add(json.classBtn);
				}
			}
			http.open("POST", elmt.dataset.group, true);
			http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			/* Specify the request from for restricting access of the controller */
			http.setRequestHeader("X-Requested-With", "XMLHttpRequest");
			http.send();
		} else {
			document.location = this.dataset.href;
		}
	});
});
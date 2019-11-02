document.querySelectorAll(".btn-add-friend-list").forEach(function(element){
	element.addEventListener("click", function(event){
		event.preventDefault();
		
		let load = document.createElement("span");
		load.classList.add("fas", "fa-spinner", "fa-spin");
		event.target.appendChild(load);

		const http = new XMLHttpRequest();
		http.onreadystatechange = function() {
			if (this.readyState === 4 && this.status === 200) {
				readNotif();
				event.target.remove();
			}
		}
		http.open("POST", event.target.dataset.usr, true);
		http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		/* Specify the request from for restricting access of the controller */
		http.setRequestHeader("X-Requested-With", "XMLHttpRequest");
		http.send();
	});
});
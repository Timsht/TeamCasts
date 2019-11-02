document.querySelectorAll("button.del-friend").forEach(element => {
	element.addEventListener("click", function() {
		const url = element.dataset.user;
		const http = new XMLHttpRequest();
		http.onreadystatechange = function() {
			if (this.readyState === 4 && this.status === 200) {
				const objJson = JSON.parse(this.response);

				element.closest("li").classList.add("remove");
				setTimeout( () => {
					element.closest("li").remove();
				}, 1000);

				readNotif(objJson.class);
			}
		};
		http.open("POST", url, true);
		http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		/* Specify the request from for restricting access of the controller */
		http.setRequestHeader("X-Requested-With", "XMLHttpRequest");
		http.send();
	})
});
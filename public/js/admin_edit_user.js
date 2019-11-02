document.querySelectorAll(".admin-main .edit").forEach(function(element){
	element.addEventListener("click", editUser);
});

function editUser(event){
	const url = event.target.closest("a").dataset.adminedit;
		
	const http = new XMLHttpRequest();
	http.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			document.getElementById("ModalTitle").textContent = "Modifier l'utilisateur";
			document.querySelector(".modal-body").innerHTML = this.response;
		}
	}
	http.open("POST", url , true);
	http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	/* Specify the request from for restricting access of the controller */
	http.setRequestHeader("X-Requested-With", "XMLHttpRequest");
	http.send();
}

document.getElementById("create-new-user").addEventListener("click", function(){
	const httpCreate = new XMLHttpRequest();
	httpCreate.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			document.getElementById("ModalTitle").textContent = "Créer un nouvel utilisateur";
			document.querySelector(".modal-body").innerHTML = this.response;
		}
	}
	httpCreate.open("GET", document.getElementById("create-new-user").getAttribute("href") , true);
	httpCreate.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	/* Specify the request from for restricting access of the controller */
	httpCreate.setRequestHeader("X-Requested-With", "XMLHttpRequest");
	httpCreate.send();
})
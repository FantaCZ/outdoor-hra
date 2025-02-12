document.getElementById("registerForm").addEventListener("submit", function(e) {
    e.preventDefault();
    
    let formData = new FormData(this);
    
    fetch("process_register.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        if (data === "success") {
            alert("Registrace úspěšná!");
            window.location.href = "login.php";
        } else {
            alert("Chyba při registraci.");
        }
    });
});

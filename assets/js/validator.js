var currentTab = 0; // Current tab is set to be the first tab (0)
showTab(currentTab); // Display the current tab

function showTab(n) {
    // This function will display the specified tab of the form...
    var x = document.getElementsByClassName("tab");
    x[n].style.display = "block";
    //... and fix the Previous/Next buttons:
    if (n == 0) {
        document.getElementById("prevBtn").style.display = "none";
    } else {
        document.getElementById("prevBtn").style.display = "inline";
    }
    if (n == (x.length - 1)) {
        document.getElementById("nextBtn").innerHTML = "Submit";
    } else {
        document.getElementById("nextBtn").innerHTML = "Next";
    }
    //... and run a function that will display the correct step indicator:
    fixStepIndicator(n)
}

function nextPrev(n) {
    // This function will figure out which tab to display
    var x = document.getElementsByClassName("tab");
    // Exit the function if any field in the current tab is invalid:
    if (n == 1 && !validateForm()) return false;
    // Hide the current tab:
    x[currentTab].style.display = "none";
    // Increase or decrease the current tab by 1:
    currentTab = currentTab + n;
    // if you have reached the end of the form...
    if (currentTab >= x.length) {
        // ... the form gets submitted:
        document.getElementById("regForm").submit();
        return false;
    }
    // Otherwise, display the correct tab:
    showTab(currentTab);
}

function validateForm() {
    // This function deals with validation of the form fields
    var x, y, i, valid = true;
    x = document.getElementsByClassName("tab");

    document.querySelectorAll(".error-span-msg").forEach(n => n.remove());

    y = x[currentTab].querySelectorAll("input[name], select[name]");
    // A loop that checks every input field in the current tab:
    for (i = 0; i < y.length; i++) {

        var constraints = {};
        if (y[i].classList.contains("not-required")) {
            constraints["presence"] = { allowEmpty: true };
        } else {
            constraints["presence"] = { allowEmpty: false, message: "No puede estar vacío" };
        }
        if (y[i].getAttribute("validators") && y[i].getAttribute("validators").indexOf("email") > -1) {
            constraints["email"] = { message: "Email no válido" };
        }
        if (y[i].getAttribute("validators") && y[i].getAttribute("validators").indexOf("numeric") > -1) {
            constraints["numericality"] = { message: "Debe ser numérico" };
        }
        console.log(y[i]);
        console.log(y[i].value);
        var validationResult = validate.single(y[i].value, constraints);
        // If a field is empty...
        if (validationResult) {
            // add an "invalid" class to the field:
            y[i].className += " invalid";
            var errorMsg = document.createElement("span");
            errorMsg.style.color = "red";
            errorMsg.className = "error-span-msg";
            errorMsg.innerHTML = validationResult.join("<br />");
            y[i].parentElement.appendChild(errorMsg);
            // and set the current valid status to false
            valid = false;
            // si email correcto, chequear si existe
        } else if (y[i].getAttribute("validators") && y[i].getAttribute("validators").indexOf("email") > -1) {
            y[i].setAttribute("readonly", "true");

            var xhr = new XMLHttpRequest();
            xhr.open("GET", "/usuarios-xhr/?email=" + y[i].value + "&action=check-email", false);
            xhr.send();

            if (xhr.responseText !== "") {
                valid = false;
                var errorMsg = document.createElement("span");
                errorMsg.style.color = "red";
                errorMsg.className = "error-span-msg";
                errorMsg.innerHTML = "Email usado por otro usuario";
                y[i].parentElement.appendChild(errorMsg);
            }
            y[i].removeAttribute("readonly");
        } else if (y[i].getAttribute("validators") && y[i].getAttribute("validators").indexOf("dni") > -1) {
            valid = validateDNI(y[i].value);

            if (!valid) {
                var errorMsg = document.createElement("span");
                errorMsg.style.color = "red";
                errorMsg.className = "error-span-msg";
                errorMsg.innerHTML = "DNI o NIE no válido";
                y[i].parentElement.appendChild(errorMsg);
            }
        }
    }
    console.log("aaa");
    // If the valid status is true, mark the step as finished and valid:
    if (valid) {
        document.getElementsByClassName("step")[currentTab].className += " finish";
    }
    return valid; // return the valid status
}

// Comprueba si es un DNI correcto (entre 5 y 8 letras seguidas de la letra que corresponda).

// Acepta NIEs (Extranjeros con X, Y o Z al principio)
function validateDNI(dni) {
    var numero, let, letra;
    var expresion_regular_dni = /^[XYZ]?\d{5,8}[A-Z]$/;

    dni = dni.toUpperCase();

    if(expresion_regular_dni.test(dni) === true){
        numero = dni.substr(0,dni.length-1);
        numero = numero.replace('X', 0);
        numero = numero.replace('Y', 1);
        numero = numero.replace('Z', 2);
        let = dni.substr(dni.length-1, 1);
        numero = numero % 23;
        letra = 'TRWAGMYFPDXBNJZSQVHLCKET';
        letra = letra.substring(numero, numero+1);
        if (letra != let) {
            //alert('Dni erroneo, la letra del NIF no se corresponde');
            return false;
        }else{
            //alert('Dni correcto');
            return true;
        }
    }else{
        //alert('Dni erroneo, formato no válido');
        return false;
    }
}


function fixStepIndicator(n) {
    // This function removes the "active" class of all steps...
    var i, x = document.getElementsByClassName("step");
    for (i = 0; i < x.length; i++) {
        x[i].className = x[i].className.replace(" active", "");
    }
    //... and adds the "active" class on the current step:
    x[n].className += " active";
}
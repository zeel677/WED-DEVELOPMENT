let form = document.getElementById("busForm");
form.addEventListener("submit", function(event)
 {
    event.preventDefault();

    let collegeId = document.getElementById("collegeId").value;
    let collegeName = document.getElementById("collegeName").value;
    let collegeRoute = document.getElementById("collegeRoute").value;
   let passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

if (password === "") {
    alert("Please enter your password.");
}
else if (!passwordRegex.test(password))
 {
    alert("Password must contain:\n" +
          "- At least 8 characters\n" +
          "- One uppercase letter\n" +
          "- One lowercase letter\n" +
          "- One number\n" +
          "- One special character");
}
else
 {
    alert("Password is valid.");
}
    let area = document.getElementById("area").value;
    let passType = document.getElementById("passType").value;
    


    if (collegeId === "" ||collegeName === "" ||collegeRoute === "" || password === "" || area === "" ||passType === "")
     {
        
        alert("Please fill all the fields.");
        

    } 
    else if(!password.regddf)
    {
        alert("Valid Password");
    }
    else
    {
        
        alert("Bus Pass Renewal Form Submitted Successfully!");

    }

   

});
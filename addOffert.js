let about = document.getElementById("about")
let phoneNumber = document.getElementById("phone")
let cost = document.getElementById('cost')

about.value = localStorage.getItem("about")
phoneNumber.value = localStorage.getItem("phoneNumber")
cost.value = localStorage.getItem("cost")

about.addEventListener("input", (e)=>{
    localStorage.setItem("about", e.target.value)
})
phoneNumber.addEventListener("input", (e)=>{
    localStorage.setItem("phoneNumber", e.target.value)
})
cost.addEventListener('input', (e)=>{
    localStorage.setItem("cost", e.target.value)
})
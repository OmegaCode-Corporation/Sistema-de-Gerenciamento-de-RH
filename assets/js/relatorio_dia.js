//Manipulação de Dados
const formRelatorioDia = document.querySelector("#formRelatorioDia")
const formBtnSubmit = document.querySelector('#enviar')

formBtnSubmit.addEventListener('click', (e) => {
    let form = formRelatorioDia
    let inputs = []
    inputs.push(...form.querySelectorAll('input'))
    inputs.push(...form.querySelectorAll('select'))
    inputs.push(...form.querySelectorAll('textarea'))
    const nullInputs = []
    let formatedText;
    let minLength;
    let maxLength;
    let formatedElements = []    

    for(const input of inputs){
        if(!nullInputs.includes(input.id) && (input.value === "" || input.value === null || input.value === undefined)){
            responseMsgField.classList.add('alert-danger')
            return responseMsgField.innerHTML = `O campo '${input.labels[0].innerHTML.replace(':', "")}' precisa ser preenchido!`
        }
    }
    
    formatedElements.forEach((element) => {
        element[0].value = element[1]
    })
    
    form.submit()
})
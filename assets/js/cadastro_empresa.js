const formEmpresa = document.querySelector("#formEmpresa")


formEmpresa.addEventListener('input', async (e) => {
    const element = e.target
    let minLength;
    let maxLength;

    switch (element.id) {
        case 'cnpj':
            maxLength = 14
            blockText(maxLength, true, element)
            element.value = formatText(element.value, element.id, 'remove')
            switch (element.value.length) {
                case maxLength:
                    element.value = formatText(element.value, element.id)
                    await getCnpjData(formatText(element.value, element.id, 'remove'));
                    break;
                case maxLength+1:
                    element.value = formatText(element.value.slice(0, element.value.length-1), element.id)
                    break;
                default:
                    break;
            }
            break;
        case 'cep':
            maxLength = 8
            blockText(maxLength, true, element)
            element.value = formatText(element.value, element.id, 'remove')
            switch (element.value.length) {
                case 8:
                    await getAddressData(element.value);
                    if(!element.value.includes('-')){
                        element.value = formatText(element.value, element.id)
                    }
                    break;
                case maxLength:
                    await getAddressData(element.value.replaceAll('-', ''));
                    break;
                case maxLength+1:
                    element.value = formatText(element.value.slice(0, element.value.length-1), element.id)
                    break;
                default:
                    break;
            }
            break;
        case 'estado':
            maxLength = 2
            blockText(maxLength, false, element)
            element.value = formatText(element.value, element.id, 'remove')
            switch (element.value.length) {
                case maxLength:
                    element.value = formatText(element.value, element.id).toUpperCase()
                    break;
                case maxLength+1:
                    element.value = formatText(element.value.slice(0, element.value.length-1), element.id).toUpperCase()
                    break;
                default:
                    break;
            }
            break;
        case 'telefone':
            maxLength = 11
            blockText(maxLength, true, element)
            element.value = formatText(element.value, element.id, 'remove')
            switch (element.value.length) {
                case 10:
                    element.value = formatText(element.value, 'telefone_fixo')
                    break;
                case maxLength:
                    element.value = formatText(element.value, element.id)
                    break;
                case maxLength+1:
                    element.value = formatText(element.value.slice(0, element.value.length-1), element.id)
                    break;
                default:
                    break;
            }
            break;
        case 'numero':
            maxLength = 4
            blockText(maxLength, true, element)
            switch (element.value.length) {
                case maxLength+1:
                    element.value = element.value.slice(0, element.value.length-1)
                    break;
                default:
                    break;
            }
            break;
        default:
            break
    }
})

const formBtn = document.querySelector('#enviar')

formBtn.addEventListener('click', (e) => {
    let form = formEmpresa

    let inputs = []
    inputs.push(...form.querySelectorAll('input'))
    inputs.push(...form.querySelectorAll('select'))
    const nullInputs = ['complemento', 'observacao']
    let formatedText;
    let minLength;
    let maxLength;
    let formatedElements = []    

    for(const input of inputs){
        if(!nullInputs.includes(input.id) && (input.value === "" || input.value === null || input.value === undefined)){
            responseMsgField.classList.add('alert-danger')
            return responseMsgField.innerHTML = `O campo '${input.labels[0].innerHTML.replace(':', "")}' precisa ser preenchido!`
        }
        input.type === 'text' ? input.value = removeAccent(input.value) : null
        switch (input.id) {
            case 'cep':
                formatedText = formatText(input.value, input.id, 'remove')
                formatedElements.push([input, formatedText])
                
                maxLength = 8
                if(formatedText.length !== 8){
                    responseMsgField.classList.add('alert-danger')
                    return responseMsgField.innerHTML = `O campo '${input.labels[0].innerHTML.replace(':', "")}' precisa possuir ${maxLength} caracteres!`
                }
                break;
            case 'cnpj':
                formatedText = formatText(input.value, input.id, 'remove')
                formatedElements.push([input, formatedText])

                maxLength = 14
                if(formatedText.length !== maxLength){
                    responseMsgField.classList.add('alert-danger')
                    return responseMsgField.innerHTML = `O campo '${input.labels[0].innerHTML.replace(':', "")}' precisa possuir ${maxLength} caracteres!`
                }
                break;
            case 'telefone':
                formatedText = formatText(input.value, input.id, 'remove')
                formatedElements.push([input, formatedText])

                minLength = 10
                maxLength = 11
                if(formatedText.length < minLength || formatedText.length > maxLength){
                    responseMsgField.classList.add('alert-danger')
                    return responseMsgField.innerHTML = `O campo '${input.labels[0].innerHTML.replace(':', "")}' precisa possuir no mínimo ${minLength} caracteres!`
                }
                break;
            default:
                break;
        }
    }
    
    formatedElements.forEach((element) => {
        element[0].value = element[1]
    })
    
    form.submit()
})

function formatText(text, type, format='add') {
    text = text.split('')
    let formatedText;
    switch (type) {
        case 'cnpj':
            if(format === 'add'){
                formatedText = `${text.slice(0, 2)}.${text.slice(2, 5)}.${text.slice(5, 8)}/${text.slice(8, 12)}-${text.slice(12, 14)}`.replaceAll(',', '')
            } else if(format === 'remove'){
                formatedText = text.join('')
                formatedText = formatedText.replaceAll('.', '')
                formatedText = formatedText.replaceAll('/', '')
                formatedText = formatedText.replaceAll('-', '')
            }
            break;

        case 'cep':
            if(format === 'add'){
                formatedText = `${text.slice(0, 5)}-${text.slice(5, 8)}`.replaceAll(',', '')
            } else if(format === 'remove'){
                formatedText = text.join('')
                formatedText = formatedText.replaceAll('-', '')
            }
            break;

            case 'estado':
                if(format === 'add'){
                    formatedText = `${text.slice(0, 2)}`.replaceAll(',', '')
                } else if(format === 'remove'){
                    formatedText = text.join('').toUpperCase()
                }
                break;

        case 'telefone_fixo':
            if(format === 'add'){
                formatedText = `(${text.slice(0, 2)}) ${text.slice(2,6)}-${text.slice(6, 10)}`.replaceAll(',', '')
            }
            break;

        case 'telefone':
            if(format === 'add'){
                if(text[0] === '('){
                    text = text.join('')
                    text = formatText(text, type, 'remove')
                    text = text.split('')
                }
                formatedText = `(${text.slice(0, 2)}) ${text.slice(2,7)}-${text.slice(7, 11)}`.replaceAll(',', '')
            } else if(format === 'remove'){
                formatedText = text.join('')
                formatedText = formatedText.replaceAll('(', '')
                formatedText = formatedText.replaceAll(') ', '')
                formatedText = formatedText.replaceAll('-', '')
            }
            break;

        default:
            break;
    }
    return formatedText
}

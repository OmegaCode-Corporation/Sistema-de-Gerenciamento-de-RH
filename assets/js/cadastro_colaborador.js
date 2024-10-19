function formatText(text, type, format="add") {
    text = text.split('')
    let formatedText;
    switch (type){
        case 'cep':
            if(format === 'add'){
                formatedText = `${text.slice(0, 5)}-${text.slice(5, 8)}`.replaceAll(',', '')
            } else if(format === 'remove'){
                formatedText = text.join('')
                formatedText = formatedText.replaceAll('-', '')
            }
            break;

        case 'numero':
            if(format === 'add'){
                formatedText = `${text.slice(0, 4)}`.replaceAll(',', '')
            } else if(format === 'remove'){
                formatedText = text.join('')
            }
            break;

        case 'estado':
            if(format === 'add'){
                formatedText = `${text.slice(0, 2)}`.replaceAll(',', '')
            } else if(format === 'remove'){
                formatedText = text.join('').toUpperCase()
            }
            break;

        case 'cpf':
            if(format === 'add'){
                formatedText = `${text.slice(0, 3)}.${text.slice(3, 6)}.${text.slice(6, 9)}-${text.slice(9, 11)}`.replaceAll(',', '')
            } else if(format === 'remove'){
                formatedText = text.join('')
                formatedText = formatedText.replaceAll('.', '')
                formatedText = formatedText.replaceAll('/', '')
                formatedText = formatedText.replaceAll('-', '')
            }
            break;

        case 'rg':
            if(format === 'add'){
                formatedText = `${text.slice(0, 2)}.${text.slice(2, 5)}.${text.slice(5, 8)}`.replaceAll(',', '')
            } else if(format === 'remove'){
                formatedText = text.join('')
                formatedText = formatedText.replaceAll('.', '')
                formatedText = formatedText.replaceAll('.', '')
                formatedText = formatedText.replaceAll('.', '')
            }
            break;

        case 'orgao':
            if(format === 'add'){
                formatedText = `${text.slice(0, 8)}`.replaceAll(',', '')
            } else if(format === 'remove'){
                formatedText = text.join('').toUpperCase()
            }
            break;

        case 'rg_uf':
            if(format === 'add'){
                formatedText = `${text.slice(0, 2)}`.replaceAll(',', '')
            } else if(format === 'remove'){
                formatedText = text.join('').toUpperCase()
            }
            break;

        case 'numero_ctps':
            if(format === 'add'){
                formatedText = `${text.slice(0, 7)}`.replaceAll(',', '')
            } else if(format === 'remove'){
                formatedText = text.join('')
                formatedText = formatedText.replaceAll(' ', '')
            }
            break;

        case 'serie_ctps':
            if(format === 'add'){
                formatedText = `${text.slice(0, 5)}`.replaceAll(',', '')
            } else if(format === 'remove'){
                formatedText = text.join('')
                formatedText = formatedText.replaceAll(' ', '')
            }
            break;

        case 'estado_ctps':
            if(format === 'add'){
                formatedText = `${text.slice(0, 2)}`.replaceAll(',', '')
            } else if(format === 'remove'){
                formatedText = text.join('').toUpperCase()
                formatedText = formatedText.replaceAll(' ', '')
            }
            break;
        
        case 'numero_pis':
            if(format === 'add'){
                formatedText = `${text.slice(0, 12)}`.replaceAll(',', '')
            } else if(format === 'remove'){
                formatedText = text.join('')
                formatedText = formatedText.replaceAll(' ', '')
            }
            break;

        case 'cnh':
            if(format === 'add'){
                formatedText = `${text.slice(0, 11)}`.replaceAll(',', '')
            } else if(format === 'remove'){
                formatedText = text.join('')
                formatedText = formatedText.replaceAll(' ', '')
            }
            break;

        case 'titulo_eleitoral':
            if(format === 'add'){
                formatedText = `${text.slice(0, 4)} ${text.slice(4, 8)} ${text.slice(8, 12)}`.replaceAll(',', '')
            } else if(format === 'remove'){
                formatedText = text.join('')
                formatedText = formatedText.replaceAll(' ', '')
                formatedText = formatedText.replaceAll(' ', '')
                formatedText = formatedText.replaceAll(' ', '')
            }
            break;

        case 'zona_eleitoral':
        if(format === 'add'){
            formatedText = `${text.slice(0, 3)}`.replaceAll(',', '')
        } else if(format === 'remove'){
            formatedText = text.join('')
            formatedText = formatedText.replaceAll(' ', '')
        }
            break;

        case 'secao_eleitoral':
            if(format === 'add'){
                formatedText = `${text.slice(0, 4)}`.replaceAll(',', '')
            } else if(format === 'remove'){
                formatedText = text.join('')
                formatedText = formatedText.replaceAll(' ', '')
            }
            break;

        case 'conta_banco':
            if(format === 'add'){
                formatedText = `${text.slice(0, 8)}`.replaceAll(',', '')
            } else if(format === 'remove'){
                formatedText = text.join('')
                formatedText = formatedText.replaceAll(' ', '')
            }
            break;

        case 'digito_conta':
            if(format === 'add'){
                formatedText = `${text.slice(0, 1)}`.replaceAll(',', '')
            } else if(format === 'remove'){
                formatedText = text.join('')
                formatedText = formatedText.replaceAll(' ', '')
            }
            break;

        case 'agencia_banco':
        if(format === 'add'){
            formatedText = `${text.slice(0, 4)}`.replaceAll(',', '')
        } else if(format === 'remove'){
            formatedText = text.join('')
            formatedText = formatedText.replaceAll(' ', '')
        }
        break;

        case 'estg_carteira_rne':
            if(format === 'add'){
                formatedText = `${text.slice(0)}`.replaceAll(',', '').toUpperCase()
            } else if(format === 'remove'){
                formatedText = text.join('')
            }
            break;

        default:
            break;
    }
    return formatedText
}

async function getAddressData(cep){
    const addressData_o = await fetch(`https://api.brasilaberto.com/v1/zipcode/${cep}`).then(res => res.json()).then(json => json.result)
    if(!addressData_o.error){
        const addressData = {
            "bairro": addressData_o.district,
            "cidade": addressData_o.city,
            "estado": formatedText(addressData_o.stateShortname, 'estado'),
            "rua": addressData_o.street
        }
        const bairroField = formColaborador.querySelector('#bairro')
        const cidadeField = formColaborador.querySelector('#cidade')
        const estadoField = formColaborador.querySelector('#estado')
        const numeroField = formColaborador.querySelector('#numero')
        const ruaField = formColaborador.querySelector('#rua')

        const fields = [bairroField, cidadeField, estadoField, ruaField]
    
        fields.forEach((field) => {
            field.value = addressData[field.id]
        })
        
        numeroField.focus()
    }
}
//EPI
const form_epi = document.querySelector("#form_epi");
const sim_btn_epi = document.querySelector("#sim_btn_epi");
const nao_btn_epi = document.querySelector("#nao_btn_epi");

if(nao_btn_epi){
    nao_btn_epi.addEventListener('click', () => {
        form_epi.classList.remove("form_epi")
        form_epi.classList.add("hide")
    }) 
}

if(sim_btn_epi){
    sim_btn_epi.addEventListener('click', () => {
        form_epi.classList.remove("hide")
        form_epi.classList.add("form_epi")
    })
}

//Banco
const form_banco = document.querySelector("#form_banco");
const sim_btn_banco = document.querySelector("#sim_btn_banco");
const nao_btn_banco = document.querySelector("#nao_btn_banco");

if(nao_btn_banco){
    nao_btn_banco.addEventListener('click', () => {
        form_banco.classList.remove("form_banco")
        form_banco.classList.add("hide")
    }) 
}

if(sim_btn_banco){
    sim_btn_banco.addEventListener('click', () => {
        form_banco.classList.remove("hide")
        form_banco.classList.add("form_banco")
    })
}

//ESTRANGEIRO
const form_estg = document.querySelector("#form_estg")
const sim_btn_estg = document.querySelector("#sim_btn_estg");
const nao_btn_estg = document.querySelector("#nao_btn_estg");

if(nao_btn_estg){
    nao_btn_estg.addEventListener('click', () => {
        form_estg.classList.remove("form_estg")
        form_estg.classList.add("hide")
    }) 
}

if(sim_btn_estg){
    sim_btn_estg.addEventListener('click', () => {
        form_estg.classList.remove("hide")
        form_estg.classList.add("form_estg")
    })
}

//SINDICATO
const form_sdct = document.querySelector("#form_sdct")
const sim_btn_sdct = document.querySelector("#sim_btn_sdct");
const nao_btn_sdct = document.querySelector("#nao_btn_sdct");

if(nao_btn_sdct){
    nao_btn_sdct.addEventListener('click', () => {
        form_sdct.classList.remove("form_sdct")
        form_sdct.classList.add("hide")
    }) 
}

if(sim_btn_sdct){
    sim_btn_sdct.addEventListener('click', () => {
        form_sdct.classList.remove("hide")
        form_sdct.classList.add("form_sdct")
    })
}

//REGISTRO
const form_rgst = document.querySelector("#form_rgst")
const sim_btn_rgst = document.querySelector("#sim_btn_rgst");
const nao_btn_rgst = document.querySelector("#nao_btn_rgst");

if(nao_btn_rgst){
    nao_btn_rgst.addEventListener('click', () => {
        form_rgst.classList.remove("form_rgst")
        form_rgst.classList.add("hide")
    }) 
}

if(sim_btn_rgst){
    sim_btn_rgst.addEventListener('click', () => {
        form_rgst.classList.remove("hide")
        form_rgst.classList.add("form_rgst")
    })
}


// MANIPULAÇÃO DE DADOS
const formColaborador = document.querySelector("#formColaborador")

formColaborador.addEventListener('input', async (e) =>{
    const element = e.target
    console.log(element)
    let minLength;
    let maxLength;

    switch (element.id) {
        case 'nome_colaborador':
            maxLength = 0
            blockText(maxLength, false, element)
            break;
        case 'pai_colaborador':
            maxLength = 0
            blockText(maxLength, false, element)
            break;
        case 'mae_colaborador':
            maxLength = 0
            blockText(maxLength, false, element)
            break;
        case 'naturalidade':
            maxLength = 0
            blockText(maxLength, false, element)
            break;
        case 'nacionalidade':
            maxLength = 0
            blockText(maxLength, false, element)
            break;
        case 'cep':
            maxLength = 8
            blockText (maxLength, true, element)
            element.value = formatText(element.value, element.id, 'remove')
            switch(element.value.length) {
                case 8:
                    await getAddressData(element.value);
                    if(!element.value.includes('-')) {
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
        case 'numero':
            maxLength = 4
            blockText(maxLength, true, element)
            element.value = formatText(element.value, element.id, 'remove')
            switch (element.value.length) {
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
        case 'cpf':
            maxLength = 11
            blockText(maxLength, true, element)
            element.value = formatText(element.value, element.id, 'remove')
            switch (element.value.length) {
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
        case 'rg':
            maxLength = 8
            blockText(maxLength, true, element)
            element.value = formatText(element.value, element.id, 'remove')
            switch (element.value.length) {
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
        case 'orgao':
            maxLength = 6
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
        case 'rg_uf':
            maxLength = 2
            blockText(maxLength, false, element)
            element.value = formatText(element.value, element.id, 'remove')
            switch (element.value.length) {
                case maxLength:
                    element.value = formatText(element.value, element.id).toUpperCase()
                    break;
                case maxLength+1:
                    element.value = formatText(element.value.slice(0, element.value.length-1), element.id)
                    break;
                default:
                    break;
            }
            break;
        case 'numero_ctps':
            maxLength = 7
            blockText(maxLength, true, element)
            element.value = formatText(element.value, element.id, 'remove')
            switch (element.value.length) {
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
        case 'serie_ctps':
            maxLength = 5
            blockText(maxLength, true, element)
            element.value = formatText(element.value, element.id, 'remove')
            switch (element.value.length) {
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
        case 'estado_ctps':
            maxLength = 2
            blockText(maxLength, false, element)
            element.value = formatText(element.value, element.id, 'remove')
            switch (element.value.length) {
                case maxLength:
                    element.value = formatText(element.value, element.id).toUpperCase()
                    break;
                case maxLength+1:
                    element.value = formatText(element.value.slice(0, element.value.length-1), element.id)
                    break;
                default:
                    break;
            }
            break;
        case 'numero_pis':
            maxLength = 12
            blockText(maxLength, true, element)
            element.value = formatText(element.value, element.id, 'remove')
            switch (element.value.length) {
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
        case 'cnh':
            maxLength = 11
            blockText(maxLength, true, element)
            element.value = formatText(element.value, element.id, 'remove')
            switch (element.value.length) {
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
        case 'titulo_eleitoral':
            maxLength = 12
            blockText(maxLength, true, element)
            element.value = formatText(element.value, element.id, 'remove')
            switch (element.value.length) {
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
        case 'zona_eleitoral':
            maxLength = 3
            blockText(maxLength, true, element)
            element.value = formatText(element.value, element.id, 'remove')
            switch (element.value.length) {
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
        case 'secao_eleitoral':
            maxLength = 4
            blockText(maxLength, true, element)
            element.value = formatText(element.value, element.id, 'remove')
            switch (element.value.length) {
                case maxLength:
                    element.value = formatText(element.value, element.id )
                    break;
                case maxLength+1:
                    element.value = formatText(element.value.slice(0, element.value.length-1), element.id)
                    break;
                default:
                    break;
            }
            break;
        case 'banco':
            maxLength = 0
            blockText(maxLength, false, element)
            break;
        case 'conta_banco':
            maxLength = 8
            blockText(maxLength, true, element)
            element.value = formatText(element.value, element.id, 'remove')
            switch (element.value.length) {
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
        case 'digito_conta':
            maxLength = 2
            blockText(maxLength, true, element)
            element.value = formatText(element.value, element.id, 'remove')
            switch (element.value.length) {
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
        case 'agencia_banco':
            maxLength = 4
            blockText(maxLength, true, element)
            element.value = formatText(element.value, element.id, 'remove')
            switch (element.value.length) {
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
        case 'codigo_ficha':
            maxLength = 0
            blockText(maxLength, true, element)
            break;
        case 'nr_recibo_ficha':
            maxLength = 0
            blockText(maxLength, true, element)
            break;
        case 'matricula_esocial':
            maxLength = 0
            blockText(maxLength, true, element)
            break;
        case 'estg_carteira_rne':
            element.value = formatText(element.value, element.id)
            break;
        case 'estg_nr_portaria':
            maxLength = 0
            blockText(maxLength, true, element)
            break;
        default:
            break;
    }
})

const formBtn = document.querySelector('#enviar')

formBtn.addEventListener('click', (e) => {
    let form = formColaborador
    let inputs = []
    inputs.push(...form.querySelectorAll('input'))
    inputs.push(...form.querySelectorAll('select'))
    const nullInputs = ['complemento', 'cnh', 'categoria_cnh', 'validade_cnh', 'banco', 'conta_banco', 'digito_conta', 'agencia_banco', 'sindicato', 'cons_profis', 'registro_profis', 'data_registro_profis', 'codigo_ficha', 'nr_recibo_ficha', 'matricula_esocial', 'japona', 'calca', 'luva', 'meiao', 'estg_data_chegada', 'estg_tipo_visto', 'estg_carteira_rne', 'estg_validade_rne', 'estg_nr_portaria', 'estg_data_portaria']
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
            case 'cpf':
                formatedText = formatText(input.value, input.id, 'remove')
                formatedElements.push([input,formatedText])
                maxLength = 11
                if(formatedText.length !==maxLength){
                    responseMsgField.classList.add('alert-danger')
                    return responseMsgField.innerHTML = `O campo '${input.labels[0].innerHTML.replace(':', "")}' precisa possuir ${maxLength} caracteres!`
                }
                break;
            case 'rg':
                formatedText = formatText(input.value, input.id, 'remove')
                formatedElements.push([input,formatedText])
                maxLength = 8
                if(formatedText.length !==maxLength){
                    responseMsgField.classList.add('alert-danger')
                    return responseMsgField.innerHTML = `O campo '${input.labels[0].innerHTML.replace(':', "")}' precisa possuir ${maxLength} caracteres!`
                }
                break;
            case 'titulo_eleitoral':
                formatedText = formatText(input.value, input.id, 'remove')
                formatedElements.push([input,formatedText])
                maxLength = 12
                if(formatedText.length !==maxLength){
                    responseMsgField.classList.add('alert-danger')
                    return responseMsgField.innerHTML = `O campo '${input.labels[0].innerHTML.replace(':', "")}' precisa possuir ${maxLength} caracteres!`
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

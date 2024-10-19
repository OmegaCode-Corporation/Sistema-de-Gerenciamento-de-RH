function formatText(text, type, format="add") {
    text = text.split('')
    let formatedText;
    switch (type){
        case 'conta_fgts':
            if(format === 'add'){
                formatedText = `${text.slice(0, 20)}-${text.slice(20, 22)}`.replaceAll(',', '')
            } else if(format === 'remove'){
                formatedText = text.join('')
                formatedText = formatedText.replaceAll('-', '')
            }
            break;
        case 'remuneracao':
            if(format === 'add'){
                formatedText = `R$ ${text.join('')}`.replaceAll(',', '')
            } else if(format === 'remove'){
                formatedText = text.join('')
                formatedText = formatedText.replaceAll('R$ ','')
            }
            break;
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
        case 'cep':
            if(format === 'add'){
                formatedText = `${text.slice(0, 5)}-${text.slice(5, 8)}`.replaceAll(',', '')
            } else if(format === 'remove'){
                formatedText = text.join('')
                formatedText = formatedText.replaceAll('-', '')
            }
            break;    
        default:
            break
    }
    return formatedText
}


window.onload = () => {
    const form_file = document.querySelector("#form_file");
    const sim_btn_ctrto = document.querySelector("#sim_btn_ctrto");
    const nao_btn_ctrto = document.querySelector("#nao_btn_ctrto");

    if(nao_btn_ctrto){
        nao_btn_ctrto.addEventListener('click', () => {
            form_file.classList.remove("form_file")
            form_file.classList.add("hide")
        }) 
    }

    if(sim_btn_ctrto){
        sim_btn_ctrto.addEventListener('click', () => {
            form_file.classList.remove("hide")
            form_file.classList.add("form_file")
        })
    }

    const formContratoColaborador = document.querySelector("#formContratoColaborador")

    formContratoColaborador.addEventListener('input', async (e) =>{
        const element = e.target
        let minLength;
        let maxLength;

        switch (element.id) {
            case 'conta_fgts':
                maxLength = 22
                blockText (maxLength, true, element)
                element.value = formatText(element.value, element.id, 'remove')
                switch(element.value.length) {
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
            case 'nome_colaborador':
                maxLength = 0
                blockText(maxLength, false, element)
                break;
            case 'cargo':
                maxLength = 0
                blockText(maxLength, false, element)
                break;
            case 'cbo':
                maxLength = 0
                blockText(maxLength, true, element)
                break;
            case 'organograma':
                maxLength = 0
                blockText(maxLength, false, element)
                break;
            case 'remuneracao':
                maxLength = 0
                blockText(maxLength, true, element)
                element.value = formatText(element.value.replaceAll('R$ ',''), element.id,)
                break;
            case 'nome_mae':
                maxLength = 0
                blockText(maxLength, false, element)
                break;
            case 'nome_pai':
                maxLength = 0
                blockText(maxLength, false, element)
                break;
            case 'nome_familiar':
                maxLength = 0
                blockText(maxLength, false, element)
                break;
            default:
                break;
        }
    })
        
    const formBtnSubmit = document.querySelector('#enviar')

    formBtnSubmit.addEventListener('click', (e) => {
        let form = formContratoColaborador
        let inputs = []
        inputs.push(...form.querySelectorAll('input'))
        inputs.push(...form.querySelectorAll('select'))
        inputs.push(...form.querySelectorAll('textarea'))
        const nullInputs = ['file', 'input_busca_colaborador', 'input_busca_empresa', 'input_busca_unidade']
        let formatedText;
        let minLength;
        let maxLength;
        let formatedElements = []  

        for(const input of inputs){
            if(!nullInputs.includes(input.id) && (input.value === "" || input.value === null || input.value === undefined)){
                responseMsgField.classList.add('alert-danger')
                return responseMsgField.innerHTML = `O campo '${input.labels[0].innerHTML.replace(':', "")}' precisa ser preenchido!`
            }
            switch (input.id) {
                case 'conta_fgts':
                    formatedText = formatText(input.value, input.id, 'remove')
                    formatedElements.push([input, formatedText])
                    break;
                case 'remuneracao':
                    formatedText = formatText(input.value, input.id, 'remove')
                    formatedElements.push([input, formatedText])
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

    const nome_colaborador = document.querySelector("#nome_colaborador")
    const id_colaborador = document.querySelector("#id_colaborador")
    const tabela_listagem_colaborador = document.querySelector("#tabela_listagem_colaborador")

    tabela_listagem_colaborador.addEventListener('click', (e) => {
        const element = e.target
        let td = element.parentElement
        let tr = td.parentElement

        let id_td = tr.querySelector("#id_colaborador_td")
        let nome_td = tr.querySelector("#nome_colaborador_td")

        nome_colaborador.value = nome_td.innerHTML
        id_colaborador.value = id_td.innerHTML
        $('#viewModal').modal('hide')
    })

    const nome_empresa = document.querySelector("#nome_empresa")
    const id_empresa = document.querySelector("#id_empresa")
    const tabela_listagem_empresa = document.querySelector("#tabela_listagem_empresa")

    tabela_listagem_empresa.addEventListener('click', (e) => {
        const element = e.target
        let td = element.parentElement
        let tr = td.parentElement

        let id_td = tr.querySelector("#id_empresa_td")
        let nome_td = tr.querySelector("#nome_empresa_td")

        nome_empresa.value = nome_td.innerHTML
        id_empresa.value = id_td.innerHTML
        $('#viewModal2').modal('hide')
    })

    const nome_unidade = document.querySelector("#nome_unidade")
    const id_unidade = document.querySelector("#id_unidade")
    const tabela_listagem_unidade = document.querySelector("#tabela_listagem_unidade")

    tabela_listagem_unidade.addEventListener('click', (e) => {
        const element = e.target
        let td = element.parentElement
        let tr = td.parentElement

        let id_td = tr.querySelector("#id_unidade_td")
        let nome_td = tr.querySelector("#nome_unidade_td")
        let endereco_td = tr.querySelector("#endereco_unidade_td")

        nome_unidade.value = nome_td.innerHTML + " " + "|" + " " + endereco_td.innerHTML
        id_unidade.value = id_td.innerHTML
        $('#viewModal3').modal('hide')
    })


    const input_busca_colaborador = document.querySelector('#input_busca_colaborador');
    const tabela_colaborador = document.querySelector('.tabela_listagem_colaborador');

    if(input_busca_colaborador){
        input_busca_colaborador.addEventListener('keyup', () => {
            let expressao = input_busca_colaborador.value.toLowerCase();

            if (expressao.length === 1) {
                return;
            }

            let linhas = tabela_colaborador.getElementsByTagName('tr');

            for (let posicao in linhas) {
                if (true === isNaN(posicao)) {
                    continue;
                }

                let conteudoDaLinha = linhas[posicao].innerHTML.toLowerCase();

                if (true === conteudoDaLinha.includes(expressao)) {
                    linhas[posicao].style.display = '';
                } else {
                    linhas[posicao].style.display = 'none';
                }
            }
        })
    }
    const input_busca_empresa = document.querySelector('#input_busca_empresa');
    const tabela_empresa = document.querySelector('.tabela_listagem_empresa');

    if(input_busca_empresa){
        input_busca_empresa.addEventListener('keyup', () => {
            let expressao = input_busca_empresa.value.toLowerCase();

            if (expressao.length === 1) {
                return;
            }

            let linhas = tabela_empresa.getElementsByTagName('tr');

            for (let posicao in linhas) {
                if (true === isNaN(posicao)) {
                    continue;
                }

                let conteudoDaLinha = linhas[posicao].innerHTML.toLowerCase();

                if (true === conteudoDaLinha.includes(expressao)) {
                    linhas[posicao].style.display = '';
                } else {
                    linhas[posicao].style.display = 'none';
                }
            }
        })
    }
    const input_busca_unidade = document.querySelector('#input_busca_unidade');
    const tabela_unidade = document.querySelector('.tabela_listagem_unidade');

    if(input_busca_unidade){
        input_busca_unidade.addEventListener('keyup', () => {
            let expressao = input_busca_unidade.value.toLowerCase();

            if (expressao.length === 1) {
                return;
            }

            let linhas = tabela_unidade.getElementsByTagName('tr');

            for (let posicao in linhas) {
                if (true === isNaN(posicao)) {
                    continue;
                }

                let conteudoDaLinha = linhas[posicao].innerHTML.toLowerCase();

                if (true === conteudoDaLinha.includes(expressao)) {
                    linhas[posicao].style.display = '';
                } else {
                    linhas[posicao].style.display = 'none';
                }
            }
        })
    }

    const stepfamiliar = document.querySelector(".step-familiar")

    stepfamiliar.addEventListener('click', (e) => {
        const element = e.target.parentElement
        const docElement = element.parentElement
        const doc = docElement.parentElement

        const allDocs = document.querySelectorAll('.doc')

        switch(element.id){
            case 'adc_familiar':
                if(allDocs.length < 3){
                    const newDoc = document.createElement('div')
                    newDoc.classList.add('doc')
                    newDoc.innerHTML = doc.innerHTML

                    const newDocBtn = newDoc.querySelector('.botao')
                    
                    newDocBtn.innerHTML = '<i class="bi bi-trash3"></i>'
                    newDocBtn.id = 'rmv_familiar'
                    newDocBtn.style.color = 'red'

                    stepfamiliar.appendChild(newDoc)
                    
                }else{
                    responseMsgField.classList.add('alert-danger')
                    responseMsgField.innerHTML = "Você não pode adicionar mais de 3 familiares!"
                    setTimeout(() => {
                        responseMsgField.style.display = 'none'  
                    }, 2000);
                    responseMsgField.style.display = 'block'
                }
                break;
            case 'rmv_familiar':
                stepfamiliar.removeChild(doc)
            default:
                break
            }
    })
}
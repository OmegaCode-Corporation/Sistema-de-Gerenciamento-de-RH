const formCadastroNovoCliente = document.querySelector("#formCadastroNovoCliente")

formCadastroNovoCliente.addEventListener('input', async (e) => {
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
        case 'qtd_lojas':
            maxLength = 0
            blockText(maxLength, true, element)
            break;
        case 'valor_total_lojas':
            maxLength = 0
            blockText(maxLength, true, element)
            break;
        default:
            break
    }
})

const formBtn = document.querySelector('#cadastrar')

formBtn.addEventListener('click', (e) => {
    let form = formCadastroNovoCliente
    let inputs = []
    inputs.push(...form.querySelectorAll('input'))
    inputs.push(...form.querySelectorAll('select'))
    let formatedText;
    let minLength;
    let maxLength;
    let formatedElements = []    

    for(const input of inputs){
        switch (input.id) {
            case 'cnpj':
                formatedText = formatText(input.value, input.id, 'remove')
                formatedElements.push([input, formatedText])
                maxLength = 14
                if(formatedText.length !== maxLength){
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
        default:
            break;
    }
    return formatedText
}



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

})

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
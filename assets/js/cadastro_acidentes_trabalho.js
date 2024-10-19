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
//Manipulação de Dados
const formAcidenteTrabalho = document.querySelector("#formAcidenteTrabalho")

formAcidenteTrabalho.addEventListener('input', async (e) =>{
    const element = e.target
    let maxLength;

    switch (element.id){
        case 'nome_colaborador':
            maxLength = 0
            blockText(maxLength, false, element)
            break;
        default:
            break;
    }
})

const formBtnSubmit = document.querySelector('#cadastrar')

formBtnSubmit.addEventListener('click', (e) => {
    let form = formAcidenteTrabalho
    let inputs = []
    inputs.push(...form.querySelectorAll('input'))
    inputs.push(...form.querySelectorAll('select'))
    inputs.push(...form.querySelectorAll('textarea'))
    const nullInputs = ['input_busca_colaborador']
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
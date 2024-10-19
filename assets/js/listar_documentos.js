const input_busca = document.querySelector('#input-busca');
const tabela_listagem = document.querySelector('.tabela-listagem');

if(input_busca){
    input_busca.addEventListener('keyup', () => {
        search(input_busca, tabela_listagem);
    })
}

function search(input, tabela){
    let expressao = input.value.toLowerCase();

    if (expressao.length === 1) {
        return;
    }

    let linhas = tabela.getElementsByTagName('tr');

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
}

const nome_entidade = document.querySelector("#nome_entidade")
const id_entidade = document.querySelector("#id_entidade")
const tabela_listagem_entidade = document.querySelector("#tabela_listagem_entidade")

tabela_listagem_entidade.addEventListener('click', (e) => {
    const element = e.target
    let td = element.parentElement
    let tr = td.parentElement

    let id_td = tr.querySelector("#id_entidade_td")
    let nome_td = tr.querySelector("#nome_entidade_td")

    nome_entidade.value = nome_td.innerHTML
    id_entidade.value = id_td.innerHTML
    $('#viewModal').modal('hide')
})
const formExame = document.querySelector("#formExame")

formExame.addEventListener('input', async (e) => {
    const element = e.target
    let minLength;
    let maxLength;

    switch (element.id) {
        case 'telefone_clinicas':
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
        default:
            break
    }
})

function formatText(text, type, format='add') {
    text = text.split('')
    let formatedText;
    switch (type) {
        case 'telefone_clinicas':
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
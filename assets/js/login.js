const versenha_btn = document.querySelector('#versenha_btn')
const versenha_icon = document.querySelector('#versenha_icon')
const senha = document.querySelector('#senha') 

if(versenha_btn){
    versenha_btn.addEventListener('click', () => {
        senha.type = senha.type === 'password' ? 'text' : 'password'  
        versenha_icon.classList = versenha_icon.className.includes('slash') ? 'bi bi-eye' : 'bi bi-eye-slash'
    })
}

const form2FA = document.querySelector("#form2FA")

form2FA.addEventListener('input', async (e) => {
    const element = e.target
    let minLength;
    let maxLength;

    switch (element.id) {
        case 'codigo':
            maxLength = 6
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

const forms = document.querySelectorAll('.modal-form');
forms.forEach(form => {
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if(window.sessionStorage.getItem('buttonClicked') === 'true' || verificarForm(form) !== true){
            return;
        }
        window.sessionStorage.setItem('buttonClicked', 'true');

        const formData = new FormData(form);
        const responseMsgField = form.parentElement.querySelector('.responseMsg');

        fetch('fetch_control.php', {
            method: 'POST',
            body: formData
        }).then(res => res.text()).then(text => {
            try{
                let json = JSON.parse(text);
                let responseMsgField = form.parentElement.querySelector('.responseMsg');

                responseMsgField.classList.add(json.responseType);
                responseMsgField.innerHTML = json.responseMsg;
                responseMsgField.style.display = 'block';

                $(`#${form.parentElement.parentElement.parentElement.parentElement.id}`).on('hidden.bs.modal', () => {
                    window.location.reload()
                })
            }catch(err){
                if(err.toString().includes("SyntaxError")){
                    // console.log(text)
                    const domParser = new DOMParser();
                    const doc = domParser.parseFromString(text, 'text/html');
                    if(doc.querySelector('#return') !== undefined && doc.querySelector('#return') !== null){
                        if(doc.querySelector('#return').innerHTML === '2FA_page'){
                            const newWindow = window.open('2FA.php');
                            newWindow.addEventListener('submit', (e) => {
                                e.preventDefault();
                                const formNewWindow = e.target;
                                const formNewWindowData = new FormData(formNewWindow);
                                fetch(formNewWindow.action, {
                                    method: formNewWindow.method,
                                    body: formNewWindowData
                                }).then(res => res.text()).then(text => {
                                    try{
                                        let jsonText = text.split('{"')[1].split('"}')[0]
                                        let json = JSON.parse(`{"${jsonText}"}`);
                                        let responseMsgField = form.parentElement.querySelector('.responseMsg');
                        
                                        if(json.responseMsg === "Autenticação Confirmada!"){
                                            newWindow.close();
                                            form.submit();
                                        }

                                        responseMsgField.classList.add(json.responseType);
                                        responseMsgField.innerHTML = json.responseMsg;
                                        responseMsgField.style.display = 'block';

                                        if(form.querySelector('p') !== undefined){
                                            form.removeChild(form.querySelector('p'))
                                        }
                                        if(form.parentElement.parentElement.querySelector('.modal-footer') !== undefined){
                                            form.parentElement.parentElement.querySelector('.modal-footer').removeChild(form.parentElement.parentElement.querySelector('.modal-footer').querySelector('button[type="submit"]'))
                                        }
                                        if(form.querySelectorAll('.info')){
                                            form.querySelectorAll('.info').forEach(info => {
                                                form.removeChild(info)
                                            })
                                        }
                                        if(form.querySelector('textarea')){
                                            form.removeChild(form.querySelector('textarea'))
                                        }
                        
                                        $(`#${form.parentElement.parentElement.parentElement.parentElement.id}`).on('hidden.bs.modal', () => {
                                            window.location.reload()
                                        })
                                    }catch(err){
                                        console.log(err)
                                        // if(err.toString().includes("SyntaxError")){
                                        //     const domParser = new DOMParser();
                                        //     const doc = domParser.parseFromString(text, 'text/html');
                                        //     console.log(doc)
                                        //     // const script = doc.querySelector('script');
                                        //     // eval(script.innerHTML)
                                        // }
                                    }
                                    newWindow.close();
                                })
                            })
                        }
                    } else {
                        let jsonText = text.split('{"')[1].split('"}')[0]
                        let json = JSON.parse(`{"${jsonText}"}`);
                        console.log(json)
                        
                        responseMsgField.classList.add(json.responseType);
                        responseMsgField.innerHTML = json.responseMsg;
                        responseMsgField.style.display = 'block';
                        responseMsgField.style.marginTop = '0';
                        
                        $(`#${form.parentElement.parentElement.parentElement.parentElement.id}`).on('hidden.bs.modal', () => {
                            window.location.reload()
                        })
                    }
                }
            }
        }).then(() => {
            window.sessionStorage.setItem('buttonClicked', 'false');
        })
    })
})

function verificarForm(form){
    const responseMsgField = form.parentElement.querySelector('.responseMsg');

    setTimeout(() => {
        responseMsgField.classList.remove('alert-danger')
        responseMsgField.style.display = 'none';
        responseMsgField.innerHTML = '';
    }, 10000)

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
            responseMsgField.style.display = 'block';
            return responseMsgField.innerHTML = `O campo '${input.labels[0].innerHTML.replace(':', "")}' precisa ser preenchido!`
        }

        input.type === 'text' ? input.value = removeAccent(input.value) : null

        switch (input.id) {
            case input.id.includes('telefone_clinicas'):
                formatedText = formatText(input.value, input.id, 'remove')
                formatedElements.push([input, formatedText])
                
                maxLength = 11
                if(formatedText.length !== 11){
                    responseMsgField.classList.add('alert-danger')
                    responseMsgField.style.display = 'block';
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

    return true;
}
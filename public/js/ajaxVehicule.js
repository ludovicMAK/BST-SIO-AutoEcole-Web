window.onload = () => {
    const FiltersForm = document.querySelector('#formCategorie');
    document.querySelector('#selectCategorie').addEventListener('change', element =>{
        document.querySelector('#content').innerHTML = "";
        const Form = new FormData(FiltersForm);
        const Params = new URLSearchParams();
        Form.forEach((value, key) => {
            Params.append(key, value);
        });
        const Url = new URL(window.location.href);
        fetch(Url.pathname + '?' + Params.toString() + '&ajax=1', {
            headers: {
                "x-Requested-With": "XMLHttpRequest"
            }
        }).then(response => response.json()
        ).then(data=> {
            const content = document.querySelector('#content')
            content.innerHTML = data.content;
        }).catch(e=>{
            console.error('Une erreur est survenue :', e)
        });
    })
}
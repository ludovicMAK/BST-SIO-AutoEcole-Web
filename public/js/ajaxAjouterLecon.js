window.onload = () => {

    const FiltersForm = document.querySelector('#AjouterLecon');
    document.querySelectorAll("#AjouterLecon input[type='radio']")?.forEach(input => {
        input?.addEventListener("change", element => {
            document.getElementById('horaire').value = "Choisir une date";
            document.querySelector('#content').classList.add('d-none');
            document.querySelector('#date').disabled = false ;
            const dateSelect = document.querySelector('#date');
            dateSelect?.addEventListener('change',date=>{

                const dateSelectionne = new Date(date.target.value);
                const dateAuj = new Date();

                if (dateSelectionne < dateAuj ){
                    const zoneErreur = document.querySelector('#erreur')
                    zoneErreur.innerHTML= "<div class='alert alert-danger'>Vous ne pouvez pas choisir une date antérieure à celle de demain</div>"
                    document.getElementById('horaire').value = "Choisir une date";
                    document.querySelector('#content').classList.add('d-none');
                }else {
                    const zoneErreur = document.querySelector('#erreur')
                    zoneErreur.innerHTML="";
                    document.querySelector('#horaire').disabled = false ;
                    document.querySelector("#horaire").addEventListener("change",()=>{

                        document.querySelector('#content').classList.remove('d-none');
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
                        }).then(response =>
                            response.json()
                        ).then(data=> {
                            const content = document.querySelector('#content')
                            content.innerHTML = data.content;
                            const inputIdMoniteur =document.querySelector("#idMoniteur");
                            const inputIdVehicule =document.querySelector("#idVehicule");
                            activeSubmit = ()=>{
                                if (inputIdMoniteur.value != 0 && inputIdVehicule.value !=0){
                                    document.querySelector("#btnSubmit").disabled = false;
                                }else {
                                    document.querySelector("#btnSubmit").disabled = true;
                                }
                            }
                            document.querySelector("#selectMoniteur")?.addEventListener('change',value=>{
                                inputIdMoniteur.value = value.target.value;
                                console.log(inputIdMoniteur.value);
                                activeSubmit()
                            })
                            document.querySelector("#selectVehicule")?.addEventListener('change',value=>{
                                inputIdVehicule.value = value.target.value;
                                activeSubmit()
                            })

                        }).catch(e=>{
                            console.error('Une erreur est survenue :', e)
                        });
                    })

                }
            })
        });
    });

};



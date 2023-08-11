window.onload=()=>{
    const formEleve =document.querySelector("#searchEleve");
    const formMoniteur = document.querySelector("#searchMoniteur");
    afficherPlanning = (data)=>{

        let donne =  data ;
        let calendarElements;
        calendarElements = document.querySelector('#calendar');

        let calendar = new FullCalendar.Calendar(calendarElements, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            headerToolbar: {
                start: 'dayGridWeek,dayGridMonth',
                center: 'title',
                end: 'prev,today,next',
            },
            dayMaxEventRows: 3,
            moreLinkClassNames: 'btn-outline-secondary',
            dayHeaderFormat: {
                weekday: 'long',
            },
            firstDay: 1,
            eventDisplay: 'block',
            eventClick: function(info) {
                document.getElementById('modalLeconTitle').innerHTML = info.event.title;
                document.getElementById('modalLeconBody').innerHTML = info.event.extendedProps.description;
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalLecon')).show();
            },

            events: donne,
        });
        calendar.render();
        document.querySelectorAll('.ferme').forEach(elem=>{
                elem.addEventListener('click',()=>{
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalLecon')).hide();
                })
            }
        )
        $('#selectCategorie').change(function(){
            let dataByCategorie = '[]';
            let categorieChoisie = $('#selectCategorie').find(":selected").text();
            if(categorieChoisie === 'Toutes'){
                dataByCategorie = data;
            }
            else{
                for(let obj of data){
                    if(obj['title'] === categorieChoisie){
                        let jsonarray = JSON.parse(dataByCategorie);
                        jsonarray.push(obj);
                        dataByCategorie = JSON.stringify(jsonarray);
                    }
                }
                if(dataByCategorie === '[]'){
                    document.getElementById('alert').innerHTML = "Vous n'avez aucune leçon pour la catégorie: "+categorieChoisie;
                    document.getElementById('alert').classList.remove('visually-hidden');
                }
                else{
                    document.getElementById('alert').classList.add('visually-hidden');
                }
                dataByCategorie = JSON.parse(dataByCategorie);
            }



            let calendar = new FullCalendar.Calendar(calendarElements, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                headerToolbar: {
                    start: 'dayGridWeek,dayGridMonth',
                    center: 'title',
                    end: 'prev,today,next',
                },
                dayMaxEventRows: 3,
                moreLinkClassNames: 'btn-outline-secondary',
                dayHeaderFormat: {
                    weekday: 'long',
                },
                firstDay: 1,
                eventDisplay: 'block',
                eventClick: function(info) {
                    /*alert('Event: ' + info.event.title);*/
                    document.getElementById('modalLeconTitle').innerHTML = info.event.title;
                    document.getElementById('modalLeconBody').innerHTML = info.event.extendedProps.description;
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalLecon')).show();
                },
                events: dataByCategorie,
            });
            calendar.render();
        });
    }

    let timeoutId;
    document.querySelector("#eleveNom").addEventListener('input',()=>{
        document.querySelector('#contentSearchEleve').classList.remove('d-none')

        timeoutId = setTimeout(function() {

            const FormEleve = new FormData(formEleve);
            const ParamsEleve = new URLSearchParams();
            FormEleve.forEach((value, key) => {
                ParamsEleve.append(key, value);
            });
            const Url = new URL(window.location.href);
            fetch(Url.pathname + '?' + ParamsEleve.toString() + '&ajax=1', {
                headers: {
                    "x-Requested-With": "XMLHttpRequest"
                }
            }).then(response =>
                response.json()

            ).then(data=> {

                const contentSearchEleve = document.querySelector('#contentSearchEleve')
                contentSearchEleve.innerHTML = data.content;
               document.querySelectorAll('.unEleve')?.forEach(unEleve=>{
                   unEleve?.addEventListener('click',e=>{

                       document.querySelector('#eleveSelectionne').value =e.target.value;
                       document.querySelector('#eleveNom').value ="";
                       const FormEleveSelectionne = new FormData(formEleve);
                       const ParamsEleveSelectionne = new URLSearchParams();
                       FormEleveSelectionne.forEach((value, key) => {
                           ParamsEleveSelectionne.append(key, value);
                       });
                       const Url = new URL(window.location.href);
                       fetch(Url.pathname + '?' + ParamsEleveSelectionne.toString() + '&ajax=1', {
                           headers: {
                               "x-Requested-With": "XMLHttpRequest"
                           }
                       }).then(response =>
                           response.json()

                       ).then(data=> {
                           document.querySelector('#eleveSelectionne').value = 0;
                           const contentPlanning = document.querySelector('#container')
                           contentPlanning.innerHTML = data.content;
                           document.querySelector('#contentSearchEleve').classList.add('d-none')
                           afficherPlanning(data.donne);
                       }).catch(e=>console.log(e))
                   })
               })

            }).catch(e=>console.log(e))

        }, 500);
    })
    document.querySelector("#searchMoniteur").addEventListener('input',()=>{
        document.querySelector('#contentSearchMoniteur').classList.remove('d-none')

        timeoutId = setTimeout(function() {

            const FormMoniteur = new FormData(formMoniteur);
            const ParamsMoniteur = new URLSearchParams();
            FormMoniteur.forEach((value, key) => {
                ParamsMoniteur.append(key, value);
            });
            const Url = new URL(window.location.href);
            fetch(Url.pathname + '?' + ParamsMoniteur.toString() + '&ajax=1', {
                headers: {
                    "x-Requested-With": "XMLHttpRequest"
                }
            }).then(response =>
                response.json()

            ).then(data=> {

                const contentSearchMoniteur = document.querySelector('#contentSearchMoniteur')
                contentSearchMoniteur.innerHTML = data.content;
                document.querySelectorAll('.unEleve')?.forEach(unMoniteur=>{
                    unMoniteur?.addEventListener('click',e=>{

                        document.querySelector('#moniteurSelectionne').value =e.target.value;
                        document.querySelector('#moniteurNom').value ="";
                        const FormMoniteurSelectionne = new FormData(formMoniteur);
                        const ParamsMoniteurSelectionne = new URLSearchParams();
                        FormMoniteurSelectionne.forEach((value, key) => {
                            ParamsMoniteurSelectionne.append(key, value);
                        });
                        const Url = new URL(window.location.href);
                        fetch(Url.pathname + '?' + ParamsMoniteurSelectionne.toString() + '&ajax=1', {
                            headers: {
                                "x-Requested-With": "XMLHttpRequest"
                            }
                        }).then(response =>
                            response.json()

                        ).then(data=> {
                            document.querySelector('#moniteurSelectionne').value =0
                            const contentPlanning = document.querySelector('#container')
                            contentPlanning.innerHTML = data.content;
                            document.querySelector('#contentSearchMoniteur').classList.add('d-none')
                            afficherPlanning( data.donne);

                        }).catch(e=>console.log(e))
                    })
                })

            }).catch(e=>console.log(e))

        }, 500);
    })

}

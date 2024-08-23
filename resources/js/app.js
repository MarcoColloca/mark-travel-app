import './bootstrap';
import '@tomtom-international/web-sdk-maps/dist/maps.css';
import '~resources/scss/app.scss';
import '~icons/bootstrap-icons.scss';
import * as bootstrap from 'bootstrap';
import.meta.glob([
    '../img/**'
])
import Trips from './components/Trips.vue';
import Days from './components/Days.vue';
import Day from './components/Day.vue';
import Stage from './components/Stage.vue';

import { createApp } from 'vue';

const app = createApp({});

app.component('Trips', Trips);
app.component('Days', Days);
app.component('Day', Day);
app.component('Stage', Stage);

app.mount("#app");


// Logica per i Form di eliminazione 

document.querySelectorAll('.item-delete-form').forEach(form => {
    form.addEventListener('submit', (ev)=>{
        ev.preventDefault();
        console.log('test')
        const modalDOMElement = form.querySelector('.my-modal');
        const modalDOMElementYes = form.querySelector('.my-modal-yes');
        const modalDOMElementNo = form.querySelector('.my-modal-no');

        modalDOMElement.classList.add('visible');

        modalDOMElementNo.addEventListener('click', function(){
            modalDOMElement.classList.remove('visible');
        })
    })
})
<script>
import axios from 'axios';
import { store } from '../../../store';

export default {

    props: {
        trips: Array,
    },

    data() {
        return {
            title: 'I Miei Viaggi',
            store,
            newTrip: false,
            editTrip: false,
            deleteTrip: false,
            deleteFormId: null,
            loading: false,
            notes: {
                active: false,
                id: null
            },
            coverName: '',
            form: {
                name: '',
                description: null,
                notes: null,
                cover: null,
                startDate: null,
                endDate: null
            },
            editForm: {
                id: null,
                name: '',
                description: null,
                notes: null,
                cover: null,
                startDate: null,
                endDate: null
            },
            csrfToken: null
        }
    },

    created() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').content
    },


    methods: {
        rowGutter()
        {
            if(this.newTrip || this.editTrip)
            {
                return ''
            }else if(!this.newTrip && !this.editTrip)
            {                
                return 'g-5'
            }
        },

        dbDate(date) {
            if (date) {
                const dbFormatDateParts = date.split('/')

                const day = dbFormatDateParts[0];
                const month = dbFormatDateParts[1];
                const year = dbFormatDateParts[2];

                const dbFormatDate = `${year}-${month}-${day}`;

                return dbFormatDate;
            }
        },

        toggleAddTrip() {
            this.newTrip = !this.newTrip;
        },

        toggleEditTrip(trip) {
            this.editTrip = !this.editTrip;
            this.editForm.cover = null;
            this.editForm.id = trip.id;
            this.editForm.name = trip.name;
            this.editForm.description = trip.description;
            this.editForm.notes = trip.notes;
            this.editForm.startDate = this.dbDate(trip.startDate);
            this.editForm.endDate = this.dbDate(trip.endDate);
            this.coverName = trip.cover;
        },

        toggleDeleteTrip(id) {
            this.deleteTrip = !this.deleteTrip;
            this.deleteFormId = id;
        },

        async submitForm() {

            const newErrorDOMElement = document.querySelector('#newTripBtn');
            let smallErr = document.querySelector('#newTripError');

            if (!smallErr) {
                smallErr = document.createElement('small');
                smallErr.id = 'newTripError';
            }else{
                smallErr.innerHTML = '';
            }



            this.loading = true;
            try {
                const formData = new FormData();
                formData.append('name', this.form.name);
                formData.append('description', this.form.description);
                formData.append('notes', this.form.notes);
                if (this.form.cover) {
                    formData.append('cover', this.form.cover);
                }
                formData.append('startDate', this.form.startDate);
                formData.append('endDate', this.form.endDate);

                await axios.post('/admin/trips/', formData, {
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Content-Type': 'multipart/form-data'
                    }
                })
                this.newTrip = false;
                this.loading = false;
                this.store.forceReload();
            }
            catch (error) {
                this.loading = false;


                const startDate = this.form.startDate;
                const endDate = this.form.endDate;


                const [startYear, startMonth, startDay] = startDate.split('-').map(Number);
                const [endYear, endMonth, endDay] = endDate.split('-').map(Number);

                let isValid = true;


                if (startYear > endYear) {
                    isValid = false;
                } else if (startYear === endYear) {
                    if (startMonth > endMonth) {
                        isValid = false;
                    } else if (startMonth === endMonth) {
                        if (startDay > endDay) {
                            isValid = false;
                        }
                    }
                }

                if (!isValid) {
                    console.error('La data di Partenza non può essere successiva rispetto alla data di Arrivo.');
                    smallErr.innerHTML = 'La data di Partenza non può essere successiva rispetto alla data di Arrivo.';
                    smallErr.classList.add('text-danger', 'ms-5', 'fw-bold');
                    newErrorDOMElement.appendChild(smallErr);                    

                } else {

                    console.error(error.response.data);
                }
            }
        },

        async editTripForm(id) {
            const editErrorDOMElement = document.querySelector('#editTripBtn');
            let smallErr = document.querySelector('#editTripError');

            if (!smallErr) {
                smallErr = document.createElement('small');
                smallErr.id = 'editTripError';
            }else{
                smallErr.innerHTML = '';
            }


            this.loading = true;
            const formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('name', this.editForm.name);
            formData.append('description', this.editForm.description);
            formData.append('notes', this.editForm.notes);
            if (this.editForm.cover !== null) {
                formData.append('cover', this.editForm.cover);
            }
            formData.append('startDate', this.editForm.startDate);
            formData.append('endDate', this.editForm.endDate);
            try {

                await axios.post(`/admin/trips/${id}`, formData, {
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Content-Type': 'multipart/form-data'
                    }
                })

                this.editTrip = false;
                this.loading = false;
                this.store.forceReload();
            }
            catch (error) {
                this.loading = false;


                const startDate = this.editForm.startDate;
                const endDate = this.editForm.endDate;


                const [startYear, startMonth, startDay] = startDate.split('-').map(Number);
                const [endYear, endMonth, endDay] = endDate.split('-').map(Number);

                let isValid = true;


                if (startYear > endYear) {
                    isValid = false;
                } else if (startYear === endYear) {
                    if (startMonth > endMonth) {
                        isValid = false;
                    } else if (startMonth === endMonth) {
                        if (startDay > endDay) {
                            isValid = false;
                        }
                    }
                }

                if (!isValid) {
                    console.error('La data di Partenza non può essere successiva rispetto alla data di Arrivo.');

                    smallErr.innerHTML = 'La data di Partenza non può essere successiva rispetto alla data di Arrivo.';
                    smallErr.classList.add('text-danger', 'ms-5', 'fw-bold');
                    editErrorDOMElement.appendChild(smallErr);                    
                    
                } else {

                    console.error(error.response.data);
                }
            }
        },

        async deleteTripForm(id) {
            this.loading = true;
            const formData = new FormData();
            formData.append('_method', 'DELETE');

            await axios.post(`/admin/trips/${id}`, formData, {
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'multipart/form-data'
                }
            })

            this.deleteTrip = false;
            this.store.forceReload();
        },

        handleFileChange(event) {
            this.form.cover = event.target.files[0];
            this.editForm.cover = event.target.files[0];
        },

        getImageUrl(cover) {
            return cover || 'https://storage.googleapis.com/travel-storage.appspot.com/default-trip.jpg';
        },

        whileLoading() {
            if (this.loading === true) {
                return 'while-loading'
            }
            else {
                return ''
            }
        },

        openNotes(id) {
            this.notes.active = true;
            this.notes.id = id;
        },

        closeNotes() {
            this.notes.active = false;
            this.notes.id = null;
        }
    }
}
</script>


<template>
    <section>
        <!-- Form per la CREAZIONE di un nuovo viaggio -->
        <div class="add-trip-modal" v-show="newTrip" :class="whileLoading()">
            <div class="add-trip-window">
                <span class="btn link-danger close-trip-modal" @click="toggleAddTrip">Annulla</span>

                <form @submit.prevent="submitForm">
                    <div class="mb-3 ">
                        <label class="mt-5 form-label fw-bold" for="name">Nome del viaggio:</label>
                        <input :class="whileLoading()" class="form-control" type="text" id="name" name="name"
                            v-model="form.name" required>
                    </div>
                    <div class="mb-3 ">
                        <label :class="whileLoading()" class="form-label fw-bold" for="description">Descrizione:</label>
                        <textarea :class="whileLoading()" class="form-control" id="description" name="description"
                            v-model="form.description"></textarea>
                    </div>


                    <div class="mb-3">
                        <label for="image" :class="whileLoading()" class="form-label fb-bold">Carica un'immagine</label>
                        <input class="form-control text-blue" type="file" name="image" id="image"
                            accept=".jpg, .jpeg, .png, .bmp, .svg, .webp" @change="handleFileChange">
                    </div>

                    <div class="mb-3 ">
                        <label :class="whileLoading()" class="form-label fw-bold" for="notes">Note:</label>
                        <textarea :class="whileLoading()" class="form-control" id="notes" name="notes"
                            v-model="form.notes"></textarea>
                    </div>

                    <div class="mb-3 ">
                        <label :class="whileLoading()" class="form-label fw-bold" for="startDate">Data di Inizio
                            Viaggio:</label>
                        <input :class="whileLoading()" class="form-control" type="date" id="startDate" name="startDate"
                            v-model="form.startDate" required>
                    </div>

                    <div class="mb-3 ">
                        <label :class="whileLoading()" class="form-label fw-bold" for="endDate">Data di Fine
                            Viaggio:</label>
                        <input :class="whileLoading()" class="form-control" type="date" id="endDate" name="endDate"
                            v-model="form.endDate" required>
                    </div>
                    <div  id="newTripBtn">
                        <button v-if="!loading" class="my-3 btn btn-outline-dark" type="submit">Inserisci Viaggio</button>
                        <p v-else class="text-center">
                            <span class="spin"></span>
                        </p>
                    </div>
                </form>
            </div>
        </div>
        <!-- Form per la MODIFICA di un viaggio esistente -->
        <div class="add-trip-modal" v-if="editTrip" :class="whileLoading()">
            <div class="add-trip-window">
                <span class="btn link-danger close-trip-modal" @click="toggleEditTrip">Annulla</span>
                <span class="w-100 d-flex justify-content-center">
                    <img :src="getImageUrl(coverName)" class="card-img-top my-card-img" alt="...">
                </span>
                <form @submit.prevent="editTripForm(editForm.id)" :class="whileLoading()">
                    <!-- Nome -->
                    <div class="mb-3 ">
                        <label :class="whileLoading()" class="mt-5 form-label fw-bold" for="name">Nome del
                            viaggio:</label>
                        <input :class="whileLoading()" class="form-control" type="text" id="name" name="name"
                            v-model="editForm.name" required>
                    </div>
                    <!-- Descrizione -->
                    <div class="mb-3 ">
                        <label :class="whileLoading()" class="form-label fw-bold" for="description">Descrizione:</label>
                        <textarea :class="whileLoading()" class="form-control" id="description" name="description"
                            v-model="editForm.description"></textarea>
                    </div>

                    <!-- Cover -->
                    <div class="mb-3">
                        <label for="image" :class="whileLoading()" class="form-label fb-bold">Carica un'immagine</label>
                        <input class="form-control text-blue" type="file" name="image" id="image"
                            accept=".jpg, .jpeg, .png, .bmp, .svg, .webp" @change="handleFileChange">
                    </div>

                    <!-- Note -->
                    <div class="mb-3 ">
                        <label :class="whileLoading()" class="form-label fw-bold" for="notes">Note:</label>
                        <textarea :class="whileLoading()" class="form-control" id="notes" name="notes"
                            v-model="editForm.notes"></textarea>
                    </div>

                    <!-- Data Inizio -->
                    <div class="mb-3 ">
                        <label :class="whileLoading()" class="form-label fw-bold" for="startDate">Data di Inizio
                            Viaggio:</label>
                        <input :class="whileLoading()" class="form-control" type="date" id="startDate" name="startDate"
                            v-model="editForm.startDate" required>
                    </div>

                    <!-- Data Fine -->
                    <div class="mb-3 ">
                        <label :class="whileLoading()" class="form-label fw-bold" for="endDate">Data di Fine
                            Viaggio:</label>
                        <input :class="whileLoading()" class="form-control" type="date" id="endDate" name="endDate"
                            v-model="editForm.endDate" required>
                    </div>

                    <div  id="editTripBtn">
                        <button v-if="!loading" class="my-3 btn btn-outline-dark" type="submit">Modifica Viaggio</button>
                        <p v-else class="text-center">
                            <span class="spin"></span>
                        </p>
                    </div>
                </form>
            </div>
        </div>
        <div class="container">
            <h1 class="text-center title">
                {{ title }}
            </h1>
            <h2>
                <span class="btn btn-outline-primary my-3" @click="toggleAddTrip">Crea Viaggio</span>
            </h2>
        </div>

        <!-- Viaggi presenti nel db -->
        <div class="container">
            <div class="row" :class="rowGutter()">
                <div class="col-12 col-md-6 col-lg-4" v-for="trip in trips" :key="trip.id">
                    <div class="card">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <a :href="'/admin/days/showOne/' + trip.id" class="text-center" :class="whileLoading()">
                                <img :src="getImageUrl(trip.cover)" class="card-img-top" alt="...">
                                <h4 class="card-title">{{ trip.name }}</h4>
                            </a>
                            <div class="my-card-description">
                                <p>{{ trip.description }}</p>
                            </div>
                            <div class="my-card-notes">
                                <i class="bi bi-card-list" @click="openNotes(trip.id)"></i>
                                <div v-show="notes.active && notes.id === trip.id" class="my-notes" @click="closeNotes">
                                    {{ trip.notes }}
                                </div>
                            </div>
                            <div class="d-flex justify-content-between px-1 mt-3">
                                <p class="btn btn-outline-success" @click="toggleEditTrip(trip)"> Modifica </p>
                                <form class="item-delete-form" :class="whileLoading()">

                                    <button class="btn btn-link link-danger"
                                        @click="toggleDeleteTrip(trip.id)">Elimina</button>

                                    <div class="my-modal">
                                        <h5 class="text-center">Vuoi Davvero Eliminare il Viaggio?!</h5>
                                        <div class="my-modal__box" v-if="!loading">
                                            <p class="btn btn-danger my-modal-yes"
                                                @click="deleteTripForm(deleteFormId)">Sì</p>
                                            <p class="btn btn-success my-modal-no">No</p>
                                        </div>
                                        <p v-else class="text-center">
                                            <span class="spin"></span>
                                        </p>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card-footer">
                            <p>Dal <strong>{{ trip.startDate }}</strong> al <strong>{{ trip.endDate }} </strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</template>


<style lang="scss" scoped>
.title {
    color: coral;
    padding: 30px 0;
}

img {
    height: 200px;
}

.spin {
    display: inline-block;
    border: 8px solid rgba(255, 255, 255, 0.1);
    border-top: 8px solid coral;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

.while-loading {
    pointer-events: none;
    color: rgba($color: #000000, $alpha: 0.4);
}

.add-trip-modal {
    height: 100vh;
    width: 100vw;
    position: absolute;
    z-index: 99;
    background-color: rgba($color: #000000, $alpha: 0.7);

    .close-trip-modal {
        //position: absolute;
        display: flex;
        justify-content: end;
    }

    .add-trip-window {
        position: fixed;
        padding: 5px;
        top: 0;
        bottom: 0;
        right: 0;
        left: 0;
        background-color: white;
        color: black;
        overflow: scroll;
    }
}

@media(min-width: 500px) {
    .add-trip-modal {
        height: 100vh;
        width: 100vw;
        position: absolute;
        z-index: 99;
        background-color: rgba($color: #000000, $alpha: 0.7);

        .close-trip-modal {
            position: absolute;
            top: 5%;
            right: 5%;
        }

        .add-trip-window {
            min-width: 50%;
            min-height: 25%;
            position: absolute;
            top: 10%;
            left: 25%;
            bottom: auto;
            right: auto;
            background-color: white;
            color: black;
            padding: 20px 30px;
        }
    }

}
</style>
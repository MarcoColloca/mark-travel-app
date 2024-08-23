<script>
import { store } from '../../../store';
import axios from 'axios';

export default {
    props: {
        days: Array,
        trip: Array
    },

    data() {
        return {
            title: 'Test',
            store,
            loading: false,
            csrfToken: null,
            myTrip: {},
            notes: {
                active: false,
                id: null
            },
            notesForm: {
                active: false,
                id: null,
                content: ''
            },
            newStage: false,
            newStageForm: {
                day_id: null,
                name: '',
                cover: null,
                notes: null,
                description: null,
                curiosities: null,
                lat: null,
                lon: null,
                address: null
            },
            showCarousel: false,
        }
    },

    mounted() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        this.getTrip()
    },

    methods: {
        getTrip() {
            this.myTrip = this.trip[0];
        },

        toggleStage(id) {
            this.newStage = !this.newStage;
            if (id) {
                this.newStageForm.day_id = id;
            } else {
                this.newStageForm.day_id = null;
            }
        },

        whileLoading() {
            if (this.loading === true) {
                return 'while-loading'
            }
            else {
                return ''
            }
        },

        toggleCarousel() {
            this.showCarousel = !this.showCarousel
        },

        openNotes(id) {
            this.notes.active = true;
            this.notes.id = id;
        },

        closeNotes() {
            this.notes.active = false;
            this.notes.id = null;
        },

        editNotes(id, content) {
            this.notesForm.active = true;
            this.notesForm.content = content;
            this.notesForm.id = id;
        },
        closeEditNotes() {
            this.notesForm.active = false;
            this.notesForm.content = '';
            this.notesForm.id = null;

        },

        clearNewStageForm() {
            this.newStageForm.name = '';
            this.newStageForm.cover = null;
            this.newStageForm.notes = null;
            this.newStageForm.description = null;
            this.newStageForm.curiosities = null;
            this.newStageForm.lat = null;
            this.newStageForm.lon = null;
            this.newStageForm.address = null;
        },

        // Metodi per l'invio dei dati
        handleFileChange(event) {
            this.newStageForm.cover = event.target.files[0];
        },

        async submitNotes() {
            this.loading = true;
            try {
                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('notes', this.notesForm.content);

                await axios.post(`/admin/days/${this.notesForm.id}`, formData, {
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                    }
                })

                this.loading = false;
                this.closeEditNotes();
                this.store.forceReload();
            }
            catch (e) {
                this.loading = false;
                console.log(e);
            }
        },

        async submitStageForm() {
            console.log(this.newStageForm)
            this.loading = true;
            const formData = new FormData();
            formData.append('day_id', this.newStageForm.day_id);
            formData.append('name', this.newStageForm.name);
            formData.append('description', this.newStageForm.description);
            formData.append('notes', this.newStageForm.notes);
            formData.append('curiosities', this.newStageForm.curiosities);
            formData.append('address', this.newStageForm.address);
            if(this.newStageForm.lat)
            {
                formData.append('lat', this.newStageForm.lat);
            }
            if(this.newStageForm.lon)
            {
                formData.append('lon', this.newStageForm.lon);
            }
            
            if (this.newStageForm.cover) {
                formData.append('cover', this.newStageForm.cover);
            }

            await axios.post('/admin/stages/', formData, {
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Content-Type': 'multipart/form-data'
                }
            })
            this.clearNewStageForm()
            this.newStage = false;
            this.loading = false;
            this.store.forceReload();
        }

    }
}
</script>



<template>
    <!-- Form per l'inserimento di una nuova Tappa -->
    <div class="add-stage-modal" v-show="newStage" :class="whileLoading()">
        <div class="add-stage-window">
            <span class="btn link-danger close-stage-modal" @click="toggleStage()">Annulla</span>
            <form @submit.prevent="submitStageForm">
                <!-- Nome -->
                <div class="mb-3 ">
                    <label :class="whileLoading()" class="mt-5 form-label fw-bold" for="name">Nome della Tappa:</label>
                    <input :class="whileLoading()" class="form-control" type="text" id="name" name="name"
                        v-model="newStageForm.name" required>
                </div>

                <!-- Descrizione -->
                <div class="mb-3 ">
                    <label :class="whileLoading()" class="form-label fw-bold" for="description">Descrizione:</label>
                    <textarea :class="whileLoading()" class="form-control" id="description" name="description"
                        v-model="newStageForm.description"></textarea>
                </div>

                <!-- Curiosità -->
                <div class="mb-3 ">
                    <label :class="whileLoading()" class="form-label fw-bold" for="curiosities">Curiosità:</label>
                    <textarea :class="whileLoading()" class="form-control" id="curiosities" name="curiosities"
                        placeholder="Metti un punto alla fine di ogni curiosità."
                        v-model="newStageForm.curiosities"></textarea>
                </div>

                <!-- Immagine -->
                <div class="mb-3">
                    <label for="image" :class="whileLoading()" class="form-label fb-bold">Carica un'immagine</label>
                    <input :class="whileLoading()" class="form-control text-blue" type="file" name="image" id="image"
                        accept=".jpg, .jpeg, .png, .bmp, .svg, .webp" @change="handleFileChange">
                </div>

                <!-- Note -->
                <div class="mb-3 ">
                    <label :class="whileLoading()" class="form-label fw-bold" for="notes">Note:</label>
                    <textarea :class="whileLoading()" class="form-control" id="notes" name="notes"
                        v-model="newStageForm.notes"></textarea>
                </div>

                <!-- Indirizzo -->
                <div class="mb-3 ">
                    <label :class="whileLoading()" class="mt-5 form-label fw-bold" for="address">Indirizzo:</label>
                    <input :class="whileLoading()" class="form-control" type="text" id="address" name="address"
                        v-model="newStageForm.address">
                </div>

                <!-- Latitudine -->
                <div class="mb-3 ">
                    <label :class="whileLoading()" class="form-label fw-bold" for="lat">Latitudine: </label>
                    <input :class="whileLoading()" class="form-control" type="number" step="0.000001" id="lat"
                        name="lat" v-model="newStageForm.lat">
                </div>
                <!-- Longitudine -->
                <div class="mb-3 ">
                    <label :class="whileLoading()" class="form-label fw-bold" for="lon">Longitudine:</label>
                    <input :class="whileLoading()" class="form-control" type="number" step="0.000001" id="lon"
                        name="lon" v-model="newStageForm.lon">
                </div>

                <div id="newTripBtn">
                    <button v-if="!loading" class="my-3 btn btn-outline-dark" type="submit">Aggiungi Tappa</button>
                    <p v-else class="text-center">
                        <span class="spinDark"></span>
                    </p>
                </div>
            </form>
        </div>
    </div>


    <div class="container">
        <h1>
            {{ myTrip.name }}
        </h1>
    </div>

    <!-- Lista dei giorni -->
    <div class="container">
        <div class="row g-5">
            <div class="col-12 col-lg-3" v-for="day in days" :key="day.id">
                <div class="day-column">
                    <h4>
                        <a :href="'/admin/days/' + day.id">
                            {{ day.date }}
                        </a>
                    </h4>
                    <!-- Note dei Giorni -->
                    <div class="my-card-notes">
                        <div class="w-100 d-flex justify-content-around">
                            <i class="bi bi-card-list my-edit-notes" @click="openNotes(day.id)"></i>
                            <i class="bi bi-calendar-plus my-edit-notes"
                                @click="editNotes(day.id, day.notes), console.log(day.id)"></i>
                        </div>
                        <div v-show="notesForm.active && notesForm.id === day.id" class="my-notes">
                            <form @submit.prevent="submitNotes" :class="whileLoading()">
                                <textarea v-model="notesForm.content" class="w-100 my-edit-textarea" name=""
                                    id=""></textarea>
                                <div class="d-flex justify-content-around align-items-baseline mt-3">
                                    <button v-if="!loading" class="btn btn-outline-reverse-coral">Invia</button>
                                    <h3 v-if="!loading" class="btn btn-outline-reverse-coral" @click="closeEditNotes">X
                                    </h3>
                                    <p v-else class="text-center"><span class="spin"></span></p>
                                </div>
                            </form>
                        </div>
                        <div v-show="notes.active && notes.id === day.id" class="my-notes" @click="closeNotes">
                            <p>{{ day.notes }}</p>
                        </div>
                    </div>
                    <!-- Lista delle Tappe -->
                    <ul class="stages-list">
                        <li v-for="stage in day.stages">
                            <a :href="'/admin/stages/' + stage.id">{{ stage.name }}</a>
                        </li>
                        <li>
                            <p class="new-stage" @click="toggleStage(day.id)">Nuova Tappa</p>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <!-- pulsante per nascondere il carosello -->
    <!-- <div class="d-flex justify-content-center mt-5" >
        <h2 @click="toggleCarousel" class="text-danger">Q</h2>
    </div> -->
    <!-- Carosello Immagini -->
    <!-- <div class="container mt-5" v-show="showCarousel">
        <div class="trip-img-carousel--container">        
            <div class="trip-img-carousel--content">
    
            </div>
        </div>
    </div> -->
</template>




<style lang="scss" scoped>
.spinDark {
    display: inline-block;
    border: 8px solid rgba(255, 255, 255, 0.1);
    border-top: 8px solid coral;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
}

.spin {
    display: inline-block;
    border: 8px solid rgba(255, 255, 255, 0.1);
    border-top: 8px solid rgb(255, 255, 255);
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

h1 {
    color: coral;
    padding: 50px 0;
    text-align: center;
}

.add-stage-modal {
    height: 100vh;
    width: 100vw;
    position: absolute;
    z-index: 99;
    background-color: rgba($color: #000000, $alpha: 0.7);

    .close-stage-modal {
        // position: absolute;
        top: 5%;
        right: 5%;
    }

    .add-stage-window {
        position: fixed;
        padding: 5px;
        top: 0;
        bottom: 0;
        right: 0;
        left: 0;
        background-color: white;
        color: black;
        padding: 20px 30px;
    }

}

@media (min-width: 500px) {
    .add-stage-modal {
        height: 100vh;
        width: 100vw;
        position: absolute;
        z-index: 99;
        background-color: rgba($color: #000000, $alpha: 0.7);

        .close-stage-modal {
            position: absolute;
            top: 5%;
            right: 5%;
        }

        .add-stage-window {
            min-width: 50%;
            min-height: 25%;
            position: absolute;
            top: 10%;
            bottom: auto;
            left: 25%;
            right: auto;
            background-color: white;
            color: black;
            padding: 20px 30px;
        }

    }
}

.day-column {
    border-left: 1px solid black;
    border-right: 1px solid black;
    min-height: 200px;
    padding-right: 5px;

    &:hover {
        border-left: 1px solid coral;
        border-right: 1px solid coral;
        transition: transform 0.1s ease;
        transform: scale(1.05);

        h4 {
            color: coral;
            margin-bottom: 20px;
        }
    }

    h4 {
        text-align: center;
    }

    .stages-list {
        list-style: square;

        li {
            margin-bottom: 10px;

            .new-stage {
                font-size: 12px;
                font-weight: 300;

                &:hover {
                    cursor: pointer;
                    color: crimson;
                    font-size: 16px;
                    font-weight: 700;
                }
            }
        }
    }
}

.trip-img-carousel--container {
    border: 1px solid salmon; //debug
    background-color: gainsboro; //debug
    height: 300px;
}
</style>